<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 14:31.
 */

namespace Wechat\Service;


use Wechat\Model\MiniCodeModel;

class MiniCodeService extends MiniService
{
    public function __construct($app_id)
    {
        parent::__construct($app_id);
    }

    /**
     * 不限制小程序码生成
     *
     * @param       $scene
     * @param array $optional
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Think\Exception
     * @return array
     */
    function getUnlimitMiniCode($scene, array $optional = [])
    {
        $response = $this->app->app_code->getUnlimit($scene, $optional);
        $path = empty($optional['page']) ? "" : $optional['page'];
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $uploadPath = C("UPLOADFILEPATH").'wechat/code/';
            $directory = rtrim($uploadPath, '/');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $fileName = md5(time().rand(1000, 9999)).'.png';
            $res = $response->saveAs($uploadPath, $fileName);
            if ($res) {
                $result = [
                    "app_id"      => $this->app_id,
                    "type"        => MiniCodeModel::CODE_TYPE_UNLIMIT,
                    "path"        => $path,
                    "scene"       => $scene,
                    "file_name"   => $fileName,
                    "file_url"    => cache("Config.sitefileurl").'wechat/code/'.$fileName,
                    "create_time" => time()
                ];
                $miniCodeModel = new MiniCodeModel();
                $addRes = $miniCodeModel->add($result);
                if ($addRes) {
                    $result['id'] = $addRes;
                    return self::createReturn(true, $result, '获取成功');
                } else {
                    return self::createReturn(false, [], '保存小程序失败');
                }
            }
            return self::createReturn(false, [], '保存小程序失败');
        }
        return self::createReturn(false, [], '获取小程序码失败');
    }

    /**
     * 获取限制类型小程序码
     *
     * @param       $path
     * @param array $optional
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Think\Exception
     * @return array
     */
    function getMiniCode($path, array $optional = [])
    {
        $response = $this->app->app_code->get($path, $optional);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $uploadPath = C("UPLOADFILEPATH").'wechat/code/';
            $directory = rtrim($uploadPath, '/');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $fileName = md5(time().rand(1000, 9999)).'.png';
            $res = $response->saveAs($uploadPath, $fileName);
            if ($res) {
                $result = [
                    "app_id"      => $this->app_id,
                    "type"        => MiniCodeModel::CODE_TYPE_LIMIT,
                    "path"        => $path,
                    "file_name"   => $fileName,
                    "file_url"    => cache("Config.sitefileurl").'wechat/code/'.$fileName,
                    "create_time" => time()
                ];
                $miniCodeModel = new MiniCodeModel();
                $addRes = $miniCodeModel->add($result);
                if ($addRes) {
                    $result['id'] = $addRes;
                    return self::createReturn(true, $result, '获取成功');
                } else {
                    return self::createReturn(false, [], '保存小程序失败');
                }
            }
            return self::createReturn(false, [], '保存小程序失败');
        }
        return self::createReturn(false, [], '获取小程序码失败');
    }
}