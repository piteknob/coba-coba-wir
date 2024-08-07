<?php

namespace App\Controllers\Admin;

use App\Controllers\Core\AuthController;
use CodeIgniter\HTTP\ResponseInterface;

class Confirmation extends AuthController
{

    public function list_transaction()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

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
            "WHERE sales_order_status = 'payed' OR sales_order_status = 'pending'"
        ];
        $query['pagination'] = [true];

        $data = generateListData($this->request->getGet(), $query, $this->db);
        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Transaction', $data);
    }

    public function list_order_product()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

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
            "WHERE sales_product_order_id = $id"
        ];
        $query['pagination'] = [true];
        $data = generateListData($this->request->getVar(), $query, $this->db);

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'List Order Product', $data);
    }

    public function confirm()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }
        $post = $this->request->getVar();
        $id = $post['id'];

        $query_sales_order['data'] = ['sales_order'];

        $query_sales_order['select'] = [
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

        $query_sales_order['where_detail'] = [
            "WHERE sales_order_status = 'payed' AND sales_order_id = {$id}"
        ];

        $query_sales_order['pagination'] = [
            'pagination' => false
        ];

        $query_sales_product['data'] = ['sales_product'];
        $query_sales_product['select'] = [
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
        $query_sales_product['where_detail'] = [
            "WHERE sales_product_order_id = {$id}"
        ];
        $query_sales_product['pagination'] = [false];

        $data_product = generateListData($this->request->getVar(), $query_sales_product, $this->db);

        // -------------------- UPDATE FROM 'PAYED' TO 'CONFIRMED' ------------------------- //

        $sql_sales_order = "UPDATE sales_order SET 
                sales_order_status = 'confirmed'
                WHERE sales_order_status = 'payed' AND sales_order_id = {$id}";

        $this->db->query($sql_sales_order);

        $sql_sales_product = "UPDATE sales_product SET
                sales_product_status = 'confirmed'
                WHERE sales_product_status = 'payed' AND sales_product_order_id = {$id}";

        $this->db->query($sql_sales_product);

        foreach ($data_product as $key => $value) {
            $id_product = $value['id'];
            $product = $value['product_name'];
            $category = $value['product_category'];
            $type = $value['product_type'];
            $product_value = $value['product_value'];
            $quantity = $value['quantity'];

            // --------- INSERT INTO log_stock --------- //
            $log = "INSERT INTO log_stock (log_stock_product_id, log_stock_product_name, log_stock_status, log_stock_quantity, log_stock_type_id, log_stock_type_name, log_stock_category_id, log_stock_category_name, log_stock_value_id, log_stock_value_value, log_stock_date)
            SELECT '{$id_product}', '{$product}', 'sold', '{$quantity}', type_id, '{$type}', category_id, '{$category}', value_id, '{$product_value}', NOW()
            FROM product, category, `type`, `value`
            WHERE product_name = '{$product}' AND type_name = '{$type}' AND category_name = '{$category}' AND value_value = '{$product_value}'";
            $this->db->query($log);
        }

        $query_confirm_order['data'] = ['sales_order'];
        $query_confirm_order['select'] = [
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
        $query_confirm_order['where_detail'] = [
            "WHERE sales_order_id = {$id} AND sales_order_status = 'confirmed'"
        ];
        $query_confirm_order['pagination'] = [false];

        $data_confirm_order = generateDetailData($this->request->getVar(), $query_confirm_order, $this->db);
        foreach ($data_confirm_order as $key => $value) {
            $data_confirm_order = $value;
        }

        $query_confirm_product['data'] = ['sales_product'];
        $query_confirm_product['select'] = [
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
        $query_confirm_product['where_detail'] = [
            "WHERE sales_product_order_id = {$id} AND sales_product_status = 'confirmed'"
        ];

        $data_confirm_product = generateListData($this->request->getVar(), $query_confirm_product, $this->db);

        $result = [
            'data' => [
                'order' => $data_confirm_order,
                'product' => $data_confirm_product
            ]
        ];




        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Order Successfully Confirmed', $result);
    }

    public function cancel()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }

        $post = $this->request->getPost();
        $db = db_connect();
        $id = $post['id'];

        $sql = "UPDATE sales_order SET sales_order_status = 'canceled' WHERE sales_order_id = '{$id}' AND (sales_order_status = 'pending' OR sales_order_status = 'payed')";

        $db->query($sql);

        $sql_product = "UPDATE sales_product SET sales_product_status = 'canceled' WHERE sales_product_order_id = '{$id}' AND (sales_product_status = 'pending' OR sales_product_status = 'payed')";

        $db->query($sql_product);

        $query_order['data'] = ['sales_order'];
        $query_order['select'] = [
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
        $query_order['where_detail'] = [
            "WHERE sales_order_id = '{$id}' AND sales_order_status = 'canceled'"
        ];

        $data_order_canceled = generateDetailData($this->request->getPost(), $query_order, $db);
        foreach ($data_order_canceled as $key => $value) {
            $data_order = $value;
        }

        $query_product['data'] = ['sales_product'];
        $query_product['select'] = [
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
        $query_product['where_detail'] = [
            "WHERE sales_product_order_id = '{$id}' AND sales_product_status = 'canceled'"
        ];
        $data_product_canceled = (array) generateListData($this->request->getVar(), $query_product, $db);

        // RESTORE STOCK 
        foreach ($data_product_canceled['data'] as $key => $value) {
            $name = $value['product_name'];
            $category = $value['product_category'];
            $type = $value['product_type'];
            $box = $value['product_value'];
            $quantity = $value['quantity'];

            $sql_add_stock = "UPDATE product_stock SET product_stock_stock = product_stock_stock + {$quantity}, product_stock_stock_out = product_stock_stock_out - {$quantity} WHERE product_stock_product_name = '{$name}' AND product_stock_type_name = '{$type}' AND product_stock_category_name = '{$category}' AND product_stock_value_value = '{$box}'";
            $this->db->query($sql_add_stock);
        }


        $data = [
            'data' => [
                'order' => $data_order,
                'product' => $data_product_canceled
            ]
        ];

        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Data Successfully Canceled', $data);
    }

    public function report()
    {
        // Authorization Token
        $token = $this->before(getallheaders());
        if (!empty($token)) {
            return $token;
        }


        $query['data'] = ['customer'];

        $query['select'] = [
            'sales_order.sales_order_customer_id' => 'customer_id',
            'customer.customer_name' => 'customer_name',
            'sales_order.sales_order_status' => 'status',
            'sales_order.sales_order_product_name' => 'product',
            'sales_order.sales_order_category' => 'category',
            'sales_order.sales_order_unit' => 'unit',
            'sales_order.sales_order_price' => 'price',
            'sales_order.sales_order_value' => 'value',
            'sales_order.sales_order_date' => "'date'",
        ];

        $query['join'] = [
            'sales_order' => 'sales_order.sales_order_customer_id = customer.customer_id'
        ];

        $query['pagination'] = [
            'pagination' => true,
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

        $query['limit'] = ['limit' => 10];

        $data = generateListData($this->request->getVar(), $query, $this->db);


        return $this->responseSuccess(ResponseInterface::HTTP_OK, 'Report Order', $data);
    }
}
