<?php
// 应用公共文件

//权限无限级树
use think\exception\HttpResponseException;
use think\Response;

function purview_tree($arr, $pid = 0, $level = 1, $idName = 'id', $pidName = 'parent_id')
{
    $tree = array();
    foreach ($arr as $k => $v) {
        if ($v[$pidName] == $pid) {
            $v['level'] = $level;
            $tree[$v[$idName]] = $v;
            $tree = $tree + purview_tree($arr, $v[$idName], $level + 1, $idName, $pidName);
        }
    }
    return $tree;
}

// 递归树
function purview_tree_sec($originArr, $resultArr, $pid = 0, $level = 0)
{
    // TODO HERE
    foreach ($originArr as $k => $v) {
        if ($v['parent_id'] == $pid) {
            $v['level'] = $level;
            $v['children'] = purview_tree_sec($originArr, $resultArr, $v['id'], $level + 1);
            $resultArr[] = $v;
        }
    }
    return $resultArr;
}

// 递归树
function purview_tree_third($data, $level = 0)
{
    $str = '';
    if (empty($data)) {
        return $str;
    }
    $space = str_repeat('&nbsp;', $level * 4);
    foreach ($data as $k => $v) {
        $str .= "<p>{$space}{$v['name']}</p>";
        $str.= purview_tree_third($v['children'] ?? [], $level++);
    }
    return $str;
}


/**
 * 格式化权限适合ztree
 * @param $arr
 * @return array
 */
function purview_format($arr, $selectArr = [])
{
    $data = [];
    $i = 0;
    foreach ($arr as $k => $v) {
        $data[$i] = [
            'id' => $v['id'],
            'pId' => $v['parent_id'],
            'name' => $v['name'],
        ];

        if ($selectArr && in_array($v['id'], $selectArr)) {
            $data[$i]['checked'] = true;
        }
        $i++;
    }
    return $data;
}

function error($msg = '', $data = '', $url = '')
{
    $result = [
        'code' => 0,
        'msg'  => $msg,
        'data' => $data,
        'url'  => $url,
        'wait' => '',
    ];

    $type = 'html';
    if ('html' == strtolower($type)) {
        $type = 'view';
        $response = Response::create(config('jump.dispatch_error_tmpl'), $type)->assign($result)->header([]);
    } else {
        $response = Response::create($result, $type)->header([]);
    }

    throw new HttpResponseException($response);
}