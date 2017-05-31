/*
**  桌面
*/
HROS.deskTop = (function(){
	return {
		init: function(){
			//绑定浏览器resize事件
			$(window).on('resize', function(){
				HROS.deskTop.resize();
			});
			$('body').on('click', '#desktop', function(){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchBar.hide();
				HROS.startMenu.hide();
			}).on('contextmenu', '#desktop', function(e){
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				HROS.searchBar.hide();
				HROS.startMenu.hide();
				HROS.popupMenu.desk(e);
				return false;
			});
		},
		/*
		**  处理浏览器改变大小后的事件
		*/
		resize: function(){
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
		updateDefaultDesk: function(i){
			if(HROS.base.checkLogin()){
				$.ajax({
					data: {
						ac: 'setDesk',
						desk: i
					}
				});
			}
		}
	}
})();