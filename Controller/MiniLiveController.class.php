<?php

namespace Wechat\Controller;


use Common\Controller\AdminBase;
use Wechat\Model\MiniLiveModel;
use Wechat\Model\OfficesModel;
use Wechat\Service\MiniLiveService;

/**
 * 小程序直播
 * Class MiniLiveController
 * @package Wechat\Controller
 */
class MiniLiveController extends AdminBase
{

    /**
     * 小程序直播列表
     */
    public function lists()
    {
        if(IS_AJAX){
            $appId = I('app_id', '');
            $title = I('title', '');
            $page = I('page', '1','trim');
            $limit = I('limit', '20','trim');
            $where = [];

            if ($appId) $where['app_id'] = ['like', '%' . $appId . '%'];
            if ($title)  $where['live_name'] = ['like', '%' . $title . '%'];

            $MiniLiveModel = new MiniLiveModel();
            $res = $MiniLiveModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $MiniLiveModel->where($where)->count();

            foreach ($res as $k => $v){
                $res[$k]['start_time'] = date("Y-m-d H:i",$v['start_time']);
                $res[$k]['end_time'] = date("Y-m-d H:i",$v['end_time']);
            }

            $this->ajaxReturn(
                self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit))
            );
        } else {
            $this->display();
        }
    }

    /**
     * 同步直播间
     */
    public function sysMiniLive(){
        $miniOfiiceModel = new OfficesModel();
        $minioffices = $miniOfiiceModel
            ->where([
                'account_type' => OfficesModel::ACCOUNT_TYPE_MINI
            ])
            ->field("app_id,secret")
            ->select();
        foreach ($minioffices as $k => $v){
            MiniLiveService::sysMiniLive($v['app_id'],$v['secret']);
        }
        $this->ajaxReturn(self::createReturn(true,[],'同步完成'));
    }

}