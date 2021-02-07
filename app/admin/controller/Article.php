<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;

/**
 * 文章管理
 * Class Admin
 * @package app\admin\controller
 */
class Article extends BaseController
{
    private $tableTitle = [
        ['field' => 'ID', 'title' => 'ID'],
        ['field' => 'category', 'title' => '分类'],
        ['field' => 'title', 'title' => '标题'],
        ['field' => 'title', 'title' => '标题'],
    ];

    /**
     * 列表
     * @return string
     */
    public function index()
    {
        return View::fetch();
    }

    /**
     * 新增
     * @return string
     */
    public function add()
    {
        return View::fetch();
    }
}
