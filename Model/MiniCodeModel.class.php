<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 10:39.
 */

namespace Wechat\Model;


use Think\Model;

class MiniCodeModel extends Model
{
    const CODE_TYPE_LIMIT = "limit";
    const CODE_TYPE_UNLIMIT = "unlimit";
    protected $tableName = 'wechat_mini_code';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}