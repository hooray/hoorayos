/*
**  任务栏
*/
HROS.taskBar = (function(){
	return {
		/*
		**  初始化
		*/
		init : function(){
			//当浏览器窗口改变大小时，任务栏的显示也需进行刷新
			$(window).on('resize', function(){
				HROS.taskBar.resize();
			});
			//绑定任务栏拖动事件
			HROS.taskBar.move();
			//绑定任务栏前进后退按钮事件
			HROS.taskBar.pageClick();
			//绑定任务栏右键事件
			$('#task-content-inner').on('contextmenu', '.task-item', function(e){
				$('.popup-menu').hide();
				$('.quick_view_container').remove();
				var popupmenu = HROS.popupMenu.task($(this));
				var l = $(window).width() - e.clientX < popupmenu.width() ? e.clientX - popupmenu.width() : e.clientX;
				var t = e.clientY - popupmenu.height();
				popupmenu.css({
					left : l,
					top : t
				}).show();
				return false;
			});
		},
		pageClick : function(){
			$('#task-next-btn').on('click', function(){
				if($(this).hasClass('disable') == false){
					var taskW = $('#task-content-inner .task-item').width();
					var marginL = parseInt($('#task-content-inner').css('margin-left')) - taskW;
					var overW = $('#task-bar').width() - $('#task-next').outerWidth(true) - $('#task-pre').outerWidth(true) - $('#task-content-inner .task-item').length * taskW;
					if(marginL <= overW){
						marginL = overW;
						$('#task-next a').addClass('disable');
					}
					$('#task-pre a').removeClass('disable');
					$('#task-content-inner').animate({
						marginLeft : marginL
					}, 200);
				}
			});
			$('#task-pre-btn').on('click', function(){
				if($(this).hasClass('disable') == false){
					var taskW = $('#task-content-inner .task-item').width();
					var marginL = parseInt($('#task-content-inner').css('margin-left')) + taskW;
					if(marginL >= 0){
						marginL = 0;
						$('#task-pre a').addClass('disable');
					}
					$('#task-next a').removeClass('disable');
					$('#task-content-inner').animate({
						marginLeft : marginL
					}, 200);
				}
			});
		},
		resize : function(){
			if(HROS.CONFIG.dockPos == 'left'){
				$('#task-bar').css({
					left : $('#dock-bar').width(),
					right : 0
				});
			}else if(HROS.CONFIG.dockPos == 'right'){
				$('#task-bar').css({
					left : 0,
					right : $('#dock-bar').width()
				});
			}else{
				$('#task-bar').css({
					left : 0,
					right : 0
				});
			}
			var realW = $('#task-content-inner .task-item').length * $('#task-content-inner .task-item').width();
			var showW = $('#task-bar').width() - $('#task-next').outerWidth(true) - $('#task-pre').outerWidth(true);
			if(realW >= showW){
				$('#task-next, #task-pre').show();
				$('#task-content').css('width', showW);
				$('#task-content-inner').stop(true, false).animate({
					marginLeft : 0
				}, 200);
				$('#task-next a').removeClass('disable');
				$('#task-pre a').addClass('disable');
			}else{
				$('#task-next, #task-pre').hide();
				$('#task-content').css('width','100%');
				$('#task-content-inner').css({
					marginLeft : 0
				});
			}
		},
		move : function(){
			$('#task-content-inner').on('mousedown', '.task-item', function(e){
				e.preventDefault();
				e.stopPropagation();
				if(e.which == 1){
					var self = $(this);
					var task_left = self.offset().left;
					var task_width = self.width();
					var drag = self.clone().addClass('task-dragging').css({
						left : task_left
					});
					var current_animate_id;
					var dx = e.clientX;
					var cx = e.clientX;
					var lay = HROS.maskBox.desk();
					$(document).on('mousemove', function(e){
						$('body').append(drag);
						self.css('opacity', 0);
						lay.show();
						cx = e.clientX;
						var left = cx - dx + task_left;
						drag.css('left', left);
						$('#task-content-inner').find('.task-item').each(function(i){
							var this_left = $(this).offset().left;
							if(left > this_left && left < this_left + task_width / 2){
								if(self.attr('id') != $(this).attr('id')){
									swapTab($(this).attr('id'), 'b');
								}
							}else if(left < this_left && left > this_left - task_width / 2){
								if(self.attr('id') != $(this).attr('id')){
									swapTab($(this).attr('id'), 'a');
								}
							}
						});
					}).on('mouseup', function(e){
						$(document).off('mousemove').off('mouseup');
						lay.hide();
						drag.animate({
							left : self.offset().left
						}, 200, function(){
							$(this).remove();
							self.css('opacity', 1);
						});
						if(dx == cx){
							if(self.hasClass('task-item-current')){
								HROS.window.hide(self.attr('appid'));
							}else{
								HROS.window.show2top(self.attr('appid'));
							}
						}
					});
					var swapTab = function(id, boa){
						if(!(self.is(':animated') && current_animate_id == id)){
							current_animate_id = id;
							self.stop(true, true);
							$('#task-content-inner').find('.task-temp').remove();
							var temp = self.clone().insertAfter(self).addClass('task-temp');
							if(boa == 'b'){
								$('#' + id).before(self.css({
									width : 0
								}));
							}else{
								$('#' + id).after(self.css({
									width : 0
								}));
							}
							self.animate({
								width : task_width
							}, 100);
							temp.animate({
								width : 0
							}, 100, function(){
								$(this).remove();
							});
						}
					};
				}
			});
		}
	}
})();