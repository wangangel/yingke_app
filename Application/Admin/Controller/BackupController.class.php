<?php

namespace Admin\Controller;
use Admin\Common\AdminController;
use Think\Page;
use Think\Upload;
class BackupController extends AdminController{
    protected $autoCheckFields =false;
    var $database = 'yingke';
    public $content; 
    /*
     * 数据备份列表
     */
    public function backup_list(){
        $database = 'yingke';
        $Model = new \Think\Model();
        $sql="show tables";
        $list = $Model->query($sql); 
       /* for ($i=0; $i < count($list) ; $i++) { 
          echo $list[$i]['tables_in_anjuyi'];
        }
        die();*/
         //为权限加上
        $actionName1["auth_a"]="backup_table";
        $backup_table = $this->checkAuth($actionName1);
        $actionName2["auth_a"]="backup_all";
        $backup_all = $this->checkAuth($actionName2);
        
        $this->assign('backup_table',$backup_table);
        $this->assign('backup_all',$backup_all);
        $this->assign('list',$list);
        $this->display();
        
        
    }
   
    /* 
     * 备份个体
     */
      public function backup_table(){
        $tab = $_GET['table'];
        if (is_array($tab)){
          $tables = $tab;
        }else{
          $tables[] = $tab;
        }
        if ($this->backup($tables)) {
            if (is_array($tab))
                $this->success('数据库备份成功！');
            else
                $this->success('数据库备份成功！',U("admin/backup/backup_list"));
        } else {
            $this->error('数据库备份失败！');
        }
      }
      /**
       * 备份
       */
      public function backup_all(){
       $data =  $_GET['selectAll'];
       $tab = explode(',',$data);
        if (is_array($tab)){
          $tables = $tab;
        }else{
          $tables[] = $tab;
        }
        if ($this->backup($tables)) {
            if (is_array($tab))
                $this->success('数据库备份成功！');
            else
                $this->success('数据库备份成功！',U("admin/backup/backup_list"));
        } else {
            $this->error('数据库备份失败！');
        }
      }

      public function backup($tables) {
        $Model = new \Think\Model();
        if (empty($tables)){
            
            $this->error('没有需要备份的数据表!');
            $this->content = '/* This file is created by MySQLReback ' . date('Y-m-d H:i:s') . ' */';
        }
           
        foreach ($tables as $i => $table) {
            $table = $this->backquote($table);                                  //为表名增加 ``
            $tableRs = $Model->query("SHOW CREATE TABLE {$table}"); 

                //获取当前表的创建语句
            if (!empty($tableRs[0]["create view"])) {
                $this->content .= "\r\n /* 创建视图结构 {$table}  */";
                $this->content .= "\r\n DROP VIEW IF EXISTS {$table};/* MySQLReback Separation */ " . $tableRs[0]["create view"] . ";/* MySQLReback Separation */";
            }
            if (!empty($tableRs[0]["create table"])) {
                $this->content .= "\r\n /* 创建表结构 {$table}  */";
                
                $this->content .= "\r\n DROP TABLE IF EXISTS {$table};/* MySQLReback Separation */ " . $tableRs[0]["create table"] . ";/* MySQLReback Separation */";
                $tableDateRow =  $Model->query("SELECT * FROM {$table}");
                $valuesArr = array();
                $values = '';
                if (false != $tableDateRow) {
                    foreach ($tableDateRow as &$y) {
                        foreach ($y as &$v) {
                           if ($v=='')                                          //纠正empty 为0的时候  返回tree
                                $v = 'null';                                    //为空设为null
                            else
                                $v = "'" . mysql_escape_string($v) . "'";       //非空 加转意符
                        }
                        $valuesArr[] = '(' . implode(',', $y) . ')';
                    }
                }
                $temp = $this->chunkArrayByByte($valuesArr);
                if (is_array($temp)) {
                    foreach ($temp as $v) {
                        $values = implode(',', $v) . ';/* MySQLReback Separation */';
                        if ($values != ';/* MySQLReback Separation */') {
                            $this->content .= "\r\n /* 插入数据 {$table} */";
                            $this->content .= "\r\n INSERT INTO {$table} VALUES {$values}";
                        }
                    }
                }
                //var_dump($this->content);
               
                
            }
        }
        if (!empty($this->content)) {
            $this->setFile();
        }
        return true;
    }
    /**
     * 给字符串添加 ` `
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function backquote($str) {
        return "`{$str}`";
    }
    /* -
     * +------------------------------------------------------------------------
     * * @ 把传过来的数据 按指定长度分割成数组
     * +------------------------------------------------------------------------
     * * @ $array 要分割的数据
     * * @ $byte  要分割的长度
     * +------------------------------------------------------------------------
     * * @ 把数组按指定长度分割,并返回分割后的数组
     * +------------------------------------------------------------------------
     */
    public function chunkArrayByByte($array, $byte = 5120) {
        $i = 0;
        $sum = 0;
        $return = array();
        foreach ($array as $v) {
            $sum += strlen($v);
            if ($sum < $byte) {
                $return[$i][] = $v;
            } elseif ($sum == $byte) {
                $return[++$i][] = $v;
                $sum = 0;
            } else {
                $return[++$i][] = $v;
                $i++;
                $sum = 0;
            }
        }
        return $return;
    }

    /* -
     * +------------------------------------------------------------------------
     * * @ 把数据写入磁盘
     * +------------------------------------------------------------------------
     */
    public function setFile() {
        $recognize = '';
        $recognize = $this->dbName;
        $fileName = $this->trimPath($this->config['path'] . $this->dir_sep . $recognize . '_' . date('YmdHis') . '_' . mt_rand(100000000, 999999999) . '.sql');
        $path = $this->setPath($fileName);
        if ($path !== true) {
            $this->error("无法创建备份目录目录 '$path'");
        }
        if ($this->config['isCompress'] == 0) {
            if (!file_put_contents($fileName, $this->content, LOCK_EX)) {
                $this->error('写入文件失败,请检查磁盘空间或者权限!');
            }
        } else {
            if (function_exists('gzwrite')) {
                $fileName .= '.gz';
                if ($gz = gzopen($fileName, 'wb')) {
                    gzwrite($gz, $this->content);
                    gzclose($gz);
                } else {
                    $this->error('写入文件失败,请检查磁盘空间或者权限!');
                }
            } else {
                $this->error('没有开启gzip扩展!');
            }
        }
       /* if ($this->config['isDownload']) {
            $this->downloadFile($fileName);
        }*/
    }
    public function trimPath($path) {
        return str_replace(array('/', '\\', '//', '\\\\'), $this->dir_sep, $path);
    }
    public function setPath($fileName) {
        $dirs = explode($this->dir_sep, dirname($fileName));
        $tmp = '';
        foreach ($dirs as $dir) {
            $tmp .= $dir . $this->dir_sep;
            if (!file_exists($tmp) && !@mkdir($tmp, 0777))
                return $tmp;
        }
        return true;
    }



}