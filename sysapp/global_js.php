<script src="../../js/jquery-2.2.4.min.js"></script>
<script src="../../js/bootstrap-3.3.6/js/bootstrap.min.js"></script>
<script src="../../js/HoorayLibs/hooraylibs.js"></script>
<script src="../../js/artDialog-6.0.4/dist/dialog-min.js"></script>
<script src="../../js/artDialog-6.0.4/dist/dialog-plus-min.js"></script>
<script src="../../js/sweetalert-1.1.1/dist/sweetalert.min.js"></script>
<script src="../../js/Validform_v5.3.2/Validform_v5.3.2_min.js"></script>
<script>
$(function(){
	//toolTip
	$('[data-toggle="tooltip"]').tooltip();
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
