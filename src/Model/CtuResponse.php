<?php
namespace BPF\CtuRisk\Model;
/**
 * Class CtuResponse
 * 顶像风控返回结果类
 * @package BPF\CtuRisk\Model
 */
class CtuResponse
{
    private $responseStatusMsgConfig = [
        'SUCCESS' => '成功',
        'INVALID_REQUEST_PARAMS' => '请求不合法,缺少必须参数',
        'INVALID_REQUEST_BODY' => '请求不合法,请求body为空',
        'INVALID_REQUEST_NO_EVENT_DATA' => '请求不合法,请求事件的数据为空',
        'INVALID_REQUEST_SIGN' => '请求签名错误',
        'INVALID_APP_ID' => '不合法的appId',
        'INVALID_EVENT_CODE' => '不合法的事件',
        'INVALID_APP_EVENT_RELATION' => '应用和事件的绑定关系错误',
        'EVENT_GRAY_SCALE' => '事件有灰度控制,非灰度请求',
        'NO_POLICY_FOUND' => '没有找到防控策略',
        'POLICY_HAS_ERROR' => '防控策略配置有错误',
        'NOT_SUPPORTED_POLICY_OPERATOR' => '不支持防控策略里的操作符',
        'QPS_EXCEEDING_MAXIMUM_THRESHOLD' => 'QPS超过最大阀值',
        'SERVICE_INTERNAL_ERROR' => '服务器内部错误',
    ];

    /**
     * 服务端返回的请求标识码，供服务端排查问题
     * @var string
     */
    public $uuid;

    /**
     * 状态码
     * @var string
     */
    public $status;

    /**
     * 防控结果
     * @var CtuResult
     */
    public $result;

    /**
     * $status 对应的提示信息
     * @var string
     */
    public $responseStatusMsg = '';

    /**
     * CtuResponse constructor.
     * @param $response
     */
    public function __construct($response)
    {
        if (is_string($response)) {
            $response = json_decode($response, true);
        }

        $this->result = new CtuResult($response['result']);
        $this->status = $response['status'];
        $this->uuid = $response['uuid'];
        $this->responseStatusMsg = isset($this->responseStatusMsgConfig[$this->status]) ?: '';
    }
}