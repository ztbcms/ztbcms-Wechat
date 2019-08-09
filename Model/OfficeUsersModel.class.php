<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-09
 * Time: 10:58.
 */

namespace Wechat\Model;


use Think\Model;

class OfficeUsersModel extends Model
{
    protected $tableName = 'wechat_office_users';

    protected $_validate = [
        ['app_id', 'require', '必须输入公众号appid！'],
    ];
}