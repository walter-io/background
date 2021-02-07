<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;

/**
 * 后台首页
 * Class Index
 * @package app\admin\controller
 */
class Index extends BaseController
{
    /**
     * 后台首页
     * @return string
     */
    public function index()
    {
        return View::fetch();
    }

    /**
     * 欢迎页
     */
    public function welcome()
    {
        return View::fetch();
    }

}
