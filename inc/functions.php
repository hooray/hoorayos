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
	function remove_xss($val) {
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=@avascript:alert('XSS')>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
			// @ @ search for the hex values
			$val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
			// @ @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
		}
		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
		$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
		$found = true; // keep replacing as long as the previous round replaced something
		while ($found == true) {
			$val_before = $val;
			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
				$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
				if ($val_before == $val) {
				// no replacements were made, so exit the loop
				$found = false;
				}
			}
		}
		return $val;
	}
	/**
	 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 * @return string
	 */
	function rand_string($len = 6, $type = '', $addChars = ''){
		$str = '';
		switch($type){
			case 0:
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 1:
				$chars = str_repeat('0123456789', 3);
				break;
			case 2:
				$chars ='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
				break;
			case 3:
				$chars ='abcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 4:
				$chars = '们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借'.$addChars;
				break;
			default:
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
				break;
		}
		if($len > 10){//位数过长重复字符串一定次数
			$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
		}
		if($type != 4){
			$chars = str_shuffle($chars);
			$str = substr($chars, 0, $len);
		}else{
			// 中文随机字
			for($i = 0; $i < $len; $i++){
			  $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
			}
		}
		return $str;
	}
	//文件地址处理
	function getFileInfo($str, $mode){
		if($str == '' || is_null($str)){
			return '';
		}
		switch($mode){
			case 'path' : return dirname($str); break;
			case 'name' : return basename($str, '.'.end(explode('.', $str))); break;
			case 'ext' : return end(explode('.', $str)); break;
			case 'simg' : return getFileInfo($str, 'path').'/s_'.getFileInfo($str, 'name').'.jpg'; break;
		}
	}
	/**
	 * 字节格式化 把字节数格式为 B K M G T 描述的大小
	 * @return string
	 */
	function byte_format($size, $dec=2){
		$a = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
		$pos = 0;
		while($size >= 1024){
			$size /= 1024;
			$pos++;
		}
		return round($size, $dec).' '.$a[$pos];
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
			default: // 'l'
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
			$rs = $db->get('tb_member', array(
				'wallpaper_id',
				'wallpapertype',
				'wallpaperwebsite',
				'wallpaperstate'
			), array(
				'tbid' => session('member_id')
			));
			switch($rs['wallpaperstate']){
				case '1':
				case '2':
					$table = $rs['wallpaperstate'] == 1 ? 'tb_wallpaper' : 'tb_pwallpaper';
					$wallpaper = $db->get($table, array('url', 'width', 'height'), array(
						'tbid' => $rs['wallpaper_id']
					));
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
			$set = $db->get('tb_setting', array('wallpaper_id', 'wallpapertype'));
			$wallpaper = $db->get('tb_wallpaper', array('url', 'width', 'height'), array(
				'tbid' => $set['wallpaper_id']
			));
			$wallpaper_array = array(
				1,
				$wallpaper['url'],
				$set['wallpapertype'],
				$wallpaper['width'],
				$wallpaper['height']
			);
		}
		return implode('<{|}>', $wallpaper_array);
	}
	//获取窗口皮肤
	function getSkin(){
		global $db;
		if(checkLogin()){
			$skin = $db->get('tb_member', 'skin', array(
				'tbid' => session('member_id')
			));
		}else{
			$skin = $db->get('tb_setting', 'skin');
		}
		return $skin;
	}
	//获取应用码头位置
	function getDockPos(){
		global $db;
		if(checkLogin()){
			$dockpos = $db->get('tb_member', 'dockpos', array(
				'tbid' => session('member_id')
			));
		}else{
			$dockpos = $db->get('tb_setting', 'dockpos');
		}
		return $dockpos;
	}
	//获取图标排列方式
	function getAppXY(){
		global $db;
		if(checkLogin()){
			$appxy = $db->get('tb_member', 'appxy', array(
				'tbid' => session('member_id')
			));
		}else{
			$appxy = $db->get('tb_setting', 'appxy');
		}
		return $appxy;
	}
	//获取图标显示尺寸
	function getAppSize(){
		global $db;
		if(checkLogin()){
			$appsize = $db->get('tb_member', 'appsize', array(
				'tbid' => session('member_id')
			));
		}else{
			$appsize = $db->get('tb_setting', 'appsize');
		}
		return $appsize;
	}
	//获取默认桌面
	function getDesk(){
		global $db;
		if(checkLogin()){
			$desk = $db->get('tb_member', 'desk', array(
				'tbid' => session('member_id')
			));
		}else{
			$desk = $db->get('tb_setting', 'desk');
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
				$appid = $db->insert('tb_member_app', array(
					'icon' => $opt['icon'],
					'name' => $opt['name'],
					'width' => 600,
					'height' => 400,
					'type' => $opt['type'],
					'dt' => date('Y-m-d H:i:s'),
					'lastdt' => date('Y-m-d H:i:s'),
					'member_id' => session('member_id')
				));
				break;
			case 'pwindow':
			case 'pwidget':
				$appid = $db->insert('tb_member_app', array(
					'icon' => $opt['icon'],
					'name' => $opt['name'],
					'url' => $opt['url'],
					'type' => $opt['type'],
					'width' => $opt['width'],
					'height' => $opt['height'],
					'isresize' => $opt['isresize'],
					'isopenmax' => $opt['isopenmax'],
					'isflash' => $opt['isflash'],
					'dt' => date('Y-m-d H:i:s'),
					'lastdt' => date('Y-m-d H:i:s'),
					'member_id' => session('member_id')
				));
				break;
			default:
				//检查应用是否已安装
				if(!$db->has('tb_member_app', array(
					'AND' => array(
						'realid' => $opt['id'],
						'member_id' => session('member_id')
					)
				))){
					//查找应用信息
					$app = $db->get('tb_app', '*', array(
						'tbid' => $opt['id']
					));
					//在安装应用表里更新一条记录
					$appid = $db->insert('tb_member_app', array(
						'realid' => $opt['id'],
						'name' => $app['name'],
						'icon' => $app['icon'],
						'type' => $app['type'],
						'width' => $app['width'],
						'height' => $app['height'],
						'isresize' => $app['isresize'],
						'isopenmax' => $app['isopenmax'],
						'issetbar' => $app['issetbar'],
						'isflash' => $app['isflash'],
						'dt' => date('Y-m-d H:i:s'),
						'lastdt' => date('Y-m-d H:i:s'),
						'member_id' => session('member_id')
					));
					//更新使用人数
					$db->update('tb_app', array(
						'usecount[+]' => 1
					), array(
						'tbid' => $opt['id']
					));
				}
		}
		if(!empty($appid) && (int)$opt['desk'] >= 1 && (int)$opt['desk'] <= 5){
			//将安装应用表返回的id记录到用户表
			$rs = $db->get('tb_member', 'desk'.(int)$opt['desk'], array(
				'tbid' => session('member_id')
			));
			$db->update('tb_member', array(
				'desk'.(int)$opt['desk'] => $rs == '' ? $appid : $rs.','.$appid
			), array(
				'tbid' => session('member_id')
			));
		}
	}
	//删除应用
	function delApp($id){
		global $db;
		$member_app = $db->get('tb_member_app', array('realid', 'type', 'folder_id'), array(
			'AND' => array(
				'tbid' => $id,
				'member_id' => session('member_id')
			)
		));
		//如果是文件夹，则先删除文件夹内的应用
		if($member_app['type'] == 'folder'){
			$rs = $db->select('tb_member_app', 'tbid', array(
				'folder_id' => $id
			));
			foreach($rs as $v){
				delApp($v);
			}
		}else if($member_app['type'] == 'window' || $member_app['type'] == 'widget'){
			$db->update('tb_app', array(
				'usecount[-]' => 1
			), array(
				'tbid' => $member_app['realid']
			));
		}
		$member = $db->get('tb_member', array('dock', 'desk1', 'desk2', 'desk3', 'desk4', 'desk5'), array(
			'tbid' => session('member_id')
		));
		$data = array();
		if($member['dock'] != ''){
			$dockapp = explode(',', $member['dock']);
			foreach($dockapp as $k => $v){
				if($v == (int)$id){
					unset($dockapp[$k]);
					break;
				}
			}
			$data['dock'] = implode(',', $dockapp);
		}
		for($i = 1; $i <= 5; $i++){
			if($member['desk'.$i] != ''){
				$deskapp = explode(',', $member['desk'.$i]);
				foreach($deskapp as $k => $v){
					if($v == (int)$id){
						unset($deskapp[$k]);
						break;
					}
				}
				$data['desk'.$i] = implode(',', $deskapp);
			}
		}
		if($set != NULL){
			$db->update('tb_member', $data, array(
				'tbid' => session('member_id')
			));
		}
		$db->delete('tb_member_app', array(
			'AND' => array(
				'tbid' => $id,
				'member_id' => session('member_id')
			)
		));
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
		return $db->get('tb_member', 'type', array(
			'tbid' => session('member_id')
		)) == 1 ? true : false;
	}
	//验证是否有权限
	function checkPermissions($app_id){
		global $db;
		$isHavePermissions = false;
		$permission_id = $db->get('tb_member', 'permission_id', array(
			'tbid' => session('member_id')
		));
		if($permission_id != ''){
			$apps_id = $db->get('tb_permission', 'apps_id', array(
				'tbid' => $permission_id
			));
			if($apps_id != ''){
				$apps = explode(',', $apps_id);
				if(in_array($app_id, $apps)){
					$isHavePermissions = true;
				}
			}
		}
		return $isHavePermissions;
	}
?>