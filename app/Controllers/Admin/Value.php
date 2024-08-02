<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Value extends AuthController
{
    public function list()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'value',
            'value_created_at' => 'created_at',
            'value_updated_at' => 'updated_at',
            'value_deleted_at' => 'deleted_at',
        ];

        $data = generateListData($this->request->getVar(), $query, $this->db);
        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Value', $data);
    }
    public function detail()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $id = $this->request->getGet();
        $id = $id['id'];

        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'value',
            'value_created_at' => 'created_at',
            'value_updated_at' => 'updated_at'
        ];
        $query['where_detail'] = [
            "WHERE value_id = $id"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        foreach ($data as $key => $value) {
            $data = $value[0];
        }
        $response = [
            'data' => $data
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Detail Type', $response);
    }

    public function insert()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $post = $this->request->getPost();

        $value = htmlspecialchars_decode($post['value']);
        $sql = "INSERT INTO `value`(value_value, value_created_at, value_updated_at, value_deleted_at)
        VALUES ('{$value}', NOW(), NULL, NULL)";

        $this->db->query($sql);

        // Get Inserted Id
        $id = $this->db->insertID();

        $query['data'] = ['value'];

        $query['select'] = [
            'value_value' => 'value',
            'value_created_at' => 'created',
        ];

        $query['where_detail'] = [
            "WHERE value_id = {$id}"
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
            $id = $value;
        }

        $value = htmlspecialchars($post['value']);
        $sql = "UPDATE `value` SET 
        value_value = '{$value}',
        value_updated_at = NOW()
        WHERE value_id = {$id}";

        $this->db->query($sql);

        $query['data'] = ['value'];
        $query['select'] = [
            'value_value' => 'value',
            'value_created_at' => 'created',
            'value_updated_at' => 'updated',
        ];
        $query['where_detail'] = [
            "WHERE value_id = {$id}"
        ];

        $data = generateDetailData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Updated', $data);
    }

    public function soft_delete()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $id = $this->request->getGet();
        foreach ($id as $key => $value) {
            $id = $value;
        }

        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'name',
            'value_created_at' => 'created',
            'value_updated_at' => 'updated',
            'value_deleted_at' => 'deleted',
        ];
        $query['where_detail'] = [
            "WHERE value_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        $sql = "UPDATE `value` 
        SET value_updated_at = NOW(),
            value_deleted_at = NOW()
        WHERE value_id = {$id}";

        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Soft Deleted', $data);
    }

    public function deleted_value()
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

    public function restore()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $id = $this->request->getGet();

        foreach ($id as $key => $value) {
            $id = $value;
        }

        $sql = "UPDATE `value` 
            SET value_deleted_at = NULL,
                value_updated_at = NOW()
            WHERE value_id = {$id}";
        $this->db->query($sql);

        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'value',
            'value_created_at' => 'created',
            'value_updated_at' => 'updated',
        ];
        $query['where_detail'] = [
            "WHERE value_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Restored', $data);
    }

    public function delete()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $id = $this->request->getGet();
        foreach ($id as $key => $value) {
            $id = $value;
        }

        $query['data'] = ['value'];
        $query['select'] = [
            'value_id' => 'id',
            'value_value' => 'name',
            'value_created_at' => 'created',
            'value_updated_at' => 'updated',
            'value_deleted_at' => 'deleted',
        ];
        $query['where_detail'] = [
            "WHERE value_id = {$id}"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        $sql = "DELETE FROM `value` WHERE value_id = {$id}";

        $this->db->query($sql);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Deleted', $data);
    }
}
