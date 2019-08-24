<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-14
 * Time: 19:38.
 */

namespace Wechat\Service;

use Wechat\Model\OfficeSendTemplateRecordModel;
use Wechat\Model\OfficeTemplateListModel;

class OfficeTemplateService extends OfficeService
{
    public function __construct($app_id)
    {
        parent::__construct($app_id);
    }

    /**
     * 发送模板消息
     *
     * @param        $openId
     * @param        $templateId
     * @param        $data
     * @param string $url
     * @param array  $miniProgram 小程序跳转信息【appid,page】
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Think\Exception
     * @return array
     */
    function sendTemplateMsg($openId, $templateId, $data, $url = '', $miniProgram = [])
    {
        $postData = [
            'touser'      => $openId,
            'template_id' => $templateId,
            'url'         => $url,
            'miniprogram' => $miniProgram,
            'data'        => $data,
        ];
        $res = $this->app->template_message->send($postData);
        if ($res['errcode'] == 0) {
            //发送成功
            $addData = [
                'app_id'      => $this->app_id,
                'open_id'     => $openId,
                'template_id' => $templateId,
                'url'         => $url,
                'result'      => '发送成功',
                'miniprogram' => json_encode($miniProgram),
                'data'        => json_encode($data),
                'create_time' => time()
            ];
            $sendTemplateModel = new OfficeSendTemplateRecordModel();
            $sendTemplateModel->add($addData);
            return self::createReturn(true, [], '发送成功');
        } else {
            $addData = [
                'app_id'      => $this->app_id,
                'open_id'     => $openId,
                'template_id' => $templateId,
                'url'         => $url,
                'result'      => $res['errmsg'],
                'miniprogram' => json_encode($miniProgram),
                'data'        => json_encode($data),
                'create_time' => time()
            ];
            $sendTemplateModel = new OfficeSendTemplateRecordModel();
            $sendTemplateModel->add($addData);
            return self::createReturn(false, [], $res['errmsg']);
        }
    }

    /**
     * 获取所有添加模板消息列表
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function getTemplateList()
    {
        $res = $this->app->template_message->getPrivateTemplates();
        if (!empty($res['template_list'])) {
            $templateList = $res['template_list'];
            foreach ($templateList as $template) {
                $postData = array_merge([
                    "app_id" => $this->app_id,
                ], $template);
                $officeTemplateListModel = new OfficeTemplateListModel();
                $isExist = $officeTemplateListModel->where(['template_id' => $template['template_id']])->find();
                if ($isExist) {
                    $officeTemplateListModel->where(['template_id' => $template['template_id']])->save($postData);
                } else {
                    $officeTemplateListModel->add($postData);
                }
            }
            return self::createReturn(true, $templateList, '获取成功');
        } else {
            return self::createReturn(false, [], '获取模板消息列表失败');
        }
    }
}