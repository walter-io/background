<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

/**
 * 角色权限
 * Class Admin
 * @package app\admin\controller
 */
class Purview extends BaseController
{
    /**
     * 权限
     * @return string
     */
    public function index()
    {
        $arrData = Db::connect()
            ->name('purview')
            ->field('id, name, parent_id, controller, action')
            ->order('sort', 'asc')
            ->select()
            ->toArray();

        $data = purview_tree_sec($arrData, [], 0);
        return View::fetch('', [
            'list' => json_encode($data),
        ]);
    }


    /**
     * 新增
     */
    public function add()
    {
        if (request()->isPost()) {
            // 保存
            $data               = [];
            $data['parent_id']  = request()->post('parent_id');
            $data['name']       = request()->post('name');
            $data['controller'] = request()->post('controller');
            $data['action']     = request()->post('action');
            $data['is_show']    = request()->post('is_show');
            $data['sort']       = request()->post('sort');
            $result = Db::connect()->name('purview')->insert($data);
            if ($result) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }

        $arrData = Db::connect()
            ->name('purview')
            ->field('id, name, parent_id, controller, action')
            ->order('sort', 'asc')
            ->select()
            ->toArray();
        $arrData = purview_tree($arrData);
        return View::fetch('', [
            'arrData' => $arrData
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