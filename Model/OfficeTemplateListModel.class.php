<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-08-07
 * Time: 16:38.
 */

namespace Wechat\Model;


use Think\Model;

class OfficeTemplateListModel extends Model
{
    protected $tableName = 'wechat_office_template_list';
    protected $_validate = [
        ['app_id', 'require', 'appid 必要填写！'],
    ];
}
