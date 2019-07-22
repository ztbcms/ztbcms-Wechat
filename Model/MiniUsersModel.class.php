<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 10:39.
 */

namespace Wechat\Model;


use Think\Model;

class MiniUsersModel extends Model
{
    protected $tableName = 'wechat_mini_users';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
        ['open_id', 'require', 'open_id 必要填写！'],
    ];
}