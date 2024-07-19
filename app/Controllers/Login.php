<?php

namespace App\Controllers;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends AuthController
{
    protected $db;

    public function login()
    {
        $post = $this->request->getPost();
        $db = db_connect();
        
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->responseErrorValidation(ResponseInterface::HTTP_PRECONDITION_FAILED, 'Error Validation', $this->validator->getErrors());
        }

        $username = $post['username'];
        $password = $post['password'];

        $user = "SELECT *
        FROM user
        WHERE user_username = '{$username}'
        ";
        $user = $db->query($user)->getFirstRow('array');

        if (!$user) {
            return $this->responseFail(ResponseInterface::HTTP_NOT_FOUND, 'Username Not Found', 'Username not registered', $user);
        }
        $pwsql = "SELECT user_password FROM user WHERE user_password = '{$password}'";
        $pwsql = $db->query($pwsql)->getFirstRow('array');
        if (!$pwsql) {
            $user = null;
            return $this->responseFail(ResponseInterface::HTTP_NOT_FOUND, 'Password Not Match', 'Wrong Password', $user);
        }

        $payload = [
            'iat' => 1356999524,
            'nbf' => 1357000000,
            "uid" => $user['user_id'],
            "username" => $user['user_username']
        ];
        $idUser = $payload['uid'];


        $token = $username;
        $token .= $password;

        $token = base64_encode($token);
        
        // find expired token + 1 hour
        $date = date("Y-m-d H:i:s");
        $currentDate = strtotime($date);
        $futureDate = $currentDate + (60 * 60); 
        $formatDate = date("Y-m-d H:i:s", $futureDate);


        $getID = "SELECT auth_user_user_id FROM auth_user WHERE auth_user_username = '{$username}'";
        $resultID = $db->query($getID);
        $id = $resultID->getResultArray();

        if (!$id) {
            $insertAuth = "INSERT INTO auth_user (auth_user_user_id, auth_user_username, auth_user_token, auth_user_date_login, auth_user_date_expired) 
                    SELECT user_id, '{$username}', '{$token}', NOW(), '{$formatDate}'
                    FROM user
                    WHERE user_username = '{$username}'";
            $db->query($insertAuth);
            return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Login Success', $token);
        }
        if ($id) {
            $updateAuth = "UPDATE auth_user SET
                    auth_user_date_login = NOW(),
                    auth_user_date_expired = '{$formatDate}' 
                    WHERE auth_user_user_id = '{$idUser}'";
            $db->query($updateAuth);
            return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Login Success', $token);
        }
    }
}
