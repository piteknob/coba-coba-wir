<?php


// OPT POST
function curlSetOptPost($curl)
{
    $url = isset($curl['url']) ?  $curl['url'] : '';
    $method = isset($curl['method']) ?  $curl['method'] : '';
    $endpoint = isset($curl['endpoint']) ?  $curl['endpoint'] : '';
    $return_transfer = isset($curl['return_transfer']) ?  $curl['return_transfer'] : '';
    $max_redirect = isset($curl['max_redirect']) ?  $curl['max_redirect'] : '';
    $timeout = isset($curl['timeout']) ?  $curl['timeout'] : '';
    $follow_location = isset($curl['follow_location']) ?  $curl['follow_location'] : '';
    $http_header = isset($curl['http_header']) ?  $curl['http_header'] : '';
    $post_field = isset($curl['post_field']) ?  $curl['post_field'] : '';


    if (!empty($url)) {
        $url = BaseUrlCurl($url);
    }

    if (!empty($method)) {
        $method = customRequest($method);
    }

    if (!empty($endpoint)) {
        $endpoint = endPoint($endpoint);
    }

    if (!empty($return_transfer)) {
        $return_transfer = returnTransfer($return_transfer);
    }

    if (!empty($max_redirect)) {
        $max_redirect = maxRedirect($max_redirect);
    }

    if (!empty($timeout)) {
        $timeout = timeOut($timeout);
    }

    if (!empty($follow_location)) {
        $follow_location = followLocation($follow_location);
    }

    if (!empty($http_header)) {
        $http_header = httpHeader($http_header);
    }

    if (!empty($post_field)) {
        $post_field = postField($post_field);
    }



    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$url}/{$endpoint}",
        CURLOPT_RETURNTRANSFER => "{$return_transfer}",
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => "{$max_redirect}",
        CURLOPT_TIMEOUT => "{$timeout}",
        CURLOPT_FOLLOWLOCATION => "{$follow_location}",
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "{$method}",
        CURLOPT_HTTPHEADER => array(
            "{$http_header}"
        ),
        CURLOPT_POSTFIELDS => array(
            $post_field
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

// OPT GET
function curlSetOptGet($curl)
{
    $url = isset($curl['url']) ?  $curl['url'] : '';
    $method = isset($curl['method']) ?  $curl['method'] : '';
    $endpoint = isset($curl['endpoint']) ?  $curl['endpoint'] : '';
    $return_transfer = isset($curl['return_transfer']) ?  $curl['return_transfer'] : '';
    $max_redirect = isset($curl['max_redirect']) ?  $curl['max_redirect'] : '';
    $timeout = isset($curl['timeout']) ?  $curl['timeout'] : '';
    $follow_location = isset($curl['follow_location']) ?  $curl['follow_location'] : '';
    $http_header = isset($curl['http_header']) ?  $curl['http_header'] : '';
    $pagination = isset($curl['pagination']) ?  $curl['pagination'] : '';
    $params = isset($curl['params']) ? $curl['params'] : '';


    if (!empty($url)) {
        $url = BaseUrlCurl($url);
    }

    if (!empty($method)) {
        $method = customRequest($method);
    }

    if (!empty($endpoint)) {
        $endpoint = endPoint($endpoint);
    }

    if (!empty($params)) {
        $params = params($params);
        $paramStatus = true;
    } else {
        $paramStatus = false;
    }

    if (!empty($pagination)) {
        $pagination = pagination($pagination, $paramStatus);
    }

    if (!empty($return_transfer)) {
        $return_transfer = returnTransfer($return_transfer);
    }

    if (!empty($max_redirect)) {
        $max_redirect = maxRedirect($max_redirect);
    }

    if (!empty($timeout)) {
        $timeout = timeOut($timeout);
    }

    if (!empty($follow_location)) {
        $follow_location = followLocation($follow_location);
    }

    if (!empty($http_header)) {
        $http_header = httpHeader($http_header);
    }


    // ---------- set CurL ---------- //
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$url}/{$endpoint}{$params}{$pagination}",
        CURLOPT_RETURNTRANSFER => "{$return_transfer}",
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => "{$max_redirect}",
        CURLOPT_TIMEOUT => "{$timeout}",
        CURLOPT_FOLLOWLOCATION => "{$follow_location}",
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "{$method}",
        CURLOPT_HTTPHEADER => array(
            "{$http_header}"
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


//----------------- PRINTILAN -----------------//
function BaseUrlCurl($url)
{
    foreach ($url as $key => $value) {
        $url = $value;
        $curl = $url;
    }
    return $curl;
}

function endPoint($point)
{
    $curl = '';
    foreach ($point as $key => $value) {
        $curl = $value;
    }
    return $curl;
}

function params($params)
{
    $curl = '?';
    foreach ($params as $key => $value) {
        $curl .= "{$key}={$value}&";
    }
    $curl = rtrim($curl, '& ');
    return $curl;
}

function pagination($pagination, $paramStatus)
{
    // get pagination value
    foreach ($pagination as $key => $value) {
        $pagination = $value;
    }

    if ($pagination == 'true') {
        if (!empty($paramStatus)) {
            $curl = "&pagination=true";
            return $curl;
        } else {
            $curl = "?pagination=true";
   
            return $curl;
        }
    } else {
        if ($paramStatus == 'false') {
            $curl = "&pagination=false";
            return $curl;
        } else {
            $curl = "?pagination=false";
            return $curl;
        }
    }


}


function returnTransfer($transfer)
{
    if ($transfer > 0) {
        return 'true';
    } else {
        return 'false';
    }
}

function maxRedirect($direct)
{
    foreach ($direct as $key => $value) {
        $direct = $value;
    }
    return $direct;
}

function timeOut($timeout)
{
    foreach ($timeout as $key => $value) {
        $timeout = $value;
    }
    return $timeout;
}

function followLocation($location)
{
    if ($location > 0) {
        return 'true';
    } else {
        return 'false';
    }
}


function customRequest($cusReq)
{
    foreach ($cusReq as $key => $value) {
        $cusReq = $value;
        // print_r($cusReq);
        // die;
    }
    return $cusReq;
}

function httpHeader($httphead)
{
    $curl = '';
    foreach ($httphead as $key => $value) {
        $curl = "{$key}: {$value},";
    }
    $curl = rtrim($curl, ', ');
    return $curl;
}

function postField($post_field)
{
    $curl = '';
    foreach ($post_field as $key => $value) {
        $curl .= "'{$key}' => '{$value}',";
    }
    $curl = rtrim($curl, ', ');
    return $post_field;
}
