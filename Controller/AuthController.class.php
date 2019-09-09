<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-09
 * Time: 10:06.
 */

namespace Wechat\Controller;


use Common\Controller\Base;
use Wechat\Model\AutoTokenModel;
use Wechat\Model\OfficeUsersModel;
use Wechat\Service\MiniUsersService;
use Wechat\Service\OfficeService;

class AuthController extends Base
{

    /**
     * 用户信息授权
     *
     * @param $appid
     *
     * @throws \Think\Exception
     */
    function oauth($appid)
    {
        $redirectUrl = urldecode(I('redirect_url'));
        $office = new OfficeService($appid);
        if ($redirectUrl) {
            session('redirect_url', $redirectUrl);
        }
        $response = $office->app->oauth->scopes(['snsapi_userinfo'])
            ->redirect(U("Wechat/Auth/callback", ['appid' => $appid]));
        $response->send();
    }

    /**
     * 用户静默授权
     *
     * @param $appid
     *
     * @throws \Think\Exception
     */
    function oauthBase($appid)
    {
        $redirectUrl = urldecode(I('redirect_url'));
        $office = new OfficeService($appid);
        if ($redirectUrl) {
            session('redirect_url', $redirectUrl);
        }
        $response = $office->app->oauth->scopes(['snsapi_base'])
            ->redirect(U("Wechat/Auth/callback", ['appid' => $appid]));
        $response->send();
    }

    /**
     * 授权回调地址
     *
     * @param $appid
     *
     * @throws \ReflectionException
     * @throws \Think\Exception
     */
    function callback($appid)
    {
        $officeService = new OfficeService($appid);
        $user = $officeService->app->oauth->user();
        $original = $user->getOriginal();
        if (!empty($original['scope']) && $original['scope'] == "snsapi_base") {
            //静默授权只拿到用户的openid
            $postData = [
                'app_id'  => $appid,
                'open_id' => $original['openid'],
            ];
        } else {
            //非静默授权可以拿到用户的具体信息
            $postData = [
                'app_id'     => $appid,
                'open_id'    => $original['openid'],
                'nick_name'  => $original['nickname'],
                'sex'        => $original['sex'],
                'avatar_url' => $original['headimgurl'],
                'country'    => $original['country'],
                'province'   => $original['province'],
                'city'       => $original['city'],
                'language'   => $original['language'],
                'union_id'   => empty($original['unionid']) ? '' : $original['unionid'],
            ];
        }
        $officeUsers = new OfficeUsersModel();
        $isExist = $officeUsers->where(['app_id' => $appid, 'open_id' => $postData['open_id']])->find();
        if ($isExist) {
            $postData['update_time'] = time();
            $res = $officeUsers->where(['id' => $isExist['id']])->save($postData);
        } else {
            $postData['create_time'] = time();
            $res = $officeUsers->add($postData);
        }
        if ($res) {
            $redirectUrl = session('redirect_url');
            if ($redirectUrl) {
                $autoTokenModel = new AutoTokenModel();
                $autoToken = $autoTokenModel->createAuthToken($postData['app_id'], $postData['open_id']);
                if ($autoToken) {
                    //创建token成功，返回待code
                    if (strpos($redirectUrl, '?')) {
                        $redirectUrl .= "&code=".$autoToken['code'];
                    } else {
                        $redirectUrl .= "?code=".$autoToken['code'];
                    }
                    redirect($redirectUrl);
                } else {
                    $this->ajaxReturn(self::createReturn(true, [], '创建登录信息失败'));
                }
            } else {
                $this->ajaxReturn(self::createReturn(true, [], '获取信息成功过'));
            }
        } else {
            $this->ajaxReturn(self::createReturn(false, [], '获取用户信息失败'));
        }
    }

    /**
     * 授权微信小程序信息
     *
     * @param $appid
     *
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     */
    function miniAuthUserInfo($appid)
    {
        $code = I('post.code');
        $iv = I('post.iv');
        $encryptedData = I('encrypted_data');
        $miniUsersModel = new MiniUsersService($appid);
        $res = $miniUsersModel->getUserInfoByCode($code, $iv, $encryptedData);
        $this->ajaxReturn($res);
    }

    /**
     * 通过临时登录凭证code 获取token信息
     */
    function getTokenByCode()
    {
        $code = I('get.code');
        $autoTokenModel = new AutoTokenModel();
        $res = $autoTokenModel->getTokenByCode($code);
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, $res, "获取成功"));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], "找不到该记录"));
        }
    }

    /**
     * 通过refresht_token更新token
     */
    function refreshToken()
    {
        $refreshToken = I('get.refresh_token');
        $autoTokenModel = new AutoTokenModel();
        $res = $autoTokenModel->refreshToken($refreshToken);
        if ($res) {
            $this->ajaxReturn(self::createReturn(true, $res, "更新token成功"));
        } else {
            $this->ajaxReturn(self::createReturn(false, [], "找不到该记录"));
        }
    }
}