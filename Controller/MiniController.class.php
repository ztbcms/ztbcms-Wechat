<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2019-07-19
 * Time: 09:35.
 */

namespace Wechat\Controller;


use Common\Controller\AdminBase;

class MiniController extends AdminBase
{
    function users()
    {
        $this->display();
    }
}