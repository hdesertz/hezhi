<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class TestController extends Controller
{

    public function test(){
        echo 'hello,world';
    }
    public function GetKjf(Request $request){
        Log::info('XXX-------------'.print_r($request->input(),true));
//        成功：
/*[2017-12-08 17:49:11] lumen.INFO: XXX-------------Array
(
    [actualPrice] => 0                              此次兑换实际扣除开发者账户费用，单位是份
    [ip] => 101.81.183.6                            IP
    [sign] => 38585a881f45762680f4cdb9bf65fd9d      MD5签名（详见签名规则）
    [description] => 测试专用优惠券                   本次积分消耗的描述(带中文，请用utf-8进行url解码)
    [orderNum] => 51632652145962C0976               兑吧订单号(请记录到数据库中)
    [waitAudit] => false                            是否需要审核(如需在自身系统进行审核处理，请记录下此信息)
    [type] => coupon
        兑换类型：
        alipay(支付宝), qb(Q币), coupon(优惠券), object(实物), phonebill(话费), phoneflow(流量), virtual(虚拟商品), turntable(大转盘),
        singleLottery(单品抽奖)，hdtoolLottery(活动抽奖),htool(新活动抽奖),manualLottery(手动开奖),gameLottery(游戏),ngameLottery(新游戏),
        questionLottery(答题),quizzLottery(测试题),guessLottery(竞猜) 所有类型不区分大小写
    [params] =>
        详情参数，
        不同的类型，返回不同的内容，中间用英文冒号分隔。(支付宝类型带中文，请用utf-8进行解码)
        实物商品：返回收货信息(姓名:手机号:省份:城市:区域:详细地址)、支付宝：返回账号信息(支付宝账号:实名)、话费：返回手机号、QB：返回QQ号
    [uid] => 123                                    用户ID
    [credits] => 1                                  本次扣除积分
    [facePrice] => 1                                兑换商品的市场价值，单位是分，请自行转换单位
    [appKey] => Q8DiDHZ1CJdeEU9GYc1TPAvMRLf
    [timestamp] => 1512726551279                    1970-01-01开始的时间戳，毫秒为单位。
)*/
        return "{
            'status': 'ok',
            'errorMessage': '',
            'bizId': '20140730192133033',
            'credits': '100'
            }";

    }
    public function GetDhjg(Request $request){
        Log::info('YYY-------------'.print_r($request->input(),true));
/*[2017-12-08 17:50:00] lumen.INFO: YYY-------------Array
(
    [uid] => 123
    [success] => false                                                               兑换是否成功，状态是true和false
    [errorMessage] => 扣积分失败，开发者服务器异常。开发者服务器响应内容JSON解析失败         出错原因(带中文，请用utf-8进行解码)
    [bizId] =>                                                                       兑吧订单号
    [sign] => d5899b8091e4c158ef3ed309591dd7e2                                       签名，详见签名规则
    [orderNum] => 51632539948439C0976                                                开发者的订单号
    [appKey] => Q8DiDHZ1CJdeEU9GYc1TPAvMRLf
    [timestamp] => 1512726600084
)*/
        return 'ok';
    }

    public function mdl_callback(){

    }

}

