/*
**  桌面
*/
HROS.deskTop = (function(){
	return {
		init : function(){
			//绑定浏览器resize事件
			$(window).on('resize', function(){
				HROS.deskTop.resize();
			});
			$('body').on('click', '#desktop', function(){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchbar.hide();
				HROS.startmenu.hide();
			}).on('contextmenu', '#desktop', function(e){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchbar.hide();
				HROS.startmenu.hide();
				var popupmenu = HROS.popupMenu.desk();
				var l = ($(window).width() - e.clientX) < popupmenu.width() ? (e.clientX - popupmenu.width()) : e.clientX;
				var t = ($(window).height() - e.clientY) < popupmenu.height() ? (e.clientY - popupmenu.height()) : e.clientY;
				popupmenu.css({
					left : l,
					top : t
				}).show();
				return false;
			});
		},
		/*
		**  处理浏览器改变大小后的事件
		*/
		resize : function(){
			if($('#desktop').is(':visible')){
				HROS.dock.setPos();
				//更新应用定位
				HROS.app.setPos();
				//更新窗口定位
				HROS.window.setPos();
				//更新文件夹预览定位
				HROS.folderView.setPos();
			}else{
				HROS.appmanage.resize();
			}
			HROS.wallpaper.set(false);
		},
		updateDefaultDesk : function(i){
			if(HROS.base.checkLogin()){
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=setDesk&desk=' + i
				});
			}
		}
	}
})();