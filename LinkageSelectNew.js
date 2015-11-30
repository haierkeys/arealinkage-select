

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
	
	function _init() {

		$(settings.province).append('<option value="0">省份/自治区</option>');
		$(settings.city).append('<option value="0">城市/地区</option>');
		$(settings.district).append('<option value="0">区/县</option>');

		console.log(settings.data);

		for(var opt_value in settings.data) {


			var opt_title	= settings.data[opt_value]['name'];
			var selected	= '';
			// if (opt_value == value) {
			// 	selected_index	= index;
			// 	selected		= 'selected="selected"';
			// }
			var option	= $('<option value="' + opt_value + '" ' + selected + '>' + opt_title + '</option>');
			$(settings.province).append(option);
			index++;
		}

	}
	_init();
	
}