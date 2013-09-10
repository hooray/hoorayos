HROS.uploadFile = (function(){
	var fileList = [];
	return {
		//获取上传文件对话框
		getDialog : function(){
			var tempData = [];
			for(var i = 0; i < fileList.length; i++){
				tempData.push({
					name : fileList[i].name,
					size : fileList[i].size < 1048576 ? Math.round(fileList[i].size / 1024) + ' kb' : Math.round(fileList[i].size / 1048576 * 100) / 100 + ' mb'
				});
			}
			var list = uploadFileDialogListTemp({
				list : tempData
			});
			//创建上传文件对话框，如果已打开则更新上传列表
			if(typeof($.dialog.list['uploadfile']) == 'object'){
				$('#uploadfile').html(list);
			}else{
				$.dialog({
					id : 'uploadfile',
					title : '上传文件',
					padding : 0,
					content : uploadFileDialogTemp({
						list : list
					}),
					button : [
						{
							name : '上传',
							callback : function(){
								//检测是否是拖拽文件到页面的操作
								if(fileList.length != 0){
									for(var i = 0, file; file = fileList[i]; i++){
										(function(file){
											var fd = new FormData();
											fd.append('xfile', file);
											var xhr = new XMLHttpRequest();
											if(xhr.upload){
												xhr.upload.addEventListener('progress', function(e){
													if(e.lengthComputable){
														$('#uploadfile .filelist:eq(' + file.index + ') .do').html('[&nbsp;--&nbsp;]');
														var loaded = Math.ceil(e.loaded / e.total * 100);
														$('#uploadfile .filelist:eq(' + file.index + ') .progress').css({
															width : loaded + '%'
														});
													}
												}, false);
												xhr.addEventListener('load', function(e){
													if(xhr.readyState == 4 && xhr.status == 200){
														var result = jQuery.parseJSON(e.target.responseText);
														if(result.error == null){
															$('#uploadfile .filelist:eq(' + file.index + ') .do').html('[&nbsp;√&nbsp;]');
														}else{
															$('#uploadfile .filelist:eq(' + file.index + ') .do').html('[&nbsp;×&nbsp;]').attr('title', result.error);
														}
													}
												}, false);
												xhr.open('post', 'ajax.php?ac=html5upload', true);
												xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
												xhr.send(fd);
											}
										})(file);
									}
									fileList = [];
								}
								return false;
							}
						},
						{
							name : '关闭',
							callback : function(){
								fileList = [];
								xhr = null;
							}
						}
					]
				});
			}
		},
		init : function(){
			//拖动上传文件
			if(window.FileReader){
				var oDragWrap = document.body;
				//拖进
				oDragWrap.addEventListener('dragenter', function(e){
					e.preventDefault();
				}, false);
				//拖离
				oDragWrap.addEventListener('dragleave', function(e){
					e.preventDefault();
				}, false);
				//拖来拖去，一定要注意dragover事件一定要清除默认事件，不然会无法触发后面的drop事件
				oDragWrap.addEventListener('dragover', function(e){
					e.preventDefault();
				}, false);
				//扔
				oDragWrap.addEventListener('drop', function(e){
					e.preventDefault();
					HROS.uploadFile.getDialog();
					getFiles(e);
					HROS.uploadFile.getDialog();
				}, false);
			}
			//普通上传
			$('body').on('change', '#uploadfilebtn', function(e){
				getFiles(e);
				HROS.uploadFile.getDialog();
			});
			//绑定删除事件
			$('body').on('click', '#uploadfile .del', function(){
				var list = $(this).parents('.filelist');
				var count = list.index();
				list.slideUp('slow', function(){
					$(this).remove();
				});
				//数据删除
				var tempList = [];
				for(var i = 0; i < fileList.length; i++){
					if(i != count){
						tempList.push(fileList[i]);
					}
				}
				fileList = tempList;
				refreshFiles();
				HROS.uploadFile.getDialog();
			});
			var getFiles = function(e){
				var files = e.target.files || e.dataTransfer.files;
				if(files.length != 0){
					var content = [];
					for(var i = 0; i < files.length; i++){
						if(files[i]['size'] > 104857600){
							content.push("\""+files[i]['name']+"\" 文件过大，请上传小于100MB的文件！")
						}else{
							fileList.push(files[i]);
						}
					}
					if(content != ''){
						contentHtml = content.join('<br>');
						$.dialog({
							padding : 10,
							content : contentHtml
						})
					}
				}
				refreshFiles();
			}
			var refreshFiles = function(){
				for(var i = 0; i < fileList.length; i++){
					fileList[i]['index'] = i;
				}
				console.log(fileList);
			}
		}
	}
})();