/*
**  透明遮罩层
**  当拖动应用、窗口等一切可拖动的对象时，会加载一个遮罩层
**  避免拖动时触发或选中一些不必要的操作，安全第一
*/
HROS.maskBox = (function(){
	return {
		desk: function(){
			if(!HROS.maskBoxCache.desk){
				HROS.maskBoxCache.desk = $('<div id="maskbox"></div>');
				$('body').append(HROS.maskBoxCache.desk);
			}
			return HROS.maskBoxCache.desk;
		},
		dock: function(){
			if(!HROS.maskBoxCache.dock){
				HROS.maskBoxCache.dock = $(
					'<div id="maskbox-dockdrap">'+
						'<div id="docktop" class="dock_drap_effect dock_drap_effect_top"></div>'+
						'<div id="dockleft" class="dock_drap_effect dock_drap_effect_left"></div>'+
						'<div id="dockright" class="dock_drap_effect dock_drap_effect_right"></div>'+
						'<div id="dockmask" class="dock_drap_mask">'+
							'<div class="dock_drop_region_top"><div class="text">拖放至顶部</div></div>'+
							'<div class="dock_drop_region_left"><div class="text">拖放至左侧</div></div>'+
							'<div class="dock_drop_region_right"><div class="text">拖放至右侧</div></div>'+
						'</div>'+
					'</div>'
				);
				$('body').append(HROS.maskBoxCache.dock);
			}
			return HROS.maskBoxCache.dock;
		},
		copyright: function(){
			if(!HROS.maskBoxCache.copyright){
				HROS.maskBoxCache.copyright = $('<div id="maskbox-copyright"></div>');
				$('body').append(HROS.maskBoxCache.copyright);
			}
			return HROS.maskBoxCache.copyright;
		}
	}
})();