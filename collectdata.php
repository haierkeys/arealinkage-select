<?php

/*
+----------------------------------------------
| Item Name: arealinkage-select
| Item Description: 中国行政区省市区三级联动,JS & PHP调用 以及最新数据采集 China's administrative provinces, three-level linkage — Edit
+----------------------------------------------
| Github: https://github.com/haierspi/arealinkage-select
| Author: Haierspi(PHP'WORLD) ...
+----------------------------------------------
*/


class ArticleCollector{
	public $header;
	public $message;

	public function httpmessagefetch($href,$post = array(),$header = NULL){


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$href);
	    if (!is_null($header) ) {
	    	curl_setopt($ch, CURLOPT_HEADER, 1);
	    }
	   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		//curl_setopt ($ch, CURLOPT_URL, "http://www.phpfensi.com"); 
		//curl_setopt ($ch, CURLOPT_REFERER, "http://www.phpfensi.com/"); 

	    if ($post) {
	    	curl_setopt($ch, CURLOPT_POST, 1 );
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
	    }
	  
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if (!is_null($header) ) {
			$header = substr($data, 0,$status['header_size']);
			$data = substr($data, $status['header_size']);
		}

		$this->header = $header;
		$this->message = $data;

	    if ($data) return $data;
	    else return false;   
	}

	public function httpfilefetch($href,$file){

		if (!$href || !$file) {
			return false;
		}

		
		$parseurl = parse_url($href);
		$REFERER = $parseurl['scheme'].'://'.$parseurl['host'];


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$href);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
		curl_setopt($ch, CURLOPT_REFERER, $REFERER);
		curl_setopt($ch, CURLOPT_HEADER ,0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,600);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		$data = curl_exec($ch);
		curl_close($ch);


		$fp = fopen ($file, "w");
		fwrite($fp, $data);
		fclose ($fp);
		
		return TRUE;

	}

	public function localimg($href){
		$ext = $this->ext($href);
		$file = 'img/'.md5($href).'.'.$ext;
		$this->httpfilefetch($href,$file);
		return $file;
	}

	public function ext($filename) {
		$stuff = pathinfo ( $filename );
		return $stuff ['extension'];
	}



}

$ArticleCollector = new ArticleCollector();

$data = $ArticleCollector->httpmessagefetch('http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/201504/t20150415_712722.html');

$match = array();
preg_match('/<div class="TRS_PreAppend" style="overflow-x: hidden; word-break: break-all">(.*)<\/div>/iUs', $data, $match);
$pregcont  = strip_tags($match[1] );

$pregcont  = $match[1];
$pregcont = preg_replace('/class="(.*)"\s*|style="(.*)"\s*/iUs', '', $pregcont);



echo '<pre>';
var_dump( $pregcont );
echo '</pre>';
exit;

?>
