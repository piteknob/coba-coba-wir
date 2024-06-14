<?php

namespace App\Controllers;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Category extends AuthController
{
    public function insert()
    {

        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $post = $this->request->getPost();

        $type = htmlspecialchars_decode($post['category']);
        $sql = "INSERT INTO `category`(category_name, category_created_at, category_updated_at, category_deleted_at)
        VALUES ('{$type}', NOW(), NULL, NULL)";

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
}