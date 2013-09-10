/*
**  壁纸
*/
HROS.wallpaper = (function(){
	return {
		/*
		**	初始化
		*/
		init : function(){
			HROS.wallpaper.set();
		},
		/*
		**	获得壁纸
		**	通过ajax到后端获取壁纸信息，同时设置壁纸
		*/
		get : function(callback){
			$.ajax({
				type : 'POST',
				url : ajaxUrl,
				data : 'ac=getWallpaper',
				success : function(msg){
					var w = msg.split('<{|}>');
					HROS.CONFIG.wallpaperState = parseInt(w[0]);
					switch(HROS.CONFIG.wallpaperState){
						case 1:
						case 2:
							HROS.CONFIG.wallpaper = w[1];
							HROS.CONFIG.wallpaperType = w[2];
							HROS.CONFIG.wallpaperWidth = parseInt(w[3]);
							HROS.CONFIG.wallpaperHeight = parseInt(w[4]);
							break;
						case 3:
							HROS.CONFIG.wallpaper = w[1];
							break;
					}
					callback && callback();
				}
			});
		},
		/*
		**	设置壁纸
		**	平铺和居中可直接用css样式background解决
		**	而填充、适应和拉伸则需要进行模拟
		*/
		set : function(isreload){
			/*
			**  判断壁纸是否需要重新载入
			**  比如当浏览器尺寸改变时，只需更新壁纸，而无需重新载入
			*/
			var isreload = typeof(isreload) == 'undefined' ? true : isreload;
			if(isreload){
				$('#zoomWallpaperGrid').remove();
			}
			var w = $(window).width(), h = $(window).height();
			switch(HROS.CONFIG.wallpaperState){
				case 1:
				case 2:
					switch(HROS.CONFIG.wallpaperType){
						//平铺
						case 'pingpu':
							if(isreload){
								$('body').append('<div id="zoomWallpaperGrid" style="position:absolute;z-index:-10;top:0;left:0;height:100%;width:100%;background:url(' + HROS.CONFIG.wallpaper + ') repeat"></div>');
							}
							break;
						//居中
						case 'juzhong':
							if(isreload){
								$('body').append('<div id="zoomWallpaperGrid" style="position:absolute;z-index:-10;top:0;left:0;height:100%;width:100%;background:url(' + HROS.CONFIG.wallpaper + ') no-repeat 50% 50%"></div>');
							}
							break;
						//填充
						case 'tianchong':
							var t = (h - HROS.CONFIG.wallpaperHeight) / 2, l = (w - HROS.CONFIG.wallpaperWidth) / 2;
							if(isreload){
								$('body').append('<div id="zoomWallpaperGrid" style="position:absolute;z-index:-10;left:0;top:0;overflow:hidden;height:' + h + 'px;width:' + w + 'px"><img id="zoomWallpaper" style="position:absolute;height:' + HROS.CONFIG.wallpaperHeight + 'px;width:' + HROS.CONFIG.wallpaperWidth + 'px;top:' + t + 'px;left:' + l + 'px"><div style="position:absolute;height:' + h + 'px;width:' + w + 'px;background:#fff;opacity:0;filter:alpha(opacity=0)"></div></div>');
								$('#zoomWallpaper').attr('src', HROS.CONFIG.wallpaper).on('load', function(){
									$(this).show();
								});
							}else{
								$('#zoomWallpaperGrid, #zoomWallpaperGrid div').css({
									height : h + 'px',
									width : w + 'px'
								});
								$('#zoomWallpaper').css({
									top : t + 'px',
									left : l + 'px'
								});
							}
							break;
						//适应
						case 'shiying':
							var imgH, imgW, t, l;
							if(HROS.CONFIG.wallpaperHeight / HROS.CONFIG.wallpaperWidth > h / w){
								imgH = h;
								imgW = HROS.CONFIG.wallpaperWidth * (h / HROS.CONFIG.wallpaperHeight);
								t = 0;
								l = (w - imgW) / 2;
							}else if(HROS.CONFIG.wallpaperHeight / HROS.CONFIG.wallpaperWidth < h / w){
								imgW = w;
								imgH = HROS.CONFIG.wallpaperHeight * (w / HROS.CONFIG.wallpaperWidth);
								l = 0;
								t = (h - imgH) / 2;
							}else{
								imgH = HROS.CONFIG.wallpaperHeight;
								imgW = HROS.CONFIG.wallpaperWidth;
								t = l = 0;
							}
							if(isreload){
								$('body').append('<div id="zoomWallpaperGrid" style="position:absolute;z-index:-10;left:0;top:0;overflow:hidden;height:' + h + 'px;width:' + w + 'px"><img id="zoomWallpaper" style="position:absolute;height:' + imgH + 'px;width:' + imgW + 'px;top:' + t + 'px;left:' + l + 'px"><div style="position:absolute;height:' + h + 'px;width:' + w + 'px;background:#fff;opacity:0;filter:alpha(opacity=0)"></div></div>');
								$('#zoomWallpaper').attr('src', HROS.CONFIG.wallpaper).on('load', function(){
									$(this).show();
								});
							}else{
								$('#zoomWallpaperGrid, #zoomWallpaperGrid div').css({
									height : h + 'px',
									width : w + 'px'
								});
								$('#zoomWallpaper').css({
									height : imgH + 'px',
									width : imgW + 'px',
									top : t + 'px',
									left : l + 'px'
								});
							}
							break;
						//拉伸
						case 'lashen':
							if(isreload){
								$('body').append('<div id="zoomWallpaperGrid" style="position:absolute;z-index:-10;left:0;top:0;overflow:hidden;height:' + h + 'px;width:' + w + 'px"><img id="zoomWallpaper" style="position:absolute;height:' + h + 'px;width:' + w + 'px;top:0;left:0"><div style="position:absolute;height:' + h + 'px;width:' + w + 'px;background:#fff;opacity:0;filter:alpha(opacity=0)"></div></div>');
								$('#zoomWallpaper').attr('src', HROS.CONFIG.wallpaper).on('load', function(){
									$(this).show();
								});
							}else{
								$('#zoomWallpaperGrid').css({
									height : h + 'px',
									width : w + 'px'
								}).children('#zoomWallpaper, div').css({
									height : h + 'px',
									width : w + 'px'
								});
							}
							break;
					}
					break;
				case 3:
					if(isreload){
						$('body').append('<div id="zoomWallpaperGrid" style="position:absolute;z-index:-10;top:0;left:0;height:100%;width:100%;overflow:hidden"><div></div><iframe id="iframeWallpaper" frameborder="no" border="0" scrolling="no" class="iframeWallpaper" style="position:absolute;left:0;top:0;overflow:hidden;width:100%;height:100%" src="' + HROS.CONFIG.wallpaper + '"></iframe></div>');
					}
					break;
			}
		},
		/*
		**	更新壁纸
		**	通过ajax到后端进行更新，同时获得壁纸
		*/
		update : function(wallpaperstate, wallpapertype, wallpaper){
			function done(){
				HROS.wallpaper.get(function(){
					HROS.wallpaper.set();
				});
			}
			if(HROS.base.checkLogin()){
				$.ajax({
					type : 'POST',
					url : ajaxUrl,
					data : 'ac=setWallpaper&wpstate=' + wallpaperstate + '&wptype=' + wallpapertype + '&wp=' + wallpaper
				}).done(function(responseText){
					done();
				});
			}else{
				done();
			}
		}
	}
})();