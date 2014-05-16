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
					case 'window':
						HROS.window.create($(this).attr('appid'), $(this).attr('type'));
						break;
					case 'widget':
						HROS.widget.create($(this).attr('appid'), $(this).attr('type'));
						break;
				}
			});
			$('#search-suggest .openAppMarket a, #pageletSearchButton').on('click', function(){
				HROS.searchbar.openAppMarket($('#pageletSearchInput').val());
			});
			$('#pageletSearchInput').on('keydown', function(e){
				if(e.keyCode == '13'){
					if($('#search-suggest .resultBox .resultList a.selected').length == 0 && $('#search-suggest > .resultList a.selected').length == 0){
						HROS.searchbar.openAppMarket($(this).val());
					}else{
						$('#search-suggest .resultList a.selected').click();
					}
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
					$('#search-bar').removeClass('above').addClass('above');
				}else{
					$('#search-suggest').hide();
					$('#search-bar').removeClass('above');
				}
			}, 1000);
			HROS.searchbar.set();
			Mousetrap.bind(['up'], function(){
				if($('#search-suggest .resultBox .resultList a.selected').length == 0 && $('#search-suggest > .resultList a.selected').length == 0){
					$('#search-suggest > .resultList:last a').addClass('selected');
				}else{
					if($('#search-suggest .resultBox .resultList:first a').hasClass('selected')){
						$('#search-suggest .resultList a').removeClass('selected');
					}else{
						if($('#search-suggest > .resultList a.selected').length != 0){
							var i = $('#search-suggest > .resultList a.selected').parent('.resultList').index();
							$('#search-suggest .resultList a').removeClass('selected');
							if(i > 1){
								$('#search-suggest > .resultList:eq(' + (i - 1) + ') a').addClass('selected');
							}else{
								$('#search-suggest .resultBox .resultList:last a').addClass('selected');
							}
						}else{
							var i = $('#search-suggest .resultBox .resultList a.selected').parent('.resultList').index();
							$('#search-suggest .resultList a').removeClass('selected');
							if(i > 0){
								$('#search-suggest .resultBox .resultList:eq(' + (i - 1) + ') a').addClass('selected');
							}
						}
					}
				}
				return false;
			});
			Mousetrap.bind(['down'], function(){
				if($('#search-suggest .resultBox .resultList a.selected').length == 0 && $('#search-suggest > .resultList a.selected').length == 0){
					if($('#search-suggest .resultBox .resultList').length == 0){
						$('#search-suggest > .resultList:first a').addClass('selected');
					}else{
						$('#search-suggest .resultBox .resultList:first a').addClass('selected');
					}
				}else{
					if($('#search-suggest > .resultList:last a').hasClass('selected')){
						$('#search-suggest .resultList a').removeClass('selected');
					}else{
						if($('#search-suggest .resultBox .resultList a.selected').length != 0){
							var i = $('#search-suggest .resultBox .resultList a.selected').parent('.resultList').index();
							$('#search-suggest .resultList a').removeClass('selected');
							if(i < $('#search-suggest .resultBox .resultList').length - 1){
								$('#search-suggest .resultBox .resultList:eq(' + (i + 1) + ') a').addClass('selected');
							}else{
								$('#search-suggest > .resultList:first a').addClass('selected');
							}
						}else{
							var i = $('#search-suggest > .resultList a.selected').parent('.resultList').index();
							$('#search-suggest .resultList a').removeClass('selected');
							if(i < $('#search-suggest > .resultList').length - 1){
								$('#search-suggest > .resultList:eq(' + (i + 1) + ') a').addClass('selected');
							}else{
								$('#search-suggest .resultBox .resultList:first a').addClass('selected');
							}
						}
					}
				}
				return false;
			});
			Mousetrap.bind(['backspace'], function(){});
		},
		set : function(){
			$('#search-bar').show();
			$('#search-suggest .resultList a').removeClass('selected');
			$('#pageletSearchInput').focus();
		},
		getSuggest : function(val){
			var apps = [];
			$(HROS.VAR.dock).each(function(){
				if($.inArray(this.type, ['window', 'widget']) >= 0){
					apps.push(this);
				}
			});
			for(var i = 1; i <= 5; i++){
				var desk = eval('HROS.VAR.desk' + i);
				$(desk).each(function(){
					if($.inArray(this.type, ['window', 'widget']) >= 0){
						apps.push(this);
					}
				});
			}
			$(HROS.VAR.folder).each(function(){
				$(this.apps).each(function(){
					if($.inArray(this.type, ['window', 'widget']) >= 0){
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
			if(suggest == ''){
				$('#search-suggest .resultBox').hide();
			}else{
				$('#search-suggest .resultBox').show();
			}
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
			$('#search-bar').removeClass('above');
			$('#search-bar, #search-suggest').hide();
			$('#pageletSearchInput').val('');
			$('#search-suggest .resultBox').html('');
			Mousetrap.unbind(['up', 'down']);
		}
	}
})();