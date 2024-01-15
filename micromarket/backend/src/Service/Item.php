<?php

namespace App\Service;

use App\Model\Item as ItemModel;
use Framework\Cache\Redis;
use Framework\Database\Model;
use Generator;

/**
 * Промежуточный сервис для работы с товарами и постраничного вывода с кэшем
 */
class Item
{
    const TOTAL_ITEMS_COUNT_KEY = 'items_total_cnt';
    const ITEMS_PAGE_KEY        = 'items_page_';
    // const ITEM_KEY              = 'item_';

    public function getPage(int $page = 1, int $per_page = 100, string $order = Model::ID_FIELD, bool $order_dir = false): Generator
    {
        // Счёт страниц начинается с нуля, не менее одного товара на страницу
        if($page <= 0) {
            $page = 1;
        }
        if($per_page < 1) {
            $per_page = 1;
        }
        if($page > ($pages_count = $this->getPagesCount($per_page))) {
            $page = $pages_count;
        }

        $offset = ($page - 1) * $per_page;

        // Формируем ключ в кэше для текущей страницы при переданных настройках
        $key = self::ITEMS_PAGE_KEY . $page . '_' . $order . '_' . (int)$order_dir;
        $ttlKey = $key . '_ttl';

        // В кэше хранятся все товары на странице в целях повышения быстродействия.
        // При извлечении пытаемся достать всё из кэша и прогреть его, если там ничего нет.
        $cached = Redis::i()->exists($key);
        $ttl = Redis::i()->get($ttlKey);

        if($cached && $ttl > time()) {
            for($i = 0; $i < $per_page; $i++) {
                $row = unserialize(Redis::i()->lIndex($key, $i));
                if(is_array($row) && isset($row['id'])) {
                    yield new Model($row['id'], $row);
                }
            }

            /* $list_ids = explode(',', $cached);
            foreach($list_ids as $id) {
                $data = Redis::i()->hGetAll(self::ITEM_KEY . $id);
                if(count($data)) {
                    yield new ItemModel($id, $data);
                }
            } */
        } else {
            Redis::i()->del($key);
            Redis::i()->set($ttlKey, time() + 60);

            foreach(ItemModel::getAll(
                $offset, $per_page, $order, $order_dir
            ) as $m) {
                // Redis::i()->hMSet(self::ITEM_KEY . $m->id, $m->getRow());
                // Redis::i()->append($key, $m->id . ',');

                Redis::i()->lPush($key, serialize($m->getRow()));
                yield $m;
            }
        }
    }

    public function getItemsCount(): int
    {
        $cnt = Redis::i()->get(self::TOTAL_ITEMS_COUNT_KEY);

        if(!$cnt) {
            $cnt = ItemModel::count();
            Redis::i()->set(self::TOTAL_ITEMS_COUNT_KEY, $cnt);
        }

        return (int)$cnt;
    }

    public function insert(array $data): int
    {
        $insert_id = ItemModel::insert($data);
        if($insert_id > 0) {
            // После удачной вставки в базу нужно сбросить и заново прогреть кэш,
            // чтобы фронтенду не пришлось после ждать долгой прогрузки списка
            $this->clearPagesCache();
            $this->reloadCache();
        }

        return $insert_id;
    }

    public function clearPagesCache()
    {
        Redis::i()->del(
            Redis::i()->keys(self::ITEMS_PAGE_KEY . '*')
        );
    }

    public function remove(int $id): bool
    {
        $item = ItemModel::getById($id);
        if(!$item) {
            return false;
        }

        if($item->remove()) {
            // Redis::i()->del(self::ITEM_KEY . $item->id);
            $this->clearPagesCache();
            $this->reloadCache();
            return true;
        } else {
            return false;
        }
    }

    public function getPagesCount(int $per_page = 100): int
    {
        return $per_page > 0
            ? ceil($this->getItemsCount() / 100) : 1;
    }

    public function reloadCache()
    {
        $this->dropCache();
        $this->getItemsCount();
    }

    public function dropCache()
    {
        Redis::i()->del(
            self::TOTAL_ITEMS_COUNT_KEY
        );
    }
}
