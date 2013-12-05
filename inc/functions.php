<?php
	function getconfig($key){
		global $_CONFIG;
		if(!isset($_CONFIG[$key])){
			return null;
		}else{
			return $_CONFIG[$key];
		}
	}
	// 参数解释
	// $string： 明文 或 密文
	// $operation：DECODE表示解密,其它表示加密
	// $key： 密匙
	// $expiry：密文有效期
	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
		// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
		// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
		// 当此值为 0 时，则不产生随机密钥
		$ckey_length = 4;
		// 密匙
		$key = md5($key != '' ? $key : getconfig('authkey'));
		// 密匙a会参与加解密
		$keya = md5(substr($key, 0, 16));
		// 密匙b会用来做数据完整性验证
		$keyb = md5(substr($key, 16, 16));
		// 密匙c用于变化生成的密文
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
		// 参与运算的密匙
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);
		// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
		// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);
		$result = '';
		$box = range(0, 255);
		$rndkey = array();
		// 产生密匙簿
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
		// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		// 核心加解密部分
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			// 从密匙簿得出密匙进行异或，再转成字符
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($operation == 'DECODE') {
			// substr($result, 0, 10) == 0 验证数据有效性
			// substr($result, 0, 10) - time() > 0 验证数据有效性
			// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
			// 验证数据有效性，请看未加密明文的格式
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
			// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
	function daddslashes($string, $force = 0, $strip = FALSE) {
		if(!get_magic_quotes_gpc() || $force) {
			if(is_array($string)) {
				//如果其为一个数组则循环执行此函数
				foreach($string as $key => $val) {
					$string[$key] = daddslashes($val, $force, $strip);
				}
			} else {
				//下面是一个三元操作符，如果$strip为true则执行stripslashes去掉反斜线字符，再执行addslashes
				//这里为什么要将$string先去掉反斜线再进行转义呢，因为有的时候$string有可能有两个反斜线，stripslashes是将多余的反斜线过滤掉
				$string = addslashes($strip ? stripslashes($string) : $string);
			}
		}
		return $string;
	}
	/**
	 * session管理函数
	 * 用法：http://doc.thinkphp.cn/manual/session.html
	 * @param string|array $name session名称 如果为数组则表示进行session设置
	 * @param mixed $value session值
	 * @return mixed
	 */
	function session($name, $value = ''){
		$prefix = getconfig('SESSION_PREFIX');
		if(is_array($name)){ // session初始化，在session_start之前调用
			if(isset($name['id'])){
				session_id($name['id']);
			}
			ini_set('session.auto_start', 0);
			if(isset($name['name']))            session_name($name['name']);
			if(isset($name['path']))            session_save_path($name['path']);
			if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
			if(isset($name['expire']))          ini_set('session.gc_maxlifetime', $name['expire']);
			if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid'] ? 1 : 0);
			if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies'] ? 1 : 0);
			if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
			if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
		}elseif('' === $value){
			if(0 === strpos($name, '[')){ // session操作
				if('[pause]' == $name){ // 暂停session
					session_write_close();
				}elseif('[start]' == $name){ // 启动session
					session_start();
				}elseif('[destroy]' == $name){ // 销毁session
					$_SESSION = array();
					session_unset();
					session_destroy();
				}elseif('[regenerate]' == $name){ // 重新生成id
					session_regenerate_id();
				}
			}elseif(0 === strpos($name, '?')){ // 检查session
				$name = substr($name, 1);
				if(strpos($name, '.')){ // 支持数组
					list($name1, $name2) = explode('.', $name);
					return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
				}else{
					return $prefix ? isset($_SESSION[$prefix][$name]) : isset($_SESSION[$name]);
				}
			}elseif(is_null($name)){ // 清空session
				if($prefix){
					unset($_SESSION[$prefix]);
				}else{
					$_SESSION = array();
				}
			}elseif($prefix){ // 获取session
				if(strpos($name, '.')){
					list($name1, $name2) = explode('.', $name);
					return isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;  
				}else{
					return isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;                
				}
			}else{
				if(strpos($name, '.')){
					list($name1, $name2) = explode('.', $name);
					return isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;  
				}else{
					return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
				}
			}
		}elseif(is_null($value)){ // 删除session
			if($prefix){
				unset($_SESSION[$prefix][$name]);
			}else{
				unset($_SESSION[$name]);
			}
		}else{ // 设置session
			if($prefix){
				if(!is_array($_SESSION[$prefix])){
					$_SESSION[$prefix] = array();
				}
				$_SESSION[$prefix][$name] = $value;
			}else{
				$_SESSION[$name] = $value;
			}
		}
	}
	/**
	 * Cookie 设置、获取、删除
	 * 用法：http://doc.thinkphp.cn/manual/cookie.html
	 * @param string $name cookie名称
	 * @param mixed $value cookie值
	 * @param mixed $options cookie参数
	 * @return mixed
	 */
	function cookie($name, $value = '', $option = NULL){
		// 默认设置
		$config = array(
			'prefix' => getconfig('COOKIE_PREFIX'), // cookie 名称前缀
			'expire' => getconfig('COOKIE_EXPIRE'), // cookie 保存时间
			'path' => getconfig('COOKIE_PATH'), // cookie 保存路径
			'domain' => getconfig('COOKIE_DOMAIN'), // cookie 有效域名
		);
		// 参数设置(会覆盖黙认设置)
		if(!is_null($option)){
			if(is_numeric($option)){
				$option = array('expire' => $option);
			}elseif(is_string($option)){
				parse_str($option, $option);
			}
			$config = array_merge($config, array_change_key_case($option));
		}
		// 清除指定前缀的所有cookie
		if(is_null($name)){
			if(empty($_COOKIE)){
				return;
			}
			// 要删除的cookie前缀，不指定则删除config设置的指定前缀
			$prefix = empty($value) ? $config['prefix'] : $value;
			if(!empty($prefix)){// 如果前缀为空字符串将不作处理直接返回
				foreach($_COOKIE as $key => $val){
					if(0 === stripos($key, $prefix)){
						setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
						unset($_COOKIE[$key]);
					}
				}
			}
			return;
		}
		$name = $config['prefix'].$name;
		if('' === $value){
			if(isset($_COOKIE[$name])){
				$value = $_COOKIE[$name];
				if(0 === strpos($value, 'hoorayos:')){
					$value  = substr($value, 6);
					return array_map('urldecode', json_decode(MAGIC_QUOTES_GPC ? stripslashes($value) : $value, true));
				}else{
					return $value;
				}
			}else{
				return null;
			}
		}else{
			if(is_null($value)){
				setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
				unset($_COOKIE[$name]); // 删除指定cookie
			}else{
				// 设置cookie
				if(is_array($value)){
					$value  = 'hoorayos:'.json_encode(array_map('urlencode', $value));
				}
				$expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
				setcookie($name, $value, $expire, $config['path'], $config['domain']);
				$_COOKIE[$name] = $value;
			}
		}
	}
	/**
	 * 浏览器友好的变量输出
	 * @param mixed $var 变量
	 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
	 * @param string $label 标签 默认为空
	 * @param boolean $strict 是否严谨 默认为true
	 * @return void|string
	 */
	function dump($var, $echo = true, $label = null, $strict = true){
		$label = ($label === null) ? '' : rtrim($label).' ';
		if(!$strict){
			if(ini_get('html_errors')){
				$output = print_r($var, true);
				$output = '<pre>'.$label.htmlspecialchars($output, ENT_QUOTES).'</pre>';
			}else{
				$output = $label.print_r($var, true);
			}
		}else{
			ob_start();
			var_dump($var);
			$output = ob_get_clean();
			if(!extension_loaded('xdebug')){
				$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
				$output = '<pre>'.$label.htmlspecialchars($output, ENT_QUOTES).'</pre>';
			}
		}
		if($echo){
			echo($output);
			return null;
		}else{
			return $output;
		}
	}
	/**
	 * URL重定向
	 * @param string $url 重定向的URL地址
	 * @param integer $time 重定向的等待时间（秒）
	 * @param string $msg 重定向前的提示信息
	 * @return void
	 */
	function redirect($url, $time=0, $msg=''){
		//多行URL地址支持
		$url = str_replace(array("\n", "\r"), '', $url);
		if(empty($msg)){
			$msg = "系统将在{$time}秒之后自动跳转到{$url}！";
		}
		if(!headers_sent()){
			// redirect
			if(0 === $time){
				header('Location: '.$url);
			}else{
				header("refresh:{$time};url={$url}");
				echo($msg);
			}
			exit();
		}else{
			$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
			if($time != 0){
				$str .= $msg;
			}
			exit($str);
		}
	}
	//文件地址处理
	function getFileInfo($str,$mode){
		if($str == '' || is_null($str)){
			return '';
		}
		switch($mode){
			case 'path' : return dirname($str); break;
			case 'name' : return basename($str,'.'.end(explode(".",$str))); break;
			case 'ext' : return end(explode(".",$str)); break;
			case 'simg' : return getFileInfo($str,"path")."/s_".getFileInfo($str,"name").".jpg"; break;
		}
	}
	//判断是否SSL协议
	function is_ssl(){
		if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
			return true;
		}elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])){
			return true;
		}
		return false;
	}
	//获取内网IP，0返回IP地址，1返回IPV4地址数字
	function getIp($type = 0){
		$type = $type ? 1 : 0;
		static $ip = NULL;
		if($ip !== NULL){
			return $ip[$type];
		}
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip = trim($arr[0]);
		}elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(isset($_SERVER['REMOTE_ADDR'])){
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = sprintf("%u",ip2long($ip));
		$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}
	//字符截断，支持中英文不乱码
	function cutstr($str,$len=0,$dot='...',$encoding='utf-8'){if(!is_numeric($len)){$len=intval($len);}if(!$len || strlen($str)<= $len){return $str;}$tempstr='';$str=str_replace(array('&', '"', '<', '>'),array('&', '"', '<', '>'),$str);if($encoding=='utf-8'){$n=$tn=$noc=0;while($n < strlen($str)){$t = ord($str[$n]);if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {$tn = 1; $n++; $noc++;} elseif (194 <= $t && $t <= 223) {$tn = 2; $n += 2; $noc += 2;} elseif (224 <= $t && $t < 239) {$tn = 3; $n += 3; $noc += 2;} elseif (240 <= $t && $t <= 247) {$tn = 4; $n += 4; $noc += 2;} elseif (248 <= $t && $t <= 251) {   $tn = 5; $n += 5; $noc += 2;} elseif ($t == 252 || $t == 253) {$tn = 6; $n += 6; $noc += 2;} else {$n++;}if($noc >= $len){break;}}if($noc > $len) {$n -= $tn;}$tempstr = substr($str, 0, $n);} elseif ($encoding == 'gbk') {for ($i=0; $i<$len; $i++) {$tempstr .= ord($str{$i}) > 127 ? $str{$i}.$str{++$i} : $str{$i};}}$tempstr = str_replace(array('&', '"', '<', '>'), array('&', '"', '<', '>'), $tempstr);return $tempstr.$dot;}
	//生成随机字符串
	function getRandStr($len = 4){
		$chars = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9");
		$charsLen = count($chars) - 1;
		shuffle($chars);
		$output = "";
		for($i=0; $i<$len; $i++){
			$output .= $chars[mt_rand(0, $charsLen)];
		}
		return $output;
	}
	//连续创建带层级的文件夹
	function recursive_mkdir($folder){
		$folder = preg_split( "/[\\\\\/]/" , $folder );
		$mkfolder = '';
		for($i=0; isset($folder[$i]); $i++){
			if(!strlen(trim($folder[$i]))){
				continue;
			}
			$mkfolder .= $folder[$i];
			if(!is_dir($mkfolder)){
				mkdir("$mkfolder",0777);
			}
			$mkfolder .= DIRECTORY_SEPARATOR;
		}
	}
	//创建缩略图
	function imageResize($source, $destination, $width = 0, $height = 0, $crop = false, $quality = 80) {
		$quality = $quality ? $quality : 80;
		$image = imagecreatefromstring($source);
		if($image){
			// Get dimensions
			$w = imagesx($image);
			$h = imagesy($image);
			if(($width && $w > $width) || ($height && $h > $height)){
				$ratio = $w / $h;
				if(($ratio >= 1 || $height == 0) && $width && !$crop){
					$new_height = $width / $ratio;
					$new_width = $width;
				}elseif($crop && $ratio <= ($width / $height)){
					$new_height = $width / $ratio;
					$new_width = $width;
				}else{
					$new_width = $height * $ratio;
					$new_height = $height;
				}
			}else{
				$new_width = $w;
				$new_height = $h;
			}
			$x_mid = $new_width * .5;  //horizontal middle
			$y_mid = $new_height * .5; //vertical middle
			// Resample
			error_log('height: ' . $new_height . ' - width: ' . $new_width);
			$new = imagecreatetruecolor(round($new_width), round($new_height));
			
			$c = imagecolorallocatealpha($new , 0 , 0 , 0 , 127);//拾取一个完全透明的颜色
			imagealphablending($new , false);//关闭混合模式，以便透明颜色能覆盖原画布
			imagefill($new , 0 , 0 , $c);//填充
			imagesavealpha($new , true);//设置保存PNG时保留透明通道信息
			
			imagecopyresampled($new, $image, 0, 0, 0, 0, $new_width, $new_height, $w, $h);
			// Crop
			if($crop){
				$crop = imagecreatetruecolor($width ? $width : $new_width, $height ? $height : $new_height);
				imagecopyresampled($crop, $new, 0, 0, ($x_mid - ($width * .5)), 0, $width, $height, $width, $height);
				//($y_mid - ($height * .5))
			}
			// Output
			// Enable interlancing [for progressive JPEG]
			imageinterlace($crop ? $crop : $new, true);

			$dext = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
			if($dext == ''){
				$dext = $ext;
				$destination .= '.' . $ext;
			}
			switch($dext){
				case 'jpeg':
				case 'jpg':
					imagejpeg($crop ? $crop : $new, $destination, $quality);
					break;
				case 'png':
					$pngQuality = ($quality - 100) / 11.111111;
					$pngQuality = round(abs($pngQuality));
					imagepng($crop ? $crop : $new, $destination, $pngQuality);
					break;
				case 'gif':
					imagegif($crop ? $crop : $new, $destination);
					break;
			}
			@imagedestroy($image);
			@imagedestroy($new);
			@imagedestroy($crop);
		}
	}
	
	/*****以下方法仅限该项目*****/
	
	//获取用户头像
	function getAvatar($memberid, $size = 's'){
		switch($size){
			case 's':
				$size = 24;
				break;
			case 'n':
				$size = 48;
				break;
			case 'l':
				$size = 120;
		}
		if(file_exists('uploads/member/'.$memberid.'/avatar/'.$size.'.jpg')){
			$avatar = 'uploads/member/'.$memberid.'/avatar/'.$size.'.jpg';
		}else{
			$avatar = 'img/ui/avatar_'.$size.'.jpg';
		}
		return $avatar;
	}
	//获取壁纸信息
	function getWallpaper(){
		global $db;
		if(checkLogin()){
			$rs = $db->select(0, 1, 'tb_member', 'wallpaper_id, wallpapertype, wallpaperwebsite, wallpaperstate', 'and tbid = '.session('member_id'));
			switch($rs['wallpaperstate']){
				case '1':
				case '2':
					$table = $rs['wallpaperstate'] == 1 ? 'tb_wallpaper' : 'tb_pwallpaper';
					$wallpaper = $db->select(0, 1, $table, 'url, width, height', 'and tbid = '.$rs['wallpaper_id']);
					$wallpaper_array = array(
						$rs['wallpaperstate'],
						$wallpaper['url'],
						$rs['wallpapertype'],
						$wallpaper['width'],
						$wallpaper['height']
					);
					break;
				case '3':
					$wallpaper_array = array(
						$rs['wallpaperstate'],
						$rs['wallpaperwebsite']
					);
					break;
			}
		}else{
			$wallpaper_array = array(1, 'img/wallpaper/wallpaper7.jpg', 'juzhong', 1920, 1080);
		}
		return implode('<{|}>', $wallpaper_array);
	}
	//获取窗口皮肤
	function getSkin(){
		global $db;
		if(checkLogin()){
			$member = $db->select(0, 1, 'tb_member', 'skin', 'and tbid = '.session('member_id'));
			$skin = $member['skin'];
		}else{
			$skin = 'default';
		}
		return $skin;
	}
	//获取应用码头位置
	function getDockPos(){
		global $db;
		if(checkLogin()){
			$member = $db->select(0, 1, 'tb_member', 'dockpos', 'and tbid = '.session('member_id'));
			$dockpos = $member['dockpos'];
		}else{
			$dockpos = 'top';
		}
		return $dockpos;
	}
	//获取图标排列方式
	function getAppXY(){
		global $db;
		if(checkLogin()){
			$member = $db->select(0, 1, 'tb_member', 'appxy', 'and tbid = '.session('member_id'));
			$appxy = $member['appxy'];
		}else{
			$appxy = 'x';
		}
		return $appxy;
	}
	//获取图标显示尺寸
	function getAppSize(){
		global $db;
		if(checkLogin()){
			$member = $db->select(0, 1, 'tb_member', 'appsize', 'and tbid = '.session('member_id'));
			$appsize = $member['appsize'];
		}else{
			$appsize = 'm';
		}
		return $appsize;
	}
	//获取默认桌面
	function getDesk(){
		global $db;
		if(checkLogin()){
			$member = $db->select(0, 1, 'tb_member', 'desk', 'and tbid = '.session('member_id'));
			$desk = $member['desk'];
		}else{
			$desk = 3;
		}
		return $desk;
	}
	//获取图片缩略图地址
	function getSimgSrc($string){
		return preg_replace("#(\w*\..*)$#U", "s_\${1}", $string);
	}
	//安装应用
	function addApp($opt){
		global $db;
		switch($opt['type']){
			case 'folder':
				$set = array(
					'icon = "'.$opt['icon'].'"',
					'name = "'.$opt['name'].'"',
					'width = 650',
					'height = 400',
					'type = "'.$opt['type'].'"',
					'dt = now()',
					'lastdt = now()',
					'member_id = '.session('member_id')
				);
				$appid = $db->insert(0, 2, 'tb_member_app', $set);
				break;
			case 'papp':
			case 'pwidget':
				$set = array(
					'icon = "'.$opt['icon'].'"',
					'name = "'.$opt['name'].'"',
					'url = "'.$opt['url'].'"',
					'type = "'.$opt['type'].'"',
					'width = '.(int)$opt['width'],
					'height = '.(int)$opt['height'],
					'isresize = '.(int)$opt['isresize'],
					'isopenmax = '.(int)$opt['isopenmax'],
					'isflash = '.(int)$opt['isflash'],
					'dt = now()',
					'lastdt = now()',
					'member_id = '.session('member_id')
				);
				$appid = $db->insert(0, 2, 'tb_member_app', $set);
				break;
			default:
				//检查应用是否已安装
				$count = $db->select(0, 2, 'tb_member_app', '*', 'and realid = '.(int)$opt['id'].' and member_id = '.session('member_id'));
				if($count == 0){
					//查找应用信息
					$app = $db->select(0, 1, 'tb_app', '*', 'and tbid = '.(int)$opt['id']);
					//在安装应用表里更新一条记录
					$set = array(
						'realid = '.$opt['id'],
						'name = "'.$app['name'].'"',
						'icon = "'.$app['icon'].'"',
						'url = "'.$app['url'].'"',
						'type = "'.$app['type'].'"',
						'width = '.(int)$app['width'],
						'height = '.(int)$app['height'],
						'isresize = '.(int)$app['isresize'],
						'isopenmax = '.(int)$app['isopenmax'],
						'issetbar = '.(int)$app['issetbar'],
						'isflash = '.(int)$app['isflash'],
						'dt = now()',
						'lastdt = now()',
						'member_id = '.session('member_id')
					);
					$appid = $db->insert(0, 2, 'tb_member_app', $set);
					//更新使用人数
					$db->update(0, 0, 'tb_app', 'usecount = usecount + 1', 'and tbid = '.(int)$opt['id']);
				}
		}
		if(!empty($appid) && (int)$opt['desk'] >= 1 && (int)$opt['desk'] <= 5){
			//将安装应用表返回的id记录到用户表
			$rs = $db->select(0, 1, 'tb_member', 'desk'.(int)$opt['desk'], 'and tbid='.session('member_id'));
			$deskapp = $rs['desk'.(int)$opt['desk']] == '' ? $appid : $rs['desk'.(int)$opt['desk']].','.$appid;
			$db->update(0, 0, 'tb_member', 'desk'.(int)$opt['desk'].'="'.$deskapp.'"', 'and tbid='.session('member_id'));
		}
	}
	//删除应用
	function delApp($id){
		global $db;
		$member_app = $db->select(0, 1, 'tb_member_app', 'realid, type, folder_id', 'and tbid = '.(int)$id.' and member_id = '.session('member_id'));
		//如果不是文件夹，则直接删除，反之先删除文件夹内的应用，再删除文件夹
		switch($member_app['type']){
			case 'folder':
				$rs = $db->select(0, 0, 'tb_member_app', 'tbid', 'and folder_id = '.(int)$id);
				if($rs != NULL){
					foreach($rs as $v){
						delApp($v['tbid']);
					}
				}
				delAppStr((int)$id);
				break;
			case 'app':
			case 'widget':
				delAppStr((int)$id);
				$db->update(0, 0, 'tb_app', 'usecount = usecount - 1', 'and tbid = '.$member_app['realid']);
				break;
			case 'papp':
			case 'pwidget':
				delAppStr((int)$id);
				break;
		}
	}
	function delAppStr($id){
		global $db;
		$rs = $db->select(0, 1, 'tb_member', 'dock, desk1, desk2, desk3, desk4, desk5', 'and tbid = '.session('member_id'));
		$flag = false;
		$set = '';
		if($rs['dock'] != ''){
			$dockapp = explode(',', $rs['dock']);
			foreach($dockapp as $k => $v){
				if($v == (int)$id){
					$flag = true;
					unset($dockapp[$k]);
					break;
				}
			}
			$set .= 'dock = "'.implode(',', $dockapp).'"';
		}else{
			$set .= 'dock = ""';
		}
		for($i = 1; $i <= 5; $i++){
			if($rs['desk'.$i] != ''){
				$deskapp = explode(',', $rs['desk'.$i]);
				foreach($deskapp as $k => $v){
					if($v == (int)$id){
						$flag = true;
						unset($deskapp[$k]);
						break;
					}
				}
				$set .= ',desk'.$i.' = "'.implode(',', $deskapp).'"';
			}else{
				$set .= ',desk'.$i.' = ""';
			}
		}
		if($flag){
			$db->update(0, 0, 'tb_member', $set, 'and tbid = '.session('member_id'));
		}
		$db->delete(0, 0, 'tb_member_app', 'and tbid = '.(int)$id.' and member_id = '.session('member_id'));
	}
	//强制格式化appid，如：'10,13,,17,4,6,'，格式化后：'10,13,17,4,6'
	function formatAppidArray($arr){
		foreach($arr as $k => $v){
			if($v == ''){
				unset($arr[$k]);
			}
		}
		return $arr;
	}
	//验证是否登入
	function checkLogin(){
		return session('?member_id') == NULL || session('member_id') == 0 ? false : true;
	}
	//验证是否为管理员
	function checkAdmin(){
		global $db;
		$user = $db->select(0, 1, 'tb_member', 'type', 'and tbid = '.session('member_id'));
		return $user['type'] == 1 ? true : false;
	}
	//验证是否有权限
	function checkPermissions($app_id){
		global $db;
		$isHavePermissions = false;
		$user = $db->select(0, 1, 'tb_member', 'permission_id', 'and tbid = '.session('member_id'));
		if($user['permission_id'] != ''){
			$permission = $db->select(0, 1, 'tb_permission', 'apps_id', 'and tbid = '.$user['permission_id']);
			if($permission['apps_id'] != ''){
				$apps = explode(',', $permission['apps_id']);
				if(in_array($app_id, $apps)){
					$isHavePermissions = true;
				}
			}
		}
		return $isHavePermissions;
	}
?>