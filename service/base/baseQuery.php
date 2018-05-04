<?php

/**
 * 生成表达式
 * @param unknown $opCode
 * @param unknown $data
 * @return string
 */
function getOperatorsStr($opCode, $data)
{
    $result = '';
    switch ($opCode) {
        case 'eq':
            $result = ' =\'' . $data . '\'';
            break;
        case 'ne':
            $result = ' <>\'' . $data . '\'';
            break;
        case 'bw':
            $result = ' like \'' . $data . '%\'';
            break;
        case 'bn':
            $result = ' not like \'' . $data . '%\'';
            break;
        case 'ew':
            $result = ' like \'%' . $data . '\'';
            break;
        case 'en':
            $result = ' not like \'%' . $data . '\'';
            break;
        case 'cn':
            $result = ' like \'%' . $data . '%\'';
            break;
        case 'nc':
            $result = ' not like \'%' . $data . '%\'';
            break;
        case 'nu':
            $result = ' is null';
            break;
        case 'nn':
            $result = ' is not null';
            break;
        case 'in':
            $result = ' in(' . $data . ')';
            break;
        case 'ni':
            $result = ' not in(' . $data . ')';
            break;
    }
    return $result;
}

/**
 * 生成where条件语句
 *
 * @param array $filters
 * @return string
 */
function generateWhereStr($filters)
{
    $result = '(';
    $rules = $filters['rules'];
    $tmpstr = '';
    foreach ($rules as $rule) {
        if ($tmpstr != '') {
            $tmpstr = $tmpstr . ' ' . $filters['groupOp'] . ' ';
        }
        $tmpstr = $tmpstr . $rule['field'] . getOperatorsStr($rule['op'], $rule['data']);
    }
    $result = $result . $tmpstr;
    if (array_key_exists("groups", $filters)) {
        $groups = $filters['groups'];
        $tmpstr_t = '';
        foreach ($groups as $group) {
            if ($tmpstr_t != '') {
                $tmpstr_t = $tmpstr_t . ' ' . $group['groupOp'] . ' ';
            }
            $tmpstr_t = $tmpstr_t . generateWhereStr($group);
        }
        if ($result != '(' && $tmpstr_t != '') {
            $result = $result . ' ' . $filters['groupOp'] . ' ';
        }
        $result = $result . $tmpstr_t;
    }
    if ($result == '(') {
        $result = '';
    } else {
        $result = $result . ')';
    }
    return $result;
}

/**
 * 查询
 *
 * @param array $sqlArray :[0]-字段名，[1]-表名，[2]-where条件（where 开头）
 * @param int $dbno =-1
 * @param string $pKey
 *            主键
 * @return array ['total']-总页数，['page']-当前页号，['records']-总记录数，['rows']-查询结果集
 */
function doQuery($sqlArray, $dbno = -1, $pKey = 'id')
{
    $sqlArray[2] = isset($sqlArray[2]) && $sqlArray[2] != null ? $sqlArray[2] : "";
    $sqlArray[3] = isset($sqlArray[3]) && $sqlArray[3] != null ? $sqlArray[3] : "";
    $result = array();
    $issearch = $_POST['_search'];
    $currPage = (int)$_POST['page'];
    $rows = (int)$_POST['rows'];
    $sidx = $_POST['sidx'];
    $sord = $_POST['sord'];
    if ($issearch != "false") {
        $filters = (array)json_decode($_POST['filters'], true);
        $whereStr = generateWhereStr($filters);
        if ($whereStr != '') {
            if (strstr($sqlArray[2], "where") !== false) {
                $sqlArray[2] = $sqlArray[2] . ' and ' . $whereStr;
            } else {
                $sqlArray[2] = 'where ' . $whereStr;
            }
        } else {
            $sqlArray[2] = '';
        }
    }
    if ($sidx && !empty($sidx) && $sidx != '') {
        $names = explode(',', $sidx);
        $sqlArray[3] = 'order by ';
        foreach ($names as $name) {
            $sqlArray[3] = $sqlArray[3] . $name . ' ' . $sord . ',';
        }
        $sqlArray[3] = substr($sqlArray[3], 0, strlen($sqlArray[3]) - 1);
    }
    $commonTools = new service\tools\ToolsClass($dbno);
    $search_result = $commonTools->getDatasBySQL_Pagin($currPage, $rows, $pKey, $sqlArray);
    $result['total'] = $search_result[0];
    $result['records'] = $search_result[1];
    $rows = array();
    foreach ($search_result[2] as $row) {
        $tmp = [
            'id' => $row[$pKey],
            'cell' => $row
        ];
        array_push($rows, $tmp);
    }
    $result['rows'] = $rows;
    $result['page'] = $currPage;
    return $result;
}