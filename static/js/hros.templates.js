HROS.template = (function(){
	return {
		//检查模版缓存是否存在
		checkCache: function(path){
			return HROS.templateCache.hasOwnProperty(path) ? true : false;
		},
		//渲染模版文件
		renderFile: function(path, data){
			var html;
			if(HROS.template.checkCache(path)){
				html = HROS.templateCache[path](data);
			}else{
				$.ajax({
					type: 'GET',
					url: location.origin + '/static/template/' + path + '?' + new Date().getTime(),
					dataType: 'text',
					async: false
				}).done(function(cb){
					var render = template.compile(cb);
					html = render(data);
					HROS.templateCache[path] = render;
				});
			}
			return html;
		},
		//桌面应用
		app: function(data){
			return HROS.template.renderFile('app.art', data);
		},
		//桌面"添加应用"应用
		add: function(data){
			return HROS.template.renderFile('add.art', data);
		},
		//任务栏
		task: function(data){
			return HROS.template.renderFile('task.art', data);
		},
		//挂件应用
		widget: function(data){
			return HROS.template.renderFile('widget.art', data);
		},
		//窗口应用
		window: function(data){
			return HROS.template.renderFile('window.art', data);
		},
		//文件夹窗口
		folder: function(data){
			return HROS.template.renderFile('folder.art', data);
		},
		//文件夹预览
		folderView: function(data){
			return HROS.template.renderFile('folderView.art', data);
		},
		//文件下载
		fileDownload: function(data){
			return HROS.template.renderFile('fileDownload.art', data);
		},
		//搜索结果列表
		suggest: function(data){
			return HROS.template.renderFile('suggest.art', data);
		},
		//应用评分
		starDialog: function(data){
			return HROS.template.renderFile('starDialog.art', data);
		},
		//锁定
		lock: function(data){
			return HROS.template.renderFile('lock.art', data);
		}
	}
})();