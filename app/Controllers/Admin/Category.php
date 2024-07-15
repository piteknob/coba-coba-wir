<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Category extends AuthController
{

    
    public function insert()
    {

        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $post = $this->request->getPost();

        $category = htmlspecialchars_decode($post['category']);
        $sql = "INSERT INTO `category`(category_name, category_created_at, category_updated_at, category_deleted_at)
        VALUES ('{$category}', NOW(), NULL, NULL)";

        $this->db->query($sql);

        // Get Inserted Id
        $id = $this->db->insertID();

        $query['data'] = ['category'];

        $query['select'] = [
            'category_name' => 'category',
            'category_created_at' => 'created',
        ];

        $query['where_detail'] = [
            "WHERE category_id = {$id}"
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

        $post = $this->request->getPost();
        $id = $this->request->getGet();

        foreach ($id as $key => $value) {
            $id = $value;
        }

        $category = htmlspecialchars($post['category']);
        $sql = "UPDATE category SET
        category_name = '{$category}',
        category_updated_at = NOW()
        WHERE category_id = {$id}";

        $this->db->query($sql);

        $query['data'] = ['category'];
        $query['select'] = [
           'category_name' => 'category',
           'category_created_at' => 'created',
           'category_updated_at' => 'updated',
        ];
        $query['where_detail'] = [
            "WHERE category_id = {$id}"
        ];

        $data = generateDetailData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Updated', $data);

    }

    public function soft_delete()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $id = $this->request->getGet();
        foreach ($id as $key => $value) {
            $id = $value;
        }

        $query['data'] = ['category'];
        $query['select'] = [
            'category_id' => 'id',
            'category_name' => 'name',
            'category_created_at' => 'created',
            'category_updated_at' => 'updated',
            'category_deleted_at' => 'deleted',
        ];
        $query['where_detail'] = [
            "WHERE category_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        $sql = "UPDATE `category` 
        SET category_updated_at = NOW(),
            category_deleted_at = NOW()
        WHERE category_id = {$id}";

        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Soft Deleted', $data);
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

    public function restore()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $id = $this->request->getGet();

        foreach ($id as $key => $value) {
            $id = $value;
        }

        $sql = "UPDATE `category` 
            SET category_deleted_at = NULL,
                category_updated_at = NOW()
            WHERE category_id = {$id}";
        $this->db->query($sql);

        $query['data'] = ['category'];
        $query['select'] = [
            'category_id' => 'id',
            'category_name' => 'category',
            'category_created_at' => 'created',
            'category_updated_at' => 'updated',
        ];  
        $query['where_detail'] = [
            "WHERE category_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Restored', $data);
    }

    public function delete()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $id = $this->request->getGet();
        foreach ($id as $key => $value) {
            $id = $value;
        }

        $query['data'] = ['category'];
        $query['select'] = [
            'category_id' => 'id',
            'category_name' => 'name',
            'category_created_at' => 'created',
            'category_updated_at' => 'updated',
            'category_deleted_at' => 'deleted',
        ];
        $query['where_detail'] = [
            "WHERE category_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        $sql = "DELETE FROM `category` WHERE category_id = {$id}";

        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Deleted', $data);
    }
}
