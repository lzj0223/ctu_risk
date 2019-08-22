<?php
namespace BPF\CtuRisk;

use BPF\CtuRisk\Model\RiskLevel;
use BPF\CtuRisk\Util\HttpUtil;
use BPF\CtuRisk\Model\CtuResponse;
use BPF\CtuRisk\Model\CtuRequest;
use BPF\CtuRisk\Util\SignUtil;

/**
 * Class CtuClient
 * 顶像风控sdk
 * @package BPF\CutRisk
 */
class CtuClient
{
    const UTF8_ENCODE = "UTF-8";
    const VERSION = 1;     //client版本号  从1开始

    /**
     * 默认风险防控服务URL
     * @var string
     */
    const DEFAULT_API_URL = 'http://sec.dingxiang-inc.com/ctu/event.do';

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

    private $url;           // 风险防控服务URL
    private $appId;         // 颁发的公钥,可公开
    private $appSecret;     // 颁发的秘钥,严禁公开,请保管好,千万不要泄露!

    /**
     * CtuClient constructor.
     * @param $appId
     * @param $appSecret
     * @param $url
     */
    public function __construct($appId, $appSecret, $url = '')
    {
        $this->url = $url ?: self::DEFAULT_API_URL;
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
     * 获取风控返回解析后的结果
     * @return CtuResponse
     */
    public function getCtuResponse() {
        return $this->ctuResponse;
    }

    /**
     * 请求风控系统接口 返回是否有风险 true 为有风险
     * @param CtuRequest $ctuRequest 请求的相关参数
     * @param string|array $rejectStrategy 风控策略，什么情况下返回有风险
     * @param bool $strict 是否严格模式，严格模式下风控接口请求失败也被认为有风险
     * @param int $timeout 超时时间，单位秒
     * @return bool|string
     */
    public function checkRisk(CtuRequest $ctuRequest, $rejectStrategy = RiskLevel::REJECT, $strict = false, $timeout = 2)
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
        if (!$this->ctuResponseString) {
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
     * @param string|array $rejectStrategy
     * @param bool $strict
     * @return bool
     */
    public function hasRisk($rejectStrategy, $strict = false)
    {
        empty($rejectStrategy) && $rejectStrategy = [];
        if (!is_array($rejectStrategy)) {
            $rejectStrategy = [$rejectStrategy];
        }
        return $this->ctuResponse->result->hasRisk($rejectStrategy, $strict);
    }
}



























