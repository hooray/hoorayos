HROS.util = (function(){
	return {
		confirmExit : function(){
			return '你确定要离开 HoorayOS 么？';
		},
		confirmLockExit : function(){
			return 'HoorayOS 已锁定，执行此操作后将丢失本次锁屏状态下的信息，确认继续？';
		}
	}
})();