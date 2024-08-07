<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Selected extends AuthController
{
    public function category_selected()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        // GET CATEGORY ID FROM PRODUCT
        $id = $this->request->getGet();
        $id = $id['id'];

        $query['data'] = ['product'];
        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'name',
            'product_price' => 'price',
            'product_type_id' => 'type_id',
            'product_type_name' => 'type_name',
            'product_category_id' => 'category_id',
            'product_category_name' => 'category_name',
            'product_value_id' => 'value_id',
            'product_value_value' => 'value_value',
            'product_created_at' => 'created_at',
            'product_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE product_id = $id"
        ];
        $data_product = (array) generateDetailData($this->request->getVar(), $query, $this->db);
        $id_category = $data_product['data'][0]['category_id'];

        // GET DATA SELECTED
        $query_category_selected = "SELECT * FROM `category` WHERE category_id = {$id_category}";
        $data_category_selected = $this->db->query($query_category_selected)->getResultArray();

        // GET DATA NOT SELECTED
        $query_category = "SELECT * FROM `category` WHERE category_id != {$id_category}";
        $data_category= $this->db->query($query_category)->getResultArray();


        // MAKE LIST CATEGORY (SELECTED OR NOT SELECTED)

        $data = [
            'data' => [
                'category_selected' => $data_category_selected,
                'category_not_selected' => $data_category
            ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Category Selected', $data);
    }

    public function type_selected()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        // GET CATEGORY ID FROM PRODUCT
        $id = $this->request->getGet();
        $id = $id['id'];

        $query['data'] = ['product'];
        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'name',
            'product_price' => 'price',
            'product_type_id' => 'type_id',
            'product_type_name' => 'type_name',
            'product_category_id' => 'category_id',
            'product_category_name' => 'category_name',
            'product_value_id' => 'value_id',
            'product_value_value' => 'value_value',
            'product_created_at' => 'created_at',
            'product_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE product_id = $id"
        ];
        $data_product = (array) generateDetailData($this->request->getVar(), $query, $this->db);
        $id_type = $data_product['data'][0]['type_id'];

        // GET DATA SELECTED
        $query_type_selected = "SELECT * FROM `type` WHERE type_id = {$id_type}";
        $data_type_selected = $this->db->query($query_type_selected)->getResultArray();

        // GET DATA NOT SELECTED
        $query_type = "SELECT * FROM `type` WHERE type_id != {$id_type}";
        $data_type = $this->db->query($query_type)->getResultArray();

        // MAKE LIST CATEGORY (SELECTED OR NOT SELECTED)

        $data = [
            'data' => [
                'type_selected' => $data_type_selected,
                'type_not_selected' => $data_type
            ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Type Selected', $data);
    }

    public function value_selected()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        // GET CATEGORY ID FROM PRODUCT
        $id = $this->request->getGet();
        $id = $id['id'];

        $query['data'] = ['product'];
        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'name',
            'product_price' => 'price',
            'product_type_id' => 'type_id',
            'product_type_name' => 'type_name',
            'product_category_id' => 'category_id',
            'product_category_name' => 'category_name',
            'product_value_id' => 'value_id',
            'product_value_value' => 'value_value',
            'product_created_at' => 'created_at',
            'product_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE product_id = $id"
        ];
        $data_product = (array) generateDetailData($this->request->getVar(), $query, $this->db);
        $id_value = $data_product['data'][0]['value_id'];

        // GET DATA SELECTED
        $query_value_selected = "SELECT * FROM `value` WHERE value_id = {$id_value}";
        $data_value_selected = $this->db->query($query_value_selected)->getResultArray();

        // GET DATA NOT SELECTED
        $query_value = "SELECT * FROM `value` WHERE value_id != {$id_value}";
        $data_value = $this->db->query($query_value)->getResultArray();

        // MAKE LIST CATEGORY (SELECTED OR NOT SELECTED)

        $data = [
            'data' => [
                'value_selected' => $data_value_selected,
                'value_not_selected' => $data_value
            ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Type Selected', $data);
    }
}
