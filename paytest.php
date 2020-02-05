<?php
/**
 *
 * 微信H5支付demo
 *
 */
header('Content-Type: text/html; charset=utf-8'); 
$b = new Wxpay();
$pay = $b->wxpaymoney();
echo $pay;
class Wxpay{
 
    /**
     * @return array
     */
    public function wxpaymoney()
    {  
       $money = 1 ; 
       $userip = $this->getClientIp(); //获得用户设备IP 自己网上百度去  
       $appid = "开发者ID(AppID)";//微信给的  
       $mch_id = "微信支付商户号";//微信官方的  
       $key = "API密钥";//自己设置的微信商家key  
       
       $rand = rand(00000,99999);  
       $out_trade_no = '20170804'.$rand;//平台内部订单号  
       $nonce_str=MD5($out_trade_no);//随机字符串  
	   $attach="支付测试";
       $body = "H5";//内容  
       $total_fee = $money; //金额  
       $spbill_create_ip = $userip; //IP  
       $notify_url = "http://www.ruidi2018.com"; //回调地址  
       $trade_type = 'MWEB';//交易类型 具体看API 里面有详细介绍  
       $scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://www.ruidi2018.com","wap_name":"支付"}}';//场景信息 必要参数  
       $signA ="appid=$appid&attach=$attach&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";  
       $strSignTmp = $signA."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确  
       $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写  
       $post_data = "<xml>  
						<appid>$appid</appid>
						<attach>$attach</attach>
						<body>$body</body>
						<mch_id>$mch_id</mch_id>
						<nonce_str>$nonce_str</nonce_str>
						<notify_url>$notify_url</notify_url>
						<out_trade_no>$out_trade_no</out_trade_no>
						<scene_info>$scene_info</scene_info>
						<spbill_create_ip>$spbill_create_ip</spbill_create_ip>
						<total_fee>$total_fee</total_fee>
						<trade_type>$trade_type</trade_type>
						<sign>$sign</sign>
                   </xml>";//拼接成XML 格式  
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址  
        $dataxml = $this->http_post($url,$post_data); //后台POST微信传参地址  同时取得微信返回的参数    POST 方法我写下面了  
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组  
	$paybutton = '<a href="'.$objectxml['mweb_url'].'">测试微信支付</a>';
        return $paybutton;
    }
     /**
     *curl请求
     */
    public function http_post($url, $data) {
	$curl = curl_init(); // 启动一个CURL会话
	curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	$res = curl_exec($curl);
	curl_close($curl);
	return $res;
    }
    
   
    /**
     * @return array
    */
    private function getClientIp()
    {
        $cip='unknown';
        if ($_SERVER['REMOTE_ADDR']){
            $cip=$_SERVER['REMOTE_ADDR'];
        }elseif (getenv($_SERVER['REMOTE_ADDR'])){
            $cip=getenv($_SERVER['REMOTE_ADDR']);
        }
        return $cip;
    }
}
?>
