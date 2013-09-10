/*
**  开始菜单
*/
HROS.startmenu = (function(){
	return {
		/*
		**	初始化
		*/
		init : function(){
			$('#startmenu-container .startmenu-nick a').on('click', function(){
				HROS.navbar.openAccount();
			});
			$('#startmenu-container .startmenu-exit a').on('click', function(){
				HROS.base.logout();
			});
			$('#startmenu-container .startmenu-feedback').on('click', function(){
				HROS.window.createTemp({
					appid : 'hoorayos-feedback',
					title : '反馈',
					url : 'http://hoorayos.com/feedback.html',
					width : 700,
					height : 500,
					isflash : false
				});
			});
			$('#startmenu-container .startmenu a').on('click', function(){
				switch($(this).attr('class')){
					case 'help':
						HROS.base.help();
						break;
					case 'about':
						HROS.copyright.show();
						break;
				}
			});
		},
		show : function(){
			HROS.popupMenu.hide();
			HROS.folderView.hide();
			HROS.searchbar.hide();
			$('#startmenu-container').show();
			switch(HROS.CONFIG.dockPos){
				case 'top':
					$('#startmenu-container').css({
						top : 75,
						left : $('#dock-container').offset().left + 350
					});
					break;
				case 'left':
					var top = $('#dock-container').offset().top + $('#dock-container').height() - $('#startmenu-container').height() - 20;
					if($('#dock-container').offset().top + $('#dock-container').height() > $(window).height()){
						top = $(window).height() - $('#startmenu-container').height() - 20;
					}
					$('#startmenu-container').css({
						top : top,
						left : 75
					});
					break;
				case 'right':
					var top = $('#dock-container').offset().top + $('#dock-container').height() - $('#startmenu-container').height() - 20;
					if($('#dock-container').offset().top + $('#dock-container').height() > $(window).height()){
						top = $(window).height() - $('#startmenu-container').height() - 20;
					}
					var left = $(window).width() - $('#dock-container').width() - $('#startmenu-container').width();
					$('#startmenu-container').css({
						top : top,
						left : left
					});
					break;
			}
		},
		hide : function(){
			$('#startmenu-container').hide();
		}
	}
})();