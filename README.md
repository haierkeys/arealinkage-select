# arealinkage-select

- 中国行政区 省/市/县区/乡镇 四级地区联动选择, 实现Javascript 动态选择
- 支持直接PHP 拿出地址数据 
- 支持PHP CLI 采集最新的数据 采集方法 命令行 php -f collectdata.php




js调用方法
```js
jQuery(function() {
	var options	= {
		data	: data,
		province : '.userinfo_province',
		city : '.userinfo_city',
		district : '.userinfo_district',
		provinceset : 120000,
		cityset : 120000,
		districtset : 120103
	}
	var LSelect = new LinkageSelect(options);
})
---
