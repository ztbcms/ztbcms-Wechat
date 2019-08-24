<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-09
 * Time: 10:07.
 */

namespace Wechat\Service;


use EasyWeChat\Factory;
use System\Service\BaseService;
use Wechat\Model\OfficesModel;
use Think\Exception;

class OfficeService extends BaseService
{
    public $app = null;
    protected $app_id = null;


    function __construct($app_id)
    {
        //获取授权小程序资料
        $officeModel = new OfficesModel();
        $office = $officeModel->where(['app_id' => $app_id, 'account_type' => OfficesModel::ACCOUNT_TYPE_OFFICE])->find();
        if ($office) {
            $config = [
                'app_id'        => $office['app_id'],
                'secret'        => $office['secret'],

                // 下面为可选项
                // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
                'response_type' => 'array',

                'log' => [
                    'level' => 'debug',
                    'file'  => __DIR__.'/wechat.log',
                ],
            ];
            $this->app_id = $app_id;
            $this->app = Factory::officialAccount($config);
        } else {
            throw new Exception("找不到该小程序信息");
        }
    }

    /**
     * 获取网页开发的jssdk
     *
     * @param       $url
     * @param array $APIs
     * @param bool  $debug
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @return array
     */
    function getJssdk($url, $APIs = [], $debug = false)
    {
        $this->app->jssdk->setUrl($url);
        $res = $this->app->jssdk->buildConfig($APIs, $debug);
        if ($res) {
            return self::createReturn(true, ['config' => $res], '获取成功');
        } else {
            return self::createReturn(false, [], '获取失败');
        }
    }
}