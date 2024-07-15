<?php

if (!function_exists('generateDetailData')) {
    function generateDetailData($params, $query, $db)
    {
        $dataQuery = isset($query['data']) ? $query['data'] : '';
        $selectQuery = isset($query['select']) ? $query['select'] : '';
        $joinQuery = isset($query['join']) ? $query['join'] : '';
        $leftJoinQuery = isset($query['left_join']) ? $query['left_join'] : '';
        $rightJoinQuery = isset($query['right_join']) ? $query['right_join'] : '';
        $whereQuery = isset($query['where']) ? $query['where'] : '';
        $whereDetailQuery = isset($query['where_detail']) ? $query['where_detail'] : '';
        $id = isset($params['id']) ? $params['id'] : '';

        $data = (object) [];

        $sql = '';

        if (!empty($selectQuery)) {
            $sql .= selectData($selectQuery);
        }

        if (!empty($dataQuery)) {
            $sql .= dataFrom($dataQuery);
        }

        if (!empty($joinQuery)) {
            $sql .= joinTable($joinQuery);
        }

        if (!empty($leftJoinQuery)) {
            $sql .= leftJoin($leftJoinQuery);
        }

        if (!empty($rightJoinQuery)) {
            $sql .= rightJoin($rightJoinQuery);
        }

        if (!empty($whereQuery)) {
            $sql .= whereData($whereQuery);
        }

        if (!empty($whereDetailQuery)) {
            $sql .= whereDataDetail($whereDetailQuery);
        }


        // print_r($sql);
        // die;

        $sql = $db->query($sql)->getResultArray();
        $data->data = $sql;

        return $data;
    }
}

if (!function_exists('generateListData')) {
    function generateListData($params, $query, $db)
    {
        // --------------- set query --------------- //
        $dataQuery = isset($query['data']) ? $query['data'] : '';
        $selectQuery = isset($query['select']) ? $query['select'] : '';
        $searchQuery = isset($query['search_data']) ? $query['search_data'] : '';
        $joinQuery = isset($query['join']) ? $query['join'] : '';
        $leftJoinQuery = isset($query['left_join']) ? $query['left_join'] : '';
        $rightJoinQuery = isset($query['right_join']) ? $query['right_join'] : '';
        $whereQuery = isset($query['where']) ? $query['where'] : '';
        $whereDetailQuery = isset($query['where_detail']) ? $query['where_detail'] : '';
        $filterQuery = isset($query['filter']) ? $query['filter'] : '';
        $filterBetweenQuery = isset($query['filter_between']) ? $query['filter_between'] : '';
        $groupByQuery = isset($query['group_by']) ? $query['group_by'] : '';
        $orderByQuery = isset($query['order_by']) ? $query['order_by'] : '';
        $paginationResult = isset($query['pagination']) ? $query['pagination'] : '';

        // --------------- set params --------------- //
        $paginationPage = isset($params['page']) ? $params['page'] : 1;
        $search = isset($params['search']) ? $params['search'] : '';
        $filter = isset($params['filter']) ? $params['filter'] : '';
        $start = isset($params['start']) ? $params['start'] : '';
        $end = isset($params['end']) ? $params['end'] : '';
        $paginationParams = isset($params['pagination']) ? $params['pagination'] : '';


        $data = (object) [];

        $sql = '';

        if (!empty($selectQuery)) {
            $sql .= selectData($selectQuery);
        }

        if (!empty($dataQuery)) {
            $sql .= dataFrom($dataQuery);
        }

        if (!empty($joinQuery)) {
            $sql .= joinTable($joinQuery);
        }

        if (!empty($leftJoinQuery)) {
            $sql .= leftJoin($leftJoinQuery);
        }

        if (!empty($rightJoinQuery)) {
            $sql .= rightJoin($rightJoinQuery);
        }

        if (!empty($whereQuery)) {
            $sql .= whereData($whereQuery);
            $where = true;
        } else {
            $where = false;
        }

        if (!empty($whereDetailQuery)) {
            $sql .= whereDataDetail($whereDetailQuery);
            $where = true;
        } else {
            if (empty($where)) {
                $where = false;
            } else {
                $where = true;
            }
        }

        if (!empty($searchQuery)) {
            if (!empty($search)) {
                $sql .= searchData($searchQuery, $search, $where);
                $where = true;
            }
        } else {
            if (empty($where)) {
                $where = false;
            } else {
                $where = true;
            }
        }
        if (!empty($filterQuery)) {
            $sql .= filterData($filter, $filterQuery, $where, $filter);
            $where = true;
        } else {
            if (empty($where)) {
                $where = false;
            } else {
                $where = true;
            }
        }
        if (!empty($filterBetweenQuery)) {
            if (!empty($start) or !empty($end)) {
                $sql .= filterBetween($filterBetweenQuery, $start, $end, $where, $search, $filter);
            }
        }



        if (!empty($groupByQuery)) {
            $sql .= groupBy($groupByQuery);
        }

        if (!empty($orderByQuery)) {
            $sql .= orderBy($orderByQuery);
        }

        // print_r($sql);
        // die;
        // Set Pagination from Params

        if ($paginationParams == 'false') {
            return $db->query($sql)->getResultArray();
        }
        if ($paginationParams == 'true') {
            $pagination = true;

            if (!empty($paginationResult)) {
                $pagination = paginationValue($paginationResult);
            }

            if (!empty($pagination)) {

                $countQuery = $sql;
                $countResult = $db->query($countQuery)->getResultArray();
                $countData = count($countResult);

                $limit = isset($query['limit']['limit']) ? $query['limit']['limit'] : 5;
                $offset = ($paginationPage - 1) * $limit;

                $sql .= " LIMIT {$offset}, {$limit}";

                $result = $db->query($sql)->getResultArray();
                $jumlahPage = ceil($countData / $limit);
                $pageSebelumnya = ($paginationPage - 1 > 0) ? ($paginationPage - 1) : null;
                $pageSelanjutnya = ($paginationPage + 1 <= $jumlahPage) ? ($paginationPage + 1) : null;


                $detailPage = detailPage($jumlahPage, $paginationPage, $countData, $limit);

                foreach ($detailPage as $key => $value) {
                    $pageStartEnd[] = $value;
                }

                $detailPage = range($pageStartEnd[0], $pageStartEnd[1]);

                // Data
                $data->data = $result;
                $data->pagination = [
                    'jumlah_data' => $countData,
                    'jumlah_page' => $jumlahPage,
                    'prev' => $pageSebelumnya,
                    'page' => $paginationPage,
                    'next' => $pageSelanjutnya,
                    'detail' => $detailPage,
                    'start' => $pageStartEnd[2],
                    'end' => $pageStartEnd[3],
                ];
                return $data;
            }
        }


        // Set Pagination from Controller 

        $pagination = true;
        if (!empty($paginationResult) && empty($paginationParams)) {
            $pagination = paginationValue($paginationResult);
        }

        if (!empty($pagination)) {

            $countQuery = $sql;
            $countResult = $db->query($countQuery)->getResultArray();
            $countData = count($countResult);

            $limit = isset($query['limit']['limit']) ? $query['limit']['limit'] : 5;
            $offset = ($paginationPage - 1) * $limit;

            $sql .= " LIMIT {$offset}, {$limit}";

            $result = $db->query($sql)->getResultArray();
            $jumlahPage = ceil($countData / $limit);
            $pageSebelumnya = ($paginationPage - 1 > 0) ? ($paginationPage - 1) : null;
            $pageSelanjutnya = ($paginationPage + 1 <= $jumlahPage) ? ($paginationPage + 1) : null;


            $detailPage = detailPage($jumlahPage, $paginationPage, $countData, $limit);


            foreach ($detailPage as $key => $value) {
                $pageStartEnd[] = $value;
            }


            $detailPage = range($pageStartEnd[0], $pageStartEnd[1]);
            $dataDetail = [];

            foreach ($detailPage as $key => $value) {
                $dataDetail = $value;
            }



            // Data
            $data->data = $result;
            $data->pagination = [
                'jumlah_data' => $countData,
                'jumlah_page' => $jumlahPage,
                'prev' => $pageSebelumnya,
                'page' => $paginationPage,
                'next' => $pageSelanjutnya,
                'detail' => $detailPage,
                'start' => $pageStartEnd[2],
                'end' => $pageStartEnd[3],
            ];
            return $data;
        }

        if (empty($pagination)) {
            return $db->query($sql)->getResultArray();
        }
    }
}





// ------------------------------------------- PRINTILAN ------------------------------------------- //

// Generate result Array to JSON
if (!function_exists('setToJSON')) {
    function setToJSON($data)
    {
        $data = json_encode($data);
        return $data;
    }
}


// Generate result JSON to Array
if (!function_exists('setToArray')) {
    function setToArray($data)
    {
        $data = json_decode($data);
        return $data;
    }
}


// Generate query data FROM ...
if (!function_exists('dataFrom')) {
    function dataFrom($data)
    {
        $query = " FROM ";
        foreach ($data as $key => $value) {
            $query .= "{$value}, ";
        }
        $query = rtrim($query, ', ');
        return $query;
    }
}


// Generate query select data
if (!function_exists('selectData')) {
    function selectData($data)
    {
        $query = 'SELECT ';
        foreach ($data as $key => $row) {
            $query .= "{$key} AS {$row}, ";
        }
        $query = rtrim($query, ', ');
        return $query;
    }
}


// Generate query limit 
if (!function_exists('limitData')) {
    function limitData($data)
    {
        $query = " LIMIT 0, {$data['limit']}";
        return $query;
    }
}


// Generate query search
if (!function_exists('searchData')) {
    function searchData($data, $search, $where)
    {
        $sql = '';
        if (empty($where)) {
            $sql .= " WHERE (";
            foreach ($data as $key => $row) {
                $sql .= "{$row} LIKE '%{$search}%' OR ";
            }
            $sql = rtrim($sql, ' OR ');
            $sql = "$sql)";
            return $sql;
        } else {
            $sql .= " AND (";
            foreach ($data as $key => $row) {
                $sql .= "{$row} LIKE '%{$search}%' OR ";
            }
            $sql = rtrim($sql, ' OR ');
            $sql = "$sql)";
            return $sql;
        }
    }
}


// Generate query filter
if (!function_exists('filterData')) {
    function filterData($data, $selectedField, $where, $filter)
    {
        if (empty($filter)) {
            return '';
        }
        if (!empty($where)) {
            $sql = ' AND (';
            foreach ($selectedField as $key => $value) {
                $sql .= "{$value} = '{$data}' OR ";
            }
            $sql = rtrim($sql, ' OR ');
            $sql = "$sql)";
            return $sql;
        } else {
            $sql = ' WHERE (';
            foreach ($selectedField as $key => $value) {
                $sql .= "{$value} = '{$data}' OR ";
            }
            $sql = rtrim($sql, ' OR ');
            $sql = "$sql)";
            return $sql;
        }
    }
}


// Generate query filter between
if (!function_exists('filterBetween')) {
    function filterBetween($data, $start, $end, $where, $search, $filter)
    {
        if (empty($where)) {
            foreach ($data as $key => $value) {
                $sql = " WHERE {$value} BETWEEN '{$start}' AND '{$end}' AND ";
            }
            $sql = rtrim($sql, ' AND ');
            return $sql;
        } else {
            if (!empty($search)) {
                if (!empty($start) && ($end)) {
                    foreach ($data as $key => $value) {
                        $sql = " AND {$value} BETWEEN '{$start}' AND '{$end}' AND ";
                    }
                    $sql = rtrim($sql, ' AND ');
                    return $sql;
                }
                if (!empty($start)) {
                    foreach ($data as $key => $value) {
                        $sql = " AND {$value} BETWEEN '{$start}' AND '9999999999999999999' AND ";
                    }
                    $sql = rtrim($sql, ' AND ');
                    return $sql;
                }
                if (!empty($end)) {
                    foreach ($data as $key => $value) {
                        $sql = " AND {$value} BETWEEN '{$start}' AND '{$end}' AND ";
                    }
                    $sql = rtrim($sql, ' AND ');
                    return $sql;
                }
            } else {
                if (!empty($filter)) {
                    if (!empty($start) && ($end)) {
                        foreach ($data as $key => $value) {
                            $sql = " AND {$value} BETWEEN '{$start}' AND '{$end}' AND ";
                        }
                        $sql = rtrim($sql, ' AND ');
                        return $sql;
                    }
                    if (!empty($start)) {
                        foreach ($data as $key => $value) {
                            $sql = " AND {$value} BETWEEN '{$start}' AND '9999999999999999999' AND ";
                        }
                        $sql = rtrim($sql, ' AND ');
                        return $sql;
                    }
                    if (!empty($end)) {
                        foreach ($data as $key => $value) {
                            $sql = " AND {$value} BETWEEN '{$start}' AND '{$end}' AND ";
                        }
                        $sql = rtrim($sql, ' AND ');
                        return $sql;
                    }
                }
            }
        }
    }
}


// Generate query inner join 
if (!function_exists('joinTable')) {
    function joinTable($data)
    {
        foreach ($data as $key => $row) {
            $join = " JOIN {$key} ON {$row}";
        }
        return $join;
    }
}


// Generate query left join
if (!function_exists('leftJoin')) {
    function leftJoin($data)
    {
        foreach ($data as $key => $row) {
            $join = " LEFT JOIN {$key} ON {$row}";
        }
        return $join;
    }
}


// Generate query right join 
if (!function_exists('rightJoin')) {
    function rightJoin($data)
    {
        foreach ($data as $key => $row) {
            $join = " RIGHT JOIN {$key} ON {$row}";
        }
        return $join;
    }
}


// Generate query where clause
if (!function_exists('whereData')) {
    function whereData($data)
    {
        $where = " WHERE ";
        foreach ($data as $key => $row) {
            $where .= "{$key} = {$row} AND ";
        }
        $where = rtrim($where, ' AND ');
        return $where;
    }
}


// Generate Where detail query
if (!function_exists('whereDataDetail')) {
    function whereDataDetail($data)
    {
        foreach ($data as $key => $value) {
            $where = ' ';
            $where .= $value;
            return $where;
        }
    }
}


// Generate pagination value 
if (!function_exists('paginationValue')) {
    function paginationValue($data)
    {
        foreach ($data as $key => $value) {
            $pagination = $value;
            return $pagination;
        }
    }
}


// Generate pagination detail
if (!function_exists('detailPage')) {
    function detailPage($page, $paginationPage, $data, $limit)
    {
        $request = 2;
        $jumlahLink = $request;
        if ($paginationPage > $jumlahLink) {
            $start_page = $paginationPage - $jumlahLink;
        } else {
            $start_page = 1;
        }

        if ($paginationPage < ($page - $request)) {
            $end_page = $paginationPage + $request;
        } else {
            $end_page = $page;
        }


        $array = range(1,$data);

        $pageSize = $limit;

        $pageNumber = $paginationPage;

        $startIndex = ($pageNumber - 1) * $pageSize;
        $endIndex = min($startIndex + $pageSize, count($array)) - 1;

        $pageData = array_slice($array, $startIndex, $pageSize);

        $startIndex = $startIndex + 1;
        $endIndex = $endIndex + 1;
        $data = [
            'start_page' => $start_page,
            'end_page' => $end_page,
            'start_index' => $startIndex,
            'end_index' => $endIndex,
        ];

        return $data;
    }
}

// Generate query group by
if (!function_exists('groupBy')) {
    function groupBy($data)
    {
        foreach ($data as $row) {
            $query = " GROUP BY {$row},";
            $query = rtrim($query, ', ');
            return $query;
        }
    }
}


// Generate query order by
if (!function_exists('orderBy')) {
    function orderBy($data)
    {
        foreach ($data as $key => $row) {
            $query = " ORDER BY {$row},";
        }
        $query = rtrim($query, ', ');
        return $query;
    }
}
