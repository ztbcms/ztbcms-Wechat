<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-15
 * Time: 15:58.
 */

namespace Wechat\Service;


use Wechat\Model\OfficeQrcodeModel;

class OfficeQrcodeService extends OfficeService
{
    /**
     * 获取永久参数二维码
     *
     * @param int $param
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function forever(int $param)
    {
        $result = $this->app->qrcode->forever($param);
        if (!empty($result['ticket'])) {
            $qrcodeUrl = $this->app->qrcode->url($result['ticket']);
            $content = file_get_contents($qrcodeUrl);
            $directory = C("UPLOADFILEPATH").'wechat/qrcode/';
            $directory = rtrim($directory, '/');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $fileName = "f".time().rand(1000, 9999).'.png';
            $filePath = C("UPLOADFILEPATH").'wechat/qrcode/'.$fileName;
            $saveRes = file_put_contents($filePath, $content); // 写入文件
            if ($saveRes) {
                $url = cache("Config.sitefileurl").'wechat/qrcode/'.$fileName;
                //生成数据入库
                $postData = [
                    'app_id'      => $this->app_id,
                    'param'       => $param,
                    'expire_time' => 0,
                    'file_path'   => $filePath,
                    'type'        => OfficeQrcodeModel::QRCODE_TYPE_FOREVER,
                    'qrcode_url'  => $url,
                    'create_time' => time()
                ];

                $officeQrcodeModel = new OfficeQrcodeModel();
                $officeQrcodeModel->add($postData);
                return self::createReturn(true, ['qrcode_url' => $url, 'expire_time' => time() + $expireTime], '获取成功');
            } else {
                return self::createReturn(false, [], '保存二维码失败');
            }
        } else {
            return self::createReturn(false, [], '获取失败');
        }
    }

    /**
     *  获取临时二维码
     *
     * @param string $param
     * @param int    $expireTime
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function temporary(string $param, int $expireTime = 2592000)
    {
        $result = $this->app->qrcode->temporary($param, $expireTime);
        if (!empty($result['ticket'])) {
            $qrcodeUrl = $this->app->qrcode->url($result['ticket']);
            $content = file_get_contents($qrcodeUrl);
            $directory = C("UPLOADFILEPATH").'wechat/qrcode/';
            $directory = rtrim($directory, '/');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $fileName = "t".time().rand(1000, 9999).'.png';
            $filePath = C("UPLOADFILEPATH").'wechat/qrcode/'.$fileName;
            $saveRes = file_put_contents($filePath, $content); // 写入文件
            if ($saveRes) {
                $url = cache("Config.sitefileurl").'wechat/qrcode/'.$fileName;
                //生成数据入库
                $postData = [
                    'app_id'      => $this->app_id,
                    'param'       => $param,
                    'expire_time' => $expireTime,
                    'file_path'   => $filePath,
                    'qrcode_url'  => $url,
                    'type'        => OfficeQrcodeModel::QRCODE_TYPE_TEMPORARY,
                    'create_time' => time()
                ];

                $officeQrcodeModel = new OfficeQrcodeModel();
                $officeQrcodeModel->add($postData);
                return self::createReturn(true, ['qrcode_url' => $url, 'expire_time' => time() + $expireTime], '获取成功');
            } else {
                return self::createReturn(false, [], '保存二维码失败');
            }
        } else {
            return self::createReturn(false, [], '获取失败');
        }
    }
}