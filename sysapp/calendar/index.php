<?php
	require('../../global.php');
	
	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>我的日历</title>
<?php include('sysapp/global_css.php'); ?>
<link rel="stylesheet" href="../../img/ui/sys.css">
<link rel="stylesheet" href="../../js/fullcalendar-1.6.1/fullcalendar/fullcalendar.css">
</head>

<body>
<div id="editbox" style="display:none">
	<form action="index.ajax.php" method="post" name="form" id="form">
	<input type="hidden" name="ac" value="edit">
	<input type="hidden" name="id">
	<div class="creatbox">
		<div class="middle">
			<p class="detile-title">编辑日程</p>
			<div class="input-label">
				<label class="label-text">日程标题：</label>
				<div class="label-box form-inline control-group">
					<input type="text" class="text" name="val_title" style="width:335px" datatype="*" nullmsg="请填写日程标题">
					<span class="help-inline errormsg"></span>
				</div>
			</div>
			<div class="input-label">
				<label class="label-text">日期：</label>
				<div class="label-box form-inline control-group">
					<input type="text" class="text" name="val_startd" style="width:70px;text-align:center" datatype="*" nullmsg="请填写完整日期">
					<input type="text" class="text" name="val_startt" style="width:60px;text-align:center" datatype="*" nullmsg="请填写完整日期">
					<span class="help-inline" style="padding-right:5px">到</span>
					<input type="text" class="text" name="val_endd" style="width:70px;text-align:center" datatype="*" nullmsg="请填写完整日期">
					<input type="text" class="text" name="val_endt" style="width:60px;text-align:center" datatype="*" nullmsg="请填写完整日期">
					<span class="help-inline errormsg"></span>
				</div>
			</div>
			<div class="input-label">
				<label class="label-text">全天活动：</label>
				<div class="label-box form-inline control-group">
					<label class="radio"><input type="radio" name="val_isallday" value="1"> 是</label>
					<label class="radio" style="margin-left:10px"><input type="radio" name="val_isallday" value="0"> 否</label>
				</div>
			</div>
			<div class="input-label">
				<label class="label-text">链接：</label>
				<div class="label-box form-inline control-group">
					<input type="text" class="text" name="val_url" style="width:335px">
				</div>
			</div>
			<div class="input-label">
				<label class="label-text">内容：</label>
				<div class="label-box form-inline control-group">
					<textarea style="width:335px" rows="5" name="val_content"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="bottom-bar">
		<div class="con">
			<a class="btn btn-primary fr" id="btn-submit" href="javascript:;"><i class="icon-white icon-ok"></i> 确定</a>
			<a class="btn fr" id="btn-back" href="javascript:;" style="margin-right:10px">返回</a>
		</div>
		<input type="text" autocomplete="off">
	</div>
	</form>
</div>
<div id="calendar" style="margin:30px"></div>
<?php include('sysapp/global_js.php'); ?>
<script src="../../js/fullcalendar-1.6.1/jquery/jquery-ui-1.10.2.custom.min.js"></script>
<script src="../../js/fullcalendar-1.6.1/fullcalendar/fullcalendar.min.js"></script>
<script src="../../js/sugar/sugar-1.3.9.min.js"></script>
<script src="../../js/My97DatePicker/WdatePicker.js"></script>
<script>
$(function(){
	var form = $('#form').Validform({
		btnSubmit: '#btn-submit',
		postonce: false,
		showAllError: true,
		//msg：提示信息;
		//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
		//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
		tiptype: function(msg, o){
			if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
				var B = o.obj.parents('.control-group');
				var T = B.children('.errormsg');
				if(o.type == 2){
					B.removeClass('error');
					T.text('');
				}else{
					B.addClass('error');
					T.text(msg);
				}
			}
		},
		ajaxPost: true,
		callback: function(){
			$('#calendar').show();
			$('#editbox').hide();
			$('#calendar').fullCalendar('refetchEvents');
		}
	});
	$('input[name="val_startd"], input[name="val_endd"]').click(function(){
		WdatePicker({
			dateFmt:'yyyy-M-d',
			skin:'ext'
		});
	});
	$('input[name="val_startt"], input[name="val_endt"]').click(function(){
		WdatePicker({
			dateFmt:'H:m:s',
			skin:'ext'
		});
	});
	$('input[name="val_isallday"]').change(function(){
		if($(this).val() == 1){
			$('input[name="val_startt"], input[name="val_endt"]').hide();
			form.ignore('input[name="val_startt"], input[name="val_endt"]');
		}else{
			$('input[name="val_startt"], input[name="val_endt"]').show();
			form.unignore('input[name="val_startt"], input[name="val_endt"]');
		}
	});
	$('#btn-back').click(function(){
		$('#calendar').show();
		$('#editbox').hide();
	});
	var calendar = $('#calendar').fullCalendar({
		firstDay: 1,
		header: {
			left: 'today prev,next',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		monthNames: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
		monthNamesShort: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
		dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
		dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
		allDayText: '全天',
		axisFormat: 'h(:mm)tt',
		buttonText: {
			prev: '&nbsp;&#9668;&nbsp;',
			next: '&nbsp;&#9658;&nbsp;',
			prevYear: '&nbsp;&lt;&lt;&nbsp;',
			nextYear: '&nbsp;&gt;&gt;&nbsp;',
			today: '&nbsp;今天&nbsp;',
			month: '&nbsp;月&nbsp;',
			week: '&nbsp;周&nbsp;',
			day: '&nbsp;日&nbsp;'
		},
		titleFormat: {
			month: 'yyyy年MMMM',
			week: "yyyy年 MMMd日[ yyyy]{'-'[ MMM]d'日'}",
			day: 'yyyy年MMMd日 dddd'
		},
		columnFormat: {
			month: 'ddd',
			week: 'M月d日 ddd',
			day: 'M月d日 dddd'
		},
		timeFormat: {
			'': 'H:mm - '
		},
		selectable: true,
		selectHelper: true,
		select: function(start, end, allDay){
			//如果对话框存在，则先关闭
			if($.dialog.list['selectDialog'] != null){
				$.dialog.list['selectDialog'].close();
			}
			var content = '', isallday = 1;
			var startdText = Date.create(start).format('{M}月{dd}日 ') + getMyDay(start.getDay());
			var enddText = Date.create(end).format('{M}月{dd}日 ') + getMyDay(end.getDay());
			var starttText = Date.create(start).format(' {H}:{m}');
			var endtText = Date.create(end).format(' {H}:{m}');
			if(starttText != ' 0:0' || endtText != ' 0:0'){
				isallday = 0;
			}
			if(isallday == 0){
				content += startdText + starttText + '&nbsp;&nbsp;–&nbsp;' + endtText;
			}else{
				if(startdText == enddText){
					content += startdText;
				}else{
					content += startdText + '&nbsp;&nbsp;–&nbsp;&nbsp;' + enddText;
				}
			}
			//创建对话框
			$.dialog({
				id: 'selectDialog',
				lock: false,
				title: '创建日程',
				content: '<table><tr><td style="width:50px;height:30px;vertical-align:middle">时间：</td><td style="vertical-align:middle">' + content + '</td></tr><tr><td style="height:30px;vertical-align:middle">标题：</td><td style="vertical-align:middle"><input type="text" id="title" style="margin-bottom:0"></td></tr><tr><td></td><td style="height:30px;vertical-align:middle">例如：下午 4 点在 星巴克 喝下午茶</td></tr></table>',
				button: [
					{
						name: '创建',
						callback: function(){
							if($.trim(document.getElementById('title').value) != ''){
								$.ajax({
									type: 'POST',
									url: 'index.ajax.php',
									data: 'ac=quick&do=add&title=' + document.getElementById('title').value + '&start=' + Date.create(start).format('{yyyy}-{MM}-{dd} {H}:{m}:{s}') + '&end=' + Date.create(end).format('{yyyy}-{MM}-{dd} {H}:{m}:{s}') + '&isallday=' + isallday,
									success: function(){
										//添加成功后刷新日历
										calendar.fullCalendar('refetchEvents');
									}
								});
							}else{
								$.dialog.tips('请填写活动标题');
								return false;
							}
						},
						focus: true
					},
					{
						name: '编辑',
						callback: function(){
							$('#calendar').hide();
							$('#editbox').show();
							//清空表单
							clearEditForm();
							//初始化表单
							$('#editbox input[name="val_title"]').val(document.getElementById('title').value);
							$('#editbox input[name="val_startd"]').val(Date.create(start).format('{yyyy}-{MM}-{dd}'));
							$('#editbox input[name="val_endd"]').val(Date.create(end).format('{yyyy}-{MM}-{dd}'));
							$('#editbox input[name="val_startt"]').val(Date.create(start).format('{H}:{m}:{s}'));
							$('#editbox input[name="val_endt"]').val(Date.create(end).format('{H}:{m}:{s}'));
							if(isallday == 0){
								$('#editbox input[name="val_isallday"]:eq(1)').click();
							}
						}
					}
				]
			});
			calendar.fullCalendar('unselect');
		},
		editable: true,
		events: 'index.ajax.php?ac=getCalendar',
		eventClick: function(event){
			var start = new Date(event._start), end = new Date(event._end), content = '';
			var startdText = Date.create(start).format('{M}月{dd}日 ') + getMyDay(start.getDay());
			var enddText = Date.create(end).format('{M}月{dd}日 ') + getMyDay(end.getDay());
			var starttText = Date.create(start).format(' {H}:{m}');
			var endtText = Date.create(end).format(' {H}:{m}');
			if(!event.allDay){
				content += startdText + starttText + '&nbsp;&nbsp;–&nbsp;' + endtText;
			}else{
				if(startdText == enddText){
					content += startdText;
				}else{
					content += startdText + '&nbsp;&nbsp;–&nbsp;&nbsp;' + enddText;
				}
			}
			$.dialog({
				title: event.title,
				content: content,
				width: 350,
				button: [
					{
						name: '跳转',
						callback: function(){
							window.open(event.url, '_blank');
						},
						disabled: typeof event.url == 'undefined' ? true : false
					},
					{
						name: '删除',
						callback: function(){
							ZENG.msgbox.show('正在更新中，请稍后...', 6, 100000);
							$.ajax({
								type: 'POST',
								url: 'index.ajax.php',
								data: 'ac=quick&do=del&id=' + event._id,
								success: function(){
									ZENG.msgbox._hide();
									calendar.fullCalendar('removeEvents', event._id);
								}
							});
						}
					},
					{
						name: '编辑',
						callback: function(){
							ZENG.msgbox.show('正在读取中，请勿操作', 6, 100000);
							$.ajax({
								type: 'POST',
								url: 'index.ajax.php',
								data: 'ac=getDate&id=' + event._id,
								dataType: 'json',
								success: function(msg){
									ZENG.msgbox._hide();
									$('#calendar').hide();
									$('#editbox').show();
									//清空表单
									clearEditForm();
									//初始化表单
									$('#editbox input[name="id"]').val(msg['tbid']);
									$('#editbox input[name="val_title"]').val(msg['title']);
									$('#editbox input[name="val_startd"]').val(msg['startd']);
									$('#editbox input[name="val_startt"]').val(msg['startt']);
									$('#editbox input[name="val_endd"]').val(msg['endd']);
									$('#editbox input[name="val_endt"]').val(msg['endt']);
									$('#editbox input[name="val_url"]').val(msg['url']);
									$('#editbox textarea[name="val_content"]').val(msg['content']);
									if(msg['isallday'] == '1'){
										$('#editbox input[name="val_isallday"]:eq(0)').prop('checked', true);
										$('#editbox input[name="val_isallday"]:eq(1)').prop('checked', false);
										$('#editbox input[name="val_startt"], #editbox input[name="val_endt"]').hide();
									}else{
										$('#editbox input[name="val_isallday"]:eq(0)').prop('checked', false);
										$('#editbox input[name="val_isallday"]:eq(1)').prop('checked', true);
										$('#editbox input[name="val_startt"], #editbox input[name="val_endt"]').show();
									}
								}
							});
						},
						focus: true
					}
				]
			});
			return false;
		},
		eventDrop: function(event, dayDelta, minuteDelta){
			ZENG.msgbox.show('正在更新中，请稍后...', 6, 100000);
			$.ajax({
				type: 'POST',
				url: 'index.ajax.php',
				data: 'ac=quick&do=drop&id=' + event._id + '&dayDelta=' + dayDelta + '&minuteDelta=' + minuteDelta,
				success: function(){
					ZENG.msgbox._hide();
				}
			});
		},
		eventResize: function(event, dayDelta, minuteDelta){
			ZENG.msgbox.show('正在更新中，请稍后...', 6, 100000);
			$.ajax({
				type: 'POST',
				url: 'index.ajax.php',
				data: 'ac=quick&do=resize&id=' + event._id + '&dayDelta=' + dayDelta + '&minuteDelta=' + minuteDelta,
				success: function(){
					ZENG.msgbox._hide();
				}
			});
		},
		loading: function(bool){
			if(bool){
				ZENG.msgbox.show('正在加载中，请稍后...', 6, 100000);
			}else{
				ZENG.msgbox._hide();
			}
		}
	});
});
function getMyDay(day){
	var text = '周';
	switch(day){
		case 0: text += '日'; break;
		case 1: text += '一'; break;
		case 2: text += '二'; break;
		case 3: text += '三'; break;
		case 4: text += '四'; break;
		case 5: text += '五'; break;
		case 6: text += '六'; break;
	}
	return text;
}
function clearEditForm(){
	$('#editbox input[name="id"], #editbox input[name="val_title"], #editbox input[name="val_url"]').val('');
	$('#editbox input[name="val_startd"], #editbox input[name="val_endd"]').val('');
	$('#editbox input[name="val_startt"], #editbox input[name="val_endt"]').val('').hide();
	$('#editbox input[name="val_isallday"]:eq(0)').prop('checked', true);
	$('#editbox input[name="val_isallday"]:eq(1)').prop('checked', false);
	$('#editbox textarea[name="val_content"]').val('');
}
</script>
</body>
</html>