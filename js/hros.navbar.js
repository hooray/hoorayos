/*
**  分页导航
*/
HROS.navbar = (function(){
	return {
		/*
		**  初始化
		*/
		init : function(){
			$('#nav-bar').css({
				'left' : $(window).width() / 2 - 105,
				'top' : 80
			}).show();
			HROS.navbar.hotkey();
			HROS.navbar.getAvatar();
			HROS.navbar.move();
		},
		/*
		**  快捷键
		*/
		hotkey : function(){
			Mousetrap.bind(['ctrl+up', 'command+up'], function(){
				HROS.appmanage.init();
				return false;
			});
			Mousetrap.bind(['ctrl+f', 'command+f'], function(){
				HROS.searchbar.get();
				return false;
			});
			Mousetrap.bind(['ctrl+1', 'command+1'], function(){
				HROS.navbar.switchDesk(1);
				return false;
			});
			Mousetrap.bind(['ctrl+2', 'command+2'], function(){
				HROS.navbar.switchDesk(2);
				return false;
			});
			Mousetrap.bind(['ctrl+3', 'command+3'], function(){
				HROS.navbar.switchDesk(3);
				return false;
			});
			Mousetrap.bind(['ctrl+4', 'command+4'], function(){
				HROS.navbar.switchDesk(4);
				return false;
			});
			Mousetrap.bind(['ctrl+5', 'command+5'], function(){
				HROS.navbar.switchDesk(5);
				return false;
			});
		},
		/*
		**  获取头像
		*/
		getAvatar : function(){
			$.ajax({
				type : 'POST',
				url : ajaxUrl,
				data : 'ac=getAvatar'
			}).done(function(msg){
				$('#nav-bar .indicator-header-img').attr('src', msg);
			});
		},
		/*
		**  账号设置窗口
		*/
		openAccount : function(){
			if(HROS.CONFIG.memberID != 0){
				HROS.window.createTemp({
					appid : 'zhsz',
					title : '账号设置',
					url : 'sysapp/account/index.php',
					width : 550,
					height : 580
				});
			}else{
				HROS.base.login();
			}
		},
		/*
		**  拖动
		*/
		move : function(){
			$('#nav-bar, #navbarHeaderImg, #nav-bar .nav-container a.indicator').on('mousedown', function(e){
				e.preventDefault();
				e.stopPropagation();
				HROS.popupMenu.hide();
				HROS.folderView.hide();
				if(e.button == 0 || e.button == 1){
					var x, y, cx, cy, dx, dy, lay, obj = $('#nav-bar'), thisobj = $(this);
					dx = cx = obj.offset().left;
					dy = cy = obj.offset().top;
					x = e.clientX - dx;
					y = e.clientY - dy;
					//绑定鼠标移动事件
					$(document).on('mousemove', function(e){
						lay = HROS.maskBox.desk();
						lay.show();
						cx = e.clientX - x <= 0 ? 0 : e.clientX - x > $(window).width() - 240 ? $(window).width() - 240 : e.clientX - x;
						cy = e.clientY - y <= 10 ? 10 : e.clientY - y > $(window).height() - 50 ? $(window).height() - 50 : e.clientY - y;
						obj.css({
							left : cx,
							top : cy
						});
					}).on('mouseup', function(){
						if(dx == cx && dy == cy){
							if(typeof(thisobj.attr('index')) !== 'undefined'){
								HROS.navbar.switchDesk(thisobj.attr('index'));
							}else if(thisobj.hasClass('indicator-manage')){
								HROS.appmanage.set();
							}else if(thisobj.hasClass('indicator-search')){
								HROS.searchbar.get();
							}else if(thisobj.hasClass('indicator-header')){
								HROS.navbar.openAccount();
							}
						}
						if(typeof(lay) !== 'undefined'){
							lay.hide();
						}
						$(this).off('mousemove').off('mouseup');
					});
				}
			}).on('click', function(e){
				e.stopPropagation();
			});
		},
		/*
		**  切换桌面
		*/
		switchDesk : function(deskNumber){
			//验证传入的桌面号是否为1-5的正整数
			var r = /^\+?[1-5]*$/;
			deskNumber = r.test(deskNumber) ? deskNumber : 1;
			var nav = $('#navContainer'), currindex = HROS.CONFIG.desk, switchindex = deskNumber,
			currleft = $('#desk-' + currindex).offset().left, switchleft = $('#desk-' + switchindex).offset().left;
			if(currindex != switchindex){
				if(!$('#desk-' + switchindex).hasClass('animated') && !$('#desk-' + currindex).hasClass('animated')){
					$('#desk-' + currindex).addClass('animated').animate({
						left : switchleft
					}, 500, 'easeInOutCirc', function(){
						$(this).removeClass('animated');
					});
					$('#desk-'+switchindex).addClass('animated').animate({
						left : currleft
					}, 500, 'easeInOutCirc', function(){
						$(this).removeClass('animated');
						nav.removeClass('nav-current-' + currindex).addClass('nav-current-' + switchindex);
						HROS.CONFIG.desk = switchindex;
					});
				}
			}else{
				nav.removeClass('nav-current-' + currindex).addClass('nav-current-' + switchindex);
			}
		}
	}
})();