/*
**  透明遮罩层
**  当拖动应用、窗口等一切可拖动的对象时，会加载一个遮罩层
**  避免拖动时触发或选中一些不必要的操作，安全第一
*/
HROS.maskBox = (function(){
	return {
		desk : function(){
			if(!TEMP.maskBoxDesk){
				TEMP.maskBoxDesk = $('<div id="maskbox"></div>');
				$('body').append(TEMP.maskBoxDesk);
			}
			return TEMP.maskBoxDesk;
		},
		dock : function(){
			if(!TEMP.maskBoxDock){
				TEMP.maskBoxDock = $('<div id="maskbox-dockdrap"><div id="docktop" class="dock_drap_effect dock_drap_effect_top"></div><div id="dockleft" class="dock_drap_effect dock_drap_effect_left"></div><div id="dockright" class="dock_drap_effect dock_drap_effect_right"></div><div id="dockmask" class="dock_drap_mask"><div class="dock_drop_region_top"><div class="text">拖放至顶部</div></div><div class="dock_drop_region_left"><div class="text">拖放至左侧</div></div><div class="dock_drop_region_right"><div class="text">拖放至右侧</div></div></div></div>');
				$('body').append(TEMP.maskBoxDock);
			}
			return TEMP.maskBoxDock;
		},
		copyright : function(){
			if(!TEMP.maskBoxCopyright){
				TEMP.maskBoxCopyright = $('<div id="maskbox-copyright"></div>');
				$('body').append(TEMP.maskBoxCopyright);
			}
			return TEMP.maskBoxCopyright;
		}
	}
})();