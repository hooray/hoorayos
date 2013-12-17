/*
**  应用布局格子
**  这篇文章里有简单说明格子的作用
**  http://www.cnblogs.com/hooray/archive/2012/03/23/2414410.html
*/
HROS.grid = (function(){
	return {
		getAppGrid : function(){
			var width = $('#desk-' + HROS.CONFIG.desk).width() - HROS.CONFIG.appButtonLeft;
			var height = $('#desk-' + HROS.CONFIG.desk).height() - HROS.CONFIG.appButtonTop;
			var top = HROS.CONFIG.appButtonTop;
			var left = HROS.CONFIG.appButtonLeft;
			var appGrid = [];
			var offsetTop = HROS.CONFIG.appSize == 's' ? 80 : 100;
			var offsetLeft = HROS.CONFIG.appSize == 's' ? 100 : 120;
			for(var i = 0; i < 10000; i++){
				appGrid.push({
					startY : top,
					endY : top + offsetTop,
					startX : left,
					endX : left + offsetLeft
				});
				if(HROS.CONFIG.appXY == 'x'){
					left += offsetLeft;
					if(left + 100 > width){
						top += offsetTop;
						left = HROS.CONFIG.appButtonLeft;
					}
				}else{
					top += offsetTop;
					if(top + 70 > height){
						top = HROS.CONFIG.appButtonTop;
						left += offsetLeft;
					}
				}
			}
			return appGrid;
		},
		searchAppGrid : function(x, y){
			var grid = HROS.grid.getAppGrid();
			var flag = null;
			for(var i = 0; i < grid.length; i++){
				if(x >= grid[i].startX && x <= grid[i].endX && y >= grid[i].startY && y <= grid[i].endY){
					flag = i;
				}
			}
			return flag;
		},
		searchAppGrid2 : function(x, y){
			var grid = HROS.grid.getAppGrid();
			var grid2 = [];
			for(var i = 0; i < grid.length; i++){
				var height = grid[i].endY - grid[i].startY;
				var width = grid[i].endX - grid[i].startX;
				var halfH = height / 2;
				var halfW = width / 2;
				if(HROS.CONFIG.appXY == 'x'){
					grid2.push({
						startY : grid[i].startY,
						endY : grid[i].startY + height,
						startX : grid[i].startX,
						endX : grid[i].startX + halfW
					},{
						startY : grid[i].startY,
						endY : grid[i].startY + height,
						startX : grid[i].startX + halfW,
						endX : grid[i].endX
					})
				}else{
					grid2.push({
						startY : grid[i].startY,
						endY : grid[i].startY + halfH,
						startX : grid[i].startX,
						endX : grid[i].startX + width
					},{
						startY : grid[i].startY + halfH,
						endY : grid[i].endY,
						startX : grid[i].startX,
						endX : grid[i].startX + width
					});
				}
			}
//			for(var i = 0; i < grid2.length / 100; i++){
//				if(HROS.CONFIG.appXY == 'x'){
//					$('.desktop-container').append('<div style="position:absolute;z-index:-2;width:'+(halfW-2)+'px;height:'+height+'px;line-height:100px;text-align:center;border:1px dotted #900;left:'+grid2[i].startX+'px;top:'+grid2[i].startY+'px"></div>');
//				}else{
//					$('.desktop-container').append('<div style="position:absolute;z-index:-2;width:'+width+'px;height:'+(halfH-2)+'px;line-height:100px;text-align:center;border:1px dotted #900;left:'+grid2[i].startX+'px;top:'+grid2[i].startY+'px"></div>');
//				}
//			}
			var flag = null;
			for(var i = 0; i < grid2.length; i++){
				if(x >= grid2[i].startX && x <= grid2[i].endX && y >= grid2[i].startY && y <= grid2[i].endY){
					flag = i;
				}
			}
			return flag;
		},
		getDockAppGrid : function(){
			var height = $('#dock-bar .dock-applist').height();
			var dockAppGrid = [];
			var left = 0;
			var top = 0;
			for(var i = 0; i < 7; i++){
				dockAppGrid.push({
					startY : top,
					endY : top + 62,
					startX : left,
					endX : left + 62
				});
				top += 62;
				if(top + 62 > height){
					top = 0;
					left += 62;
				}
			}
//			for(var i = 0; i < dockAppGrid.length; i++){
//				$('.dock-applist').append('<div style="position:absolute;z-index:-2;width:58px;height:58px;line-height:100px;text-align:center;border:1px dotted #900;left:'+dockAppGrid[i].startX+'px;top:'+dockAppGrid[i].startY+'px"></div>');
//			}
			return dockAppGrid;
		},
		searchDockAppGrid : function(x, y){
			var grid = HROS.grid.getDockAppGrid();
			var flag = null;
			for(var i = 0; i < grid.length; i++){
				if(x >= grid[i].startX && x <= grid[i].endX && y >= grid[i].startY && y <= grid[i].endY){
					flag = i;
				}
			}
			return flag;
		},
		searchDockAppGrid2 : function(x, y){
			var grid = HROS.grid.getDockAppGrid();
			var grid2 = [];
			for(var i = 0; i < grid.length; i++){
				var height = grid[i].endY - grid[i].startY;
				var width = grid[i].endX - grid[i].startX;
				var halfH = height / 2;
				var halfW = width / 2;
				if(HROS.CONFIG.dockpos == 'top'){
					grid2.push({
						startY : grid[i].startY,
						endY : grid[i].startY + height,
						startX : grid[i].startX,
						endX : grid[i].startX + halfW
					},{
						startY : grid[i].startY,
						endY : grid[i].startY + height,
						startX : grid[i].startX + halfW,
						endX : grid[i].endX
					})
				}else{
					grid2.push({
						startY : grid[i].startY,
						endY : grid[i].startY + halfH,
						startX : grid[i].startX,
						endX : grid[i].startX + width
					},{
						startY : grid[i].startY + halfH,
						endY : grid[i].endY,
						startX : grid[i].startX,
						endX : grid[i].startX + width
					});
				}
			}
//			for(var i = 0; i < grid2.length; i++){
//				if(HROS.CONFIG.dockpos == 'top'){
//					$('.dock-applist').append('<div style="position:absolute;z-index:-2;width:'+(halfW-2)+'px;height:'+height+'px;line-height:100px;text-align:center;border:1px dotted #900;left:'+grid2[i].startX+'px;top:'+grid2[i].startY+'px"></div>');
//				}else{
//					$('.dock-applist').append('<div style="position:absolute;z-index:-2;width:'+width+'px;height:'+(halfH-2)+'px;line-height:100px;text-align:center;border:1px dotted #900;left:'+grid2[i].startX+'px;top:'+grid2[i].startY+'px"></div>');
//				}
//			}
			var flag = null;
			for(var i = 0; i < grid2.length; i++){
				if(x >= grid2[i].startX && x <= grid2[i].endX && y >= grid2[i].startY && y <= grid2[i].endY){
					flag = i;
				}
			}
			return flag;
		},
		getFolderGrid : function(){
			var folderGrid = [];
			$('.quick_view_container, .folder-window:visible').each(function(){
				folderGrid.push({
					zIndex : $(this).css('z-index'),
					id : $(this).attr('appid'),
					startY : $(this).offset().top,
					endY : $(this).offset().top + $(this).height(),
					startX :  $(this).offset().left,
					endX :  $(this).offset().left +  $(this).width()
				});
			});
			folderGrid.sort(function(x, y){
				return y['zIndex'] - x['zIndex'];
			});
			return folderGrid;
		},
		searchFolderGrid : function(x, y){
			var folderGrid = HROS.grid.getFolderGrid();
			var flag = null;
			for(var i = 0; i < folderGrid.length; i++){
				if(x >= folderGrid[i].startX && x <= folderGrid[i].endX && y >= folderGrid[i].startY && y <= folderGrid[i].endY){
					flag = folderGrid[i]['id'];
				}
			}
			return flag;
		},
		getManageDockAppGrid : function(){
			var manageDockAppGrid = [];
			var left = 20;
			for(var i = 0; i < 10000; i++){
				manageDockAppGrid.push({
					startX : left,
					endX : left + 72
				});
				left += 72;
			}
			return manageDockAppGrid;
		},
		getManageDockAppGridOnMove : function(){
			var manageDockAppGrid = [], _left = 20;
			for(var i = 0; i < 10000; i++){
				manageDockAppGrid.push({
					startX : _left,
					endX : _left + (i == 0 ? 29 : 72)
				});
				_left += (i == 0 ? 29 : 72);
			}
			return manageDockAppGrid;
		},
		searchManageDockAppGrid : function(x){
			var grid = HROS.grid.getManageDockAppGridOnMove(), flags = 0;
			var returnInfo = 0;
			for(var i = 0; i < grid.length; i++){
				if(x >= grid[i].startX && x <= grid[i].endX){
					flags += 1;
				}
				if(flags === 1){
					returnInfo = i;
					break;
				}else{
					flags = 0;
				}
			}
			return returnInfo;
		},
		getManageAppGrid : function(){
			var manageAppGrid = [], _top = 0;
			for(var i = 0; i < 10000; i++){
				manageAppGrid.push({
					startY : _top,
					endY : _top + 40
				});
				_top += 40;
			}
			return manageAppGrid;
		},
		getManageAppGridOnMove : function(){
			var manageAppGrid = [], _top = 0;
			for(var i = 0; i < 10000; i++){
				manageAppGrid.push({
					startY : _top,
					endY : _top + (i == 0 ? 20 : 40)
				});
				_top += (i == 0 ? 20 : 40);
			}
			return manageAppGrid;
		},
		searchManageAppGrid : function(y){
			var grid = HROS.grid.getManageAppGridOnMove(), flags = 0;
			var returnInfo = 0;
			for(var i = 0; i < grid.length; i++){
				if(y >= grid[i].startY && y <= grid[i].endY){
					flags += 1;
				}
				if(flags === 1){
					returnInfo = i;
					break;
				}else{
					flags = 0;
				}
			}
			return returnInfo;
		}
	}
})();