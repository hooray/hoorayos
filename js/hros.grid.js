/*
**  应用布局格子
**  这篇文章里有简单说明格子的作用
**  http://hooray.cnblogs.com/p/3480087.html
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
					if(left + offsetLeft > width){
						top += offsetTop;
						left = HROS.CONFIG.appButtonLeft;
					}
				}else{
					top += offsetTop;
					if(top + offsetTop > height){
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
					break;
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
					});
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
			var flag = null;
			for(var i = 0; i < grid2.length; i++){
				if(x >= grid2[i].startX && x <= grid2[i].endX && y >= grid2[i].startY && y <= grid2[i].endY){
					flag = i;
					break;
				}
			}
			return flag;
		},
		getDockAppGrid : function(){
			var height = $('#dock-bar .dock-applist').height();
			var dockAppGrid = [];
			var top = 0;
			var left = 0;
			var offsetTop = 68;
			var offsetLeft = 68;
			for(var i = 0; i < 7; i++){
				dockAppGrid.push({
					startY : top,
					endY : top + offsetTop,
					startX : left,
					endX : left + offsetLeft
				});
				top += offsetTop;
				if(top + offsetTop > height){
					top = 0;
					left += offsetLeft;
				}
			}
			return dockAppGrid;
		},
		searchDockAppGrid : function(x, y){
			var grid = HROS.grid.getDockAppGrid();
			var flag = null;
			for(var i = 0; i < grid.length; i++){
				if(x >= grid[i].startX && x <= grid[i].endX && y >= grid[i].startY && y <= grid[i].endY){
					flag = i;
					break;
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
				if(HROS.CONFIG.dockPos == 'top'){
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
					});
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
			var flag = null;
			for(var i = 0; i < grid2.length; i++){
				if(x >= grid2[i].startX && x <= grid2[i].endX && y >= grid2[i].startY && y <= grid2[i].endY){
					flag = i;
					break;
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
					break;
				}
			}
			return flag;
		},
		getManageDockAppGrid : function(){
			var manageDockAppGrid = [];
			var left = 20;
			for(var i = 0; i < 100; i++){
				manageDockAppGrid.push({
					startX : left,
					endX : left + 70
				});
				left += 70;
			}
			return manageDockAppGrid;
		},
		searchManageDockAppGrid : function(x){
			var grid = HROS.grid.getManageDockAppGrid();
			var flag = null;
			for(var i = 0; i < grid.length; i++){
				if(x >= grid[i].startX && x <= grid[i].endX){
					flag = i;
					break;
				}
			}
			return flag;
		},
		searchManageDockAppGrid2 : function(x){
			var grid = HROS.grid.getManageDockAppGrid();
			var grid2 = [];
			for(var i = 0; i < grid.length; i++){
				var width = grid[i].endX - grid[i].startX;
				var halfW = width / 2;
				grid2.push({
					startX : grid[i].startX,
					endX : grid[i].startX + halfW
				},{
					startX : grid[i].startX + halfW,
					endX : grid[i].endX
				});
			}
			var flag = null;
			for(var i = 0; i < grid2.length; i++){
				if(x >= grid2[i].startX && x <= grid2[i].endX){
					flag = i;
					break;
				}
			}
			return flag;
		},
		getManageAppGrid : function(){
			var manageAppGrid = [];
			var top = 0;
			for(var i = 0; i < 10000; i++){
				manageAppGrid.push({
					startY : top,
					endY : top + 40
				});
				top += 40;
			}
			return manageAppGrid;
		},
		searchManageAppGrid : function(y){
			var grid = HROS.grid.getManageAppGrid();
			var flag = null;
			for(var i = 0; i < grid.length; i++){
				if(y >= grid[i].startY && y <= grid[i].endY){
					flag = i;
					break;
				}
			}
			return flag;
		},
		searchManageAppGrid2 : function(y){
			var grid = HROS.grid.getManageAppGrid();
			var grid2 = [];
			for(var i = 0; i < grid.length; i++){
				var height = grid[i].endY - grid[i].startY;
				var width = grid[i].endX - grid[i].startX;
				var halfH = height / 2;
				grid2.push({
					startY : grid[i].startY,
					endY : grid[i].startY + halfH
				},{
					startY : grid[i].startY + halfH,
					endY : grid[i].endY
				});
			}
			var flag = null;
			for(var i = 0; i < grid2.length; i++){
				if(y >= grid2[i].startY && y <= grid2[i].endY){
					flag = i;
					break;
				}
			}
			return flag;
		}
	}
})();