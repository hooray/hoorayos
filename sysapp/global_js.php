<script src="//cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-select/1.12.3/js/bootstrap-select.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-select/1.12.3/js/i18n/defaults-zh_CN.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap-slider/9.8.1/bootstrap-slider.min.js"></script>
<script src="//cdn.bootcss.com/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="//cdn.bootcss.com/artDialog/7.0.0/dialog-plus.js"></script>
<script src="../../static/plugins/HoorayLibs/hooraylibs.js"></script>
<script src="../../static/plugins/Validform_v5.3.2/Validform_v5.3.2_min.js"></script>
<script src="../../static/plugins/Waves-0.7.5/dist/waves.min.js"></script>
<script>
$(function(){
	//toolTip
	$('[data-toggle="tooltip"]').tooltip();
	//下拉列表
	$('[data-plugin="bootstrapSelect"]').selectpicker();
	//开关滑块
	$('[data-plugin="bootstrapSwitch"]').bootstrapSwitch();
	//Material Design风格按钮效果
	Waves.attach('.btn', ['waves-float']);
    Waves.init();
	//detailIframe
	openDetailIframe = function(url){
		ZENG.msgbox.show('正在载入中，请稍后...', 6, 100000);
		$('#detailIframe iframe').attr('src', url).load(function(){
			$('body').css('overflow', 'hidden');
			ZENG.msgbox._hide();
			$('#detailIframe').animate({
				'top' : 0,
				'opacity' : 'show'
			}, 500);
		});
	};
	closeDetailIframe = function(callback){
		$('body').css('overflow', 'auto');
		$('#detailIframe').animate({
			'top' : '-100px',
			'opacity' : 'hide'
		}, 500, function(){
			callback && callback();
		});
	};
});
</script>