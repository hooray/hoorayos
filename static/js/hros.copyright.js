/*
**  版权信息
*/
HROS.copyright = (function(){
	return {
		/*
		**	初始化
		*/
		init : function(){
			$('#copyright .close').on('click', function(){
				HROS.copyright.hide();
			});
		},
		show : function(){
			var mask = HROS.maskBox.copyright();
			mask.show();
			$('#copyright').show();
		},
		hide : function(){
			var mask = HROS.maskBox.copyright();
			mask.hide();
			$('#copyright').hide();
		}
	}
})();