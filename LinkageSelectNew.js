

function LinkageSelect(options) {
	$ = jQuery;
	// 默认参数
	var settings = {
		data		: {},
		province	: '',
		city		: '',
		district 	: '',
		provinceset	: '',
		cityset		: '',
		districtset : ''
	}; 
	
	// 自定义参数
	if(options) {  
		jQuery.extend(settings, options); 
	}

	function build(province,city,district){
		var province_options = '<option value="0">省份/自治区</option>';
		var city_options = '<option value="0">城市/地区</option>';
		var district_options = '<option value="0">区/县</option>';
		var opt,opt_title,opt_value,opt_selected;
		for(var key1 in settings.data) {
			opt1 = settings.data[key1];
			opt_title	= opt1['name'];
			opt_value	= opt1['id'];
			opt_selected = province == opt_value?'selected="selected"':''
			province_options += '<option value="' + opt_value + '" ' + opt_selected + '>' + opt_title + '</option>';

			if (province == opt_value) {
				for(var key2 in opt1['child']) {

					opt2 = opt1['child'][key2];
					opt_title	= opt2['name'];
					opt_value	= opt2['id'];
					opt_selected = city == opt_value?'selected="selected"':''
					city_options += '<option value="' + opt_value + '" ' + opt_selected + '>' + opt_title + '</option>';
					if (city == opt_value) {
						for(var key3 in opt2['child']) {
							opt3 = opt2['child'][key3];
							opt_title	= opt3['name'];
							opt_value	= opt3['id'];
							opt_selected = district == opt_value?'selected="selected"':''
							district_options += '<option value="' + opt_value + '" ' + opt_selected + '>' + opt_title + '</option>';
						}
					};
				}
			};
		}
		$(settings.province).html(province_options)
		$(settings.city).html(city_options);
		$(settings.district).html(district_options);
	}
	
	function _init() {
		
		$(settings.province+', '+settings.city).change(function(){
			province = $(settings.province).find("option:selected").val();
			city = $(settings.city).find("option:selected").val();
			build(province,city);
		});
		build(settings.provinceset,settings.cityset,settings.districtset);
	}
	_init();
}