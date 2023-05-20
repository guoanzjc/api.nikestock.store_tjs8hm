<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 服装
 *
 * @icon fa fa-circle-o
 */
class Fuzhuang extends Backend
{

    /**
     * Fuzhuang模型对象
     * @var \app\admin\model\Fuzhuang
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Fuzhuang;

    }

/**
     * 批量导入数据
     */
     public function import(){
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS .$file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
       





// thinkphp 执行SQL 语句方式
Db::execute("truncate table fa_fuzhuang");
$rs = Db::execute("LOAD DATA LOCAL INFILE '{$filePath}' INTO TABLE fa_fuzhuang character set utf8  
fields terminated by ','          
lines terminated by '\r\n'       
ignore 1 lines(date,store,sku,lianjie,size,category,des,yuanjia,xianjia,shuliang)");
$this->assign('jumpUrl', "javascript:window.parent.location.reload();");
$this->success("导入成功");
     }
    


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


}
