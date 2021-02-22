<?php
// 应用公共文件

//权限无限级树
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
function purview_tree_sec($originArr, $resultArr, $pid = 0)
{
    foreach ($originArr as $k => $v) {
        if ($v['parent_id'] == $pid) {
            $v['children'] = purview_tree_sec($originArr, $resultArr, $v['id']);
            $resultArr[] = $v;
        }
    }
    return $resultArr;
}
