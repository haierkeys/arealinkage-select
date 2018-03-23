# arealinkage-select

 -基于JQuery 的 中国行政区省市区地区三级联动菜单
- 已经写好了PHP采集页面,可以自动从政府官网采集地区数据
- 地区也可以通过 php来调用



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
