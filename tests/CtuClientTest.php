<?php
require __DIR__ . '/../vendor/autoload.php';

use BPF\CtuRisk\CtuClient;
use BPF\CtuRisk\Model\RiskLevel;


$appId = 'xxx';
$appSecret = 'xxxx';


$ctuClient = new CtuClient($appId, $appSecret);

$eventRejectStrategy = [RiskLevel::REJECT];
