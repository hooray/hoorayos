/*
**  小挂件
*/
HROS.widget = (function(){
	return {
		init: function(){
			//挂件上各个按钮
			HROS.widget.handle();
			//挂件移动
			HROS.widget.move();
			//还原上次退出系统时widget的状态
			HROS.widget.reduction();
			$('#desk').on('mouseover', '.widget', function(){
				$(this).children('.move').show();
			}).on('mouseout', '.widget', function(){
				$(this).children('.move').hide();
			});
		},
		/*
		**  创建挂件
		**  自定义挂件：HROS.widget.createTemp({url,width,height,top,right});
		**       示例：HROS.widget.createTemp({url:"http://www.baidu.com",width:800,height:400,top:100,right:100});
		*/
		createTemp: function(obj){
			var appid = obj.appid == null ? Date.parse(new Date()): obj.appid;
			//判断窗口是否已打开
			var iswidgetopen = false;
			$('#desk .widget').each(function(){
				if($(this).attr('appid') == appid){
					iswidgetopen = true;
					return false;
				}
			});
			//如果没有打开，则进行创建
			if(!iswidgetopen){
				function nextDo(options){
					var widgetId = '#w_' + options.appid;
					var widgetData = {
						'width': options.width,
						'height': options.height,
						'type': 'widgetTemp',
						'id': 'w_' + options.appid,
						'appid': options.appid,
						'realappid': options.appid,
						'top': options.top,
						'right': options.right,
						'url': options.url,
						'zIndex': HROS.CONFIG.widgetIndexid,
						'issetbar': 0
					};
					$('#desk').append(HROS.template.widgetWindow(widgetData));
					$(widgetId).data('info', widgetData).css({
						opacity: 0,
						scale: 1.1
					}).transition({
						opacity: 1,
						scale: 1
					}, 200);
					HROS.CONFIG.widgetIndexid += 1;
				}
				nextDo({
					appid: appid,
					url: obj.url,
					width: obj.width,
					height: obj.height,
					top: obj.top == null ? 0: obj.top,
					right: obj.right == null ? 0: obj.right
				});
			}
		},
		create: function(appid, type, realappid){
			var type = type == null ? 'widget': type;
			//判断窗口是否已打开
			var iswidgetopen = false;
			$('#desk .widget').each(function(){
				if($(this).attr('appid') == appid){
					iswidgetopen = true;
					return false;
				}
			});
			//如果没有打开，则进行创建
			if(!iswidgetopen && $('#d_' + appid).attr('opening') != 1){
				$('#d_' + appid).attr('opening', 1);
				function nextDo(options){
					var widgetId = '#w_' + options.appid;
					if(HROS.widget.checkCookie(appid, type)){
						var widgetState = JSON.parse(Cookies.get(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID));
						$(widgetState).each(function(){
							if(this.appid == options.appid && this.type == options.type){
								options.top = this.top;
								options.right = this.right;
							}
						});
					}else{
						HROS.widget.addCookie(options.appid, options.type, 0, 0);
					}
					var widgetData = {
						'title': options.title,
						'width': options.width,
						'height': options.height,
						'type': options.type,
						'id': 'w_' + options.appid,
						'appid': options.appid,
						'realappid': options.realappid == 0 ? options.appid: options.realappid,
						'top': typeof(options.top) == 'undefined' ? 0: options.top,
						'right': typeof(options.right) == 'undefined' ? 0: options.right,
						'url': options.url,
						'zIndex': HROS.CONFIG.widgetIndexid,
						'issetbar': 1
					};
					$('#desk').append(HROS.template.widgetWindow(widgetData));
					$(widgetId).data('info', widgetData).css({
						opacity: 0,
						scale: 1.1
					}).transition({
						opacity: 1,
						scale: 1
					}, 200);
					HROS.CONFIG.widgetIndexid += 1;
				}
				$.ajax({
					data: 'ac=getMyAppById&id=' + appid + '&type=' + type,
					dataType: 'json'
				}).done(function(widget){
					if(widget != null){
						if(widget['error'] == 'ERROR_NOT_FOUND'){
							swal({
								type: 'error',
								title: '小挂件不存在，建议删除',
								timer: 2000,
								showConfirmButton: false
							});
							HROS.widget.removeCookie(appid, type);
						}else if(widget['error'] == 'ERROR_NOT_INSTALLED'){
							HROS.window.createTemp({
								appid: 'hoorayos-yysc',
								title: '应用市场',
								url: 'sysapp/appmarket/index.php?id=' + (realappid == null ? $('#d_' + appid).attr('realappid'): realappid),
								width: 800,
								height: 484,
								isflash: false,
								refresh: true
							});
							HROS.widget.removeCookie(appid, type);
						}else{
							nextDo({
								appid: widget['appid'],
								realappid: widget['realappid'],
								title: widget['name'],
								url: widget['url'],
								type: widget['type'],
								width: widget['width'],
								height: widget['height'],
								top: 0,
								right: 0
							});
						}
					}else{
						swal({
							type: 'error',
							title: '小挂件加载失败',
							timer: 2000,
							showConfirmButton: false
						});
					}
					$('#d_' + appid).attr('opening', 0);
				});
			}
		},
		//还原上次退出系统时widget的状态
		reduction: function(){
			var widgetState = Cookies.get(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID);
			if(typeof widgetState !== 'undefined'){
				widgetState = JSON.parse(widgetState);
				$(widgetState).each(function(){
					HROS.widget.create(this.appid, this.type);
				});
			}
		},
		//根据id验证是否存在cookie中
		checkCookie: function(appid, type){
			var flag = false;
			var widgetState = Cookies.get(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID);
			if(typeof widgetState !== 'undefined'){
				widgetState = JSON.parse(widgetState);
				$(widgetState).each(function(){
					if(this.appid == appid && this.type == type){
						flag = true;
					}
				});
			}
			return flag;
		},
		/*
		**  以下2个方法：addCookie、removeCookie
		**  用于记录widget打开状态以及摆放位置
		**  实现用户再次登入系统时，还原上次widget的状态
		*/
		addCookie: function(appid, type, top, right){
			if(type == 'widget' || type == 'pwidget'){
				//检查是否存在，如果存在则更新，反之则添加
				if(HROS.widget.checkCookie(appid, type)){
					var widgetState = JSON.parse(Cookies.get(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID));
					$(widgetState).each(function(){
						if(this.appid == appid && this.type == type){
							this.top = top;
							this.right = right;
						}
					});
				}else{
					var widgetState = Cookies.get(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID);
					if(typeof widgetState !== 'undefined'){
						widgetState = JSON.parse(widgetState);
					}else{
						widgetState = [];
					}
					widgetState.push({
						appid: appid,
						type: type,
						top: top,
						right: right
					});
				}
				Cookies.set(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID, $.toJSON(widgetState), {expires: 95});
			}
		},
		removeCookie: function(appid, type){
			if(type == 'widget' || type == 'pwidget'){
				if(HROS.widget.checkCookie(appid, type)){
					var widgetState = JSON.parse(Cookies.get(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID));
					$(widgetState).each(function(i){
						if(this.appid == appid && this.type == type){
							widgetState.splice(i, 1);
							return false;
						}
					});
					Cookies.set(cookie_prefix + 'widgetState' + HROS.CONFIG.memberID, $.toJSON(widgetState), {expires: 95});
				}
			}
		},
		move: function(){
			$('#desk').on('mousedown', '.widget .move', function(e){
				var obj = $(this).parents('.widget');
				HROS.widget.show2top(obj.attr('appid'));
				var x = e.clientX - obj.offset().left;
				var y = e.clientY - obj.offset().top;
				var lay;
				var t;
				var r;
				//绑定鼠标移动事件
				$(document).on('mousemove', function(e){
					lay = HROS.maskBox.desk();
					lay.show();
					t = e.clientY - y < 0 ? 0: e.clientY - y;
					r = $(window).width() - obj.width() - (e.clientX - x);
					obj.css({
						top: t,
						right: r
					});
				}).on('mouseup', function(){
					$(this).off('mousemove').off('mouseup');
					if(typeof(lay) !== 'undefined'){
						lay.hide();
					}
					if(obj.attr('type') != 'widgetTemp'){
						HROS.widget.addCookie(obj.attr('appid'), obj.attr('type'), t, r);
					}
				});
			});
		},
		close: function(appid){
			var widgetId = '#w_' + appid;
			HROS.widget.removeCookie($(widgetId).attr('appid'), $(widgetId).attr('type'));
			$(widgetId).css({
				opacity: 1,
				scale: 1
			}).transition({
				opacity: 0,
				scale: 1.1
			}, 200, function(){
				$(this).removeData('info').html('').remove();
			});
		},
		show2top: function(appid){
			var widgetId = '#w_' + appid;
			$(widgetId).css('z-index', HROS.CONFIG.widgetIndexid);
			HROS.CONFIG.widgetIndexid += 1;
		},
		handle: function(){
			$('#desk').on('mousedown', '.widget a', function(e){
				e.preventDefault();
				e.stopPropagation();
			});
			$('#desk').on('click', '.widget .ha-close', function(e){
				var obj = $(this).parents('.widget');
				HROS.widget.close(obj.attr('appid'));
			}).on('click', '.widget .ha-star', function(){
				var obj = $(this).parents('.widget');
				$.ajax({
					data: 'ac=getAppStar&id=' + obj.data('info').realappid
				}).done(function(starnum){
					starnum = starnum['starnum'];
					dialog({
						title: '给“' + obj.data('info').title + '”打分',
						padding: '10px 40px 15px 40px',
						id: 'star',
						content: HROS.template.starDialog({
							'realappid': obj.data('info').realappid,
							'point': Math.floor(starnum),
							'realpoint': starnum * 20
						})
					}).showModal();
				});
			}).on('click', '.widget .ha-share', function(){
				var obj = $(this).parents('.widget');
				dialog({
					title: '分享应用',
					width: 320,
					id: 'share',
					content: HROS.template.shareDialog({
						'title': '我正在使用 %23HoorayOS%23 中的 %23' + obj.data('info').title + '%23 应用，很不错哦，推荐你也来试试！',
						'url': HROS.CONFIG.website + '?run=' + obj.data('info').realappid + '%26type=app'
					})
				}).showModal();
			});
		}
	}
})();
