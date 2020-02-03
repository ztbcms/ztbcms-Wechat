<?php
/**
 * User: jayinton
 * Date: 2019/12/24
 * Time: 13:59
 */

namespace Wechat\Controller;


use Common\Controller\AdminBase;
use Wechat\Model\MiniSendMessageRecordModel;
use Wechat\Model\MiniSubscribeMessageModel;
use Wechat\Model\OfficesModel;
use Wechat\Service\MiniSubscribeMessageService;

class MiniSubscribeMessageController extends AdminBase
{
    /**
     * 订阅消息列表
     */
    function lists()
    {
        if (IS_AJAX) {
            $appId = I('app_id', '');
            $title = I('title', '');
            $page = I('page', 1);
            $limit = I('limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%' . $appId . '%'];
            }
            if ($title) {
                $where['title'] = ['like', '%' . $title . '%'];
            }
            $miniSubscribeMessageModel = new MiniSubscribeMessageModel();
            $res = $miniSubscribeMessageModel->where($where)->page($page, $limit)->order('id DESC')->select();
            $totalCount = $miniSubscribeMessageModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit)));
        }
        $this->display();
    }

    function doSyncSubscribeMessageList()
    {
        $miniOfiiceModel = new OfficesModel();
        $minioffices = $miniOfiiceModel->where(['account_type' => OfficesModel::ACCOUNT_TYPE_MINI])->field("app_id")->select();
        $res=self::createReturn(true,[],'同步完成');
        foreach ($minioffices as $minioffice) {
            $appId = $minioffice['app_id'];
            $service = new MiniSubscribeMessageService($appId);
            $res = $service->syncSubscribeMessageList();
        }
        $this->ajaxReturn($res);
    }

    function getDetail()
    {
        $id = I('id');
        $msg = M('wechat_mini_subscribe_message')->where(['id' => $id])->find();
        if (empty($msg)) {
            $this->ajaxReturn(self::createReturn(false, null, '找不到信息'));
            return;
        }
        $content = $msg['content'];
        $list = explode("\n", $content);
        $data_param = [];
        foreach ($list as $item) {
            $str = explode(":", $item);
            if (count($str) == 2) {
                $str[1] = trim($str[1], '{{}}');
                $key = explode(".", $str[1])[0];
                $data_param [] = [
                    'name' => $str[0],
                    'key' => $key,
                    'value' => '',
                ];
            }
        }
        $msg['data_param'] = $data_param;
        $this->ajaxReturn(self::createReturn(true, $msg));
    }

    function testSend()
    {
        $this->display();
    }

    function doTestSend()
    {
        $app_id = I('app_id');
        $openid = I('open_id');
        $template_id = I('template_id');
        $data_param = I('data_param');
        $page = I('page');
        $service = new MiniSubscribeMessageService($app_id);
        $data = [];
        foreach ($data_param as $param){
            $data[$param['key']] = [
                'value' => $param['value']
            ];
        }
        $res = $service->sendSubscribeMessage($openid, $template_id, $data, $page);
        $this->ajaxReturn($res);
    }

    /**
     * 发送记录列表
     */
    function sendMessageRecordList(){
        if (IS_AJAX) {
            $appId = I('app_id', '');
            $open_id = I('open_id', '');
            $page = I('page', 1);
            $limit = I('limit', 20);
            $where = [];
            if ($appId) {
                $where['app_id'] = ['like', '%' . $appId . '%'];
            }
            if ($open_id) {
                $where['open_id'] = ['like', '%' . $open_id . '%'];
            }
            $miniSendMessageRecordModel = new MiniSendMessageRecordModel();
            $res = $miniSendMessageRecordModel->where($where)->page($page, $limit)->order('id DESC')->select();
            foreach ($res as $index => &$item){
                $item['create_time_date'] = date('Y-m-d H:i:s', $item['create_time']);
            }
            $totalCount = $miniSendMessageRecordModel->where($where)->count();
            $this->ajaxReturn(self::createReturnList(true, $res ? $res : [], $page, $limit, $totalCount, ceil($totalCount / $limit)));
        }
        $this->display();
    }

}