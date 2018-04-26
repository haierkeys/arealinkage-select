<?php

/*
+----------------------------------------------
| Item Name: arealinkage-select v2.0
| Item Description:

+----------------------------------------------
| Github: https://github.com/haierspi/arealinkage-select
| Author: Haierspi(PHP'WORLD) ...
+----------------------------------------------
| 国家统计局行政区划分数据发布源: http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/
| 省市县镇乡
+----------------------------------------------
 */

if (PHP_SAPI !== 'cli') {
    exit('NO CLI');
}
chdir(dirname(__FILE__));

class ArticleCollector
{
    public $header;
    public $message;
    public $ch;

    public function startcurl()
    {
        $this->ch = curl_init();
    }

    public function httpmessagefetch($href, $post = array(), $header = null)
    {
        $this->startcurl();

        if (isset($this->ch)) {
            $this->startcurl();
        }

        curl_setopt($this->ch, CURLOPT_URL, $href);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_LOW_SPEED_TIME, '1');
        curl_setopt($this->ch, CURLOPT_LOW_SPEED_LIMIT, '1');

        $data = curl_exec($this->ch);
        $errno = curl_errno($this->ch);

        while ($errno) { //检查$targe是否存在
            echo "sleep 2\r\n";
            usleep(500000); //阻塞1s
            $data = curl_exec($this->ch);
            $errno = curl_errno($this->ch);
        }

        if ($data) {
            return $data;
        } else {
            return false;
        }

    }

    public function ext($filename)
    {
        $stuff = pathinfo($filename);
        return $stuff['extension'];
    }

}

$ArticleCollector = new ArticleCollector();

$baseurl = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2016/';
$baseHomeurl = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2016/index.html';

$data = $ArticleCollector->httpmessagefetch($baseHomeurl);

$data = iconv('gb2312', "UTF-8//IGNORE", $data);

preg_match_all("/<a href='(\d+)\.html'>([\x{4e00}-\x{9fa5}]+)<br\/><\/a><\/td>/ius", $data, $match);

$addressdata = [];
$addressdata['0'] = [];
foreach ($match[1] as $key => $value) {
    $addressdata[0][$value] = $match[2][$key];
    getaddress($value . '.html', $value, dirname($baseHomeurl) . '/', $match[2][$key]);
}

function getaddress($url, $wordkey, $thisbaseurl = '', $thisaddressname = '', $level = 1)
{

    global $ArticleCollector, $baseurl, $addressdata;
    $data = $ArticleCollector->httpmessagefetch($thisbaseurl . $url);
    $data = iconv('gb2312', "UTF-8//IGNORE", $data);

    preg_match('/<TD width="1%" height="200" vAlign=top>(.*)<\/table>/iUs', $data, $match);

    preg_match_all("/<a href='([\d\/]+)\.html'>([\x{4e00}-\x{9fa5}]+)<\/a>/ius", $data, $match);

    foreach ($match[1] as $key => $value) {
        $adress_array = explode('/', $value);
        $key2 = array_pop($adress_array);

        $wordkey2 = str_replace(',', '', $wordkey);

        $key2 = str_replace($wordkey2, '', $key2);

        $addressname = str_replace('办事处', '', $match[2][$key]);
        $addressdata[$wordkey][$key2] = $addressname;
        if ($level < 3) {
            getaddress($value . '.html', $wordkey . ',' . $key2, dirname($thisbaseurl . $url) . '/', $thisaddressname . ',' . $addressname, $level + 1);
        }

        echo $thisaddressname . ',' . $addressname . " \r\n";
    }

}

$jsondata = json_encode($addressdata, JSON_UNESCAPED_UNICODE);

$fp = fopen('data.js', "w");
fwrite($fp, "var data = $jsondata");
fclose($fp);

$fp = fopen('data.json', "w");
fwrite($fp, $jsondata);
fclose($fp);

echo "OK";
