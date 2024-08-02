<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class User extends AuthController
{
    public function list_user()
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

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List User', $data);
    }

    public function register()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $post = $this->request->getPost();

        $rules = [
            'username' => 'required|is_unique[user.user_username]',
            'password' => 'required|min_length[5]',
            'confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return $this->responseErrorValidation(ResponseInterface::HTTP_PRECONDITION_FAILED, 'Error Validation', $this->validator->getErrors());
        }

        $username = htmlspecialchars($post['username']);
        $password = htmlspecialchars($post['password']);

        $insert = "INSERT INTO user VALUES('', '{$username}', '{$password}', NOW(), NULL)";

        $this->db->query($insert);

        $query['data'] = ['user'];
        $query['select'] = [
            'user_id' => 'id',
            'user_username' => 'username',
            'user_created_at' => 'created_at',
        ];
        $query['where_detail'] = [
            "WHERE user_username = '$username'"
        ];

        $data = generateDetailData($this->request->getGet(), $query, $this->db);
        foreach ($data as $key => $value) {
            $data = $value[0];
        }
        $response = [
            'data' => $data
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Account Successfully Registered', $response);
    }

    public function update()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $post = $this->request->getPost();
        $getID = $this->request->getGet();

        foreach ($getID as $key => $value) {
            $id = $value;
        }

        $username = htmlspecialchars($post['username']);
        $password = htmlspecialchars($post['password']);

        // update user
        $sql = "UPDATE user 
        SET user_username = '{$username}',
            user_password = '{$password}'
        WHERE user_id = {$id}";
        $this->db->query($sql);

        // generate TOKEN
        $auth = $username;
        $auth .= $password;

        $auth = base64_encode($auth);

        // update auth_user
        $authSql = "UPDATE auth_user 
            SET auth_user_username = '{$username}',
                auth_user_token = '{$auth}' 
        WHERE auth_user_user_id = {$id}";

        $this->db->query($authSql);


        // generate detail user
        $query['data'] = ['user'];
        $query['select'] = [
            'user_id' => 'user_id',
            'user_username' => 'username',
            'user_password' => 'password',
        ];
        $query['where_detail'] = [
            "WHERE user_id = $id"
        ];

        $data = generateDetailData(ResponseInterface::HTTP_OK, $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Account Successfully Updated', $data);
    }

    public function delete()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }
        // get ID from params
        $id = $this->request->getGet();
        $id = $id['id'];

        // generate detail data
        $query['data'] = ['user'];
        $query['select'] = [
            'user_id' => 'id',
            'user_username' => 'username',
        ];
        $query['where_detail'] = [
            "WHERE user_id = $id"
        ];
        $data = generateDetailData($this->request->getGet(), $query, $this->db);

        // delete data from database
        $sql = "DELETE FROM `user` WHERE user_id = {$id}";
        $this->db->query($sql);
        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Deleted', $data);
    }
}
