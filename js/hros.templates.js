//桌面应用
var appbtnTemp = template.compile(
	'<li id="<%=id%>" class="appbtn" title="<%=title%>" appid="<%=appid%>" realappid="<%=realappid%>" type="<%=type%>">'+
		'<div><img src="<%=imgsrc%>" alt="<%=title%>"></div>'+
		'<span><%=title%></span>'+
	'</li>'
);
//桌面"添加应用"应用
var addbtnTemp = template.compile(
	'<li class="appbtn add">'+
		'<i class="addicon"></i>'+
		'<span>添加应用</span>'+
	'</li>'
);
//任务栏
var taskTemp = template.compile(
	'<a id="<%=id%>" class="task-item task-item-current" title="<%=title%>" appid="<%=appid%>" realappid="<%=realappid%>" type="<%=type%>">'+
		'<div class="task-item-icon">'+
			'<img src="<%=imgsrc%>" alt="<%=title%>">'+
		'</div>'+
		'<div class="task-item-txt"><%=title%></div>'+
	'</a>'
);
//小挂件
var widgetWindowTemp = template.compile(
	'<div id="<%=id%>" class="widget" appid="<%=appid%>" realappid="<%=realappid%>" type="<%=type%>" style="z-index:<%=zIndex%>;width:<%=width%>px;height:<%=height%>px;top:<%=top%>px;right:<%=right%>px">'+
		'<div class="move">'+
			'<a class="ha-close" href="javascript:;" title="关闭"></a>'+
			'<% if(issetbar){ %>'+
				'<a class="ha-star" href="javascript:;" title="评分"></a>'+
				'<a class="ha-share" href="javascript:;" title="分享"></a>'+
			'<% } %>'+
		'</div>'+
		'<div class="frame">'+
			'<iframe src="<%=url%>" frameborder="0" allowtransparency="true"></iframe>'+
		'</div>'+
	'</div>'
);
//应用窗口
var windowTemp = template.compile(
	'<div id="<%=id%>" class="window-container window-current<% if(isflash){ %> window-container-flash<% } %>" appid="<%=appid%>" realappid="<%=realappid%>" type="<%=type%>" state="show" style="<% if(isopenmax){ %>width:100%;height:100%;<% }else{ %>width:<%=width%>px;height:<%=height%>px;<% } %>z-index:<%=zIndex%>" ismax="<% if(isopenmax){ %>1<% }else{ %>0<% } %>">'+
		'<div style="height:100%">'+
			'<div class="title-bar">'+
				'<img class="icon" src="<%=imgsrc%>" alt="<%=title%>"><span class="title"><%=title%></span>'+
				'<div class="title-handle">'+
					'<a class="ha-hide" btn="hide" href="javascript:;" title="最小化"><b class="hide-b"></b></a>'+
					'<% if(istitlebar){ %>'+
						'<a class="ha-max" btn="max" href="javascript:;" title="最大化" <% if(isopenmax){ %>style="display:none"<% } %>><b class="max-b"></b></a>'+
						'<a class="ha-revert" btn="revert" href="javascript:;" title="还原" <% if(!isopenmax){ %>style="display:none"<% } %>><b class="revert-b"></b><b class="revert-t"></b></a>'+
					'<% } %>'+
					'<% if(istitlebarFullscreen){ %>'+
						'<a class="ha-fullscreen" btn="fullscreen" href="javascript:;" title="全屏">+</a>'+
					'<% } %>'+
					'<a class="ha-close" btn="close" href="javascript:;" title="关闭">×</a>'+
				'</div>'+
			'</div>'+
			'<div class="window-frame">'+
				'<% if(isflash){ %>'+
					'<div class="window-mask"><div class="maskbg"></div><div>运行中，点击恢复显示 :)</div></div>'+
				'<% }else{ %>'+
					'<div class="window-mask window-mask-noflash"></div>'+
				'<% } %>'+
				'<div class="window-loading"></div>'+
				'<iframe id="<%=id%>_iframe" frameborder="0" src="<%=url%>"></iframe>'+
			'</div>'+
			'<div class="set-bar"><div class="fr">'+
				'<% if(issetbar){ %>'+
					'<a class="btn share"><i class="icon icon104"></i><span class="btn-con">分享</span></a>'+
					'<a class="btn star"><i class="icon icon177"></i><span class="btn-con">评分</span></a>'+
					'<a class="btn detail"><i class="icon icon120"></i><span class="btn-con">详情</span></a>'+
				'<% } %>'+
				'<a class="btn refresh"><i class="icon icon158"></i><span class="btn-con">刷新</span></a>'+
			'</div></div>'+
		'</div>'+
		'<% if(isresize){ %>'+
			'<div class="window-resize window-resize-t" resize="t"></div>'+
			'<div class="window-resize window-resize-r" resize="r"></div>'+
			'<div class="window-resize window-resize-b" resize="b"></div>'+
			'<div class="window-resize window-resize-l" resize="l"></div>'+
			'<div class="window-resize window-resize-rt" resize="rt"></div>'+
			'<div class="window-resize window-resize-rb" resize="rb"></div>'+
			'<div class="window-resize window-resize-lt" resize="lt"></div>'+
			'<div class="window-resize window-resize-lb" resize="lb"></div>'+
		'<% } %>'+
	'</div>'
);
//文件夹窗口
var folderWindowTemp = template.compile(
	'<div id="<%=id%>" class="folder-window window-container window-current" appid="<%=appid%>" realappid="<%=realappid%>" type="<%=type%>" state="show" style="width:<%=width%>px;height:<%=height%>px;z-index:<%=zIndex%>">'+
		'<div style="height:100%">'+
			'<div class="title-bar">'+
				'<img class="icon" src="<%=imgsrc%>" alt="<%=title%>"><span class="title"><%=title%></span>'+
				'<div class="title-handle">'+
					'<a class="ha-hide" btn="hide" href="javascript:;" title="最小化"><b class="hide-b"></b></a>'+
					'<% if(istitlebar){ %>'+
						'<a class="ha-max" btn="max" href="javascript:;" title="最大化"><b class="max-b"></b></a>'+
						'<a class="ha-revert" btn="revert" href="javascript:;" title="还原" style="display:none"><b class="revert-b"></b><b class="revert-t"></b></a>'+
					'<% } %>'+
					'<a class="ha-close" btn="close" href="javascript:;" title="关闭">×</a>'+
				'</div>'+
			'</div>'+
			'<div class="window-frame">'+
				'<div class="folder_body"></div>'+
			'</div>'+
			'<div class="set-bar"><div class="fr">'+
				'<a class="btn refresh"><i class="icon icon158"></i><span class="btn-con">刷新</span></a>'+
			'</div></div>'+
		'</div>'+
		'<% if(isresize){ %>'+
			'<div class="window-resize window-resize-t" resize="t"></div>'+
			'<div class="window-resize window-resize-r" resize="r"></div>'+
			'<div class="window-resize window-resize-b" resize="b"></div>'+
			'<div class="window-resize window-resize-l" resize="l"></div>'+
			'<div class="window-resize window-resize-rt" resize="rt"></div>'+
			'<div class="window-resize window-resize-rb" resize="rb"></div>'+
			'<div class="window-resize window-resize-lt" resize="lt"></div>'+
			'<div class="window-resize window-resize-lb" resize="lb"></div>'+
		'<% } %>'+
	'</div>'
);
//文件夹预览
var folderViewTemp = template.compile(
	'<div id="<%=id%>" class="quick_view_container" appid="<%=appid%>" realappid="<%=realappid%>" style="top:<%=top%>px;left:<%=left%>px">'+
		'<div class="perfect_nine_box">'+
			'<div class="perfect_nine_t">'+
				'<div class="perfect_nine_t_m"></div>'+
			'</div>'+
			'<span class="perfect_nine_t_l"></span>'+
			'<span class="perfect_nine_t_r"></span>'+
			'<div class="perfect_nine_middle">'+
				'<span class="perfect_nine_m_l">'+
					'<div class="perfect_nine_m_l_t" style="top:0px;height:<%=mlt%>px"></div>'+
					'<div class="perfect_nine_m_l_m" style="top:<%=mlt%>px;height:20px;display:<% if(mlm){ %>block<% }else{ %>none<% } %>"></div>'+
					'<div class="perfect_nine_m_l_b" style="top:<% if(mlm){ %><%=mlt+20%><% }else{ %><%=mlt%><% } %>px;height:<%=mlb%>px"></div>'+
				'</span>'+
				'<span class="perfect_nine_m_r">'+
					'<div class="perfect_nine_m_r_t" style="top:0px;height:<%=mrt%>px"></div>'+
					'<div class="perfect_nine_m_r_m" style="top:<%=mrt%>px;height:20px;display:<% if(mrm){ %>block<% }else{ %>none<% } %>"></div>'+
					'<div class="perfect_nine_m_r_b" style="top:<% if(mrm){ %><%=mrt+20%><% }else{ %><%=mrt%><% } %>px;height:<%=mrb%>px"></div>'+
				'</span>'+
				'<div class="perfect_nine_context">'+
					'<div class="quick_view_container_control">'+
						'<a href="javascript:;" class="quick_view_container_open">打开</a>'+
					'</div>'+
					'<div class="quick_view_container_list" id="quick_view_container_list_<%=appid%>" realid="<%=appid%>">'+
						'<div class="quick_view_container_list_in" id="quick_view_container_list_in_<%=appid%>" style="height:<%=height%>px">'+
							'<%==apps%>'+
						'</div>'+
						'<div class="scrollBar"></div>'+
						'<div class="scrollBar_bgc"></div>'+
					'</div>'+
				'</div>'+
			'</div>'+
			'<div class="perfect_nine_b">'+
				'<div class="perfect_nine_b_m"></div>'+
			'</div>'+
			'<span class="perfect_nine_b_l"></span>'+
			'<span class="perfect_nine_b_r"></span>'+
		'</div>'+
	'</div>'
);
//文件下载
var fileDownloadTemp = template.compile(
	'<iframe src="filedownload.php?appid=<%=appid%>" frameborder="0" style="display:none"></iframe>'
);
//搜索结果列表
var suggestTemp = template.compile(
	'<li class="resultList" appid="<%=appid%>" realappid="<%=realappid%>" type="<%=type%>">'+
		'<a href="javascript:;"><div><%=name%></div></a>'+
	'</li>'
);
//新建&修改文件夹窗口
var editFolderDialogTemp = template.compile(
	'<div id="addfolder">'+
		'<a class="folderSelector"><img src="<%=src%>"></a>'+
		'<div class="folderNameTxt">请输入文件夹名称：</div>'+
		'<div class="folderInput"><input type="text" class="folderName" id="folderName" value="<%=name%>"></div>'+
		'<div class="folderNameError">文件夹名称不能只包含空字符</div>'+
		'<div class="fcDropdown">'+
			'<a class="fcDropdown_item" title="默认"><img class="fcDropdown_img" src="img/ui/folder_default.png"></a>'+
			'<a class="fcDropdown_item" title="文本"><img class="fcDropdown_img" src="img/ui/folder_doc.png"></a>'+
			'<a class="fcDropdown_item" title="游戏"><img class="fcDropdown_img" src="img/ui/folder_game.png"></a>'+
			'<a class="fcDropdown_item" title="生活"><img class="fcDropdown_img" src="img/ui/folder_life.png"></a>'+
			'<a class="fcDropdown_item" title="音乐"><img class="fcDropdown_img" src="img/ui/folder_music.png"></a>'+
			'<a class="fcDropdown_item" title="工具"><img class="fcDropdown_img" src="img/ui/folder_tool.png"></a>'+
			'<a class="fcDropdown_item" title="视频"><img class="fcDropdown_img" src="img/ui/folder_video.png"></a>'+
		'</div>'+
	'</div>'
);
//应用评分
var starDialogTemp = template.compile(
	'<div id="star" realappid="<%=realappid%>">'+
		'<div class="grade-box">'+
			'<div class="star-num"><%=point%></div>'+
			'<div class="star-box">'+
				'<div>打分：</div>'+
				'<i style="width:<%=realpoint%>%"></i>'+
				'<ul>'+
					'<li class="grade-1" num="1"><a href="javascript:;"><em>很不好用</em></a></li>'+
					'<li class="grade-2" num="2"><a href="javascript:;"><em>体验一般般</em></a></li>'+
					'<li class="grade-3" num="3"><a href="javascript:;"><em>比较好用</em></a></li>'+
					'<li class="grade-4" num="4"><a href="javascript:;"><em>很好用</em></a></li>'+
					'<li class="grade-5" num="5"><a href="javascript:;"><em>棒极了，推荐</em></a></li>'+
				'</ul>'+
			'</div>'+
		'</div>'+
	'</div>'
);
//分享
var shareDialogTemp = template.compile(
	'<div id="share">'+
		'<a class="tsina" href="http://service.weibo.com/share/share.php?title=<%=title%>&url=<%=url%>&searchPic=true&appkey=<%=sinaweiboAppkey%>" target="_blank">新浪微博</a>'+
		'<a class="tqq" href="http://v.t.qq.com/share/share.php?title=<%=title%>&url=<%=url%>&appkey=<%=tweiboAppkey%>" target="_blank">腾讯微博</a>'+
		'<a class="tsohu" href="http://t.sohu.com/third/post.jsp?url=<%=url%>&title=<%=title%>&content=utf-8" target="_blank">搜狐微博</a>'+
		'<a class="twangyi" href="http://t.163.com/article/user/checkLogin.do?info=<%=title%>+<%=url%>" target="_blank">网易微博</a>'+
		'<a class="qzone" href="http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<%=url%>&title=<%=title%>" target="_blank">QQ空间</a>'+
		'<a class="renren" href="http://share.renren.com/share/buttonshare.do?link=<%=url%>&title=<%=title%>" target="_blank">人人网</a>'+
		'<a class="baidu" href="http://cang.baidu.com/do/add?it=<%=title%>&iu=<%=url%>" target="_blank">百度收藏</a>'+
		'<a class="douban" href="http://shuo.douban.com/!service/share?image=&href=<%=url%>&name=<%=title%>" target="_blank">豆瓣</a>'+
	'</div>'
);
//锁定
var lockTemp = template.compile(
	'<div id="lock">'+
		'<div class="title">'+
			'<div class="time"></div>'+
			'<div class="week"></div>'+
		'</div>'+
		'<div id="lock-info">'+
			'<div class="img"><img src="<%=avatar%>" title="<%=username%>" alt="<%=username%>"></img></div>'+
			'<div class="text"><input type="password" class="mousetrap" id="lockpassword" placeholder="请输入解锁密码"></div>'+
			'<div class="text-tip"></div>'+
			'<div class="text"><input type="button" id="lockbtn" value="解 锁"></div>'+
		'</div>'+
		'<div class="tip">点击屏幕，开启解锁</div>'+
	'</div>'
);
//新手帮助提示
var helpTemp = template.compile(
	'<div id="help">'+
		'<a href="javascript:;" class="close" title="关闭新手帮助">×</a>'+
		'<div id="step1" class="step" step="1" style="position:relative;left:50%;top:50%;margin-left:-160px;margin-top:-60px;width:280px;height:100px">'+
			'<p style="text-align:center">'+
				'<span class="h2">欢迎使用HoorayOS</span>'+
				'<br>下面我会简单介绍下如何使用，以便你快速上手<br>'+
				'<a href="javascript:;" class="next">下一步</a>'+
			'</p>'+
		'</div>'+
		'<div id="step2" class="step" step="2" style="top:90px;left:140px;width:250px">'+
			'<b class="jt jt_left" style="left:-40px;top:20px"></b>'+
			'<p>'+
				'<span class="h1">①</span><span class="h2">桌面</span>'+
				'<br>可以在应用中心添加自己需要的应用到桌面<br>'+
				'<a href="javascript:;" class="next">下一步</a>'+
			'</p>'+
		'</div>'+
		'<div id="step3" class="step" step="3" style="top:90px;left:50%;margin-left:-125px;width:250px">'+
			'<b class="jt jt_top" style="top:-40px;left:30px"></b>'+
			'<p>'+
				'<span class="h1">②</span><span class="h2">应用码头</span>'+
				'<br>可以将桌面应用拖放到这<br>'+
				'<a href="javascript:;" class="next">下一步</a>'+
			'</p>'+
		'</div>'+
		'<div id="step4" class="step" step="4" style="top:130px;left:50%;margin-left:-125px;width:250px">'+
			'<b class="jt jt_top" style="top:-40px;left:30px"></b>'+
			'<p>'+
				'<span class="h1">③</span><span class="h2">翻页导航</span>'+
				'<br>可以快速切换当前桌面<br>'+
				'<a href="javascript:;" class="next">下一步</a>'+
			'</p>'+
		'</div>'+
		'<div id="step5" class="step" step="5" style="bottom:50px;left:50%;margin-left:-125px;width:250px">'+
			'<b class="jt jt_bottom" style="bottom:-40px;left:30px"></b>'+
			'<p>'+
				'<span class="h1">④</span><span class="h2">任务栏</span>'+
				'<br>这个就不用我介绍了吧 =)<br>'+
				'<a href="javascript:;" class="over">&nbsp;完&nbsp;成&nbsp;</a>'+
			'</p>'+
		'</div>'+
	'</div>'
);