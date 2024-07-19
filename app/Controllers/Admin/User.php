<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class User extends AuthController
{
    protected $db;

    public function register()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
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
        $pw = $post['password'];
        $password = htmlspecialchars($post['password']);

        $insert = "INSERT INTO user VALUES('', '{$username}', '{$password}')";

        $this->db->query($insert);
        $data = [
            'username' => $username,
            'password' => $pw,
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Account Successfully Registered', $data);
    }

    public function update()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)){
            return $token;
        }

        $post = $this->request->getPost();
        $getID = $this->request->getGet();

        foreach ($getID as $key => $value) {
            $id = $value;
        }

        $username = htmlspecialchars($post['username']);
        $password = htmlspecialchars($post['password']);

        $query['data'] = ['user'];
        $query['select'] = [
            'user_username' => 'username',
            'user_password' => 'password'
        ];
        $query['where_detail'] = [
            "WHERE user_id = {$id}"
        ];

        $data = generateDetailData($this->request->getVar(), $query, $this->db);


        $sql = "UPDATE user 
        SET user_username = '{$username}',
            user_password = '{$password}',
        WHERE user_id = {$id}";

        $this->db->query($sql);

        $auth = $username;
        $auth .= $password;

        $auth = base64_encode($auth);

        $authSql = "UPDATE auth_user 
            SET auth_user_username = '{$username}',
                auth_user_token = '{$auth}' 
        WHERE auth_user_user_id = {$id}";

        $this->db->query($authSql);


        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Account Successfully Updated', $data);

    }
}
