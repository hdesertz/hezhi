<?php
namespace App\Http\Controllers\Api;

class DuibaController extends Controller
{
//AppKey： Q8DiDHZ1CJdeEU9GYc1TPAvMRLf
//AppSecret为：3jpvKB3jLDESwtibKSmtoTbYx6DU
    const APPKEY = 'GWJa19ggBLfrdhPKAig4SS4T9PZ';
    const APPSECRET = '4U3XGWGT8DtjBgtQHuvdyyfXA8M1';
    /*
    *  md5签名，$array中务必包含 appSecret
    */
    function index(){
        $url = $this->buildCreditAutoLoginRequest(self::APPKEY,self::APPSECRET,'123',10,'www.baidu.com');
        return $url;
    }
    function sign($array){
        ksort($array);
        $string="";
        while (list($key, $val) = each($array)){
            $string = $string . $val ;
        }
        return md5($string);
    }
    /*
    *  签名验证,通过签名验证的才能认为是合法的请求
    */
    function signVerify($appSecret,$array){
        $newarray=array();
        $newarray["appSecret"]=$appSecret;
        reset($array);
        while(list($key,$val) = each($array)){
            if($key != "sign" ){
                $newarray[$key]=$val;
            }

        }
        $sign=$this->sign($newarray);
        if($sign == $array["sign"]){
            return true;
        }
        return false;
    }
    /*
    *构建参数请求的URL
    */
    function AssembleUrl($url, $array)
    {
        unset($array['appSecret']);
        foreach ($array as $key=>$value) {
            $url=$url.$key."=".urlencode($value)."&";
        }
        return $url;
    }

    /*
    *  生成自动登录地址
    *  通过此方法生成的地址，可以让用户免登录，进入积分兑换商城
    */
    function buildCreditAutoLoginRequest($appKey,$appSecret,$uid,$credits){
        $url = "http://www.duiba.com.cn/autoLogin/autologin?";
        $timestamp=time()*1000 . "";
        $array=array("uid"=>$uid,"credits"=>$credits,"appSecret"=>$appSecret,"appKey"=>$appKey,"timestamp"=>$timestamp);
        $sign=$this->sign($array);
        $array['sign']=$sign;
        $url=$this->AssembleUrl($url,$array);
        return $url;
    }

    /*
    *  生成直达商城内部页面的免登录地址
    *  通过此方法生成的免登陆地址，可以通过redirect参数，跳转到积分商城任意页面
    */
    function buildRedirectAutoLoginRequest($appKey,$appSecret,$uid,$credits,$redirect){
        $url = "http://www.duiba.com.cn/autoLogin/autologin?";
        $timestamp=time()*1000 . "";
        $array=array("uid"=>$uid,"credits"=>$credits,"appSecret"=>$appSecret,"appKey"=>$appKey,"timestamp"=>$timestamp);
        if($redirect!=null){
            $array['redirect']=$redirect;
        }
        $sign=$this->sign($array);
        $array['sign']=$sign;
        $url=$this->AssembleUrl($url,$array);
        return $url;
    }



    /*
    *  生成订单查询请求地址
    *  orderNum 和 bizId 二选一，不填的项目请使用空字符串
    */
    function buildCreditOrderStatusRequest($appKey,$appSecret,$orderNum,$bizId){
        $url="http://www.duiba.com.cn/status/orderStatus?";
        $timestamp=time()*1000 . "";
        $array=array("orderNum"=>$orderNum,"bizId"=>$bizId,"appKey"=>$appKey,"appSecret"=>$appSecret,"timestamp"=>$timestamp);
        $sign=$this->sign($array);
        $url=$url . "orderNum=" . $orderNum . "&bizId=" . $bizId . "&appKey=" . $appKey . "&timestamp=" . $timestamp . "&sign=" . $sign ;
        return $url;
    }
    /*
    *  兑换订单审核请求
    *  有些兑换请求可能需要进行审核，开发者可以通过此API接口来进行批量审核，也可以通过兑吧后台界面来进行审核处理
    */
    function buildCreditAuditRequest($appKey,$appSecret,$passOrderNums,$rejectOrderNums){
        $url="http://www.duiba.com.cn/audit/apiAudit?";
        $timestamp=time()*1000 . "";
        $array=array("appKey"=>$appKey,"appSecret"=>$appSecret,"timestamp"=>$timestamp);
        if($passOrderNums !=null && !empty($passOrderNums)){
            $string=null;
            while(list($key,$val)=each($passOrderNums)){
                if($string == null){
                    $string=$val;
                }else{
                    $string= $string . "," . $val;
                }
            }
            $array["passOrderNums"]=$string;
        }
        if($rejectOrderNums !=null && !empty($rejectOrderNums)){
            $string=null;
            while(list($key,$val)=each($rejectOrderNums)){
                if($string == null){
                    $string=$val;
                }else{
                    $string= $string . "," . $val;
                }
            }
            $array["rejectOrderNums"]=$string;
        }
        $sign = $this->sign($array);
        $url=$url . "appKey=".$appKey."&passOrderNums=".$array["passOrderNums"]."&rejectOrderNums=".$array["rejectOrderNums"]."&sign=".$sign."&timestamp=".$timestamp;
        return $url;
    }


    /*
        *  加积分请求的解析方法
        *  当用点击签到，或者有签到弹层时候，兑吧会发起加积分请求，开发者收到请求后，可以通过此方法进行签名验证与解析，然后返回相应的格式
        *  返回格式为：
        *  成功：{"status":"ok", 'errorMessage':'', 'bizId': '20140730192133033', 'credits': '100'}
        *  失败：{'status': 'fail','errorMessage': '失败原因（显示给用户）','credits': '100'}
        */
    function addCreditsConsume($appKey,$appSecret,$request_array){
        if($request_array["appKey"] != $appKey){
            throw new Exception("appKey not match");
        }
        if($request_array["timestamp"] == null ){
            throw new Exception("timestamp can't be null");
        }
        $verify=$this->signVerify($appSecret,$request_array);
        if(!$verify){
            throw new Exception("sign verify fail");
        }

        $ret=$request_array;
        return $ret;
    }


    /*
        *  虚拟商品充值的解析方法
        *  当用兑换虚拟商品时候，兑吧会发起虚拟商品请求，开发者收到请求后，可以通过此方法进行签名验证与解析，然后返回相应的格式
        *  返回格式为：
        *   成功：   {status:"success",credits:"10", supplierBizId:"no123456"}
        *	处理中： {status:"process ",credits:"10" , supplierBizId:"no123456"}
        *	失败：   {status:"fail ", errorMessage:"签名签证失败", supplierBizId:"no123456"}
        */
    function virtualRecharge($appKey,$appSecret,$request_array){
        if($request_array["appKey"] != $appKey){
            throw new Exception("appKey not match");
        }
        if($request_array["timestamp"] == null ){
            throw new Exception("timestamp can't be null");
        }
        $verify=$this->signVerify($appSecret,$request_array);
        if(!$verify){
            throw new Exception("sign verify fail");
        }

        $ret=$request_array;
        return $ret;
    }
    /*
    *  积分消耗请求的解析方法
    *  当用户进行兑换时，兑吧会发起积分扣除请求，开发者收到请求后，可以通过此方法进行签名验证与解析，然后返回相应的格式
    *  返回格式为：
    *  成功：{"status":"ok", 'errorMessage':'', 'bizId': '20140730192133033', 'credits': '100'}
    *  失败：{'status': 'fail','errorMessage': '失败原因（显示给用户）','credits': '100'}
    */
    //兑吧向开发者发起扣积分请求时，兑吧设置超时时间为30秒，由于开发者服务器响应过慢，或者网络异常等原因，可能会出现超时情况。 针对超时情况，兑吧将该订单标记为失败，
    //并向开发者发出失败通知。 如果开发者已经扣积分成功了，当收到通知时，需要对用户积分进行回滚。
    //因为扣积分请求超时，兑吧并没有收到开发者的订单号，因此在发送失败通知时，不会携带bizId数据。 开发者在接受兑吧通知时，请以兑吧订单号orderNum进行处理，
    //而不要根据bizId进行处理！
//响应示例
//
//status	        yes	string	255	扣积分结果状态，回复ok或者fail （不要使用0和1）
//errorMessage	    no	string	255	出错原因
//bizId	            yes	string	255	开发者的订单号(兑吧会判断该订单号的唯一性，注意区分测试和正式对接时的订单号，以免重复)
//credits	        yes	long	20	用户积分余额
//成功：
//{
//'status': 'ok',
//'errorMessage': '',
//'bizId': '20140730192133033',
//'credits': '100'
//}
//失败：
//{
//    'status': 'fail',
//    'errorMessage': '失败原因（显示给用户）',
//    'credits': '100'
//}
    function parseCreditConsume($appKey,$appSecret,$request_array){
        if($request_array["appKey"] != $appKey){
            throw new Exception("appKey not match");
        }
        if($request_array["timestamp"] == null ){
            throw new Exception("timestamp can't be null");
        }
        $verify=$this->signVerify($appSecret,$request_array);
        if(!$verify){
            throw new Exception("sign verify fail");
        }

        $ret=$request_array;
        return $ret;
    }

    /*
    *  兑换订单的结果通知请求的解析方法
    *  当兑换订单成功时，兑吧会发送请求通知开发者，兑换订单的结果为成功或者失败，如果为失败，开发者需要将积分返还给用户
    */

//该接口由开发者开放给兑吧
//不管兑换行为最终成功还是失败，兑吧都会向开发者发出通知，汇报兑换结果。
//开发者收到通知后，请以兑吧订单号 orderNum 为准进行处理，不要以开发者订单号bizId进行处理。原因详见上一章节的：扣积分超时处理方式。
//如果兑换成功，开发者在系统内标记此订单为成功状态即可。如果兑换失败，开发者需要将该订单标记为失败，并将之前扣积分接口预扣的积分返还给用户。
//无论接受到的结果如何，只要开发者收到该请求，请返回 ok 字符串（不含双引号），否则兑吧将进行重复通知，直到开发者收到或者24小时内最多重复 8 次
    function parseCreditNotify($appKey,$appSecret,$request_array){
        if($request_array["appKey"] != $appKey){
            throw new Exception("appKey not match");
        }
        if($request_array["timestamp"] == null ){
            throw new Exception("timestamp can't be null");
        }
        $verify=$this->signVerify($appSecret,$request_array);
        if(!$verify){
            throw new Exception("sign verify fail");
        }
        $ret=array("success"=>$request_array["success"],"errorMessage"=>$request_array["errorMessage"],"bizId"=>$request_array["bizId"]);
        return $ret;
    }


}
