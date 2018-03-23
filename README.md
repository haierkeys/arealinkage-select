# arealinkage-select
中国行政区省市区三级联动,JS &amp; PHP调用 以及最新数据采集  China's administrative provinces, three-level linkage 


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
