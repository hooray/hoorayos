HROS.hotkey = (function(){
	return {
		init : function(){
			Mousetrap.bind(['backspace'], function(){
				return false;
			});
			//显示桌面（最小化所有窗口）
			Mousetrap.bind(['alt+d'], function(){
				HROS.window.hideAll();
				return false;
			});
			//显示全局视图
			Mousetrap.bind(['ctrl+up', 'command+up'], function(){
				HROS.appmanage.set();
				return false;
			});
			//调出查询栏
			Mousetrap.bind(['ctrl+f', 'command+f'], function(){
				HROS.searchbar.get();
				return false;
			});
			Mousetrap.bind(['ctrl+1', 'command+1'], function(){
				HROS.dock.switchDesk(1);
				return false;
			});
			Mousetrap.bind(['ctrl+2', 'command+2'], function(){
				HROS.dock.switchDesk(2);
				return false;
			});
			Mousetrap.bind(['ctrl+3', 'command+3'], function(){
				HROS.dock.switchDesk(3);
				return false;
			});
			Mousetrap.bind(['ctrl+4', 'command+4'], function(){
				HROS.dock.switchDesk(4);
				return false;
			});
			Mousetrap.bind(['ctrl+5', 'command+5'], function(){
				HROS.dock.switchDesk(5);
				return false;
			});
			Mousetrap.bind(['ctrl+left', 'command+left'], function(){
				if(parseInt(HROS.CONFIG.desk) - 1 < 1){
					HROS.dock.switchDesk(5);
				}else{
					HROS.dock.switchDesk(parseInt(HROS.CONFIG.desk) - 1);
				}
				return false;
			});
			Mousetrap.bind(['ctrl+right', 'command+right'], function(){
				if(parseInt(HROS.CONFIG.desk) + 1 > 5){
					HROS.dock.switchDesk(1);
				}else{
					HROS.dock.switchDesk(parseInt(HROS.CONFIG.desk) + 1);
				}
				return false;
			});
			//锁定
			Mousetrap.bind(['ctrl+l', 'command+l'], function(){
				HROS.lock.show();
				return false;
			});
		}
	}
})();