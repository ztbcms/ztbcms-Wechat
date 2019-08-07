<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-07
 * Time: 16:38.
 */

namespace Wechat\Model;


use Think\Model;

class MiniTemplateListModel extends Model
{
    protected $tableName = 'wechat_mini_template_list';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}
