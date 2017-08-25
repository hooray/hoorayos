<%@ page session="false" pageEncoding="UTF-8" contentType="text/html; charset=UTF-8" %>
<%@ page import="java.io.*"%>
<%@ page import="java.util.*"%>
<%@ page import="java.text.*"%>
<%@ page import="org.apache.commons.fileupload.*"%>
<%@ page import="org.apache.commons.fileupload.disk.*"%>
<%@ page import="org.apache.commons.fileupload.servlet.*"%>
<%@ page import="org.apache.commons.fileupload.util.*"%>
<%@ page import="com.alibaba.fastjson.*"%>
<%
String contentType = request.getContentType();

if ( contentType.indexOf("multipart/form-data") >= 0 )
{
	Result result = new Result();
	result.avatarUrls = new ArrayList();
	result.success = false;
	result.msg = "Failure!";

	String userid;
	String username;

	FileItemFactory factory = new DiskFileItemFactory();
	ServletFileUpload upload = new ServletFileUpload(factory);
	FileItemIterator fileItems = upload.getItemIterator(request);
	//定义一个变量用以储存当前头像的序号
	int avatarNumber = 1;
	//取服务器时间+8位随机码作为部分文件名，确保文件名无重复。
	SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyyMMddHHmmssS"); 
	String fileName = simpleDateFormat.format(new Date());
	Random random = new Random();
	String randomCode = "";
	for ( int i = 0; i < 8; i++ )
	{
		randomCode += Integer.toString(random.nextInt(36), 36);
	}
	fileName = fileName + randomCode;
	//基于原图的初始化参数
	String initParams = "";
	BufferedInputStream	inputStream;
	BufferedOutputStream outputStream;
	//遍历表单域
	while( fileItems.hasNext() )
	{
		FileItemStream fileItem = fileItems.next();
		String fieldName = fileItem.getFieldName();
		//是否是原始图片 file 域的名称（默认的 file 域的名称是__source，可在插件配置参数中自定义。参数名：src_field_name）
		Boolean isSourcePic = fieldName.equals("__source");
		//当前头像基于原图的初始化参数（只有上传原图时才会发送该数据，且发送的方式为POST），用于修改头像时保证界面的视图跟保存头像时一致，提升用户体验度。
		//修改头像时设置默认加载的原图url为当前原图url+该参数即可，可直接附加到原图url中储存，不影响图片呈现。
		if ( fieldName.equals("__initParams") )
		{
			inputStream = new BufferedInputStream(fileItem.openStream());
			byte[] bytes = new byte [inputStream.available()];
			inputStream.read(bytes); 
			initParams = new String(bytes, "UTF-8");
			inputStream.close();
		}
		//如果是原始图片 file 域的名称或者以默认的头像域名称的部分“__avatar”打头(默认的头像域名称：__avatar1,2,3...，可在插件配置参数中自定义，参数名：avatar_field_names)
		else if ( isSourcePic || fieldName.startsWith("__avatar") )
		{
			String virtualPath = "/upload/jsp_avatar" + avatarNumber + "_" + fileName + ".jpg";
			//原始图片（默认的 file 域的名称是__source，可在插件配置参数中自定义。参数名：src_field_name）。
			if( isSourcePic )
			{
				//文件名，如果是本地或网络图片为原始文件名、如果是摄像头拍照则为 *FromWebcam.jpg
				String sourceFileName = fileItem.getName();	
				//原始文件的扩展名(不包含“.”)
				String sourceExtendName = sourceFileName.substring(sourceFileName.lastIndexOf('.') + 1);
				result.sourceUrl = virtualPath = String.format("/upload/jsp_source_%s.%s", fileName, sourceExtendName);
			}
			//头像图片（默认的 file 域的名称：__avatar1,2,3...，可在插件配置参数中自定义，参数名：avatar_field_names）。
			else
			{
				result.avatarUrls.add(virtualPath);
				avatarNumber++;
			}
			inputStream = new BufferedInputStream(fileItem.openStream());
			outputStream = new BufferedOutputStream(new FileOutputStream(application.getRealPath("/") + virtualPath.replace("/", "\\")));
			Streams.copy(inputStream, outputStream, true);
			inputStream.close();
            outputStream.flush();
            outputStream.close();
		}
		else
		{
			//注释① upload_url中传递的查询参数，如果定义的method为post请使用下面的代码，否则请删除或注释下面的代码块并使用注释②的代码
			inputStream = new BufferedInputStream(fileItem.openStream());
			byte[] bytes = new byte [inputStream.available()];
			inputStream.read(bytes); 
			inputStream.close();
			if (fieldName.equals("userid"))
			{
				result.userid = new String(bytes, "UTF-8");
			}
			else if (fieldName.equals("username"))
			{
				result.username = new String(bytes, "UTF-8");
			}
		}
	}
	//注释② upload_url中传递的查询参数，如果定义的method为get请使用下面注释的代码
	/*
	result.userid = request.getParameter("userid");
	result.username = request.getParameter("username");
	*/

	if ( result.sourceUrl != null )
	{
		result.sourceUrl += initParams;
	}
	result.success = true;
	result.msg = "Success!";
	/*
		To Do...可在此处处理储存事项
	*/
	//返回图片的保存结果（返回内容为json字符串，可自行构造，该处使用fastjson构造）
	out.println(JSON.toJSONString(result));
}
%>
<%!
/**
* 表示上传的结果。
*/
private class Result
{
	/**
	* 表示图片是否已上传成功。
	*/
	public Boolean success;
	public String userid;
	public String username;
	/**
	* 自定义的附加消息。
	*/
	public String msg;
	/**
	* 表示原始图片的保存地址。
	*/
	public String sourceUrl;
	/**
	* 表示所有头像图片的保存地址，该变量为一个数组。
	*/
	public ArrayList avatarUrls;
}
%>