<?php
namespace BPF\CutRisk\Util;
/**
 * Class HttpUtil
 * 顶像风http请求类
 * @package BPF\CutRisk\Util
 */
class HttpUtil
{
    /**
     * 错误信息
     * @var string
     */
    private $errMsg = '';

    /**
     * 请求超时时间
     * @var int
     */
    private $timeout = 1;

    /**
     * HttpUtil constructor.
     * @param int $timeout
     * @param bool $debug
     */
    public function __construct($timeout = 2)
    {
        $this->timeout = $timeout;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * 网络请求
     * @param $url
     * @param $data
     * @param int $timeout
     * @return bool|string
     */
    public function doPostRequest($url, $data, $timeout = 2)
    {
        $params = array(
            'http' => array(
                'method' => 'POST',
                'content' => $data,
                'header' => 'Content-type:text/plain; charset=utf-8',
                'timeout' => $timeout
            )
        );

        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            $this->errMsg = 'server connect failed!';
            $this->close($fp);
            return false;
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            $this->errMsg = 'get response failed!';
            $this->close($fp);
            return false;
        }
        $this->close($fp);
        return $response;
    }

    /**
     * 关闭请求
     * @param $fp
     */
    public function close($fp)
    {
        try {
            if ($fp != null) {
                fclose($fp);
            }
        } catch (\Exception $e) {
            $this->errMsg = "close error:" . $e->getMessage();
        }
    }

}