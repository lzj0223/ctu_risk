<?php
require __DIR__ . '/../vendor/autoload.php';

use BPF\CtuRisk\CtuClient;
use BPF\CtuRisk\Model\RiskLevel;
use BPF\CtuRisk\Model\CtuRequest;

$appId = 'xxx'; // 风控app id
$appSecret = 'xxxx'; // 风控 secret

// 风控策略
$eventRejectStrategy = [RiskLevel::REJECT];

$ctuClient = new CtuClient($appId, $appSecret);

$ctuRequest = new CtuRequest();
$ctuRequest->flag = 'flag';     // flag与风控约定
$ctuRequest->data = [           // 具体风控参数

];
$ctuRequest->eventCode = '';   // 事件code，风控定义

$risk = $ctuClient->checkRisk($ctuRequest, $eventRejectStrategy, true);

$ctuResponse = $ctuClient->getCtuResponse();

echo '接口返回状态：', $ctuResponse->responseStatusMsg, PHP_EOL;

// 如果有风险
if ($risk) {
    echo '有风险，分控规则:', $ctuResponse->result->hitPolicyName, PHP_EOL;
} else {
    echo '无风险', PHP_EOL;
}