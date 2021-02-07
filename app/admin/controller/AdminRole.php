<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

/**
 * 管理员角色
 * Class Admin
 * @package app\admin\controller
 */
class AdminRole extends BaseController
{
    /**
     * 表头
     * @var \string[][]
     */
    private $tableTitle = [
        ['field' => 'id', 'title' => 'ID'],
        ['field' => 'name', 'title' => '管理员名称'],
        ['field' => 'description', 'title' => '角色说明'],
        ['field' => 'create_dt', 'title' => '创建时间'],
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
     * 列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLists()
    {
        $limit = request()->post('limit');
        $result = Db::connect()->name('admin_role')->paginate($limit)->toArray();
        $this->success('', '', $result);
    }

    /**
     * 新增
     */
    public function add()
    {
        if (request()->isPost()) {
            // 保存
            $data = [
                'name'        => request()->post('name'),
                'description' => request()->post('description'),
                'create_dt'   => date('Y-m-d H:i:s'),
                'update_dt'   => date('Y-m-d H:i:s'),
            ];
            $result = Db::connect()->name('admin_role')->insert($data);
            if ($result) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
        return View::fetch();
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
            $data = [
                'name'        => request()->post('name'),
                'description' => request()->post('description'),
                'create_dt'   => date('Y-m-d H:i:s'),
                'update_dt'   => date('Y-m-d H:i:s'),
            ];
            $result = Db::connect()->name('admin_role')->where(['id' => $id])->update($data);
            if ($result) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
        $data = Db::connect()->name('admin_role')->where(['id' => $id])->findOrEmpty();
        return View::fetch('', ['data' => $data]);
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
        Db::connect()->name('admin_role')->where(['id' => $id])->delete();
        $this->success('删除成功');
    }
}