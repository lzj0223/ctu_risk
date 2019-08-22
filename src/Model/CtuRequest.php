<?php
namespace BPF\CutRisk\Model;
/**
 * Class CtuRequest
 * 顶像风控请求对象
 * @package BPF\CutRisk\Model
 */
class CtuRequest
{
    public $eventCode;             // 事件code
    public $flag;                  // 客户端请求标记,用来标识该次请求
    public $data;                  // 事件参数
}
