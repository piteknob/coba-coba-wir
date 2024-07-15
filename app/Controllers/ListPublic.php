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
            'product_name' => 'product',
            'product_value_value' => 'value',
            'product_type_name' => 'type',
            'product_category_name' => 'category',
            'product_price' => 'price',
<<<<<<< HEAD
=======
        ];
        $query['pagination'] = [
            'pagination' => true
>>>>>>> 53d3be09a120a3d762591ddd6e901c780e799a3b
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
            'value_value' => 'value',
        ];
        $query['where_detail'] = [
            "WHERE value_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Value', $data);
    }

}
