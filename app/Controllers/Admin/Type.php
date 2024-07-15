<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Type extends AuthController
{
    public function insert()
    {

        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $post = $this->request->getPost();

        $type = htmlspecialchars_decode($post['type']);
        $sql = "INSERT INTO `type`(type_name, type_created_at, type_updated_at, type_deleted_at)
        VALUES ('{$type}', NOW(), NULL, NULL)";

        $this->db->query($sql);

        // Get Inserted Id
        $id = $this->db->insertID(); 
        
        $query['data'] = ['type'];

        $query['select'] = [
            'type_name' => 'type',
            'type_created_at' => 'created',
        ];

        $query['where_detail'] = [
            "WHERE type_id = {$id}"
        ];

        $data = generateDetailData($this->request->getPost(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Added', $data);

    }

    public function update()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $id = $this->request->getGet();
        $post = $this->request->getPost();
        foreach ($id as $key => $value) {
            $id = $value;
        }

        $type = htmlspecialchars($post['type']);
        $sql = "UPDATE `type` SET 
        type_name = '{$type}',
        type_updated_at = NOW()
        WHERE type_id = {$id}";

        $this->db->query($sql);

        $query['data'] = ['type'];
        $query['select'] = [
           'type_name' => 'type',
           'type_created_at' => 'created',
           'type_updated_at' => 'updated',
        ];
        $query['where_detail'] = [
            "WHERE type_id = {$id}"
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

        $query['data'] = ['type'];
        $query['select'] = [
            'type_id' => 'id',
            'type_name' => 'name',
            'type_created_at' => 'created',
            'type_updated_at' => 'updated',
            'type_deleted_at' => 'deleted',
        ];
        $query['where_detail'] = [
            "WHERE type_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        $sql = "UPDATE `type` 
        SET type_updated_at = NOW(),
            type_deleted_at = NOW()
        WHERE type_id = {$id}";

        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Soft Deleted', $data);
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

        $sql = "UPDATE `type` 
            SET type_deleted_at = NULL,
                type_updated_at = NOW()
            WHERE type_id = {$id}";
        $this->db->query($sql);

        $query['data'] = ['type'];
        $query['select'] = [
            'type_id' => 'id',
            'type_name' => 'type',
            'type_created_at' => 'created',
            'type_updated_at' => 'updated',
        ];  
        $query['where_detail'] = [
            "WHERE type_id = {$id}"
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

        $query['data'] = ['type'];
        $query['select'] = [
            'type_id' => 'id',
            'type_name' => 'name',
            'type_created_at' => 'created',
            'type_updated_at' => 'updated',
            'type_deleted_at' => 'deleted',
        ];
        $query['where_detail'] = [
            "WHERE type_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        $sql = "DELETE FROM `type` WHERE type_id = {$id}";

        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Deleted', $data);
    }
}