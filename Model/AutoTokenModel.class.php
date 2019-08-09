<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-09
 * Time: 14:05.
 */

namespace Wechat\Model;


use Think\Model;

class AutoTokenModel extends Model
{
    const TOKEN_EXPIRE_TIME = 604800;//默认一个星期过期
    protected $tableName = 'wechat_auto_token';
    protected $_validate = [
        ['app_id', 'require', '必须输入公众号appid！'],
    ];

    /**
     * 创建登录token
     *
     * @param        $appid
     * @param        $openId
     * @param string $appAccountType
     *
     * @throws \Think\Exception
     * @return array|bool
     */
    function createAuthToken($appid, $openId, $appAccountType = 'office')
    {
        $postData = [
            'app_id'           => $appid,
            'app_account_type' => $appAccountType,
            'open_id'          => $openId,
            'code'             => base_convert(md5(time().rand(1000, 9999)), 16, 10),
            'token'            => sha1($this->app_id.time().rand(10000, 99999)),
            'expire_time'      => time() + self::TOKEN_EXPIRE_TIME,
            'refresh_token'    => sha1($this->app_id.time().rand(10000, 99999)),
            'create_time'      => time()
        ];
        $res = $this->add($postData);
        if ($res) {
            return $postData;
        } else {
            return false;
        }
    }

    /**
     * 通过登录的临时凭证code获取token
     *
     * @param $code
     *
     * @throws \Think\Exception
     * @return bool|mixed
     */
    function getTokenByCode($code)
    {
        $res = $this->where(['code' => $code])->field("token,expire_time,refresh_token")->find();
        if ($res) {
            //校验过后，code信息更新为空
            $this->where(['code' => $code])->save(['code' => '']);
            return $res;
        } else {
            return false;
        }
    }

    function refreshToken($refreshToken)
    {
        $res = $this->where(['refresh_token' => $refreshToken])->field("id,token,expire_time,refresh_token")->find();
        if ($res) {
            $updateData = [
                'token'         => sha1($this->app_id.time().rand(10000, 99999)),
                'expire_time'   => time() + self::TOKEN_EXPIRE_TIME,
                'refresh_token' => $refreshToken
            ];
            $this->where(['id' => $res['id']])->save($updateData);
            return $updateData;
        } else {
            return false;
        }
    }

    /**
     * 通过token 获取用户信息
     *
     * @param $token
     *
     * @throws \Think\Exception
     * @return bool|mixed
     */
    function getUserInfoByToken($token)
    {
        $where = [
            'token'       => $token,
            'expire_time' => ['gt', time()]
        ];
        $res = $this->where($where)->find();
        if ($res) {
            $userWhere = [
                'app_id'  => $res['app_id'],
                'open_id' => $res['open_id']
            ];
            if ($res == OfficesModel::ACCOUNT_TYPE_OFFICE) {
                //公众号用户
                $officeUsersModel = new OfficeUsersModel();
                $userInfo = $officeUsersModel->where($userWhere)->find();
            } else {
                $miniUsersModel = new MiniUsersModel();
                $userInfo = $miniUsersModel->where($userWhere)->find();
            }
            return $userInfo;
        } else {
            return false;
        }
    }
}