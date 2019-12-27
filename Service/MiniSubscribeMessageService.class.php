<?php
/**
 * User: jayinton
 * Date: 2019/12/24
 * Time: 10:43
 */

namespace Wechat\Service;


use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;
use System\Service\BaseService;
use Wechat\Model\MiniSendMessageRecordModel;
use Wechat\Model\MiniSendTemplateRecordModel;
use Wechat\Model\MiniSubscribeMessageModel;

/**
 * 小程序订阅消息
 * 微信文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.addTemplate.html
 * Class MiniSubscribeMessageService
 * @package Wechat\Service
 */
class MiniSubscribeMessageService extends MiniService
{

    /**
     * 发送订阅消息
     * @param string $openid 接收者（用户）的 openid
     * @param string $template_id 所需下发的订阅模板id
     * @param array $data 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
     * @param string $page 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
     * @return array
     */
    function sendSubscribeMessage($openid, $template_id, $data = [], $page = '')
    {
        $sendData = [
            'template_id' => $template_id,
            'touser' => $openid,
            'page' => $page,
            'data' => $data,
        ];
        $result = '';

        try {
            $res = $this->app->subscribe_message->send($sendData);
            if ($res['errcode'] == 0) {
                $result = '发送成功';
                $response = self::createReturn(true, null, '发送成功');
            } else {
                //发送失败，记录发送结果
                $result = $res['errmsg'];
                $response = self::createReturn(false, null, '发送失败：' . $res['errmsg']);
            }
        } catch (InvalidArgumentException $e) {
            $response = self::createReturn(false, null, '发送失败:' . $e->getMessage());
        } catch (InvalidConfigException $e) {
            $response = self::createReturn(false, null, '发送失败:' . $e->getMessage());
        } catch (GuzzleException $e) {
            $response = self::createReturn(false, null, '发送失败:' . $e->getMessage());
        }

        $log = [
            'app_id' => $this->app_id,
            'send_time' => time(),
            'template_id' => $template_id,
            'open_id' => $openid,
            'page' => $page,
            'data' => json_encode($data),
            'result' => $result,
            'create_time' => time(),
        ];
        $miniSendMessageRecordModel = new MiniSendMessageRecordModel();
        $res = $miniSendMessageRecordModel->add($log);
        return $response;
    }

    /**
     * 获取当前帐号下的个人模板列表
     * 返回数据格式参考：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getTemplateList.html
     *{
        "priTmplId": "9Aw5ZV1j9xdWTFEkqCpZ7mIBbSC34khK55OtzUPl0rU",
        "title": "报名结果通知",
        "content": "会议时间:{{date2.DATA}}\n会议地点:{{thing1.DATA}}\n",
        "example": "会议时间:2016年8月8日\n会议地点:TIT会议室\n",
        "type": 2
        }
     * @return array
     */
    function getSubscribeMessageList()
    {
        try {
            $res = $this->app->subscribe_message->getTemplates();
            if ($res['errcode'] == 0) {
                $templateList = $res['data'];
                return self::createReturn(true, $templateList, '获取成功');
            } else {
                return self::createReturn(false, null, '获取模板消息列表失败,原因：' . $res['errmsg']);
            }
        } catch (InvalidConfigException $e) {
            return self::createReturn(false, null, '获取模板消息列表失败,原因：' . $e->getMessage());
        } catch (GuzzleException $e) {
            return self::createReturn(false, null, '获取模板消息列表失败,原因：' . $e->getMessage());
        }
    }

    /**
     * 同步订阅消息
     * @return array
     */
    function syncSubscribeMessageList()
    {
        $res = $this->getSubscribeMessageList();
        if (!$res['status']) {
            return $res;
        }

        $templateList = $res['data'];
        foreach ($templateList as $template) {
            $postData = array_merge([
                "app_id" => $this->app_id,
                "template_id" => $template['priTmplId']
            ], $template);
            $MiniSubscribeMessageModel = new MiniSubscribeMessageModel();
            $isExist = $MiniSubscribeMessageModel->where(['template_id' => $template['priTmplId']])->find();
            if ($isExist) {
                $postData['update_time'] = time();
                $MiniSubscribeMessageModel->where(['template_id' => $template['priTmplId']])->save($postData);
            } else {
                $postData['create_time'] = time();
                $MiniSubscribeMessageModel->add($postData);
            }
        }

        return self::createReturn(true, $templateList, '同步完成');
    }
}