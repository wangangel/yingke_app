<?php
/**
 * 极光推送
 * @author 晨曦
 * @Email jakehu1991@163.com
 * @Website http://www.jakehu.me/
 * @version 20130706
 */

      include('Jpush.php');
	  $n_title   =  '1231232';
	  $n_content =  '12312321231232123123212312321231232';	
      $appkeys='123123212312321231232';
      $masterSecret='1231232123123212312321231232';
      $sendno = 4;
      $receiver_value = '';			
	  $platform = 'android,ios' ;
	  $msg_content = json_encode(array('n_builder_id'=>0, 'n_title'=>$n_title, 'n_content'=>$n_content));        
	  $obj = new Jpush($masterSecret,$appkeys);			 
	  $res = $obj->send($sendno, 4, $receiver_value, 1, $msg_content, $platform);				
	  print_r($res);
	  exit();		
?>