<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Product extends AuthController
{
    public function detail()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }
        $id = $this->request->getVar('id');

        $query['data'] = ['product'];

        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'product',
            'product_price' => 'price',
            'product_type_id' => 'type_id',
            'product_type_name' => 'type',
            'product_category_id' => 'category_id',
            'product_category_name' => 'category',
            'product_value_id' => 'value_id',
            'product_value_value' => 'value',
            'product_created_at' => 'created_at',
            'product_updated_at' => 'updated_at',
        ];

        $query['where_detail'] = [
            "WHERE product_id = $id"
        ];

        $data = generateDetailData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Detail Data', $data);
    }

    public function insert()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $post = $this->request->getPost();

        $product = htmlspecialchars($post['product']);
        $category = htmlspecialchars($post['category']);
        $type = htmlspecialchars($post['type']);
        $value = htmlspecialchars($post['value']);
        $price = htmlspecialchars($post['price']);

        $sql = "INSERT INTO product (product_name, product_price, product_category_id, product_category_name, product_type_id, product_type_name, product_value_id, product_value_value, product_created_at, product_updated_at)
        SELECT '{$product}', {$price}, '{$category}', category_name, '{$type}', type_name, '{$value}', value_value, NOW(), NULL
        FROM category, `type`, `value`
        WHERE category_id = '{$category}' AND type_id = '{$type}' AND value_id = '{$value}'
        ";

        $this->db->query($sql);

        // Get Inserted Id
        $id = $this->db->insertID();

        $query['data'] = ['product'];

        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'bakpia',
            'product_price' => 'price',
            'product_type_id' => 'type_id',
            'product_type_name' => 'type_name',
            'product_category_id' => 'category_id',
            'product_category_name' => 'category_name',
            'product_value_id' => 'value_id',
            'product_value_value' => 'value_value',
            'product_created_at' => 'created_at',
        ];

        $query['where_detail'] = [
            "WHERE product_id = {$id}"
        ];

        $data = generateDetailData($this->request->getPost(), $query, $this->db);
        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Added', $data);
    }

    public function update()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $id = $this->request->getGet();
        $post = $this->request->getPost();
        foreach ($id as $key => $value) {
            $id = $value; // ID product
        }

        $product = htmlspecialchars($post['product']);
        $category = htmlspecialchars($post['category']);
        $type = htmlspecialchars($post['type']);
        $value = htmlspecialchars($post['value']);
        $price = htmlspecialchars($post['price']);

        $sql = "UPDATE product 
            SET product_name = '{$product}',
            product_price = '{$price}',
            product_type_id = {$type},
            product_type_name = (SELECT type_name FROM `type` WHERE type_id = '{$type}'),
            product_category_id = {$category},
            product_category_name = (SELECT category_name FROM category WHERE category_id = '{$category}'),
            product_value_id = {$value},
            product_value_value = (SELECT value_value FROM `value` WHERE value_id = '{$value}'),
            product_updated_at = NOW()
        WHERE product_id = {$id}
        ";

        $this->db->query($sql);

        // Get Data Updated
        $query['data'] = ['product'];

        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'bakpia',
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
            "WHERE product_id = {$id}"
        ];

        $data = generateDetailData($this->request->getPost(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfull Updated', $data);
    }

    public function delete()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $id = $this->request->getVar();
        foreach ($id as $key => $value) {
            $id = $value;
        }

        $query['data'] = ['product'];

        $query['select'] = [
            'product_id' => 'id',
            'product_name' => 'bakpia',
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
            "WHERE product_id = {$id}"
        ];

        $data = generateDetailData($this->request->getVar(), $query, $this->db);

        $sql = "DELETE FROM product WHERE product_id = {$id}";
        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Deleted', $data);
    }
}
