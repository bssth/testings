<?php

namespace App\Api;

use App\Model\Item;
use App\Service\Item as ItemService;
use App\Validator\Item as Validator;
use Exception;
use Framework\Rest\EndpointController;
use Framework\Web\Request;
use Framework\Web\Response;

final class Catalog extends EndpointController
{
    public function actionList(Request $request): Response
    {
        $result = [];
        $last_id = -1;

        $post = $request->getPost();
        $page = $post->page ? (int)$post->page : 1;
        $order = ($post->order && (int)$post->order === 2) ? 'price' : 'id';
        $order_dir = (bool)$post->order_dir;

        $items = new ItemService();

        foreach($items->getPage($page, 100, $order, $order_dir) as $i) {
            if($i->id > $last_id) {
                $last_id = $i->id;
            }

            $result[] = [
                'id' => (int)$i->id,
                'name' => $i->name ?? 'Без названия',
                'desc' => $i->desc ?? '',
                'price' => (int)($i->price ?? 0),
                'image_url' => $i->image_url ?? ''
            ];
        }

        return $this->apiResponse($request, [
            'count' => $items->getItemsCount(),
            'pages' => $items->getPagesCount(),
            'list' => $result,
            'offset' => $last_id
        ]);
    }

    public function actionEdit(Request $request): Response
    {
        $id = (int)$request->getPost()->id;
        if($id <= 0) {
            return $this->apiError($request, 400, 'Не передан ID');
        }

        $item = Item::getById($id);
        if(!$item) {
            return $this->apiError($request, 404, 'Товар не найден');
        }

        $new_data = (new Validator())->sanitize(
            (array)$request->getPost()
        );

        if(count($new_data)) {
            foreach ($new_data as $key => $value) {
                $item->{$key} = $value;
            }

            $result = $item->save();
        } else {
            $result = false;
        }

        return $this->apiResponse($request, [
            'result' => $result
        ]);
    }

    public function actionCreate(Request $request): Response
    {
        $data = (new Validator())->sanitize(
            (array)$request->getPost()
        );

        if(!$data || !isset($data['name'])) {
            return $this->apiError($request, 400, 'Укажите имя товара');
        }

        try {
            $items = new ItemService();
            $insert_id = $items->insert($data);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $this->apiError($request, 500, 'Ошибка создания товара');
        }

        return $this->apiResponse($request, [
            'result' => $insert_id > 0, 'item_id' => $insert_id
        ]);
    }

    public function actionDelete(Request $request): Response
    {
        $id = (int)$request->getPost()->id;
        if($id <= 0) {
            return $this->apiError($request, 400, 'Не передан ID');
        }

        $items = new ItemService();
        $tryRemove = $items->remove($id);
        if(!$tryRemove) {
            return $this->apiError($request, 404, 'Товар не найден');
        }

        return $this->apiResponse($request, ['result' => true]);
    }
}
