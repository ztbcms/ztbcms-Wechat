<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 09:49.
 */

namespace Wechat\Service;


use EasyWeChat\Factory;
use System\Service\BaseService;
use Think\Exception;
use Wechat\Model\OfficesModel;

class MiniService extends BaseService
{
    public $app = null;
    protected $app_id = null;

    function __construct($app_id)
    {
        //获取授权小程序资料
        $officeModel = new OfficesModel();
        $office = $officeModel->where(['app_id' => $app_id, 'account_type' => OfficesModel::ACCOUNT_TYPE_MINI])->find();
        if ($office) {
            $config = [
                'app_id'        => $office['app_id'],
                'secret'        => $office['secret'],

                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file'  => C("UPLOADFILEPATH").'/wechat.log',
                ],
            ];
            $this->app_id = $app_id;
            $this->app = Factory::miniProgram($config);
        } else {
            return self::createReturn(false, [], "找不到该小程序信息");
        }
    }

    /**
     * 内容安全检查
     *
     * @param $content
     * @return array
     */
    function checkContent($content){
        $res = $this->app->content_security->checkText($content);
        if($res['errcode'] == '87014'){
            return self::createReturn(false, null, '内容含有敏感信息');
        }else{
            return self::createReturn(true, null, 'ok');
        }
    }
}