<?php
namespace BPF\CutRisk;

use BPF\CutRisk\Model\RiskLevel;
use BPF\CutRisk\Util\HttpUtil;
use BPF\CutRisk\Model\CtuResponse;
use BPF\CutRisk\Model\CtuRequest;
use BPF\CutRisk\Util\SignUtil;

/**
 * Class CtuClient
 * 顶像风控sdk
 * @package BPF\CutRisk
 */
class CtuClient
{
    /**
     * 当前请求参数
     * @var CtuRequest
     */
    private $ctuRequest;

    /**
     * @var string
     */
    private $ctuResponseString;

    /**
     * @var CtuResponse
     */
    private $ctuResponse;

    public $url;           // 风险防控服务URL
    public $appId;         // 颁发的公钥,可公开
    public $appSecret;     // 颁发的秘钥,严禁公开,请保管好,千万不要泄露!

    public $connectTimeout = 3000;
    public $connectionRequestTimeout = 2000;
    public $socketTimeout = 5000;

    const UTF8_ENCODE = "UTF-8";
    const VERSION = 1;     //client版本号  从1开始


    /**
     * CtuClient constructor.
     * @param $url
     * @param $appId
     * @param $appSecret
     */
    public function __construct($url, $appId, $appSecret)
    {
        $this->url = $url;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * 获取请求参数
     * @return CtuRequest $ctuRequest
     */
    public function getCtuRequest() {
       return $this->ctuRequest;
    }

    /**
     * 获取风控原始返回结果
     * @return string
     */
    public function getCtuResponseString(){
        return $this->ctuResponseString;
    }

    /**
     * 请求风控系统接口 返回是否有风险 true 为有风险
     * @param CtuRequest $ctuRequest
     * @param $rejectStrategy
     * @param $strict
     * @param $timeout
     * @return bool|string
     */
    public function checkRisk(CtuRequest $ctuRequest, $rejectStrategy, $strict = false, $timeout = 2)
    {
        // 重置相关属性，以免获取到上一次的请求内容
        $this->ctuResponse = new CtuResponse('');
        $this->ctuResponseString = '';
        $this->ctuRequest = $ctuRequest;

        // 计算签名
        $sign = SignUtil::sign($this->appSecret, $ctuRequest);

        // 拼接请求URL
        $params['appKey'] = $this->appId;
        $params['sign'] = $sign;
        $params['version'] = self::VERSION;
        $requestUrl = $this->url . '?' . http_build_query($params);

        $reqJsonString = json_encode($ctuRequest, JSON_UNESCAPED_UNICODE);
        $postData = base64_encode($reqJsonString);

        //调用风控引擎
        $httpUtil = new HttpUtil($timeout);
        $this->ctuResponseString = $httpUtil->doPostRequest($requestUrl, $postData);
        if ( $this->ctuResponseString === false) {
            $result = [
                'uuid' => '',
                'status' => '',
                'result' => [
                    'riskLevel' => $strict ? RiskLevel::REJECT : RiskLevel::ACCEPT,
                    'msg' => $httpUtil->getErrMsg()
                ]
            ];
            $this->ctuResponseString = json_encode($result, JSON_FORCE_OBJECT);
            $this->ctuResponse = new CtuResponse($result);
        }

        // 解析结果
        $this->ctuResponse = new CtuResponse($this->ctuResponseString);
        return $this->hasRisk($rejectStrategy, $strict);
    }

    /**
     * 请求风控系统接口 返回是否有风险 true 为有风险
     * @param $rejectStrategy
     * @param $strict
     * @return bool
     */
    public function hasRisk($rejectStrategy, $strict)
    {
        return $this->ctuResponse->result->hasRisk($rejectStrategy, $strict);
    }
}



























