<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-07
 * Time: 17:08.
 */

namespace Wechat\Model;


use Think\Model;

class MiniFormIdModel extends Model
{
    const USED_YES = 1;
    const USED_NO = 0;
    protected $tableName = 'wechat_mini_form_id';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}