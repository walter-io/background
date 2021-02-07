<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

/**
 * 管理员
 * Class Admin
 * @package app\admin\controller
 */
class Admin extends BaseController
{
    /**
     * 密码的盐
     * @var string
     */
    public $salt = 'my_background'; // 盐 请自行配置

    /**
     * 表头
     */
    private $tableTitle = [
        ['field' => 'id', 'title' => 'ID'],
        ['field' => 'name', 'title' => '登录名'],
        ['field' => 'real_name', 'title' => '真名'],
        ['field' => 'role_name', 'title' => '角色'],
        ['field' => 'phone', 'title' => '手机'],
        ['field' => 'create_dt', 'title' => '添加时间'],
        ['field' => 'toolbar', 'title' => '操作', 'width' => '250', 'toolbar' => '#toolbar'],
    ];

    /**
     * 管理员
     * @return string
     */
    public function index()
    {
        return View::fetch('', [
            'tableTitle' => json_encode($this->tableTitle),
        ]);
    }

    /**
     * 获取列表
     */
    public function getLists()
    {
        $limit = request()->post('limit');
        $arrData = Db::connect()
            ->name('admin')
            ->alias('A')
            ->field('A.id, A.name, A.real_name, A.phone, A.create_dt, ROLE.name role_name')
            ->leftJoin('admin_role ROLE', 'A.role_id = ROLE.id')
            ->paginate($limit)
            ->toArray();

        $this->success('', '', $arrData);
    }

    /**
     * 新增
     */
    public function add()
    {
        if (request()->isPost()) {
            // 保存新增
            $data              = [];
            $data['name']      = request()->post('name');
            $data['role_id']   = request()->post('role_id');
            $data['real_name'] = request()->post('real_name');
            $data['phone']     = request()->post('phone');
            $data['create_dt'] = date('Y-m-d H:i:s');
            $data['update_dt'] = date('Y-m-d H:i:s');
            $password          = request()->post('password');
            $rePassword        = request()->post('re_password');
            if (empty($password) || empty($rePassword) || $password != $rePassword) {
                $this->error('密码不能为空且两次密码需一致');
            }
            $data['password']  = md5($this->salt . $password);
            $id = Db::connect()->name('admin')->insertGetId($data);
            if ($id) {
                $this->success('添加成功');
            } else {
                $this->success('添加失败');
            }
        }

        // 角色列表
        $arrRole = Db::connect()->name('admin_role')->field('id, name')->select()->toArray();
        return View::fetch('', [
            'arrRole' => $arrRole
        ]);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = request()->param('id');
        if (empty($id)) {
            $this->error('缺少参数');
        }
        if (request()->isPost()) {
            // 保存
            $data              = [];
            $data['name']      = request()->post('name');
            $data['role_id']   = request()->post('role_id');
            $data['real_name'] = request()->post('real_name');
            $data['phone']     = request()->post('phone');
            $data['update_dt'] = date('Y-m-d H:i:s');
            if (!empty($password) || !empty($rePassword)) {
                $password   = request()->post('password');
                $rePassword = request()->post('re_password');
                if ($password != $rePassword) {
                    $this->error('密码不一致');
                }
                $data['password'] = md5($this->salt . $password);
            }
            $result = Db::connect()->name('admin')->where(['id' => $id])->update($data);
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }

        // 角色列表
        $data    = Db::connect()->name('admin')->where(['id' => $id])->findOrEmpty();
        $arrRole = Db::connect()->name('admin_role')->field('id, name')->selectOrFail()->toArray();
        return View::fetch('', [
            'arrRole' => $arrRole,
            'data'    => $data
        ]);
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = request()->param('id');
        if (empty($id)) {
            $this->error('缺少参数');
        }
        Db::connect()->name('admin')->where(['id' => $id])->delete();
        $this->success('删除成功');
    }
}
