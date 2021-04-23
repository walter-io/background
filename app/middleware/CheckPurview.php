<?php
declare (strict_types = 1);

namespace app\middleware;

use liliuwei\think\Jump;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Db;
use think\Response;

class CheckPurview
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 解析url
        $url = request()->pathinfo();
        if (empty($url) || session('roleId') == config('const.superRoleId')) {
            return $next($request);
        }
        $url = substr($url, 0, strpos($url, '.html'));
        // 加缓存
        Cache::set('purview_list', '');
        $arrCachePurview = Cache::get('purview_list');
        if (empty($arrCachePurview)) {
            $arrPurView = Db::name('purview')->field('id, CONCAT(controller, "/", action) url')->select()->toArray();
            $arrCachePurview = array_column($arrPurView, 'id', 'url');
            Cache::set('purview_list', $arrCachePurview);
        }
        if ($url !='admin/admin/index' && !array_key_exists($url, $arrCachePurview)) {
            error('您没有权限');
        }


        return $next($request);
    }

    /**
     * 回调行为：记录管理员操作日志
     * @param \think\Response $response
     * @throws mixed
     */
    public function end(\think\Response $response)
    {
        // 解析url
        $url = request()->pathinfo();
        $url = substr($url, 0, strpos($url, '.html'));
        // 加缓存
        $arrCachePurview = Cache::get('purview_list');
        if (empty($arrCachePurview)) {
            $arrPurView = Db::name('purview')->field('id, CONCAT(controller, "/", action) url')->select()->toArray();
            $arrCachePurview = array_column($arrPurView, 'id', 'url');
            Cache::set('purview_list', $arrCachePurview);
        }
        // 参数

        $purviewId = $arrCachePurview[$url] ?? 0;
        if (!empty($purviewId)) {
            Db::name('admin_log')->insert([
                'admin_id'   => session('adminId'),
                'create_dt'  => date('Y-m-d H:i:s'),
                'purview_id' => $purviewId,
                'params'     => json_encode(request()->param()),
            ]);
        }
    }


}
