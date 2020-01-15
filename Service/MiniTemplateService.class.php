<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-07
 * Time: 16:22.
 */

namespace Wechat\Service;


use Wechat\Model\MiniFormIdModel;
use Wechat\Model\MiniTemplateListModel;
use Wechat\Model\OfficeSendTemplateRecordModel;

class MiniTemplateService extends MiniService
{
    public function __construct($app_id)
    {
        parent::__construct($app_id);
    }

    /**
     * 发送模板消息
     *
     * @param $openId
     * @param $templateId
     * @param $page
     * @param $data
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Think\Exception
     * @return array
     */
    function sendTemplateMessage($openId, $templateId, $page, $data)
    {
        //获取最近拿到的 formId
        $miniFormIdModel = new MiniFormIdModel();
        $formIdRecord = $miniFormIdModel->where(['app_id' => $this->app_id, 'open_id' => $openId, 'used' => MiniFormIdModel::USED_NO])->order('id DESC')->find();
        if ($formIdRecord) {
            $formId = $formIdRecord['form_id'];
            $sendData = [
                'touser'      => $openId,
                'template_id' => $templateId,
                'page'        => $page,
                'form_id'     => $formId,
                'data'        => $data
            ];
            $res = $this->app->template_message->send($sendData);
            if ($res['errcode'] == 0) {
                $result = '发送成功';
                $response = self::createReturn(true, [], '发送成功');
            } else {
                //发送失败，记录发送结果
                $result = $res['errmsg'];
                $response = self::createReturn(false, [], '发送失败：'.$res['errmsg']);
            }
            $updateData = [
                'result'    => $result,
                'used'      => MiniFormIdModel::USED_YES,
                'send_time' => time()
            ];
            $miniFormIdModel->where(['id' => $formIdRecord['id']])->save($updateData);
            return $response;
        } else {
            return self::createReturn(false, [], '找不到form_id');
        }
    }

    /**
     * 获取发送模板消息所需的form_id
     *
     * @param $openId
     * @param $formId
     *
     * @throws \Think\Exception
     * @return array
     */
    function addFormId($openId, $formId)
    {
        $postData = [
            'app_id'      => $this->app_id,
            'open_id'     => $openId,
            'form_id'     => $formId,
            'create_time' => time()
        ];
        $miniFormIdModel = new MiniFormIdModel();
        $res = $miniFormIdModel->add($postData);
        if ($res) {
            return self::createReturn(false, [], '添加form_id成功');
        } else {
            return self::createReturn(false, [], '添加form_id失败');
        }
    }

    /**
     * 发送统一模板消息
     * @param $mp_appid
     * @param $openId
     * @param $templateId
     * @param $data
     * @param string $url
     * @param string $page
     * @return array
     */
    function sendUniformMessage($mp_appid, $openId, $templateId, $data, $url = '', $page = ''){
        $miniProgram = [
            'appid' => $this->app_id,
            'pagepath' => $page
        ];
        $sendData = [
            'touser' => $openId,
            'weapp_template_msg' => [],
            'mp_template_msg' => [
                'appid' => $mp_appid,
                'template_id' => $templateId,
                'url' => $url,
                'miniprogram' => $miniProgram,
                'data' => $data
            ]
        ];
        $res = $this->app->uniform_message->send($sendData);
        if ($res['errcode'] == 0) {
            //发送成功
            $addData = [
                'app_id'      => $mp_appid,
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
            $response = self::createReturn(true, [], '发送成功');
        } else {
            //发送失败，记录发送结果
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
            $response = self::createReturn(false, [], '发送失败：'.$res['errmsg']);
        }
        return $response;
    }

    /**
     * 获取模板消息列表
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function getTemplateList()
    {
        $res = $this->app->template_message->getTemplates(0, 20);
        if ($res['errcode'] == 0) {
            $templateList = $res['list'];
            foreach ($templateList as $template) {
                $postData = array_merge([
                    "app_id" => $this->app_id,
                ], $template);
                $miniTemplateListModel = new MiniTemplateListModel();
                $isExist = $miniTemplateListModel->where(['template_id' => $template['template_id']])->find();
                if ($isExist) {
                    $postData['update_time'] = time();
                    $miniTemplateListModel->where(['template_id' => $template['template_id']])->save($postData);
                } else {
                    $postData['create_time'] = time();
                    $miniTemplateListModel->add($postData);
                }
            }
            return self::createReturn(true, $templateList, '获取成功');
        } else {
            return self::createReturn(false, [], '获取模板消息列表失败');
        }
    }
}