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
            ->field('id, name, parent_id pId, controller, action')
            ->order('sort', 'asc')
            ->select()
            ->toArray();

//        $arrData = purview_tree_sec($arrData, [], 0);
        $editPurviewFlag = true;

        $deletePurviewFlag = true;
        return View::fetch('', [
            'list' => $arrData,
            'editPurviewFlag'   => $editPurviewFlag,
            'deletePurviewFlag' => $deletePurviewFlag
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
            $data['create_dt']  = date('Y-m-d H:i:s');
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

        if (request()->isPost()) {
            // 编辑
            $id = request()->param('id');
            $arrParams = [

                'name'       => request()->param('name'),
                'controller' => request()->param('controller'),
                'action'     => request()->param('action'),
                'parent_id'  => request()->param('pid'),
                'is_show'    => request()->param('is_show'),
                'sort'       => request()->param('sort'),
                'update_dt'  => date('Y-m-d H:i:s'),
            ];
            $result = Db::connect()->name('purview')->where(['id' => $id])->update($arrParams);
            if ($result) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
        // 显示页面
        $id = request()->param('purviewId');
        if (empty($id)) {
            $this->error('缺少参数');
        }

        // 权限详情
        $data = Db::connect()
            ->name('purview')
            ->field('name, parent_id, controller, action, is_show, sort')
            ->where('id', $id)
            ->find();
        
        // 权限列表
        $arrPurview = Db::connect()
            ->name('purview')
            ->field('id, name, parent_id')
            ->order('sort','asc')
            ->select()
            ->toArray();
        $arrPurview = purview_tree($arrPurview);
       
        return View::fetch('', ['arrPurview' => $arrPurview, 'data' => $data, 'id' => $id]);
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
        Db::connect()->name('purview')->where(['id' => $id])->delete();
        $this->success('删除成功');
    }
}