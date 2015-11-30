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
$pregcont = str_replace('</p>', "</p>\r\n", $pregcont);
$pregcont = preg_replace('/\s*class="(.*)"\s*|\s*style="(.*)"\s*|\s*lang="(.*)"\s*/iUs', '', $pregcont);
$pregcont = preg_replace('/<p><span>([0-9]*)<span>[^<]*<\/span><\/span><span>(　*)([^<]*)<\/span><\/p>/is', '\1\2\3', $pregcont);
$pregcont = strip_tags( $pregcont);
$pregcont = str_replace('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ', "　　　", $pregcont);
$pregcont = str_replace('　', "%", $pregcont);
$pregcont = explode("\r\n", $pregcont );


$city = $city1 = $city2 = $city3 = array();

$i = $x = $y = 0;
foreach ($pregcont as $key => $value) {
	$match = array();
	preg_match('/([0-9]*)(%*)(.*)/is', $value,$match);

	if (in_array($match[1], array('710000','810000','820000'))) {
		continue;
	}

	$ary = array(
		'id'=>$match[1],
		'name'=>$match[3]
	);


	if ( strlen($match[2]) == 1) {
		if (in_array($match[1], array('230000','150000','110000','120000','500000','310000'))) {
			$ary['name'] = substr($ary['name'], 0,9);
		}else{
			$ary['name'] = substr($ary['name'], 0,6);
		}
		
		$id = $match[1];
		$k1 = $i;
		$x = $y = 0;
		$city[$k1] = $ary;
		$city1[$match[1]] = $ary;
		$i++;
	}

	if (in_array($id, array('110000','120000','500000','310000'))) {
		if (!isset($city[$k1]['child'])) {
			$city[$k1]['child'][0] = $city[$k1];
			$city[$k1]['name'] = str_replace('市', '', $city[$k1]['name']);
		}
		if (strlen($match[2]) == 3) {
			$k3 = $y;
			$city[$k1]['child'][0]['child'][$k3] = $ary;
			$city3[$match[1]] =  $ary;
			$y++;
		}
		continue;
	}



	if ( strlen($match[2]) == 2) {
		$k2 = $x;
		$y = 0;
		$city[$k1]['child'][$k2] = $ary;
		$city2[$match[1]] = $ary;
		$x++;
	}
	if ( strlen($match[2]) == 3) {
		if ($match[3] == '市辖区') {
			continue;
		}
		$k3 = $y;
		$city[$k1]['child'][$k2]['child'][$k3] = $ary;
		$city3[$match[1]] =  $ary;
		$y++;
	}
	
}


$data = json_encode($city);
$datajs = "var data = $data";


$fp = fopen ('data.js', "w");
fwrite($fp, $datajs);
fclose ($fp);


echo "OK";
?>
