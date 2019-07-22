<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 09:47.
 */

namespace Wechat\Service;


use Wechat\Model\MiniUsersModel;

class MiniUsersService extends MiniService
{
    public function __construct($app_id)
    {
        parent::__construct($app_id);
    }

    /**
     * 通过token获取用户信息
     *
     * @param $accessToken
     *
     * @throws \Think\Exception
     * @return array
     */
    function getUserInfoByToken($accessToken)
    {
        $usersModel = new MiniUsersModel();
        $fields = 'open_id,nick_name,gender,language,city,province,country,avatar_url,access_token';
        $user = $usersModel->where(['app_id' => $this->app_id, 'access_token' => $accessToken])->field($fields)->find();
        if ($user) {
            return self::createReturn(true, $user, '获取成功');
        } else {
            return self::createReturn(false, [], '找不到用户信息', 500);
        }
    }

    /**
     * 通过小程序授权的code 获取用户信息
     *
     * @param $code
     * @param $iv
     * @param $encryptedData
     *
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Think\Exception
     * @return array
     */
    function getUserInfoByCode($code, $iv, $encryptedData)
    {
        $res = $this->app->auth->session($code);
        if (!empty($res['session_key'])) {
            //获取session_key 成功
            $sessionKey = $res['session_key'];
            $openid = $res['openid'];
            $unionid = !empty($res['unionid']) ? $res['unionid'] : '';
            $info = $this->app->encryptor->decryptData($sessionKey, $iv, $encryptedData);
            if (!empty($info['openId'])) {
                $accessToken = md5($sessionKey.time().rand(1000, 9999));
                $data = [
                    'app_id'       => $this->app_id,
                    'open_id'      => $openid,
                    'union_id'     => $unionid,
                    'nick_name'    => $info['nickName'],
                    'gender'       => $info['gender'],
                    'language'     => $info['language'],
                    'city'         => $info['city'],
                    'province'     => $info['province'],
                    'country'      => $info['country'],
                    'avatar_url'   => $info['avatarUrl'],
                    'access_token' => $accessToken
                ];
                $usersModel = new MiniUsersModel();
                $user = $usersModel->where(['app_id' => $data['app_id'], 'open_id' => $data['open_id']])->find();
                if ($user) {
                    $data['update_time'] = time();
                    $usersModel->where(['id' => $user['id']])->save($data);
                } else {
                    $data['create_time'] = time();
                    $usersModel->add($data);
                }
                $fields = 'open_id,nick_name,gender,language,city,province,country,avatar_url,access_token';
                $usersModel->where(['app_id' => $data['app_id'], 'open_id' => $data['open_id']])->field($fields)->find();
                return self::createReturn(true, $user, '获取成功');
            } else {
                return self::createReturn(false, [], '获取用户信息失败', 500);
            }
        } else {
            return self::createReturn(false, [], '获取session失败', 500);
        }
    }
}