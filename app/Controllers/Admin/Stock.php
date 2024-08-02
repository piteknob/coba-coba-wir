<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Stock extends AuthController
{
    public function list_stock()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $query['data'] = ['product_stock'];
        $query['select'] = [
            'product_stock_id' => 'id',
            'product_stock_product_id' => 'product_id',
            'product_stock_product_name' => 'product_name',
            'product_stock_type_id' => 'type_id',
            'product_stock_type_name' => 'type_name',
            'product_stock_category_id' => 'category_id',
            'product_stock_category_name' => 'category_name',
            'product_stock_value_id' => 'value_id',
            'product_stock_value_value' => 'value_value',
            'product_stock_stock' => 'stock',
            'product_stock_stock_in' => 'stock_in',
            'product_stock_stock_out' => 'stock_out',
        ];
        $query['pagination'] = [
            'pagination' => true
        ];

        $data = generateListData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Stock', $data);
    }

    public function reduce()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }
        $id = $this->request->getVar();
        $post = $this->request->getPost();
        $id = $id['id'];

        // get detail value, type, category
        $query['data'] = ['product'];
        $query['select'] = [
            'product_type_id' => 'type_id',
            'product_category_id' => 'category_id',
            'product_value_id' => 'value_id',
        ];
        $query['where_detail'] = [
            "WHERE product_id = $id"
        ];
        $data = generateDetailData($this->request->getGet(), $query, $this->db);
        foreach ($data as $key => $value) {
            $data = $value[0];
        }

        $queryB['data'] = ['product_stock'];
        $queryB['select'] = [
            'product_stock_product_name' => 'product_name',
            'product_stock_stock' => 'stock'
        ];
        $queryB['where_detail'] = [
            "WHERE product_stock_product_id = $id"
        ];

        $data1 = generateDetailData($this->request->getGet(), $queryB, $this->db);

        // get file to insert
        $quantity = htmlspecialchars($post['value']);
        $type = $data['type_id'];
        $category = $data['category_id'];
        $value = $data['value_id'];

        $sql = "UPDATE product_stock SET product_stock_stock = product_stock_stock - {$quantity},
        product_stock_stock_out = product_stock_stock_out + {$quantity} WHERE product_stock_product_id = '{$id}'";
        $this->db->query($sql);

        $log = "INSERT INTO log_stock (log_stock_product_id, log_stock_product_name, log_stock_status, log_stock_quantity, log_stock_type_id, log_stock_type_name, log_stock_category_id, log_stock_category_name, log_stock_value_id, log_stock_value_value, log_stock_date)
            SELECT product_id, product_name, 'reduce', {$quantity}, {$type}, type_name, {$category}, category_name, {$value}, value_value, NOW()
            FROM product, category, `type`, `value`
            WHERE product_id = '$id' AND type_id = {$type} AND category_id = {$category} AND value_id = {$value}";
        $this->db->query($log);

        $queryA['data'] = ['product_stock'];

        $queryA['select'] = [
            'product_stock_product_name' => 'product_name',
            'product_stock_stock' => 'stock',
        ];

        $queryA['where_detail'] = [
            " WHERE product_stock_product_id = '{$id}'"
        ];

        $data2 = generateDetailData($this->request->getVar(), $queryA, $this->db);

        foreach ($data1 as $key => $value) {
            $data1 = $value[0];
        }
        foreach ($data2 as $key => $value) {
            $data2 = $value[0];
        }
        $data = ['data' => [
            'before' => $data1,
            'after' => $data2,
        ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Reduced', $data);
    }

    public function add()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }
        $id = $this->request->getVar();
        $post = $this->request->getPost();
        $id = $id['id'];

        // get detail value, type, category
        $query['data'] = ['product'];
        $query['select'] = [
            'product_type_id' => 'type_id',
            'product_category_id' => 'category_id',
            'product_value_id' => 'value_id',
        ];
        $query['where_detail'] = [
            "WHERE product_id = $id"
        ];
        $data = generateDetailData($this->request->getGet(), $query, $this->db);
        foreach ($data as $key => $value) {
            $data = $value[0];
        }

        $queryB['data'] = ['product_stock'];
        $queryB['select'] = [
            'product_stock_product_name' => 'product_name',
            'product_stock_stock' => 'stock'
        ];
        $queryB['where_detail'] = [
            "WHERE product_stock_product_id = $id"
        ];

        $data1 = generateDetailData($this->request->getGet(), $queryB, $this->db);

        // get file to insert
        $quantity = htmlspecialchars($post['value']);
        $type = $data['type_id'];
        $category = $data['category_id'];
        $value = $data['value_id'];

        $sql = "UPDATE product_stock SET product_stock_stock = product_stock_stock + {$quantity},
        product_stock_stock_in = product_stock_stock_in + {$quantity} WHERE product_stock_product_id = '{$id}'";
        $this->db->query($sql);

        $log = "INSERT INTO log_stock (log_stock_product_id, log_stock_product_name, log_stock_status, log_stock_quantity, log_stock_type_id, log_stock_type_name, log_stock_category_id, log_stock_category_name, log_stock_value_id, log_stock_value_value, log_stock_date)
            SELECT product_id, product_name, 'add', {$quantity}, {$type}, type_name, {$category}, category_name, {$value}, value_value, NOW()
            FROM product, category, `type`, `value`
            WHERE product_id = '$id' AND type_id = {$type} AND category_id = {$category} AND value_id = {$value}";
        $this->db->query($log);

        $queryA['data'] = ['product_stock'];

        $queryA['select'] = [
            'product_stock_product_name' => 'product_name',
            'product_stock_stock' => 'stock',
        ];

        $queryA['where_detail'] = [
            " WHERE product_stock_product_id = '{$id}'"
        ];

        $data2 = generateDetailData($this->request->getVar(), $queryA, $this->db);

        foreach ($data1 as $key => $value) {
            $data1 = $value[0];
        }
        foreach ($data2 as $key => $value) {
            $data2 = $value[0];
        }
        $data = ['data' => [
            'before' => $data1,
            'after' => $data2,
        ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Added', $data);
    }
}
