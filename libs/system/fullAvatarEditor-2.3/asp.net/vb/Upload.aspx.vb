Imports Newtonsoft.Json
Partial Class Upload
	Inherits System.Web.UI.Page

	Protected Sub Page_Load(ByVal sender As Object, ByVal e As System.EventArgs)
		Dim UploadResult As Result = New Result()
		UploadResult.avatarUrls = New ArrayList()
		UploadResult.msg = "Failure!"
		UploadResult.sourceUrl = String.Empty
		UploadResult.success = False
		REM 取服务器时间+8位随机码作为部分文件名，确保文件名无重复。
		Dim filename As String = Now.ToString("yyyyMMddhhmmssff") & CreateRandomCode(8)
		
		REM 处理原始图片开始------------>

		REM 默认的 file 域名称是__source，可在插件配置参数中自定义。参数名：src_field_name
		Dim file As HttpPostedFile = Request.Files("__source")
		REM 如果在插件中定义可以上传原始图片的话，可在此处理，否则可以忽略。
		If file IsNot Nothing Then
			REM 原始图片的文件名，如果是本地或网络图片为原始文件名、如果是摄像头拍照则为 *FromWebcam.jpg
			Dim sourceFileName  As String  = file.FileName
			REM 原始文件的扩展名
			Dim sourceExtendName As String = sourceFileName.Substring(sourceFileName.LastIndexOf("."c)+1)
			REM filename = file.FileName;
			REM 当前头像基于原图的初始化参数（即只有上传原图时才会发送该数据），用于修改头像时保证界面的视图跟保存头像时一致，提升用户体验度。
			REM 修改头像时设置默认加载的原图url为当前原图url+该参数即可，可直接附加到原图url中储存，不影响图片呈现。
			Dim initParams As String = Request.Form("__initParams")
			UploadResult.sourceUrl = String.Format("upload/vb_source_{0}.{1}", filename, sourceExtendName)
			file.SaveAs(Server.MapPath(UploadResult.sourceUrl))
			UploadResult.sourceUrl = UploadResult.sourceUrl & initParams
			REM 可在此将 UploadResult.sourceUrl 储存到数据库，如果有需要的话
			REM Save to database...
		End If

		REM <------------处理原始图片结束

		REM 处理头像图片开始------------>

		REM 默认的 file 域的名称：__avatar1,2,3...，可在插件配置参数中自定义，参数名：avatar_field_names
		Dim avatars() As String = {"__avatar1", "__avatar2", "__avatar3"}
		Dim avatar_number as Integer = 1
		Dim avatar_length as Integer = UBound(avatars)
		For i = 0 To avatar_length
			file = Request.Files(avatars(i))
			Dim virtualPath As String = String.Format("upload/vb_avatar{0}_{1}.jpg", avatar_number, filename)
			UploadResult.avatarUrls.Add(virtualPath)
			file.SaveAs(Server.MapPath(virtualPath))
			avatar_number = avatar_number + 1
		Next

		REM <------------处理头像图片结束

		REM upload_url中传递的额外的参数，如果定义的method为get请将下面的Request.Form换为Request.QueryString
		UploadResult.userid		= Request.Form("userid")
		UploadResult.username	= Request.Form("username")

		UploadResult.success = True
		UploadResult.msg = "Success!"
		rem 返回图片的保存结果（返回内容为json字符串，可自行构造，该处使用Newtonsoft.Json构造）
		Response.Write(JsonConvert.SerializeObject(UploadResult))
	End Sub

	'生成指定长度的随机码。
	Private Function CreateRandomCode(ByVal Length As Integer) As String
		Randomize()
		Dim RandCode As String		= String.Empty
		Dim RandChar As String		= "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z"
		Dim RandCharArray As Array	= Split(RandChar, ",")
		Dim i As Integer
		For i = 1 To Length
			RandCode = RandCode & RandCharArray(Int(36 * Rnd()))
		Next
		CreateRandomCode = RandCode
	End Function

	REM 表示上传结果
	Private Structure Result
		REM 表示图片是否已上传成功。
		Public success As Boolean
		public userid As String
		public username As String
		REM 自定义的附加消息。
		Public msg As String
		REM 表示原始图片的保存地址。
		Public sourceUrl As String
		REM 表示所有头像图片的保存地址，该变量为一个数组。
		Public avatarUrls As ArrayList
	End Structure

End Class