<script src="../../static/plugins/jquery-2.2.4.min.js"></script>
<script src="../../static/plugins/HoorayLibs/hooraylibs.js"></script>
<script src="../../static/plugins/bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script src="../../static/plugins/bootstrap-table-1.11.1/dist/bootstrap-table.min.js"></script>
<script src="../../static/plugins/bootstrap-table-1.11.1/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="../../static/plugins/bootstrap-select-1.12.2/dist/js/bootstrap-select.min.js"></script>
<script src="../../static/plugins/bootstrap-select-1.12.2/dist/js/i18n/defaults-zh_CN.min.js"></script>
<script src="../../static/plugins/bootstrap-switch-3.3.3/dist/js/bootstrap-switch.min.js"></script>
<script src="../../static/plugins/artDialog-6.0.4/dist/dialog-min.js"></script>
<script src="../../static/plugins/artDialog-6.0.4/dist/dialog-plus-min.js"></script>
<script src="../../static/plugins/sweetalert-1.1.1/dist/sweetalert.min.js"></script>
<script src="../../static/plugins/Validform_v5.3.2/Validform_v5.3.2_min.js"></script>
<script>
$(function(){
	//toolTip
	$('[data-toggle="tooltip"]').tooltip();
	//下拉列表
	$('[data-plugin="bootstrapSelect"]').selectpicker();
	//开关滑块
	$('[data-plugin="bootstrapSwitch"]').bootstrapSwitch();
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