<?php
namespace BPF\CtuRisk\Util;
/**
 * Class SignUtil
 * 顶像风控签名类
 * @package BPF\CtuRisk\Util
 */
class SignUtil
{

    private static $EVENT_CODE = "eventCode";
    private static $FLAG = "flag";

    public static function sign($appSecret, $ctuRequest)
    {
        $sortedParams = self::sortedParams($ctuRequest);
        return md5($appSecret . $sortedParams . $appSecret);
    }

    private static function sortedParams($ctuRequest)
    {

        $eventCode = $ctuRequest->eventCode;
        $flag = $ctuRequest->flag;
        $data = $ctuRequest->data;

        ksort($data);

        $paramStr = self::$EVENT_CODE . $eventCode . self::$FLAG . $flag;
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $paramStr .= $key . "null";
            } else {
                $paramStr .= $key . $value;
            }
        }
        return $paramStr;
    }

}