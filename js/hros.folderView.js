HROS.folderView = (function(){
	return {
		init : function(){
			//当浏览器窗口改变大小时，文件夹预览窗口也需进行调整
			$(window).on('resize', function(){
				HROS.folderView.resize();
			});
			$('body').on('click', '.quick_view_container', function(){
				HROS.popupMenu.hide();
			}).on('click', '.quick_view_container_open', function(){
				HROS.window.create($(this).parents('.quick_view_container').attr('appid'), 'folder');
				HROS.folderView.hide();
			}).on('click', '.appbtn', function(){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchbar.hide();
				HROS.startmenu.hide();
			});
			HROS.folderView.moveScrollbar();
		},
		get : function(obj){
			setTimeout(function(){
				//判断文件夹窗口是否已打开
				var iswindowopen = false;
				$('body .quick_view_container').each(function(){
					if($(this).attr('realappid') == obj.attr('realappid')){
						iswindowopen = true;
						return false;
					}
				});
				if(iswindowopen){
					var folderViewId = '#qv_' + obj.attr('appid');
				}else{
					HROS.folderView.hide();
				}
				var sc = '';
				$(HROS.VAR.folder).each(function(){
					if(this.appid == obj.attr('appid')){
						sc = this.apps;
						return false;
					}
				});
				var folderViewHtml = '', height = 0;
				if(sc != ''){
					$(sc).each(function(){
						switch(this.type){
							case 'app':
							case 'widget':
							case 'papp':
							case 'pwidget':
								folderViewHtml += appbtnTemp({
									'top' : 0,
									'left' : 0,
									'title' : this.name,
									'type' : this.type,
									'id' : 'd_' + this.appid,
									'appid' : this.appid,
									'realappid' : this.realappid,
									'imgsrc' : this.icon
								});
								break;
						}
					});
					if(sc.length % 4 == 0){
						height += Math.floor(sc.length / 4) * 60;
					}else{
						height += (Math.floor(sc.length / 4) + 1) * 60;
					}
				}else{
					folderViewHtml = '文件夹为空';
					height += 30;
				}
				//判断是桌面上的文件夹，还是应用码头上的文件夹
				var left, top;
				if(obj.parent('div').hasClass('dock-applist')){
					left = obj.offset().left + obj.width();
					top = obj.offset().top;
				}else{
					left = obj.offset().left + obj.width();
					top = obj.offset().top - 20;
				}
				//判断预览面板是否有超出屏幕
				var isScrollbar = false;
				if(height + top + 46 > $(window).height()){
					var outH = height + top + 46 - $(window).height();
					if(outH <= top){
						top -= outH;
					}else{
						height -= outH - top;
						top = 0;
						isScrollbar = true;
					}
				}
				if(left + 340 > $(window).width()){
					left -= (340 + obj.width());
					//预览居左
					if(iswindowopen){
						$(folderViewId + ' .quick_view_container_list_in').html('').append(folderViewHtml);
						$(folderViewId).stop(true, false).animate({'left' : left, 'top' : top}, 500);
						$(folderViewId + ' .perfect_nine_m_l_t').stop(true, false).animate({'top' : 0, 'height' : Math.ceil((height + 26) / 2)}, 200);
						$(folderViewId + ' .perfect_nine_m_l_m').stop(true, false).animate({'top' : Math.ceil((height + 26) / 2)}, 200).hide();
						$(folderViewId + ' .perfect_nine_m_l_b').stop(true, false).animate({'top' : Math.ceil((height + 26) / 2), 'height' : Math.ceil((height + 26) / 2) + 20}, 200);
						$(folderViewId + ' .perfect_nine_m_r_t').stop(true, false).animate({'top' : 0, 'height' : obj.offset().top - top}, 200);
						$(folderViewId + ' .perfect_nine_m_r_m').stop(true, false).animate({'top' : obj.offset().top - top}, 200).show();
						$(folderViewId + ' .perfect_nine_m_r_b').stop(true, false).animate({'top' : obj.offset().top - top + 20, 'height' : height + 26 - (obj.offset().top - top) - 20 + 20}, 200);
						$(folderViewId + ' .quick_view_container_list_in').stop(true, false).animate({'height' : height}, 200);
					}else{
						$('body').append(folderViewTemp({
							'id' : 'qv_' + obj.attr('appid'),
							'appid' : obj.attr('appid'),
							'realappid' : obj.attr('realappid'),
							'apps' : folderViewHtml,
							'top' : top,
							'left' : left,
							'height' : height,
							'mlt' : Math.ceil((height + 26) / 2),
							'mlm' : false,
							'mlb' : Math.ceil((height + 26) / 2),
							'mrt' : obj.offset().top - top,
							'mrm' : true,
							'mrb' : height + 26 - (obj.offset().top - top) - 20
						}));
					}
				}else{
					//预览居右
					if(iswindowopen){
						$(folderViewId + ' .quick_view_container_list_in').html('').append(folderViewHtml);
						$(folderViewId).stop(true, false).animate({'left' : left, 'top' : top}, 500);
						$(folderViewId + ' .perfect_nine_m_l_t').stop(true, false).animate({'top' : 0, 'height' : obj.offset().top - top}, 200);
						$(folderViewId + ' .perfect_nine_m_l_m').stop(true, false).animate({'top' : obj.offset().top - top}, 200).show();
						$(folderViewId + ' .perfect_nine_m_l_b').stop(true, false).animate({'top' : obj.offset().top - top + 20, 'height' : height + 26 - (obj.offset().top - top) - 20}, 200);
						$(folderViewId + ' .perfect_nine_m_r_t').stop(true, false).animate({'top' : 0, 'height' : Math.ceil((height + 26) / 2)}, 200);
						$(folderViewId + ' .perfect_nine_m_r_m').stop(true, false).animate({'top' : Math.ceil((height + 26) / 2)}, 200).hide();
						$(folderViewId + ' .perfect_nine_m_r_b').stop(true, false).animate({'top' : Math.ceil((height + 26) / 2), 'height' : Math.ceil((height + 26) / 2)}, 200);
						$(folderViewId + ' .quick_view_container_list_in').stop(true, false).animate({'height' : height}, 200);
					}else{
						$('body').append(folderViewTemp({
							'id' : 'qv_' + obj.attr('appid'),
							'appid' : obj.attr('appid'),
							'realappid' : obj.attr('realappid'),
							'apps' : folderViewHtml,
							'top' : top,
							'left' : left,
							'height' : height,
							'mlt' : obj.offset().top - top,
							'mlm' : true,
							'mlb' : height + 26 - (obj.offset().top - top) - 20,
							'mrt' : Math.ceil((height + 26) / 2),
							'mrm' : false,
							'mrb' : Math.ceil((height + 26) / 2)
						}));
					}
				}
				var view = '#quick_view_container_list_in_' + obj.attr('appid');
				var scrollbar = '#quick_view_container_list_' + obj.attr('appid') + ' .scrollBar';
				$('#quick_view_container_list_' + obj.attr('appid') + ' .scrollBar_bgc').hide();
				$(scrollbar).hide().height(0);
				if(isScrollbar){
					$('#quick_view_container_list_' + obj.attr('appid') + ' .scrollBar_bgc').show();
					$(scrollbar).show().height($(view).height() / (Math.ceil($(view).children().length / 4) * 60) * $(view).height());
				}
			}, 0);
		},
		resize : function(){
			$('body .quick_view_container').each(function(){
				HROS.folderView.get($('#d_' + $(this).attr('appid')));
			});
		},
		moveScrollbar : function(){
			/*
			**  手动拖动
			*/
			$('body').on('mousedown', '.quick_view_container .quick_view_container_list .scrollBar', function(e){
				var scrollbar = $(this), container = scrollbar.prev('.quick_view_container_list_in');
				var offsetTop = container.offset().top;
				var y, cy, containerrealh, moveh;
				containerrealh = Math.ceil(container.children().length / 4) * 60;
				moveh = container.height() - scrollbar.height();
				y = e.clientY - scrollbar.offset().top;
				$(document).on('mousemove', function(e){
					cy = e.clientY - y - offsetTop < 0 ? 0 : e.clientY - y - offsetTop > moveh ? moveh : e.clientY - y - offsetTop;
					scrollbar.css('top', cy);
					container.scrollTop(cy / container.height() * containerrealh);
				}).on('mouseup', function(){
					$(this).off('mousemove').off('mouseup');
				});
			});
			/*
			**  鼠标滚轮
			*/
			$('body').on('mousewheel', '.quick_view_container_list_in', function(event, delta){
				var desk = $(this), deskrealh = Math.ceil($(this).children().length / 4) * 60, scrollupdown;
				/*
				**  delta == -1   往下
				**  delta == 1    往上
				*/
				if(delta < 0){
					scrollupdown = desk.scrollTop() + 40 > deskrealh - desk.height() ? deskrealh - desk.height() : desk.scrollTop() + 40;
				}else{
					scrollupdown = desk.scrollTop() - 40 < 0 ? 0 : desk.scrollTop() - 40;
				}
				desk.stop(false, true).animate({
					scrollTop : scrollupdown
				}, 300);
				$(this).next('.scrollBar').stop(false, true).animate({
					top : scrollupdown / deskrealh * desk.height()
				}, 300);
			});
		},
		hide : function(){
			$('.quick_view_container').remove();
		}
	}
})();