<?php 

namespace App\Controllers\Core;


use CodeIgniter\HTTP\RequestInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Controllers\Core\DataController;


class AuthController extends DataController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
    }

    public function before($request)
    {
        $db = db_connect();

        // Authorization Token
        $token = $request['Token'];
        $getResult = "SELECT auth_user_token FROM auth_user WHERE auth_user_token = '{$token}'";
        $getResult = $db->query($getResult);
        $getResult = $getResult->getResultArray();

        // check token exist
        if (empty($token)) {
            return Services::response()
                ->setJSON([
                    'status' => ResponseInterface::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
                    'message' => 'Token Required',
                    'error' => 'Token not inputed',
                    'inputed_token' => $token
                ]);
        }
        if (!$getResult) {
            return Services::response()
                ->setJSON([
                    'status' => ResponseInterface::HTTP_UNAUTHORIZED,
                    'message' => 'Invalid Token',
                    'error' => 'Token is not registered',
                    'inputed_token' => $token
                ]);
        } else {
            $user = "SELECT *
            FROM auth_user
            WHERE auth_user_token = '{$token}';
            ";
            $user = $db->query($user)->getFirstRow('array');

            $payload = [
                "expired" => $user['auth_user_date_expired']
            ];

            $expired = $payload["expired"];
            $tanggal = date('Y-m-d H:i:s');

            // Check Expired Token
            if ($expired < $tanggal) {
                return Services::response()
                    ->setJSON([
                        'status' => ResponseInterface::HTTP_REQUEST_TIMEOUT,
                        'message' => 'Token Expired',
                        'error' => 'Token date expired',
                        'inputed_token' => $token
                    ]);
            }
            // Add Active Time Expired Token (1 hour)
            $tanggal = date('Y-m-d H:i:s');
            $strtotime = strtotime($tanggal);
            $tambahBiling = $strtotime + (60 * 60);
            $biling = date('Y-m-d H:i:s', $tambahBiling);

            $updateExpired = "UPDATE auth_user 
            SET 
            auth_user_date_login = NOW(),
            auth_user_date_expired = '{$biling}'
            WHERE auth_user_token = '{$token}'";
            $db->query($updateExpired);
        }
    }
    
    

}