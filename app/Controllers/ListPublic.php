<?php

namespace App\Controllers;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class ListPublic extends AuthController
{
    public function product()
    {
        $query['data'] = ['product'];
        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'product',
            'product_value_value' => 'value',
            'product_type_name' => 'type',
            'product_category_name' => 'category',
            'product_price' => 'price',
        ];

        $query['search_data'] = [
            'product_category_name',
            'product_name',
        ];

        $query['filter'] = [
            "product_category_name",
            "product_value_value",
        ];


        $data = generateListData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Product', $data);
    }

    public function category()
    {
        $query['data'] = ['category'];
        $query['select'] = [
            'category_id' => 'id',
            'category_name' => 'category',
        ];
        $query['where_detail'] = [
            "WHERE category_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Category', $data);
    }

    public function type()
    {
        $query['data'] = ['type'];
        $query['select'] = [
            'type_id' => 'id',
            'type_name' => 'type',
        ];
        $query['where_detail'] = [
            "WHERE type_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Type', $data);
    }

    public function value()
    {
        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'value',
        ];
        $query['where_detail'] = [
            "WHERE value_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Value', $data);
    }

}
