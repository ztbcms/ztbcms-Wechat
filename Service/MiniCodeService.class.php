<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 14:31.
 */

namespace Wechat\Service;


class MiniCodeService extends MiniService
{
    public function __construct($app_id)
    {
        parent::__construct($app_id);
    }

    function getMiniCode($path, array $optional = [])
    {
        $response = $this->app->app_code->get($path, $optional);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $imgPath = C("UPLOADFILEPATH").'wechat/code', md5(time().rand(1000, 9999));
            $res = $response->saveAs($imgPath);
            if ($res) {
                return self::createReturn(true, ['file_path' => $imgPath], '获取成功');
            }
            return self::createReturn(false, [], '保存小程序失败');
        }
        return self::createReturn(false, [], '获取小程序码失败');
    }
}