<?php

namespace App\Controllers;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Transaction extends AuthController
{
    public function select()
    {
        $json = $this->request->getJSON();
        $db = db_connect();
        $name_cust = $json->customer->name;
        $address_cust = $json->customer->address;
        $hp_cust = $json->customer->no_handphone;


        // --------------------------- VALIDATION PRODUCT ---------------------------- //
        foreach ($json->product as $key) {
            $validator = \Config\Services::validation();
            $validator->setRules([
                'product_id' => 'required',
            ]);

            if (!$validator->run((array)$key)) {
                $error = $validator->getErrors();
                if ($error) {
                    return $this->responseErrorValidation(ResponseInterface::HTTP_PRECONDITION_FAILED, 'error validation', $error);
                }
            }
        }

        // validation customer
        $validator = \Config\Services::validation();
        $validator->setRules([
            'name' => 'required',
            'no_handphone' => 'required|min_length[10]|max_length[20]',
            'address' => 'required'
        ]);

        $customer = (array) $json->customer;

        if (!$validator->run($customer)) {
            $error = $validator->getErrors();
            if ($error) {
                return $this->responseErrorValidation(ResponseInterface::HTTP_PRECONDITION_FAILED, 'error validation', $error);
            }
        }

        // check customer & add
        $name = htmlspecialchars($json->customer->name);
        $hp = $json->customer->no_handphone;
        $address = $json->customer->address;
        $error = [];

        $check = "SELECT customer_no_handphone, customer_name
            FROM customer
            WHERE customer_no_handphone = '{$hp}' AND customer_name = '{$name}'
            ;";

        $error = $db->query($check)->getResultArray();

        if (empty($error)) {
            $sql = "INSERT INTO customer (customer_id, customer_name, customer_address, customer_no_handphone)
                    VALUES ('', '{$name}', '{$address}', '{$hp}')";
            $sql = $db->query($sql);
            $idCustomer = $db->insertID();
        } else {
            // search id customer
            $idCustomer = "SELECT customer_id FROM customer WHERE customer_no_handphone = '{$hp}' AND customer_name = '{$name}'";
            $idCustomer = $db->query($idCustomer)->getResultArray();
            $idCustomer = $idCustomer[0]['customer_id'];
        }


        //------------------------------- CHECK PENDING -------------------------------//
        $query = "SELECT sales_order_status FROM sales_order WHERE sales_order_customer_id = ? AND  sales_order_status = 'pending'";

        $pending = $db->query($query, [$idCustomer])->getResultArray();

        if (empty($pending)) {
            $status = false;
        } else {
            $status = 'pending';
        }

        $selectHarga = "SELECT sales_order_price FROM sales_order WHERE sales_order_customer_id = {$idCustomer} AND sales_order_status = 'pending'";
        $selectHarga = $db->query($selectHarga)->getResultArray();

        $totalHarga = 0;
        foreach ($selectHarga as $key => $value) {
            $totalHarga += $value['sales_order_price'];
        }

        $gas['data'] = ['sales_order'];

        $gas['select'] = [
            'sales_order_id' => 'id',
            'sales_order_status' => 'status',
            'sales_order_price' => 'total_price',
            'sales_order_customer_id' => 'customer_id',
            'sales_order_customer_name' => 'customer_name',
            'sales_order_customer_address' => 'customer_address',
            'sales_order_customer_no_handphone' => 'customer_no_handphone',
            'sales_order_date' => 'date',
        ];

        $gas['where_detail'] = [
            "WHERE sales_order_status = 'pending' AND sales_order_customer_id = {$idCustomer}"
        ];

        $gas['pagination'] = [
            'pagination' => false
        ];

        $gas = generateListData($this->request->getPost(), $gas, $db);


        // check id customer 
        $cust_name = $json->customer->name;
        $cust_hp = $json->customer->no_handphone;
        $cust_address = $json->customer->address;
        $query_id_customer['data'] = ['customer'];
        $query_id_customer['select'] = [
            'customer_id' => 'id'
        ];
        $query_id_customer['where_detail'] = [
            "WHERE customer_name = '{$cust_name}' AND customer_address = '{$cust_address}' AND customer_no_handphone = {$cust_hp}"
        ];
        $id_customer = generateDetailData($this->request->getGet(), $query_id_customer, $this->db);

        foreach ($id_customer as $key => $value) {
            $id_customer = $value[0]['id'];
        }

        $product_pending['data'] = ['sales_product'];
        $product_pending['select'] = [
            'sales_product_id' => 'id',
            'sales_product_product_name' => 'product_name',
            'sales_product_product_category' => 'product_category',
            'sales_product_product_type' => 'product_type',
            'sales_product_product_value' => 'product_value',
            'sales_product_quantity' => 'quantity',
            'sales_product_price' => 'price',
        ];
        $product_pending['where_detail'] = [
            "WHERE sales_product_customer_id = $id_customer"
        ];
        $product_pending = generateDetailData($this->request->getGet(), $product_pending, $this->db);
        foreach ($product_pending as $key => $value) {
            $product_pending = $value;
        }

        $comeplete = (object) [];
        $comeplete->customer_order_detail = [
            $gas
        ];
        $comeplete->product = [
            $product_pending
        ];
        $comeplete->payment = [
            'price_total' => $totalHarga,
            'payment_method' => 'transfer',
        ];
        $data_pending = [];
        $data_pending = [
            'data' => $comeplete
        ];

        if ($status == 'pending') {
            return $this->responseFail(ResponseInterface::HTTP_FAILED_DEPENDENCY, 'please complete ur last order', '', $data_pending);
        }


        // ------------------------------ TRANSACTION BEGIN ------------------------------- //

        // -------------------------------- REDUCE STOCK -------------------------------- //
        $data_stock = (array) $json->product;
        foreach ($data_stock as $key => $value) {
            $product_id_stock = $value->product_id;
            $product_stock_stock = $value->product_quantity;

            $reduce_stock = "UPDATE product_stock SET product_stock_stock = product_stock_stock - '{$product_stock_stock}', product_stock_stock_out = product_stock_stock_out - {$product_stock_stock} WHERE product_stock_product_id = '{$product_id_stock}'";
            $db->query($reduce_stock);
        }

        // -------------- TOTAL PRICE CHECK ------------- //

        // get detail data
        $post = $this->request->getJSON();
        $product = [];
        foreach ($post->product as $json) {
            $product[htmlspecialchars($json->product_id)] = $json->product_quantity;
        }
        $price_total = 0;
        foreach ($product as $id => $quantity) {
            $detail_price['data'] = ['product'];
            $detail_price['select'] = [
                'product_price' => 'price'
            ];
            $detail_price['where_detail'] = [
                "WHERE product_id = $id"
            ];
            $price = generateDetailData($this->request->getGet(), $detail_price, $this->db);
            foreach ($price as $key => $value) {
                $price = $value[0]['price'];
            }

            $price = $price * $quantity;
            $price_total += $price;
        }

        $query_insert_sales_order = "INSERT INTO sales_order (
                            sales_order_status,
                            sales_order_price,
                            sales_order_customer_id,
                            sales_order_customer_name,
                            sales_order_customer_address,
                            sales_order_customer_no_handphone,
                            sales_order_date,
                            sales_order_proof
                            ) SELECT 
                            'pending',
                            {$price_total},
                            $idCustomer,
                            '{$name_cust}',
                            '{$address_cust}',
                            '{$hp_cust}',
                            NOW(),
                            NULL
                            FROM customer
                            WHERE customer_id = {$idCustomer}";
        $this->db->query($query_insert_sales_order);
        $id_order = $db->insertID();


        foreach ($product as $key => $value) {
            $detail_data['data'] = ['product'];
            $detail_data['select'] = [
                'product_id' => 'id',
                'product_name' => 'name',
                'product_price' => 'price',
                'product_type_id' => 'type_id',
                'product_type_name' => 'type_name',
                'product_category_id' => 'category_id',
                'product_category_name' => 'category_name',
                'product_value_id' => 'value_id',
                'product_value_value' => 'value_value',
            ];
            $detail_data['where_detail'] = [
                "WHERE product_id = $key"
            ];
            $data = generateDetailData($this->request->getPost(), $detail_data, $this->db);
            foreach ($data as $key => $value) {
                // from post
                $id = $json->product_id;
                $quantity = $json->product_quantity;
                // from databasa detail
                $product_detail = $value[0]['name'];
                $category_detail = $value[0]['category_name'];
                $type_detail = $value[0]['type_name'];
                $value_detail = $value[0]['value_value'];
                $price_detail = $value[0]['price'];

                $query_insert_sales_product = "INSERT INTO sales_product (
                sales_product_status,
                sales_product_product_name,
                sales_product_product_category,
                sales_product_product_type,
                sales_product_product_value,
                sales_product_quantity,
                sales_product_price,
                sales_product_order_id,
                sales_product_customer_id
                    ) VALUES (  
                        'pending',
                        '{$product_detail}',
                        '{$category_detail}',
                        '{$type_detail}',
                        '{$value_detail}',
                        '{$quantity}',
                        '{$price_detail}',
                        '{$id_order}',
                        '{$idCustomer}'
                    );";
                $this->db->query($query_insert_sales_product); // exec insert to sales_product
            }
        }

        $return_order['data'] = ['sales_order'];
        $return_order['select'] = [
            'sales_order_id' => 'id',
            'sales_order_status' => 'status',
            'sales_order_price' => 'price',
            'sales_order_customer_id' => 'customer_id',
            'sales_order_customer_name' => 'customer_name',
            'sales_order_customer_address' => 'customer_address',
            'sales_order_customer_no_handphone' => 'customer_no_handphone',
            'sales_order_date' => 'date',
        ];
        $return_order['where_detail'] = [
            "WHERE sales_order_id = $id_order"
        ];
        $return_order = generateDetailData($this->request->getGet(), $return_order, $this->db);
        $return_order = (array) $return_order;

        $return_product['data'] = ['sales_product'];
        $return_product['select'] = [
            'sales_product_id' => 'id',
            'sales_product_product_name' => 'product_name',
            'sales_product_product_category' => 'product_category',
            'sales_product_product_type' => 'product_type',
            'sales_product_product_value' => 'product_value',
            'sales_product_quantity' => 'quantity',
            'sales_product_price' => 'price',
        ];
        $return_product['where_detail'] = [
            "WHERE sales_product_customer_id = $id_customer AND sales_product_status = 'pending'"
        ];
        $return_product = generateListData($this->request->getGet(), $return_product, $this->db);
        $return_product = (array) $return_product;
        $data_success = [
            'data' => [
                'product' => $return_product['data'],
                'pagination' => $return_product['pagination'],
                'customer_data' => $return_order['data']
            ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Order Result', $data_success);
    }

    public function payment()
    {
        // ------------------- GET ID ------------------- //
        $id = $this->request->getVar();
        $id = $id['id'];

        // ------------------- GET FILE -------------------- //
        $photo = $this->request->getFile('upload');
        $db = db_connect();

        // --------------------- CHECK ORDER EXIST ------------------------ //

        // pending
        $query_exist_pending['data'] = ['sales_order'];
        $query_exist_pending['select'] = [
            'sales_order_price' => 'price'
        ];
        $query_exist_pending['where_detail'] = [
            "WHERE sales_order_id = {$id} AND sales_order_status = 'pending'"
        ];
        $query_exist_pending['pagination'] = [false];

        $query_exist_pending = generateListData($this->request->getVar(), $query_exist_pending, $this->db);

        // payed
        $query_exist_payed['data'] = ['sales_order'];
        $query_exist_payed['select'] = [
            'sales_order_price' => 'price'
        ];
        $query_exist_payed['where_detail'] = [
            "WHERE sales_order_id = {$id} AND sales_order_status = 'payed'"
        ];
        $query_exist_payed['pagination'] = [false];

        $query_exist_payed = generateListData($this->request->getVar(), $query_exist_payed, $this->db);

        if (empty($query_exist_pending) && empty($query_exist_payed)) {
            $data_exist = [
                'data' => [
                    'message' => 'Order Not Found',
                ]
            ];
            return $this->responseFail(ResponseInterface::HTTP_NOT_FOUND, 'Order Not Found', '', $data_exist);
        }
        // -------------------------------- GET TOTAL HARGA --------------------------------- //

        $query['data'] = ['sales_order'];

        $query['select'] = [
            'sales_order_price' => 'price',
        ];

        $query['where_detail'] = [
            "WHERE sales_order_id = {$id} AND sales_order_status = 'pending'"
        ];

        $query['pagination'] = [
            'pagination' => false
        ];

        $price = generateListData($this->request->getVar(), $query, $db);
        $totalHarga = 0;
        foreach ($price as $key => $value) {
            $totalHarga += $value['price'];
        }

        // --------------------- CHECK STATUS ORDER ------------------------ // 

        $sql = "SELECT sales_order_status FROM sales_order WHERE sales_order_id = {$id}";
        $data_check = $db->query($sql);
        $data_check = $data_check->getResultArray();

        $sql = "SELECT sales_product_status FROM sales_product WHERE sales_product_order_id = {$id}";
        $data_product = $db->query($sql);
        $data_product = $data_product->getResultArray();
        
        $data_check_real = 'payed'; 
        $data_product_real = 'payed';
        foreach ($data_check as $key => $value) {
            $data_check = $value['sales_order_status'];
            if ($data_check == 'pending') {
                $data_check_real = 'pending';
            }
        }

        foreach ($data_product as $key => $value) {
            $data_product = $value['sales_product_status'];
            if ($data_product == 'pending') {
                $data_product_real = 'pending';
            }
        }


        if ($data_check_real == 'payed' && $data_product_real == 'payed') {
            $data = [
                'data' => [
                    'message' => 'Order has been paid',
                ]
            ];
            return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Payment Already Done', $data);
        }

        // --------------------- SET VALIDATION ------------------------ //
        $validator = \Config\Services::validation();
        $validator->setRules([
            'upload' => 'uploaded[upload]|max_size[upload, 2048]|is_image[upload]|mime_in[upload,image/jpg,image/jpeg,image/png]',
        ]);

        if (!$validator->run((array)$photo)) {
            $error = $validator->getErrors();
            if ($error) {
                return $this->responseErrorValidation(ResponseInterface::HTTP_PRECONDITION_FAILED, 'error validation', $error);
            }
        }
        $pure_photo_name = $photo->getName();
        $photo_name = "customer_" . "$id" . "_";
        $photo_name .= $photo->getRandomName();
        $photo->move('./upload/photo', $photo_name);




        // ---------------------------------- UPDATE FROM 'PENDING' TO 'PAYED' ---------------------------------- //

        $sql_sales_order = "UPDATE sales_order 
        SET sales_order_proof = '{$photo_name}',
        sales_order_status = 'payed'
        WHERE sales_order_id = {$id} AND sales_order_status = 'pending'";
        $db->query($sql_sales_order);

        $sql_sales_product = "UPDATE sales_product
        SET sales_product_status = 'payed'
        WHERE sales_product_order_id = {$id}";
        $db->query($sql_sales_product);

        $data = [
            'total Payment' => $totalHarga,
            'image' => $pure_photo_name,
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Payed', $data);
    }

    public function customer_cancel()
    {
        $post = $this->request->getPost();
        $id = $post['id'];

        $break_order = "UPDATE sales_order SET sales_order_status = 'break' WHERE sales_order_id = {$id} AND sales_order_status = 'pending'";
        $break_product = "UPDATE sales_product SET sales_product_status = 'break' WHERE sales_product_order_id = {$id} AND sales_product_status = 'pending'";

        $this->db->query($break_order);
        $this->db->query($break_product);

        $check_payed['data'] = ['sales_order'];
        $check_payed['select'] = [
            'sales_order_id' => 'id',
            'sales_order_status' => 'status',
            'sales_order_price' => 'price',
            'sales_order_customer_id' => 'customer_id',
            'sales_order_customer_name' => 'customer_name',
            'sales_order_customer_address' => 'customer_address',
            'sales_order_customer_no_handphone' => 'customer_no_handphone',
            'sales_order_date' => 'date',
            'sales_order_proof' => 'proof',
        ];
        $check_payed['where_detail'] = [
            "WHERE sales_order_id = '{$id}'"
        ];
        $check_payed = (array) generateDetailData($this->request->getVar(), $check_payed, $this->db);

        foreach ($check_payed as $key => $value) {
            $status_payed = $value[0];
        }

        if($status_payed['status'] === 'payed'){
            return $this->responseFail(ResponseInterface::HTTP_OK, 'You Can Not Cancel This Order, Contact Admin To Cancel This Order', '', $status_payed);
        }
        if($status_payed['status'] === 'confirmed'){
            return $this->responseFail(ResponseInterface::HTTP_OK, 'You Can Not Cancel This Order, Contact Admin To Cancel This Order', '', $status_payed);
        }

        // ----------------- RESTORE STOCK ---------------- //

        $data_order['data'] = ['sales_order'];

        $data_order['select'] = [
            'sales_order_id' => 'id',
            'sales_order_price' => 'price',
            'sales_order_customer_id' => 'customer_id',
            'sales_order_customer_name' => 'customer_name',
            'sales_order_customer_address' => 'customer_address',
            'sales_order_customer_no_handphone' => 'customer_no_handphone',
            'sales_order_date' => 'date',
            'sales_order_proof' => 'proof',
        ];

        $data_order['where_detail'] = [
            "WHERE sales_order_id = {$id} AND sales_order_status = 'break'"
        ];

        $data_order['pagination'] = [
            'pagination' => false
        ];

        $data_order = generateListData($this->request->getPost(), $data_order, $this->db);

        $data_product['data'] = ['sales_product'];
        $data_product['select'] = [
            'sales_product_id' => 'id',
            'sales_product_status' => 'status',
            'sales_product_product_name' => 'product_name',
            'sales_product_product_category' => 'product_category',
            'sales_product_product_type' => 'product_type',
            'sales_product_product_value' => 'product_value',
            'sales_product_quantity' => 'quantity',
            'sales_product_price' => 'price',
            'sales_product_order_id' => 'order_id',
        ];
        $data_product['where_detail'] = [
            "WHERE sales_product_order_id = {$id} AND sales_product_status = 'break'"
        ];
        $data_product['pagination'] = [false];

        $data_product = generateListData($this->request->getPost(), $data_product, $this->db);

        $sql_order = "UPDATE sales_order SET sales_order_status = 'customer_canceled' WHERE sales_order_id = {$id} AND sales_order_status = 'break'";
        $this->db->query($sql_order);

        $sql_product = "UPDATE sales_product SET sales_product_status = 'customer_canceled' WHERE sales_product_order_id = {$id} AND sales_product_status = 'break'";
        $this->db->query($sql_product);

        // ------------------ RESTORE STOCK --------------- //
        foreach ($data_product as $value) {
            $quantity_stock = $value['quantity'];
            $product_stock_product_name = $value['product_name'];
            $product_stock_category = $value['product_category'];
            $product_stock_value = $value['product_value'];
            $product_stock_type = $value['product_type'];

            $sql_stock = "UPDATE product_stock SET product_stock_stock = product_stock_stock + {$quantity_stock}, product_stock_stock_out = product_stock_stock_out - {$quantity_stock} WHERE product_stock_product_name = '{$product_stock_product_name}' AND product_stock_type_name = '{$product_stock_type}' AND product_stock_category_name = '{$product_stock_category}' AND product_stock_value_value = '{$product_stock_value}'";
            $this->db->query($sql_stock);
        }


        if (!empty($data_order) && !empty($data_product)) {
            $data = [
                'data' => [
                    'order_detail' => $data_order,
                    'product_order_detail' => $data_product
                ]
            ];
            return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Successfully Canceled', $data);
        } else {
            $data = [
                'data' => [
                    'message' => 'Data Already Canceled'
                ]
            ];
            return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Already Canceled', $data);
        }
    }

    public function history_customer()
    {
        $id = $this->request->getVar();
        $id = $id['id'];


        $query['data'] = ['sales_order'];

        $query['select'] = [
            'sales_order_id' => 'id',
            'sales_order_status' => 'status',
            'sales_order_price' => 'price',
            'sales_order_customer_id' => 'customer_id',
            'sales_order_customer_name' => 'customer_name',
            'sales_order_customer_address' => 'customer_address',
            'sales_order_customer_no_handphone' => 'customer_no_handphone',
            'sales_order_date' => 'date',
            'sales_order_proof' => 'proof',
        ];

        $query['where_detail'] = [
            "WHERE sales_order_customer_id = {$id}"
        ];

        $query['pagination'] =
            [
                'pagination' => true,
            ];

        $query['limit'] = [
            'limit' => 10,
        ];

        $query['search_data'] = [
            'sales_order_category',
            'sales_order_unit',
            'sales_order_product_name',
        ];

        $query['filter'] = [
            "sales_order_category",
            "sales_order_unit",
        ];

        $data = generateListData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Data Customer', $data);
    }

    public function history_detail_customer()
    {
        $id = $this->request->getVar();
        $id = $id['id'];

        $query['data'] = ['sales_product'];
        $query['select'] = [
            'sales_product_id' => 'id',
            'sales_product_status' => 'status',
            'sales_product_product_name' => 'product_name',
            'sales_product_product_category' => 'product_category',
            'sales_product_product_type' => 'product_type',
            'sales_product_product_value' => 'product_value',
            'sales_product_quantity' => 'quantity',
            'sales_product_price' => 'price',
            'sales_product_order_id' => 'order_id',
        ];
        $query['where_detail'] = [
            "WHERE sales_product_order_id = {$id}"
        ];

        $data = generateListData($this->request->getGet(), $query, $this->db);
        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Detail Order', $data);
    }
}
