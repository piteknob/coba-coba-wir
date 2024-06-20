<?php

namespace App\Controllers;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class ListPublic extends AuthController
{
    public function admin()
    {

        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $query['data'] = ['user'];
        $query['select'] = [
            'user.user_id' => 'user_id',
            'user.user_username' => 'username',
            'user.user_password' => 'password',
            'auth_user.auth_user_token' => 'token',
            'auth_user.auth_user_date_login' => 'date_login',
            'auth_user.auth_user_date_expired' => 'date_expired',
        ];
        $query['left_join'] = [
            'auth_user' => 'user.user_id = auth_user.auth_user_id',
        ];
        $query['pagination'] = [
            'pagination' => true
        ];

        $data = generateListData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Admin', $data);
    }

    public function product()
    {
        $query['data'] = ['product'];
        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'product',
            'product_price' => 'price',
            'product_type_name' => 'type',
            'product_category_name' => 'category',
            'product_value_value' => 'value',
            'product_created_at' => 'created_at',
            'product_updated_at' => 'updated_at',
        ];
        $query['pagination'] = [
            'pagination' => true
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
            'category_created_at' => 'created_at',
            'category_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE category_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Category', $data);
    }

    public function DeletedCategory()
    {
        $query['data'] = ['category'];
        $query['select'] = [
            'category_id' => 'id',
            'category_name' => 'category',
            'category_created_at' => 'created_at',
            'category_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE category_deleted_at is not null"
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
            'type_created_at' => 'created_at',
            'type_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE type_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Type', $data);
    }

    public function DeletedType()
    {
        $query['data'] = ['type'];
        $query['select'] = [
            'type_id' => 'id',
            'type_name' => 'type',
            'type_created_at' => 'created_at',
            'type_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE type_deleted_at is not null"
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
            'value_created_at' => 'created_at',
            'value_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE value_deleted_at is null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Value', $data);
    }

    public function DeletedValue()
    {
        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'value',
            'value_created_at' => 'created_at',
            'value_updated_at' => 'updated_at',
        ];
        $query['where_detail'] = [
            "WHERE value_deleted_at is not null"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Value', $data);
    }
}
