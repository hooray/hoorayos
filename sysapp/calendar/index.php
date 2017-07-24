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
	<link href="//cdn.bootcss.com/fullcalendar/3.4.0/fullcalendar.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../../static/plugins/bootstrap-datetimepicker-2.4.4/css/bootstrap-datetimepicker.min.css">
</head>
<body>
	<div id="editbox" style="display:none">
		<form action="index.ajax.php" method="post" name="form" id="form" class="form-horizontal">
			<div class="title-bar">编辑应用</div>
			<div class="creatbox">
				<div class="panel panel-default">
					<div class="panel-body">
						<input type="hidden" name="ac" value="edit">
						<input type="hidden" name="id">
						<div class="form-group">
							<label for="inputName" class="col-sm-2 control-label">日程标题：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="val_title" datatype="*" nullmsg="请填写日程标题">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">日期：</label>
							<div class="col-sm-10">
								<div class="input-group">
									<span class="input-group-addon">开始</span>
									<input type="text" class="form-control" name="val_startdt" style="text-align:center" datatype="*" nullmsg="请填写完整日期">
									<span class="input-group-addon">结束</span>
									<input type="text" class="form-control" name="val_enddt" style="text-align:center" datatype="*" nullmsg="请填写完整日期">
								</div>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">全天活动：</label>
							<div class="col-sm-10">
								<input type="checkbox" name="val_isallday" data-plugin="bootstrapSwitch" data-on-color="info" data-on-text="是" data-off-text="否">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">链接：</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="val_url">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">内容：</label>
							<div class="col-sm-10">
								<textarea rows="5" class="form-control" name="val_content"></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="bottom-bar">
				<a class="btn btn-primary pull-right" id="btn-submit" href="javascript:;"><i class="glyphicon glyphicon-ok"></i> 确定</a>
				<a class="btn btn-default pull-right" id="btn-back" href="javascript:;" style="margin-right:10px"><i class="glyphicon glyphicon-chevron-up"></i> 返回</a>
			</div>
		</form>
	</div>
	<div id="calendar" style="margin:30px"></div>
	<?php include('sysapp/global_js.php'); ?>
	<script src="//cdn.bootcss.com/moment.js/2.18.1/moment.min.js"></script>
	<script src="//cdn.bootcss.com/fullcalendar/3.4.0/fullcalendar.min.js"></script>
	<script src="//cdn.bootcss.com/fullcalendar/3.4.0/locale/zh-cn.js"></script>
	<script src="../../static/plugins/bootstrap-datetimepicker-2.4.4/js/bootstrap-datetimepicker.min.js"></script>
	<script src="../../static/plugins/bootstrap-datetimepicker-2.4.4/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
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
					var B = o.obj.parents('.form-group');
					var T = B.find('.help-block');
					if(o.type == 2){
						B.removeClass('has-error');
						T.text('');
					}else{
						B.addClass('has-error');
						T.text(msg);
					}
				}
			},
			ajaxPost: true,
			callback: function(data){
				if(data.status == 'y'){
					$('#calendar').show();
					$('#editbox').hide();
					$('#calendar').fullCalendar('refetchEvents');
				}else{
					swal({
						type: 'error',
						title: '警告',
						text: data.info,
						timer: 2000,
						showConfirmButton: false
					});
				}
			}
		});
		$('#editbox input[name="val_isallday"]').on('switchChange.bootstrapSwitch', function(event, state) {
			var startdt = $('input[name="val_startdt"]').val();
			var enddt = $('input[name="val_enddt"]').val();
			$('input[name="val_startdt"]').datetimepicker('remove');
			$('input[name="val_enddt"]').datetimepicker('remove');
			if(state){
				$('input[name="val_startdt"]').datetimepicker({
					language: 'zh-CN',
					format: 'yyyy-mm-dd',
					weekStart: 1,
					startView: 2,
					minView: 2,
					todayBtn: true,
					todayHighlight: true
				});
				$('input[name="val_enddt"]').datetimepicker({
					language: 'zh-CN',
					format: 'yyyy-mm-dd',
					weekStart: 1,
					startView: 2,
					minView: 2,
					todayBtn: true,
					todayHighlight: true
				});
				$('input[name="val_startdt"]').val(moment(startdt).format('YYYY-MM-DD'));
				$('input[name="val_enddt"]').val(moment(startdt).format('YYYY-MM-DD'));
			}else{
				$('input[name="val_startdt"]').datetimepicker({
					language: 'zh-CN',
					format: 'yyyy-mm-dd hh:ii',
					weekStart: 1,
					startView: 2,
					minView: 0,
					todayBtn: true,
					todayHighlight: true
				});
				$('input[name="val_enddt"]').datetimepicker({
					language: 'zh-CN',
					format: 'yyyy-mm-dd hh:ii',
					weekStart: 1,
					startView: 2,
					minView: 0,
					todayBtn: true,
					todayHighlight: true
				});
				$('input[name="val_startdt"]').val(moment(startdt).format('YYYY-MM-DD HH:mm'));
				$('input[name="val_enddt"]').val(moment(enddt).format('YYYY-MM-DD HH:mm'));
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
				right: 'month,agendaWeek,agendaDay,listYear'
			},
			selectable: true,
			selectHelper: true,
			select: function(start, end, allDay){
				var content = '', isallday = 1;
				var startdText = start.format('M月D日 ') + getMyDay(start.day());
				var enddText = end.format('M月D日 ') + getMyDay(end.day());
				var starttText = start.format(' HH:mm');
				var endtText = end.format(' HH:mm');
				if(starttText != ' 00:00' || endtText != ' 00:00'){
					isallday = 0;
				}
				if(isallday == 0){
					content += startdText + starttText + ' - ' + endtText;
				}else{
					if(startdText == enddText){
						content += startdText;
					}else{
						content += startdText + ' - ' + enddText;
					}
				}
				//创建对话框
				dialog({
					id: 'selectDialog',
					title: '创建日程',
					content: '<table><tr><td style="width:50px;height:30px;vertical-align:middle">时间：</td><td style="vertical-align:middle">' + content + '</td></tr><tr><td style="height:30px;vertical-align:middle">标题：</td><td style="vertical-align:middle"><input type="text" id="title" style="margin-bottom:0"></td></tr><tr><td></td><td style="height:30px;vertical-align:middle">例如：下午 4 点在 星巴克 喝下午茶</td></tr></table>',
					button: [
						{
							value: '创建',
							callback: function(){
								if($.trim(document.getElementById('title').value) != ''){
									$.ajax({
										type: 'POST',
										url: 'index.ajax.php',
										data: 'ac=quick&do=add&title=' + document.getElementById('title').value + '&start=' + start.format('YYYY-MM-DD HH:mm') + '&end=' + end.format('YYYY-MM-DD HH:mm') + '&isallday=' + isallday,
										success: function(){
											//添加成功后刷新日历
											calendar.fullCalendar('refetchEvents');
										}
									});
								}else{
									swal({
										type: 'error',
										title: '请填写活动标题',
										showConfirmButton: false,
										timer: 1000
									});
									return false;
								}
							},
							autofocus: true
						},
						{
							value: '编辑',
							callback: function(){
								$('#calendar').hide();
								$('#editbox').show();
								//清空表单
								clearEditForm();
								//初始化表单
								$('#editbox input[name="val_title"]').val(document.getElementById('title').value);
								if(isallday == 0){
									$('#editbox input[name="val_isallday"]').bootstrapSwitch('state', false);
									$('#editbox input[name="val_startdt"]').val(start.format('YYYY-MM-DD HH:mm'));
									$('#editbox input[name="val_enddt"]').val(end.format('YYYY-MM-DD HH:mm'));
								}else{
									$('#editbox input[name="val_isallday"]').bootstrapSwitch('state', true);
									$('#editbox input[name="val_startdt"]').val(start.format('YYYY-MM-DD'));
									$('#editbox input[name="val_enddt"]').val(end.format('YYYY-MM-DD'));
								}
							}
						}
					]
				}).showModal();
				calendar.fullCalendar('unselect');
			},
			editable: true,
			events: function(start, end, timezone, callback){
				$.ajax({
					type: 'POST',
					url: 'index.ajax.php',
					data: {
						ac: 'getCalendar',
						start: start.unix(),
						end: end.unix()
					},
					dataType: 'json'
				}).done(function(msg){
					callback(msg);
				});
			},
			eventClick: function(event){
				var start = event._start, end = event._end, content = '';
				var startdText = start.format('M月D日 ') + getMyDay(start.day());
				var enddText = end.format('M月D日 ') + getMyDay(end.day());
				var starttText = start.format('HH:mm');
				var endtText = end.format('HH:mm');
				if(!event.allDay){
					content += startdText + starttText + ' - ' + endtText;
				}else{
					if(startdText == enddText){
						content += startdText;
					}else{
						content += startdText + ' - ' + enddText;
					}
				}
				dialog({
					title: event.title,
					content: content,
					width: 350,
					button: [
						{
							value: '跳转',
							callback: function(){
								window.open(event.url, '_blank');
							},
							disabled: typeof event.url == 'undefined' ? true : false
						},
						{
							value: '删除',
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
							value: '编辑',
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
										$('#editbox input[name="val_url"]').val(msg['url']);
										$('#editbox textarea[name="val_content"]').val(msg['content']);
										if(msg['isallday'] == '1'){
											$('#editbox input[name="val_isallday"]').bootstrapSwitch('state', true);
											$('#editbox input[name="val_startdt"]').val(moment(msg['startdt']).format('YYYY-MM-DD'));
											$('#editbox input[name="val_enddt"]').val(moment(msg['enddt']).format('YYYY-MM-DD'));
										}else{
											$('#editbox input[name="val_isallday"]').bootstrapSwitch('state', false);
											$('#editbox input[name="val_startdt"]').val(moment(msg['startdt']).format('YYYY-MM-DD HH:mm'));
											$('#editbox input[name="val_enddt"]').val(moment(msg['enddt']).format('YYYY-MM-DD HH:mm'));
										}
									}
								});
							},
							autofocus: true
						}
					]
				}).showModal();
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
		$('#editbox input[name="val_startdt"], #editbox input[name="val_enddt"]').val('');
		$('#editbox textarea[name="val_content"]').val('');
	}
	</script>
</body>
</html>