<?php

namespace Kollway\Idface;

/**
 * 可信网络身份验证系统客户端
 */
class KexinClient
{
  private $kexin_akey;
  private $kexin_skey;
  private $kexin_api_url;
  
  function __construct( $config )
  {
	$this->kexin_akey	=	$config['kexin_akey'];
	$this->kexin_skey	=	$config['kexin_skey'];
	$this->kexin_api_url	=	$config['kexin_api_url'];
  }
  /*------------------------------------------------------ */
  //-- 提交查询验证
  //-- $number  身份证号
  //-- $name    姓名
  //-- $img_arr 图片二进制内容数组
  //-- by col
  /*------------------------------------------------------ */
  function identity_verification($number, $name, $img_arr, $user_ip='')
  {
    $d = array(
      "number" => $number,
      "name" => $name,
	  "user_ip"=>$user_ip
      );
    foreach ($img_arr as &$img)
    {
      $img = $this -> get_feature_code($img);
    }
    $d["feature_code"] = $img_arr;
    //$d["feature_code"]  =  array("1", "2", "3");
    return $this -> post_verification($d);
  }
  /*------------------------------------------------------ */
  //-- 图片特殊处理, 提取特征码
  //-- by col
  /*------------------------------------------------------ */
  function get_feature_code($code_2)
  {
    return base64_encode($code_2);
  }
  /*------------------------------------------------------ */
  //-- 提交动作
  //-- by col
  /*------------------------------------------------------ */
  function post_verification($data)
  {

    $ch=curl_init($this -> kexin_api_url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch,CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch,CURLOPT_USERPWD, $this -> kexin_akey .":". $this -> kexin_skey);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	//curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $content	=	curl_exec($ch);
	$error 		= 	curl_error($ch);
	curl_close($ch);
	return empty($error) ? $content : $error;

  }
}


?>
