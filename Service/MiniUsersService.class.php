<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 09:47.
 */

namespace Wechat\Service;


use Wechat\Model\AutoTokenModel;
use Wechat\Model\MiniPhoneNumberModel;
use Wechat\Model\MiniUsersModel;
use Wechat\Model\OfficesModel;

class MiniUsersService extends MiniService
{
    public function __construct($app_id)
    {
        parent::__construct($app_id);
    }

    /**
     * 通过授权code 获取手机号码
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
    function getPhoneNumberByCode($code, $iv, $encryptedData)
    {
        $res = $this->app->auth->session($code);
        if (!empty($res['session_key'])) {
            //获取session_key 成功
            $sessionKey = $res['session_key'];
            $openid = $res['openid'];
            $info = $this->app->encryptor->decryptData($sessionKey, $iv, $encryptedData);
            if (!empty($info['phoneNumber'])) {
                $postData = [
                    'app_id'            => $this->app_id,
                    'open_id'           => $openid,
                    'country_code'      => $info['countryCode'],
                    'phone_number'      => $info['phoneNumber'],
                    'pure_phone_number' => $info['purePhoneNumber'],
                    'create_time'       => time()
                ];
                $miniPhoneNumber = new MiniPhoneNumberModel();
                $isExist = $miniPhoneNumber->where(['app_id' => $this->app_id, 'open_id' => $openid])->find();
                if ($isExist) {
                    $postData['update_time'] = time();
                    $res = $miniPhoneNumber->where(['id' => $isExist['id']])->save($postData);
                } else {
                    $res = $miniPhoneNumber->add($postData);
                }
                if ($res) {
                    return self::createReturn(true, $info, '获取成功');
                } else {
                    return self::createReturn(false, $info, '数据插入有误');
                }
            } else {
                return self::createReturn(false, [], '获取用户信息失败', 500);
            }
        } else {
            return self::createReturn(false, [], '获取session失败', 500);
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
                $data = [
                    'app_id'     => $this->app_id,
                    'open_id'    => $openid,
                    'union_id'   => $unionid,
                    'nick_name'  => $info['nickName'],
                    'gender'     => $info['gender'],
                    'language'   => $info['language'],
                    'city'       => $info['city'],
                    'province'   => $info['province'],
                    'country'    => $info['country'],
                    'avatar_url' => $info['avatarUrl'],
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

                //生成登录token
                $authTokenModel = new AutoTokenModel();
                $authToken = $authTokenModel->createAuthToken($this->app_id, $openid, OfficesModel::ACCOUNT_TYPE_MINI);
                if ($authToken) {
                    $result = array_merge($data, [
                        'token'         => $authToken['token'],
                        'expire_time'   => $authToken['expire_time'],
                        'refresh_token' => $authToken['refresh_token'],
                    ]);
                    return self::createReturn(true, $result, '获取成功');
                } else {
                    return self::createReturn(false, [], '生成登录信息失败', 500);
                }
            } else {
                return self::createReturn(false, [], '获取用户信息失败', 500);
            }
        } else {
            return self::createReturn(false, [], '获取session失败', 500);
        }
    }
}