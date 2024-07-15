<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class ListUser extends AuthController
{
    protected $db;

    public function list_admin_get()
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
}
