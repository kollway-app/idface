<?php
namespace Kollway\Idface;

class IdfaceApi {

    private $kexin_client;

    public function __construct() {
        $config = array(
            'kexin_akey'	=>	env('IDFACE_AKEY'),		//client_id
            'kexin_skey'	=>	env('IDFACE_SKEY'),			//password
            'kexin_api_url'	=>	env('IDFACE_API_URL', 'https://api.kexin.net:18443/identity/format/json/')		//请求URL 可以在URL中指定返回json 或 xml
        );
        $this->kexin_client = new KexinClient($config);
    }

    public function requestVerify($id_number, $name, $img_arr, $ip='') {
        if(!$ip) {
            $ip = $this->get_host_ip();
        }
        $result = $this->kexin_client->identity_verification($id_number, $name, $img_arr, $ip);
        return $result;
    }

    public function requestVerifyForImageCode($id_number, $name, $image_code, $ip='') {
        if(!$ip) {
            $ip = $this->get_host_ip();
        }
        $data = array(
            "number" => $id_number,
            "name" => $name,
            "feature_code" => array($image_code),
            "user_ip"=> $ip
        );
        $result = $this->kexin_client->post_verification($data);
        return $result;
    }

    private function get_host_ip(){

        $cip = (isset($_SERVER['HTTP_CLIENT_IP']) AND $_SERVER['HTTP_CLIENT_IP'] != "") ? $_SERVER['HTTP_CLIENT_IP'] : FALSE;
        $rip = (isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'] != "") ? $_SERVER['REMOTE_ADDR'] : FALSE;
        $fip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND $_SERVER['HTTP_X_FORWARDED_FOR'] != "") ? $_SERVER['HTTP_X_FORWARDED_FOR'] : FALSE;

        if ($cip && $rip)	$IP = $cip;
        elseif ($rip)		$IP = $rip;
        elseif ($cip)		$IP = $cip;
        elseif ($fip)		$IP = $fip;

        if (strpos($IP, ',') !== FALSE)
        {
            $x = explode(',', $IP);
            $IP = end($x);
        }

        if ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $IP))
        {
            $IP = '0.0.0.0';
        }

        unset($cip);
        unset($rip);
        unset($fip);

        return $IP;
    }
}

?>