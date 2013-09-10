/*
**  搜索栏
*/
HROS.searchbar = (function(){
	return {
		/*
		**  初始化
		*/
		init : function(){
			$('#pageletSearchInput').on('click', function(){
				return false;
			});
			$('#search-suggest .resultBox').on('click', 'li', function(){
				switch($(this).attr('type')){
					case 'app':
						HROS.window.create($(this).attr('realappid'), $(this).attr('type'));
						break;
					case 'widget':
						HROS.widget.create($(this).attr('realappid'), $(this).attr('type'));
						break;
				}
			});
			$('#search-suggest .openAppMarket a, #pageletSearchButton').on('click', function(){
				HROS.searchbar.openAppMarket($('#pageletSearchInput').val());
			});
			$('#pageletSearchInput').on('keydown', function(e){
				if(e.keyCode == '13'){
					HROS.searchbar.openAppMarket($(this).val());
				}
			});
		},
		get : function(){
			var oldSearchVal = '';
			searchFunc = setInterval(function(){
				var searchVal = $('#pageletSearchInput').val();
				if(searchVal != ''){
					$('#search-suggest').show();
					if(searchVal != oldSearchVal){
						oldSearchVal = searchVal;
						HROS.searchbar.getSuggest(searchVal);
					}
				}else{
					$('#search-suggest').hide();
				}
			}, 1000);
			HROS.searchbar.set();
		},
		set : function(){
			$('#search-bar').css({
				'left' : $('#nav-bar').offset().left + 27,
				'top' : $('#nav-bar').offset().top + 35
			}).removeClass('above').show();
			$('#search-suggest').css({
				'left' : $('#nav-bar').offset().left + 27,
				'top' : $('#nav-bar').offset().top + 68
			});
			$('#search-suggest .openAppMarket').css('top', $('#search-suggest .resultBox').height()).removeClass('above');
			$('#pageletSearchInput').focus();
			//如果导航条距离桌面底部小于50px，则向上显示
			if($('#nav-bar').offset().top + 35 + $('#search-suggest .resultBox').height() + 44 + 50 > $(window).height()){
				$('#search-bar').addClass('above').css('top', $('#nav-bar').offset().top - 35);
				if($('#search-suggest').is(':visible')){
					$('#search-suggest .openAppMarket').addClass('above');
					$('#search-suggest').css('top', $('#search-bar').offset().top - $('#search-suggest .resultBox').height());
					$('#search-suggest .openAppMarket').css('top', -44);
				}
			}
		},
		getSuggest : function(val){
			var apps = [];
			$(HROS.VAR.dock).each(function(){
				if(jQuery.inArray(this.type, ['app', 'widget']) >= 0){
					apps.push(this);
				}
			});
			for(var i = 1; i <= 5; i++){
				var desk = eval('HROS.VAR.desk' + i);
				$(desk).each(function(){
					if(jQuery.inArray(this.type, ['app', 'widget']) >= 0){
						apps.push(this);
					}
				});
			}
			$(HROS.VAR.folder).each(function(){
				$(this.apps).each(function(){
					if(jQuery.inArray(this.type, ['app', 'widget']) >= 0){
						apps.push(this);
					}
				});
			});
			var suggest = '';
			$(apps).each(function(){
				if(this.name.indexOf(val) >= 0){
					suggest += suggestTemp({
						'name' : this.name,
						'appid' : this.appid,
						'realappid' : this.realappid,
						'type' : this.type
					});
				}
			});
			$('#search-suggest .resultBox').html(suggest);
			HROS.searchbar.set();
		},
		openAppMarket : function(searchkey){
			if(searchkey != ''){
				HROS.window.createTemp({
					appid : 'hoorayos-yysc',
					title : '应用市场',
					url : 'sysapp/appmarket/index.php?searchkey=' + searchkey,
					width : 800,
					height : 484,
					isflash : false,
					refresh : true
				});
			}
			HROS.searchbar.hide();
		},
		hide : function(){
			if(typeof(searchFunc) != 'undefined'){
				clearInterval(searchFunc);
			}
			$('#search-bar, #search-suggest').hide();
			$('#pageletSearchInput').val('');
			$('#search-suggest .resultBox').html('');
		}
	}
})();