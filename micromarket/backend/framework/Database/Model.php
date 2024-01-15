<?php

namespace Framework\Database;

use Generator;

/**
 * Слой абстракции над базой данных, предоставляющий удобный
 * доступ к строкам в таблице в виде мутабельных классов
 */
class Model
{
    const ID_FIELD = 'id';

    protected int $id;
    protected array $row;
    protected array $changed = [];

    public function __construct(int $id, array $row = null)
    {
        $this->id = $id;
        $this->row = $row;
    }

    /**
     * Название таблицы. Перезаписывается в дочерних классах-моделях
     */
    public static function getTableName(): string
    {
        return 'test';
    }

    /**
     * Вспомогательная функция для безопасной передачи названия таблицы в запрос.
     * PDO не позволяет сделать это из коробки.
     */
    protected static function bindTableName(string $query): string
    {
        return str_replace(':table',
            str_replace(['\'', '"'], '`', static::getTableName()), $query
        );
    }

    public static function count(): int
    {
        $pdo = PDO::i();
        $query = $pdo->query(
            static::bindTableName('SELECT COUNT(*) FROM :table')
        );
        $query->execute();
        return $query->fetchColumn();
    }

    public static function getAll(int $offset = 0, int $limit = 100, string $order_by = null, bool $asc = false): Generator
    {
        $query_str = static::bindTableName('SELECT * FROM :table');

        // Определяет, что мы сортируем результаты исключительно по id
        // В таком случае применяется более быстрый алгоритм постраничного вывода
        $order_query = 'ORDER BY `' . $order_by . '` ' . ($asc ? 'ASC' : 'DESC');

        $query_str .= ' ' . $order_query . ' LIMIT :offset,:limit';

        $pdo = PDO::i();
        $query = $pdo->prepare(
            $query_str
        );

        $query->bindValue('limit', $limit, \PDO::PARAM_INT);
        $query->bindValue('offset', $offset, \PDO::PARAM_INT);
        $query->execute();

        while($row = $query->fetch()) {
            yield new Model($row[static::ID_FIELD], $row);
        }
    }

    public static function getById(int $id): ?Model
    {
        $pdo = PDO::i();
        $query = $pdo->prepare(
            static::bindTableName('SELECT * FROM :table WHERE `'.static::ID_FIELD.'` = :id')
        );

        $query->execute([
            static::ID_FIELD => $id
        ]);
        $result = $query->fetch();

        if(!$result) {
            return null;
        }

        return new static($id, $result);
    }

    /**
     * Хелпер для генерации значений выражения SET в (например, для insert и update запросов)
     */
    protected static function generateSet(array $data): array
    {
        $query_parts = [];
        $pdo_params = [];

        foreach($data as $key => $value) {
            $query_parts[] = '`' . $key . '`=:' . $key;
            $pdo_params[ trim($key) ] = $value;
        }

        return [
            implode(', ', $query_parts),
            $pdo_params
        ];
    }

    /**
     * Вставить строку на основе массива в базу данных
     */
    public static function insert(array $data): ?int
    {
        $set = self::generateSet($data);

        $pdo = PDO::i();
        $query = $pdo->prepare(
            static::bindTableName('INSERT INTO :table SET '
                . $set[0]
            )
        );

        $created = $query->execute($set[1]);

        if($created) {
           return $pdo->lastInsertId();
        } else {
            return null;
        }
    }

    public function save(): bool
    {
        $data = [];
        foreach($this->changed as $key) {
            if(isset($this->row[$key])) {
                $data[$key] = $this->row[$key];
            }
        }

        $set = static::generateSet($data);

        $pdo = PDO::i();
        $query = $pdo->prepare(
            static::bindTableName('UPDATE :table SET '
                . $set[0] . ' WHERE `'.static::ID_FIELD.'` = :id'
            )
        );

        return $query->execute(
            array_merge($set[1], [static::ID_FIELD => $this->id])
        );
    }

    public function __get(string $key)
    {
        return $this->row[$key] ?? '';
    }

    public function __set(string $key, $value)
    {
        $this->row[$key] = $value;
        $this->changed[] = $key;
    }

    public function remove(): bool
    {
        $pdo = PDO::i();
        $query = $pdo->prepare(
            static::bindTableName('DELETE FROM :table WHERE `'.self::ID_FIELD.'` = :id')
        );

        return $query->execute(['id' => $this->id]);
    }

    public function getRow(): array
    {
        return $this->row;
    }
}
