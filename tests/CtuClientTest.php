<?php
require __DIR__ . '/../vendor/autoload.php';

use BPF\CtuRisk\CtuClient;
use BPF\CtuRisk\Model\RiskLevel;


$appId = 'xxx'; // 风控app id
$appSecret = 'xxxx'; // 风控 secret

// 风控策略
$eventRejectStrategy = [RiskLevel::REJECT];

$ctuClient = new CtuClient($appId, $appSecret);

//$ctuClient->connectionRequestTimeout
