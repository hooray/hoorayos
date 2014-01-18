HROS.lock = (function(){
	return {
		init : function(){
			Mousetrap.bind(['space'], function(){
				$('#lock').click();
				return false;
			});
			$('body').on('click', '#lock', function(){
				if($('#lock-info').is(':visible')){
					$('#lock .title').animate({
						top : '10%'
					}, 500);
					$('#lock .tip').animate({
						top : '80%'
					}, 500);
					$('#lock-info').fadeOut();
					Mousetrap.bind(['space'], function(){
						$('#lock').click();
						return false;
					});
					Mousetrap.unbind('esc');
				}else{
					$('#lock .title').animate({
						top : '-200px'
					}, 500);
					$('#lock .tip').animate({
						top : '100%'
					}, 500);
					$('#lock-info').fadeIn();
					$('#lockpassword').val('').focus();
					$('#lock-info .text-tip').text('');
					Mousetrap.bind(['esc'], function(){
						$('#lock').click();
						return false;
					});
					Mousetrap.unbind('space');
				}
			});
			$('body').on('click', '#lock-info', function(){
				return false;
			});
			$('body').on('click', '#lockbtn', function(){
				if($('#lockpassword').val() != ''){
					$.ajax({
						type : 'POST',
						url : 'login.ajax.php',
						data : 'ac=unlock&password=' + $('#lockpassword').val(),
					}).done(function(responseText){
						if(responseText == 'ERROR_LOCKPASSWORD'){
							$('#lockpassword').val('').focus();
							$('#lock-info .text-tip').text('解锁密码错误');
						}else{
							HROS.lock.hide();
						}
					});
				}else{
					$('#lock-info .text-tip').text('请输入解锁密码');
				}
			});
			$('body').on('keydown', '#lockpassword', function(e){
				if(e.keyCode == '13'){
					$('#lockbtn').click();
				}
			});
		},
		show : function(){
			if($('#lock').length == 0){
				if(!HROS.base.checkLogin()){
					$.dialog({
						title : '温馨提示',
						icon : 'warning',
						content : '锁定功能需要登录后才能使用，为了更好的操作，是否登录？',
						ok : function(){
							HROS.base.login();
						}
					});
				}else{
					var lock = function(){
						$.ajax({
							type : 'POST',
							url : 'login.ajax.php',
							data : 'ac=logout'
						});
						$('#desktop').hide();
						var userinfo = $.parseJSON($.cookie(cookie_prefix + 'userinfo'));
						$('body').append(lockTemp({
							'avatar' : userinfo.avatar,
							'username' : userinfo.username
						}));
						//时间，日期，星期信息的显示
						var getTimeDateWeek = function(){
							var time = new Date();
							$('#lock .time').text((time.getHours() < 10 ? '0' + time.getHours() : time.getHours()) + ':' + (time.getMinutes() < 10 ? '0' + time.getMinutes() : time.getMinutes()));
							var date = time.getFullYear() + '/' + (time.getMonth() + 1) + '/' + time.getDate() + '，星期';
							switch(time.getDay()){
								case '1' : date += '一'; break;
								case '1' : date += '二'; break;
								case '1' : date += '三'; break;
								case '1' : date += '四'; break;
								case '1' : date += '五'; break;
								case '1' : date += '六'; break;
								default : date += '日';
							}
							$('#lock .week').text(date);
						};
						getTimeDateWeek();
						lockFunc = setInterval(function(){
							//检查是否有恶意修改源程序绕过锁屏界面
							var iswarning = false;
							if($('#desktop').is(':visible')){
								iswarning = true;
							}
							if($('#lock').length == 0){
								iswarning = true;
							}
							if($('#lock').is(':hidden')){
								iswarning = true;
							}
							//如果有则重新载入锁屏
							if(iswarning){
								clearInterval(lockFunc);
								$('#lock').remove();
								HROS.lock.show();
							}
							getTimeDateWeek();
						}, 1000);
					};
					if($.cookie(cookie_prefix + 'isfirstlock' + HROS.CONFIG.memberID) == null){
						$.cookie(cookie_prefix + 'isfirstlock' + HROS.CONFIG.memberID, 1);
						$.dialog({
							title : '温馨提示',
							icon : 'warning',
							content : '解锁密码默认为登录密码，是否要先进行修改？',
							button : [
								{
									name : '继续锁定',
									callback : function(){
										lock();
									},
									focus : true
								},
								{
									name : '修改解锁密码',
									callback : function(){
										HROS.window.createTemp({
											appid : 'zhsz',
											title : '账号设置',
											url : 'sysapp/account/security.php',
											width : 550,
											height : 580
										});
									}
								}
							]
						});
					}else{
						lock();
					}
				}
			}
		},
		hide : function(){
			clearInterval(lockFunc);
			$('#lock').remove();
			$('#desktop').show();
		}
	}
})();