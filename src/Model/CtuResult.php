<?php
namespace BPF\CutRisk\Model;
/**
 * Class CtuResult
 * @package BPF\CutRisk\Model
 */
class CtuResult
{
    /**
     * 所有风险类型名称
     * @var array
     */
    private $riskTypeNameConfig = [
        'UNKNOWN' => '未定义',
        'RUBBISH_REGISTRATION' => '垃圾注册',
        'ACCOUNT_STOLEN' => '账号盗用',
        'MACHINE_CRAWLING' => '机器爬取',
        'BATCH_LOGON' => '批量登陆',
        'MALICIOUS_GRAB' => '黄牛抢单',
    ];

    public $riskLevel;           // 请求的风险级别
    public $riskType;            // 风险类型
    public $riskTypeName;        // 风险类型名称
    public $hitPolicyCode;       // 命中策略code
    public $hitPolicyName;       // 命中策略标题
    public $hitRules;            // 命中规则
    public $suggestPolicies;     // 建议防控策略
    public $suggestion;          // 命中策略处置建议
    public $flag;                // 客户端请求带上来的标记
    public $extraInfo;           // 附加信息

    /**
     * CtuResult constructor.
     * @param $response
     */
    public function __construct($response)
    {
        if ($response && is_string($response)) {
            $response = json_decode($response, true);
        }

        if ($response && is_array($response)) {
            foreach ($response as $key => $value) {
                $this->$key = $value;
            }
        }

        $riskType = $this->riskType ? strtoupper($this->riskType) : 'UNKNOWN';
        $this->riskTypeName = isset($this->riskTypeNameConfig[$riskType]) ? $this->riskTypeNameConfig[$riskType] : $this->riskTypeNameConfig['UNKNOWN'] ;
    }

    /**
     * 返回true， 表示有风险
     * @param array $rejectStrategy
     * @param bool $strict
     * @return bool
     */
    public function hasRisk(array $rejectStrategy, $strict = false)
    {
        if (!$this->riskLevel)
        {
            return $strict;
        }

        if (empty($rejectStrategy))
        {
            $rejectStrategy = [RiskLevel::REJECT, RiskLevel::REVIEW];
        }

        return in_array($this->riskLevel, $rejectStrategy);
    }
}