<?php
/**
 * PHP SDK for douban.com (using OAuth2)
 * 
 * @author lazypeople <hfutming@gmail.com>
 */
class OAuthException extends Exception {
	// pass
}

/**
 * 豆瓣 OAuth 认证类(OAuth2)
 *
 * 授权机制说明请大家参考豆瓣开放平台文档：{@link http://developers.douban.com/wiki/?title=api_v2}
 *
 * @package lazypeople
 * @author lazypeople
 * @version 1.0
 */

class DoubanOAuthV2{
	/**
	 * @ignore
	 */
	public $client_id;
	/**
	 * @ignore
	 */
	public $client_secret;
	/**
	 * @ignore
	 */
	public $access_token;
	/**
	 * @ignore
	 */
	public $refresh_token;
	/**
	 * Contains the last HTTP status code returned. 
	 *
	 * @ignore
	 */
	public $http_code;
	/**
	 * Contains the last API call.
	 *
	 * @ignore
	 */
	public $url;
	/**
	 * Set up the API root URL.
	 *
	 * @ignore
	 */
	public $host = "";
	/**
	 * Set timeout default.
	 *
	 * @ignore
	 */
	public $timeout = 30;
	/**
	 * Set connect timeout.
	 *
	 * @ignore
	 */
	public $connecttimeout = 30;
	/**
	 * Verify SSL Cert.
	 *
	 * @ignore
	 */
	public $ssl_verifypeer = FALSE;
	/**
	 * Respons format.
	 *
	 * @ignore
	 */
	public $format = 'json';
	/**
	 * Decode returned json data.
	 *
	 * @ignore
	 */
	public $decode_json = TRUE;
	/**
	 * Contains the last HTTP headers returned.
	 *
	 * @ignore
	 */
	public $http_info;
	/**
	 * Set the useragnet.
	 *
	 * @ignore
	 */
	public $useragent = 'douban T OAuth2 v0.1';

	/**
	 * print the debug info
	 *
	 * @ignore
	 */
	public $debug = FALSE;

	/**
	 * boundary of multipart
	 * @ignore
	 */
	public static $boundary = '';

	/**
	 * Set API URLS
	 */
	/**
	 * @ignore
	 */
	function accessTokenURL()  { return 'https://www.douban.com/service/auth2/token'; }
	/**
	 * @ignore
	 */
	function authorizeURL()    { return ''; }

	/**
	 * construct WeiboOAuth object
	 */
	function __construct($client_id, $client_secret, $access_token = NULL, $refresh_token = NULL) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->access_token = $access_token;
		$this->refresh_token = $refresh_token;
	}

	function getAuthorizeURL( $url, $response_type = 'code', $state = NULL, $display = NULL ) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['redirect_uri'] = $url;
		$params['response_type'] = $response_type;
		$params['state'] = $state;
		$params['display'] = $display;
		return $this->authorizeURL() . "?" . http_build_query($params);
	}

	function getAccessToken( $type = 'code', $keys ) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['client_secret'] = $this->client_secret;
		if ( $type === 'token' ) {
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = $keys['refresh_token'];
		} elseif ( $type === 'code' ) {
			$params['grant_type'] = 'authorization_code';
			$params['code'] = $keys['code'];
			$params['redirect_uri'] = $keys['redirect_uri'];
		} elseif ( $type === 'password' ) {
			$params['grant_type'] = 'password';
			$params['username'] = $keys['username'];
			$params['password'] = $keys['password'];
		} else {
			throw new OAuthException("wrong auth type");
		}

		$response = $this->oAuthRequest($this->accessTokenURL(), 'POST', $params);
		$token = json_decode($response, true);
		if ( is_array($token) && !isset($token['error']) ) {
			$this->access_token = $token['access_token'];
			$this->refresh_token = $token['refresh_token'];
		} else {
			throw new OAuthException("get access token failed." . $token['error']);
		}
		return $token;
	}

	/**
	 * 解析 signed_request
	 *
	 * @param string $signed_request 应用框架在加载iframe时会通过向Canvas URL post的参数signed_request
	 *
	 * @return array
	 */
	function parseSignedRequest($signed_request) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
		$sig = self::base64decode($encoded_sig) ;
		$data = json_decode(self::base64decode($payload), true);
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') return '-1';
		$expected_sig = hash_hmac('sha256', $payload, $this->client_secret, true);
		return ($sig !== $expected_sig)? '-2':$data;
	}

	/**
	 * @ignore
	 */
	function base64decode($str) {
		return base64_decode(strtr($str.str_repeat('=', (4 - strlen($str) % 4)), '-_', '+/'));
	}

	/**
	 * 读取jssdk授权信息，用于和jssdk的同步登录
	 *
	 * @return array 成功返回array('access_token'=>'value', 'refresh_token'=>'value'); 失败返回false
	 */
	function getTokenFromJSSDK() {
		$key = "weibojs_" . $this->client_id;
		if ( isset($_COOKIE[$key]) && $cookie = $_COOKIE[$key] ) {
			parse_str($cookie, $token);
			if ( isset($token['access_token']) && isset($token['refresh_token']) ) {
				$this->access_token = $token['access_token'];
				$this->refresh_token = $token['refresh_token'];
				return $token;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * 从数组中读取access_token和refresh_token
	 * 常用于从Session或Cookie中读取token，或通过Session/Cookie中是否存有token判断登录状态。
	 *
	 * @param array $arr 存有access_token和secret_token的数组
	 * @return array 成功返回array('access_token'=>'value', 'refresh_token'=>'value'); 失败返回false
	 */
	function getTokenFromArray( $arr ) {
		if (isset($arr['access_token']) && $arr['access_token']) {
			$token = array();
			$this->access_token = $token['access_token'] = $arr['access_token'];
			if (isset($arr['refresh_token']) && $arr['refresh_token']) {
				$this->refresh_token = $token['refresh_token'] = $arr['refresh_token'];
			}

			return $token;
		} else {
			return false;
		}
	}

	/**
	 * GET wrappwer for oAuthRequest.
	 *
	 * @return mixed
	 */
	function get($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * POST wreapper for oAuthRequest.
	 *
	 * @return mixed
	 */
	function post($url, $parameters = array(), $multi = false) {
		$response = $this->oAuthRequest($url, 'POST', $parameters, $multi );
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * DELTE wrapper for oAuthReqeust.
	 *
	 * @return mixed
	 */
	function delete($url, $parameters = array()) {
		$response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}

	/**
	 * Format and sign an OAuth / API request
	 *
	 * @return string
	 * @ignore
	 */
	function oAuthRequest($url, $method, $parameters, $multi = false) {

		if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
	}

	switch ($method) {
		case 'GET':
			$url = $url . '?' . http_build_query($parameters);
			return $this->http($url, 'GET');
		default:
			$headers = array();
			if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
				$body = http_build_query($parameters);
			} else {
				$body = self::build_http_query_multi($parameters);
				$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
			}
			return $this->http($url, $method, $body, $headers);
	}
	}

	/**
	 * Make an HTTP request
	 *
	 * @return string API results
	 * @ignore
	 */
	function http($url, $method, $postfields = NULL, $headers = array()) {
		$this->http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ( isset($this->access_token) && $this->access_token )
			$headers[] = "Authorization: OAuth2 ".$this->access_token;

		$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;

		if ($this->debug) {
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo '=====info====='."\r\n";
			print_r( curl_getinfo($ci) );

			echo '=====$response====='."\r\n";
			print_r( $response );
		}
		curl_close ($ci);
		return $response;
	}

	/**
	 * Get the header info to store.
	 *
	 * @return int
	 * @ignore
	 */
	function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	/**
	 * @ignore
	 */
	public static function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
}

class Douban_Tclientv2{
	/**
	* 构造函数
	*/
	function __construct( $akey, $skey, $access_token = NULL, $refresh_token = NULL)
	{
		$this->oauth = new DoubanOAuthV2( $akey, $skey, $access_token = NULL,$refresh_token = NULL);		
	}

	//图书API2 @link http://developers.douban.com/wiki/?title=book_v2

     /**
     * 获取图书信息
     *
     * 对应API：{@link https://api.douban.com/v2/book/:id}
     * @param int bookid
     */
     function book_information($bookid)
     {
         $request_url = "https://api.douban.com/v2/book/".$bookid;
         $result = $this->oauth->get($request_url);
         return $result;
     }
     
     /**
     * 根据isbn获取图书信息
     *
     * 对应API {@link https://api.douban.com/v2/book/isbn/}
     * @param int bookid
     */
     function book_info_bysibn($isbn)
     {
     	$request_url = "https://api.douban.com/v2/book/isbn/".$isbn;
     	return $this->oauth->get($request_url);
     }

     /**
     * 搜索图书
     *
     * 对应API {@link https://api.douban.com/v2/book/search}
     * @param string q,string tag 查询的关键字或者标签，两者必传其一
     * @param int start 取结果的offset 默认为0
     * @param int count 取结果的条数
     */
     function book_search($q,$tag,$start = 0,$count = NULL)
     {
     	$request_url = "https://api.douban.com/v2/book/search";
     	$params['q'] = $q;
     	$params['tag'] = $tag;
     	$params['start'] = $start;
     	$params['count'] = $count;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     *
     * 某个图书中标记最多的标签
     * @link https://api.douban.com/v2/book/:id/tags
     * @param int bookid
     */
     function book_mosttag($bookid)
     {
     	$request_url = "https://api.douban.com/v2/book/".$bookid."/tags";
     	return $this->oauth->get($request_url);
     }

     /**
     *
     * 发表新评论
     * @link https://api.douban.com/v2/book/reviews
     * @param int book 评论所针对的book id 必传
     * @param string title 评论头 必传
     * @param string content 内容 必传
     * @param rating 打分 可选参数
     */

     function book_commit($bookid,$title,$content,$rating = NULL)
     {
     	$request_url = "https://api.douban.com/v2/book/reviews";
     	$params['book'] = $bookid;
     	$params['title'] = $title;
     	$params['$content'] = $content;
     	$params['rating'] = $rating;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     *
     * 修改评论
     * @link https://api.douban.com/v2/book/review/:id
     * @param int id 评论id 必传
     * @param string title 评论头 必传
     * @param string content 内容 必传
     * @param rating 打分 可选参数
     */
     function book_edit_commit($id,$title,$content,$rating)
     {
     	$request_url = "https://api.douban.com/v2/book/review/".$id;
     	$params['title'] = $title;
     	$params['content'] = $content;
     	$params['rating'] = $rating;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     *
     * 删除评论
     * @link https://api.douban.com/v2/book/review/:id
     * @param int id 评论id 必传
     */
     function book_delete_commit($id)
     {
     	$request_url = "https://api.douban.com/v2/book/review/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     *
     * 用户对图书的所有标签
     * @link https://api.douban.com/v2/book/user_tags/:id
     * @param int id 评论id 必传
     */
     function book_get_alltags($id)
     {
     	$request_url = "https://api.douban.com/v2/book/user_tags/".$id;
     	return $this->oauth->get($request_url);
     }

     //电影部分
     /**
     * 获取电影信息
     * @link https://api.douban.com/v2/movie/:id
     * @param int id
     */
     function movies_info($id)
     {
     	$request_url = "https://api.douban.com/v2/movie/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 根据imdb号获取电影信息
     * @link https://api.douban.com/v2/movie/imdb/:name
     * @param int imdb
     */
     function movies_imdb($imdb)
     {
     	$request_url = "https://api.douban.com/v2/movie/imdb/".$imdb;
     	return $this->oauth->get($request_url);
     }

     /**
     * 搜索电影
     * @link https://api.douban.com/v2/movie/search
     * @param string q 关键字
     * @param string tag
     * @param int start 取结果的offset
     * @param int count 
     */
     function movies_search($q,$tag,$start = 0,$count = NULL)
     {
     	$request_url = "https://api.douban.com/v2/movie/search";
     	$params['q'] = $q;
     	$params['tag'] = $tag;
     	$params['start'] = $start;
     	$params['count'] = $count;
     	return $this->oauth($request_url,$params);
     }

     /**
     * 某个电影中标记最多的标签
     * @link https://api.douban.com/v2/movie/:id/tags
     * @param int id
     */
     function movies_mosttag($id)
     {
     	$request_url = "https://api.douban.com/v2/movie/".$id."tags";
     	return $this->oauth($request_url);
     }

     /**
     * 发表新评论
     * @link https://api.douban.com/v2/movie/reviews
     * @param movie 评论所针对的movie id 必填
     * @param title 评论头 必填
     * @param content 评论内容 必填
     * @param rating 评分
     */
     function movies_new_commit($id,$title,$content,$rating = NULL)
     {
     	$request_url = "https://api.douban.com/v2/movie/reviews";
     	$params['movie'] = $movie;
     	$params['title'] = $title;
     	$params['content'] = $content;
     	$params['rating'] = $rating;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 修改评论
     * @link https://api.douban.com/v2/movie/review/:id
     * @param movie 评论所针对的movie id 必填
     * @param title 评论头 必填
     * @param content 评论内容 必填
     * @param rating 评分
     */
     function movies_edit_commit($id,$title,$content,$rating = NULL)
     {
     	$request_url = "https://api.douban.com/v2/movie/review/".$id;
     	$params['title'] = $title;
     	$params['content'] = $content;
     	$params['rating'] = $rating;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除评论
     * @link https://api.douban.com/v2/movie/review/:id
     * @param int id
     */
     function movies_delete_commit($id)
     {
     	$request_url = "https://api.douban.com/v2/movie/review/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 用户对电影的所有标签
     * @link https://api.douban.com/v2/movie/user_tags/:id
     * @param int id
     */
     function movies_getalltags($id)
     {
     	$request_url = "https://api.douban.com/v2/movie/user_tags/".$id;
     	return $this->oauth($request_url);
     }

     //音乐开始
     /**
     * 获取音乐信息
     * @link https://api.douban.com/v2/music/:id
     * @param int id
     */
     function music_info($id)
     {
     	$request_url = "https://api.douban.com/v2/music/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 搜索音乐
     * @link https://api.douban.com/v2/music/search
     * @param string q 关键字
     * @param string tag
     * @param int start 取结果的offset
     * @param int count 
     */
     function music_search($q,$tag,$start = 0,$count = NULL)
     {
     	$request_url = "https://api.douban.com/v2/music/search";
     	$params['q'] = $q;
     	$params['tag'] = $tag;
     	$params['start'] = $start;
     	$params['count'] = $count;
     	return $this->oauth($request_url,$params);
     }

     /**
     * 某个电影中标记最多的标签
     * @link https://api.douban.com/v2/music/:id/tags
     * @param int id
     */
     function music_mosttag($id)
     {
     	$request_url = "https://api.douban.com/v2/music/".$id."tags";
     	return $this->oauth($request_url);
     }

     /**
     * 发表新评论
     * @link https://api.douban.com/v2/music/reviews
     * @param music 评论所针对的music id 必填
     * @param title 评论头 必填
     * @param content 评论内容 必填
     * @param rating 评分
     */
     function music_new_commit($id,$title,$content,$rating = NULL)
     {
     	$request_url = "https://api.douban.com/v2/music/reviews";
     	$params['movie'] = $movie;
     	$params['title'] = $title;
     	$params['content'] = $content;
     	$params['rating'] = $rating;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 修改评论
     * @link https://api.douban.com/v2/music/review/:id
     * @param movie 评论所针对的movie id 必填
     * @param title 评论头 必填
     * @param content 评论内容 必填
     * @param rating 评分
     */
     function music_edit_commit($id,$title,$content,$rating = NULL)
     {
     	$request_url = "https://api.douban.com/v2/music/review/".$id;
     	$params['title'] = $title;
     	$params['content'] = $content;
     	$params['rating'] = $rating;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除评论
     * @link https://api.douban.com/v2/music/review/:id
     * @param int id
     */
     function music_delete_commit($id)
     {
     	$request_url = "https://api.douban.com/v2/music/review/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 用户对音乐的所有标签
     * @link https://api.douban.com/v2/music/user_tags/:id
     * @param int id
     */
     function music_getalltags($id)
     {
     	$request_url = "https://api.douban.com/v2/music/user_tags/".$id;
     	return $this->oauth($request_url);
     }

     //豆瓣同城 V2 开始
     /**
     * 获取活动
     * @link http://api.douban.com/v2/event/:id
     * @param int $id
     */
     function event_getbyid($id)
     {
     	$request_url = "http://api.douban.com/v2/event/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取参加活动的用户
     * @link http://api.douban.com/v2/event/:id/participants
     * @param int $id
     */
     function event_users($id)
     {
     	$request_url = "http://api.douban.com/v2/event/".$id."/participants";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取活动感兴趣的用户
     * @link http://api.douban.com/v2/event/:id/wishers
     * @param int $id
     */
     function event_wisher($id)
     {
     	$request_url = "http://api.douban.com/v2/event/".$id."/wishers";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取用户创建的活动
     * @link http://api.douban.com/v2/event/user_created/:id
     * @param int $id
     */
     function event_user_created($id)
     {
     	$request_url = "http://api.douban.com/v2/event/user_created/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取用户参加的活动
     * @link http://api.douban.com/v2/event/user_participated/:id
     * @param int $id
     */
     function event_user_participated($id)
     {
     	$request_url = "http://api.douban.com/v2/event/user_participated/".$id;
     	return $this->oauth->get($request_url);
     }


     /**
     * 获取用户感兴趣的活动
     * @link http://api.douban.com/v2/event/user_wished/:id
     * @param int $id
     */
     function event_user_wished($id)
     {
     	$request_url = "http://api.douban.com/v2/event/user_wished/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取活动列表
     * @link http://api.douban.com/v2/event/list
     * @param null
     */
     function event_list()
     {
        return $this->oauth->get("http://api.douban.com/v2/event/list");
     }


     /**
     * 获取城市
     * @link http://api.douban.com/v2/loc/:id
     * @param int $cityid
     */
     function event_city($cityid)
     {
     	$request_url = "http://api.douban.com/v2/loc/".$cityid;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取城市列表
     * @link http://api.douban.com/v2/loc/list
     * @param null
     */
     function event_city_lists()
     {
     	return $this->oauth->get("http://api.douban.com/v2/loc/list");
     }


     /**
     * 参加活动
     * @link https://api.douban.com/v2/event/:id/participants
     * @param int $eventid
     * @param date $participate_date 
     */
     function event_participants($eventid,$participate_date = NULL)
     {
     	$request_url = "https://api.douban.com/v2/event/".$eventid."/participants";
     	$params['participate_date'] = $participate_date;
     	return $this->oauth->post($request_url,$params);
     }


     /**
     * 不参加活动
     * @link https://api.douban.com/v2/event/:id/participants
     * @param int $eventid
     */
     function event_noparticipants($eventid)
     {
     	$request_url = "https://api.douban.com/v2/event/".$eventid."/participants";
     	return $this->oauth->delete($request_url);
     }

     /**
     * 对活动感兴趣
     * @link https://api.douban.com/v2/event/:id/wishers
     * @param int $eventid
     */
     function event_wisher22($eventid)
     {
     	$request_url = "https://api.douban.com/v2/event/".$eventid."/wishers";
     	return $this->oauth->post($request_url);
     }

     /**
     * 对活动不感兴趣
     * @link https://api.douban.com/v2/event/:id/wishers
     * @param int $eventid
     */
     function event_nowisher($eventid)
     {
     	$request_url = "https://api.douban.com/v2/event/".$eventid."/wishers";
     	return $this->oauth->delete($request_url);
     }




     //start 豆瓣广播 Api V2
     /**
     * 发送一条广播
     * @link https://api.douban.com/shuo/v2/statuses/
     * @param string $text
     * @param string $image
     * @param mixed $attachments attachments是一个json array格式的字符串， array里面的元素称为物, 目前每条广播只支持单个物，物是每条广播表述的行为中的那个宾语
     * @description 具体参见{@link http://developers.douban.com/wiki/?title=shuo_v2}
     */
     function shuo_push($text,$image = null,$attachments = null)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/";
     	$params['text'] = $text;
     	if($image != NULL)
     	{
     		$params['image'] = $image;
     	}
     	if($attachments != NULL)
     	{
     		$params['attachments'] = $attachments;
     	}     	
     	return $this->oauth->post($request_url,$params);

     }

     /**
     * 获取当前登录用户及其所关注用户的最新广播(友邻广播
     * @link https://api.douban.com/shuo/v2/statuses/home_timeline
     * @param int64 $since_id
     * @param int64 $until_id
     * @param int $count
     * @param int $start
     */
     function shuo_hometimeline($since_id = NULL,$until_id = NULL,$count = NULL,$start = null)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/home_timeline";
     	$params['since_id'] = $since_id;
     	$params['until_id'] = $until_id;
     	$params['count'] = $count;
     	$params['start'] = $start;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 获取用户发布的广播列表
     * @link https://api.douban.com/shuo/v2/statuses/user_timeline/:user_id
     * @link https://api.douban.com/shuo/v2/statuses/user_timeline/:screen_name
     * @param $user_id or $screen_name
     * @param int64 $since_id
     * @param int64 $until_id
     */
     function shuo_user_timeline($user_id,$screen_name,$since_id = NULL,$until_id = NULL)
     {
     	$params['since_id'] = $since_id;
     	$params['until_id'] = $until_id;
     	if($user_id != NULL)
     	{
     		$request_url = "https://api.douban.com/shuo/v2/statuses/user_timeline/".$user_id;
     		return $this->oauth->get($request_url,$params);
     	}elseif ($screen_name != NULL) {
     		$request_url = "https://api.douban.com/shuo/v2/statuses/user_timeline/".$screen_name;
     		return $this->oauth->get($request_url,$params);
     	}
     }

     /**
     * 读取一条广播
     * @link https://api.douban.com/shuo/v2/statuses/:id
     * @param int $id
     * @param bool $pack
     */
     function shuo_readone($id,$pack = false)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/".$id;
     	$params['pack'] = $pack;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 删除一条广播
     * @link https://api.douban.com/shuo/v2/statuses/:id
     * @param int $id
     */
     function shuo_deleteone($id)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/".$id;
     	return $this->oauth->delete($request_url);
     }


     /**
     * 获取一条广播的回复列表
     * @link https://api.douban.com/shuo/v2/statuses/:id/comments
     * @param int $id
     * @param count
     * @param start
     */
     function shuo_commit_timeline($id,$start,$count)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/".$id."/comments";
     	$params['start'] = $start;
     	$params['count'] = $count;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 添加一条评论
     * @link https://api.douban.com/shuo/v2/statuses/:id/comments
     * @param int $id
     * @param string text?
     */

     function shuo_add_commit($id,$text)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/".$id."/comments";
     	$params['text'] = $text;
     	return $this->oauth->post($request_url,params);
     }
     

     /**
     * 获取单条回复的内容
     * @link https://api.douban.com/shuo/v2/statuses/comment/:id
     * @param int $id
     */
     function shuo_commitcontent($id)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/comment/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 删除该回复
     * @link https://api.douban.com/shuo/v2/statuses/comment/:id
     * @param int $id
     */
     function shuo_deletecommit($id)
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/comment/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 转播
     * @link https://api.douban.com/shuo/v2/statuses/:id/reshare
     * @param int $id
     * @param string $method
     */
     function shuo_repost($id,$method = 'POST')
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/".$id."/reshare";
     	if($method == 'POST')
     	{
     		return $this->oauth->post($request_url);
     	}elseif ($method == 'GET') {
     		 return $this->oauth->get($request_url);
     	}
     }


     /**
     * 赞
     * @link https://api.douban.com/shuo/v2/statuses/:id/like
     * @param int $id
     * @param string $method
     */
     function shuo_like($id,$method = 'POST')
     {
     	$request_url = "https://api.douban.com/shuo/v2/statuses/".$id."/like";
     	if($method == 'POST')
     	{
     		return $this->oauth->post($request_url);
     	}elseif ($method == 'GET') {
     		return $this->oauth->get($request_url);
     	}elseif ($method == 'DELETE') {
     		return $this->oauth->delete($request_url);
     	}

     }


     /**
     * 根据ID获取用户资料
     * @link https://api.douban.com/shuo/v2/users/:id
     * @param int $id
     */
     function shuo_userinfo($id)
     {
     	$request_url = "https://api.douban.com/shuo/v2/users/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取用户关注列表
     * @link https://api.douban.com/users/:id/following
     * @param int $id
     */
     function shuo_userfollowing($id)
     {
     	$request_url = "https://api.douban.com/users/".$id."/following";
     	return $this->oauth->get($request_url);
     }


     /**
     * 获取用户关注者列表
     * @link https://api.douban.com/users/:id/followers
     * @param int $id
     */
     function shuo_userfollowers($id)
     {
     	$request_url = "https://api.douban.com/users/".$id."/followers";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取共同关注的用户列表
     * @link https://api.douban.com/shuo/v2/users/:id/follow_in_common
     * @param int $id
     */
     function shuo_follow_in_commom($id)
     {
     	$request_url = "https://api.douban.com/shuo/v2/users/".$id."/follow_in_common";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取关注的人关注了该用户的列表
     * @link https://api.douban.com/users/suggestions
     */
     function shuo_user_suggestions(){
     	$request_url = "https://api.douban.com/users/suggestions";
     	return $this->oauth->get($request_url);
     }

     /**
     * 搜索用户
     * @link https://api.douban.com/shuo/users/search
     * @param string $q
     */
     function shuo_user_search($q)
     {
     	$params['q'] = $q;
     	$request_url = "https://api.douban.com/shuo/users/search";
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * block用户 将指定用户加入黑名单
     * @link https://api.douban.com/shuo/v2/users/:id/block
     * @param int $id
     */
     function shuo_block_user($id)
     {
     	$request_url = "https://api.douban.com/shuo/v2/users/".$id."/block";
     	return $this->oauth->post($request_url);
     }

     /**
     * 建立关注
     * @link https://api.douban.com/shuo/friendships/create
     * @param int user_id
     */
     function shuo_followone($id)
     {
     	$request_url = "https://api.douban.com/shuo/friendships/create";
     	$params['user_id'] = $id;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 取消关注
     * @link https://api.douban.com/shuo/friendships/destroy
     * @param int $user_id
     */
     function shuo_unfollowone($user_id)
     {
     	$params['user_id'] = $user_id;
     	$request_url = "https://api.douban.com/shuo/friendships/destroy";
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 获取两个用户的关系
     * @link https://api.douban.com/shuo/friendships/show
     * @param int $source_id
     * @param int $target_id
     */
     function shuo_friendship($source_id = NULL,$target_id)
     {
     	$request_url = "https://api.douban.com/shuo/friendships/show";
     	$params['source_id'] = $source_id;
     	$params['target_id'] = $target_id;
     	return $this->oauth->get($request_url,$params);
     }

     //豆瓣用户API V2
     /**
     * 获取用户信息
     * @link https://api.douban.com/v2/user/:name
     * @param string $screenname
     */
     function user_info($screenname)
     {
     	$request_url = "https://api.douban.com/v2/user/".$screenname;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取当前授权用户信息
     * @link https://api.douban.com/v2/user/~me
     * 需要必须先进行API认证授权，返回当前授权的UserInfo
     */
     function user_login()
     {
     	$request_url = "https://api.douban.com/v2/user/~me";
     	return $this->oauth->get($request_url);
     }


     /**
     * 搜索用户
     * @link https://api.douban.com/v2/user
     * @param string $q 全文检索的关键词
     * @param int $start 起始元素
     * @param int $count 返回结果的数量
     */
     function user_search($q,$start = NULL,$count = NULL)
     {
     	$params['q'] = $q;
     	$params['start'] = $start;
     	$params['count'] = $count;
     	$request_url = "https://api.douban.com/v2/user";
     	return $this->oauth->get($request_url,$params);
     }

     //豆邮Api V2
     /**
     * 获取一封豆邮
     * @link https://api.douban.com/v2/doumail/:id
     * @param boolean $keep-unread
     */
     function doumail_getone($id,$keep_unread = false)
     {
     	$request_url = "https://api.douban.com/v2/doumail/".$id;
     	$params['keep-unread'] = $keep_unread;
     	return $this->oauth->get($request_url,$params);
     }


     /**
     * 获取用户收件箱
     * @link https://api.douban.com/v2/doumail/inbox
     * @param null
     */
     function doumail_inbox()
     {
     	$request_url = "https://api.douban.com/v2/doumail/inbox";
     	return $this->oauth->get($request_url);
     }


     /**
     * 获取用户发件箱
     * @link https://api.douban.com/v2/doumail/outbox
     */
     function doumail_outbox()
     {
     	$request_url = "https://api.douban.com/v2/doumail/outbox";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取用户未读邮件
     * @link https://api.douban.com/v2/doumail/inbox/unread
     */
     function doumail_unread()
     {
     	$request_url = "https://api.douban.com/v2/doumail/inbox/unread";
     	return $this->oauth->get($request_url);
     }


     /**
     * 标记已读一封邮件
     * @link https://api.douban.com/v2/doumail/:id
     */
     function doumail_mark_read($id)
     {
     	$request_url = "https://api.douban.com/v2/doumail/".$id;
     	return $this->oauth->post($request_url);
     }

     /**
     *
     * 批量标记豆邮为已读
     * @link https://api.douban.com/v2/doumail/read
     * @param string $ids 必选参数 每个id 用 ","号隔开 比如 "258003077,25800385,34938344,"
     */
     function doumail_mark_read_many($ids)
     {
     	$request_url = "https://api.douban.com/v2/doumail/read";
     	$params['ids'] = $ids;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除一封豆邮
     * @link https://api.douban.com/v2/doumail/:id
     * @param int $id
     */
     function doumail_deleteone($id)
     {
     	$request_url = "https://api.douban.com/v2/doumail/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 批量删除豆邮
     * @link https://api.douban.com/v2/doumail/delete
     * @param string ids 必选参数 每个id 用 ","号隔开 比如 "258003077,25800385,34938344,"
     */
     function doumail_deletemany($ids)
     {
     	$params['ids'] = $ids;
     	$request_url = "https://api.douban.com/v2/doumail/delete";
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 发送一封豆邮
     * @link https://api.douban.com/v2/doumails
     * @param string $title 豆邮标题 必填字段
     * @param string $content 豆邮正文 必填字段
     * @param int $receiver_id 接收邮件的用户id 必填字段
     * @param string $captcha_token 系统验证码 token	选填字段
     * @param string $captcha_string	用户输入验证码	选填字段
     */
     function doumail_send($title,$content,$receiver_id,$captcha_token = NULL,$captcha_string = NULL)
     {
     	$params['title'] = $title;
     	$params['content'] = $content;
     	$params['receiver_id'] = $receiver_id;
     	$params['captcha_token'] = $captcha_token;
     	$params['captcha_string'] = $captcha_string;
     	$request_url = "https://api.douban.com/v2/doumails";
     	return $this->oauth->post($request_url,$params);
     }


     //豆瓣日记 API V2
     /**
     * 获取用户的日记列表
     * @link https://api.douban.com/v2/note/user_created/:id
     * @param int $id
     * @param $format 取值为html_full, html_short, abstract, text，默认为text
     */
     function note_list($id,$format = 'text')
     {
     	$request_url = "https://api.douban.com/v2/note/user_created/".$id;
     	$params['format'] = $format;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 获取用户标记为喜欢的日记列表
     * @link https://api.douban.com/v2/note/user_liked/:id
     * @param int $id
     * @param $format 取值为html_full, html_short, abstract, text，默认为text
     */
     function note_user_liked($id,$format = 'text')
     {
     	$request_url = "https://api.douban.com/v2/note/user_liked/".$id;
     	$params['format'] = $format;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 获取推荐给用户的日记列表
     * @link https://api.douban.com/v2/note/people_notes/:id/guesses
     * @param int $id
     * @param $format 取值为html_full, html_short, abstract, text，默认为text
     */
     function note_guesses($id,$format = 'text')
     {
     	$params['format'] = $format;
     	$request_url = "https://api.douban.com/v2/note/people_notes/".$id."/guesses";
     	return $this->oauth->get($request_url,$params);
     }


     /**
     * 获取一条日记
     * @link https://api.douban.com/v2/note/:id
     * @param int $id
     * @param $format 
     */
     function note_getone($id,$format='text')
     {
     	$params['format'] = $format;
     	$request_url = "https://api.douban.com/v2/note/".$id;
     	return $this->oauth->get($request_url,$params);
     }


     /**
     * 增加一条日记
     * @link https://api.douban.com/v2/notes
     * @param string $title	日记标题	必传，不能为空
     * @param $privacy	隐私控制	为public，friend，private，分布对应公开，朋友可见，仅自己可见
     * @param boolean $can_reply	是否允许回复	必传, true或者false
     * @param string $content	日记内容, 如果含图片，使用“<图片p_pid>”伪tag引用图片, 如果含链接，使用html的链接标签格式，或者直接使用网址	必传	日记正文<图片p_2>正文<图片p_3>
     * @param $pids	上传的图片pid本地编号，使用前缀"p_"	用逗号分隔，最多一次传3张图片	p_2,p_3,p_4
     * @param $layout_pid	对应pid的排版	有L, C, R 分别对应居左，居中，居右3种排版
     * @param $desc_pid	对应pid的图注	可以为空
     * @param $image_pid	对应pid的图片内容
     */
     function note_post($title,$privacy = 'public',$can_reply='true',$content,$pids=NULL,$layout_pid='L',$desc_pid=NULL,$image_pid=null)
     {
     	$request_url = "https://api.douban.com/v2/notes";
     	$params['title'] = $title;
     	$params['privacy'] = $privacy;
     	$params['can_reply'] = $can_reply;
     	$params['content'] = $content;
     	$params['pids'] = $pids;
     	$params['layout_pid'] = $layout_pid;
     	$params['desc_pid'] = $desc_pid;
     	$params['image_pid'] = $image_pid;
     	return $this->oauth->post($request_url,$params);
     }


     /**
     * 喜欢一条日记
     * @link https://api.douban.com/v2/note/:id/like
     * @param $id
     */
     function note_likeone($id)
     {
     	$request_url = "https://api.douban.com/v2/note/".$id."/like";
     	return $this->oauth->post($request_url);
     }

     /**
     * 取消喜欢一条日记
     * @link https://api.douban.com/v2/note/:id/like
     * @param $id
     */
     function note_nolikeone($id)
     {
     	$request_url = "https://api.douban.com/v2/note/".$id."/like";
     	return $this->oauth->delete($request_url);
     }

     /**
     * 更新一条日记
     * @link https://api.douban.com/v2/note/:id
     * @param int $id
     * @param $title	日记标题	必选参数
     * @param $content	日记内容	必选参数;
     * @param $privacy	日记可见权限	可选参数,默认为public ,对所有人都可见,public:表示所有人可见 friend:只朋友可见 private:只有自己可见
     * @param $can_reply	日记是否可评论	可选参数,默认为true ,true表示可以评论,false表示不能评论
     */
     function note_update_one($id,$title,$content,$privacy='public',$can_reply='true')
     {
     	$request_url = "https://api.douban.com/v2/note/".$id;
     	$params['title'] = $title;
     	$params['content']= $content;
     	$params['privacy'] = $privacy;
     	$params['can_reply'] = $can_reply;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 上传照片到日记
     * @link https://api.douban.com/v2/note/:id
     * @desc 采用multipart/form-data编码方式，上传图片大小限制为<3M，name 是 image_pid
     * @param $pids	上传的图片pid本地编号，使用前缀"p_"	用逗号分隔，最多一次传3张图片	p_2,p_3,p_4
     * @param $layout_pid	对应pid的排版	有L, C, R 分别对应居左，居中，居右3种排版
     * @param $desc_pid	对应pid的图注	可以为空
     * @param $content	日记内容, 使用“<图片p_pid>”伪tag引用图片	可选，如果不传的话，图片会追加到日记末尾	日记正文<图片p_2>正文<图片p_3>
     * @param $image_pid	对应pid的图片内容
     */
     function note_uploadimg($id,$pids,$layout_pid,$desc_pid = NULL,$content,$image_pid)
     {
     	$request_url = "https://api.douban.com/v2/note/".$id;
     	$params['pids'] = $pids;
     	$params['layout_pid'] = $layout_pid;
     	$params['desc_pid'] = $desc_pid;
     	$params['content'] = $content;
     	$params['image_pid'] = $image_pid;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除一条日记
     * @link https://api.douban.com/v2/note/:id
     * @param int $id
     */
     function note_deleteone($id)
     {
     	$request_url = "https://api.douban.com/v2/note/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 获取讨论回复列表
     * @link https://api.douban.com/v2/note/:id/comments
     * @param int $id
     */
     function note_commitlist($id)
     {
     	$request_url = "https://api.douban.com/v2/note/".$id."/comments";
     	return $this->oauth->get($request_url);
     }

     /**
     * 回复讨论对于一篇日志
     * @link https://api.douban.com/v2/note/:id/comments
     * @param int $id
     * @param string $content
     */
     function note_commitonr($id,$content)
     {
     	$params['content'] = $content;
     	$request_url = "https://api.douban.com/v2/note/".$id."/comments";
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 获取讨论单条回复
     * @link /v2/note/:id/comment/:id
     * @param int $noteid
     * @param int $commitid
     */
     function note_get_onecommit($noteid,$commitid)
     {
     	$request_url = "https://api.douban.com/v2/note/".$noteid."/commit/".$commitid;
     	return $this->oauth->get($request_url);
     }

     /**
     * 删除讨论回复
     * @param int $noteid
     * @param int $commitid
     */ 
     function note_delete_onecommit($noteid,$commitid)
     {
     	$request_url = "https://api.douban.com/v2/note/".$noteid."/commit/".$commitid;
     	return $this->oauth->delete($request_url);
     }


     //相册Api V2
     // 注意，相册Api V2都需要登录后才能访问

     /**
     * 获取相册
     * @link https://api.douban.com/v2/album/:id
     * @param int $id
     */
     function album_list($id)
     {
     	$request_url = "https://api.douban.com/v2/album/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 创建相册
     * @link https://api.douban.com/v2/albums
     * @param $title	相册名	必传
     * @param $desc	相册描述	默认为空
     * @param $order	顺序	asc, desc, 默认desc
     * @param $privacy	可见性	public, friend, private, 默认public
     */
     function album_create($title,$desc = NULL,$order ='desc',$privacy='public')
     {
     	$request_url = "https://api.douban.com/v2/albums";
     	$params['title'] = $title;
     	$params['desc'] = $desc;
     	$params['order'] = $order;
     	$params['privacy'] = $privacy;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 更新相册
     * @param $title	相册名	必传
     * @param $desc	相册描述	默认为空
     * @param $order	顺序	asc, desc, 默认desc
     * @param $privacy	可见性	public, friend, private, 默认public
     */
     function album_update($title,$desc = NULL,$order ='desc',$privacy='public')
     {
     	$request_url = "https://api.douban.com/v2/albums";
     	$params['title'] = $title;
     	$params['desc'] = $desc;
     	$params['order'] = $order;
     	$params['privacy'] = $privacy;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除相册
     * @link https://api.douban.com/v2/album/:id
     * @param int $id
     */
     function album_delete($id)
     {
     	$request_url = "https://api.douban.com/v2/album/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 获得相册照片列表
     * @link { https://api.douban.com/v2/album/:id/photos};
     * @param int $id
     * @param int $start	起始	从0开始，默认为0
     * @param int $count	数量
     * @param $order	排序	asc, desc, 默认为相册本身的排序
     * @param $sortby	排序方式	time 上传时间，vote 推荐数，comment 回复数，默认为time
     */
     function album_piclist($id,$start=0,$count,$order='desc',$sortby='time')
     {
     	$request_url = "https://api.douban.com/v2/album/".$id."/photos";
     	$params['start'] = $start;
     	$params['count'] = $count;
     	$params['order'] = $order;
     	$params['sortby'] = $sortby;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 喜欢相册
     * @link {https://api.douban.com/v2/album/:id/like}
     * @param int $id
     */
     function album_likeone($id)
     {
     	$request_url = "https://api.douban.com/v2/album/".$id."/like";
     	return $this->oauth->post($request_url);
     }

     /**
     * 取消喜欢相册
     * @link {https://api.douban.com/v2/album/:id/like}
     * @param int $id
     */
     function album_unlikeone($id)
     {
     	$request_url = "https://api.douban.com/v2/album/".$id."/like";
     	return $this->oauth->delete($request_url);
     }

     /**
     * 获取用户相册列表
     * @link {https://api.douban.com/v2/album/user_created/:id}
     * @param int $userid
     */
     function album_userlist($userid)
     {
     	$request_url = "https://api.douban.com/v2/album/user_created/".$userid;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获得用户喜欢的相册列表
     * @link {https://api.douban.com/v2/album/user_liked/:id}
     * @param int $id
     */
     function album_userlikes($id)
     {
     	$request_url = "https://api.douban.com/v2/album/user_liked/".$id;
     	return $this->oauth->get($request_url);
     }

     /**
     * 获得照片
     * @link {https://api.douban.com/v2/photo/:id}
     * @param int $photeid
     */
     function album_photo($photeid)
     {
     	$request_url = "https://api.douban.com/v2/photo/".$photoid;
     	return $this->oauth->get($request_url);
     }

     /**
     * 上传照片
     * 采用multipart/form-data编码方式，上传图片大小限制为<3M，name 是 image
     * @link {https://api.douban.com/v2/album/:id}
     * @param int $id 专辑
     * @param $image	照片名称，照片内容使用multipart/form-data编码	必传
     * @param $desc	照片描述	默认为空
     */
     function album_upload($id,$image,$desc = NULL)
     {
     	$params['image'] = $image;
     	$params['desc'] = $desc;
     	$request_url = "https://api.douban.com/v2/album/".$id;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 更新照片描述
     * @link {https://api.douban.com/v2/photo/:id}
     * @param int $id;
     * @param string $desc
     */
     function album_update_desc($id,$desc)
     {
     	$request_url = "https://api.douban.com/v2/photo/".$id;
     	$params['desc'] = $desc;
     	return $this->oauth->post($request_url,$params);
     }


     /**
     * 删除照片
     * @link {https://api.douban.com/v2/photo/:id}
     * @param int $id
     */
     function album_deleteone($id)
     {
     	$request_url = "https://api.douban.com/v2/photo/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 喜欢照片
     * @link {https://api.douban.com/v2/photo/:id/like}
     * @param int $id
     */
     function album_phote_likeone($id)
     {
     	$request_url = "https://api.douban.com/v2/photo/".$id."/like";
     	return $this->oauth->post($request_url);
     }

     /**
     * 取消喜欢照片
     * @link{ https://api.douban.com/v2/photo/:id/like}
     * @param int $id
     */
     function album_photo_unlikeone($id)
     {
     	$request_url = "https://api.douban.com/v2/photo/".$id."/like";
     	return $this->oauth->delete($request_url);
     }

     /**
     * 照片回复或者获取评论列表
     * @link{/v2/photo/:id/comments}
     * @param int $id
     * @param string method，默认为POST表示回复，当method为GET时为获取评论列表
     */
     function album_commit($id,$method='POST',$content = NULL)
     {
     	$request_url = "https://api.douban.com/v2/photo/".$id."/comments";
     	if($method == 'POST')
     	{
     		//回复一条
     		$params['content']  = $content;
     		return $this->oauth->post($request_url,$params);
     	}elseif ($method == 'GET') {
     		//取列表
     		return $this->oauth->get($request_url);
     	}
     }

     /**
     * 获取或者删除单条评论
     * @link {/v2/photo/:id/comment/:id}
     * @param int $photoid 
     * @param int $commitid
     * @param string $method .默认为delete，当为GET时为获取评论
     */
     function album_single_commit($photoid,$commitid,$method='DELETE')
     {
     	$request_url = "https://api.douban.com/v2/photo/".$photoid."/comment/".$commitid;
     	if($method == 'DELETE')
     	{
     		return $this->oauth->delete($request_url);
     	}elseif ($method == 'GET') {
     		return $this->oauth->get($request_url);
     	}
     }

     //豆瓣线上活动 API V2
     /**
     * 获取线上活动
     * @link {https://api.douban.com/v2/online/:id}
     * @param int $id
     */
     function online_listbyid($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id;
     	return $this->oauth->get($request_url);
     }


     /**
     * 获取线上活动参加成员列表
     * @link{https://api.douban.com/v2/online/:id/participants}
     * @param int $id
     */
     function online_peoplelist($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/participants";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取线上活动论坛列表
     * @link{https://api.douban.com/v2/online/:id/discussions}
     * @param int $id
     */
     function online_forum_list($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/discussions";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取线上活动列表
     * @link {https://api.douban.com/v2/onlines}
     * @param $cate	类别	day，week，latest分别对应每天，每周，最新
     */
     function online_list($cate)
     {
     	$request_url = "https://api.douban.com/v2/onlines";
     	$params['cate'] = $cate;
     	return $this->oauth->get($request_url,$params);
     }

     /**
     * 创建线上活动
     * @link {https://api.douban.com/v2/onlines}
     * @param $title	题目	不能为空
     * @param $desc	描述	不能为空
     * @param $begin_time	开始时间	不能为空，不是是过去的时间，时间格式"%Y-%m-%d %H:%M"
     * @param $end_time	结束时间	不能为空，不能早于开始时间，活动期限不能长于3个月(90天)
     * @param $lated_url	关联的url或者小组链接	可以为空
     * @param $cascade_invite	是否允许参与的成员邀请朋友参加	默认为false
     * @param tags	标签	不超过4个，用空格分开，默认为空
     */
     function online_create($title,$desc,$begin_time,$end_time,$last_url=NULL,$cascade_invite =false,$tags=null)
     {
     	$request_url = "https://api.douban.com/v2/onlines";
     	$params['title'] = $title;
     	$params['desc'] = $desc;
     	$params['begin_time'] = $begin_time;
     	$params['end_time'] = $end_time;
     	$params['last_url'] = $last_url;
     	$params['cascade_invite'] = $cascade_invite;
     	$params['tags'] = $tags;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 更新线上活动
     * @link {https://api.douban.com/v2/onlines}
     * @param $title	题目	不能为空
     * @param $desc	描述	不能为空
     * @param $begin_time	开始时间	不能为空，不是是过去的时间，时间格式"%Y-%m-%d %H:%M"
     * @param $end_time	结束时间	不能为空，不能早于开始时间，活动期限不能长于3个月(90天)
     * @param $lated_url	关联的url或者小组链接	可以为空
     * @param $cascade_invite	是否允许参与的成员邀请朋友参加	默认为false
     * @param tags	标签	不超过4个，用空格分开，默认为空
     */
     function online_update($title,$desc,$begin_time,$end_time,$last_url=NULL,$cascade_invite =false,$tags=null)
     {
     	$request_url = "https://api.douban.com/v2/onlines";
     	$params['title'] = $title;
     	$params['desc'] = $desc;
     	$params['begin_time'] = $begin_time;
     	$params['end_time'] = $end_time;
     	$params['last_url'] = $last_url;
     	$params['cascade_invite'] = $cascade_invite;
     	$params['tags'] = $tags;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除线上活动
     * @link{https://api.douban.com//v2/online/:id}
     * @param int $id
     */
     function online_deleteone($id)
     {
     	$request_url = "https://api.douban.com//v2/online/".$id;
     	return $this->oauth->delete($request_url);
     }

     /**
     * 参加线上活动
     * @link{https://api.douban.com/v2/online/:id/participants}
     * @param int $id
     */
     function online_participants($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/participant";
     	return $this->oauth->post($request_url);
     }

     /**
     * 退出线上活动
     * @link{https://api.douban.com/v2/online/:id/participants}
     * @param int $id
     */
     function online_left($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/participants";
     	return $this->oauth->delete($request_url);
     }


     /**
     * 喜欢线上活动
     * @link{https://api.douban.com/v2/online/:id/like}
     * @param int $id
     */
     function online_likeone($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/like";
     	return $this->oauth->post($request_url);
     }

     /**
     * 取消喜欢线上活动
     * @link{https://api.douban.com/v2/online/:id/like}
     * @param int $id
     */
     function online_unlikeone($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/like";
     	return $this->oauth->delete($request_url);
     }

     /**
     * 图片列表
     * @link{https://api.douban.com/v2/online/:id/photos}
     * @param int $id
     */
     function online_piclist($id)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/photos";
     	return $this->oauth->get($request_url);
     }


     /**
     * 上传图片
     * @link {https://api.douban.com/v2/online/:id/photos}
     * @param $image	照片名称，照片内容使用multipart/form-data编码	必传
     * @param $desc	照片描述	默认为空
     */
     function online_uploadimg($id,$image,$desc=NULL)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/photos";
     	$params['image'] = $image;
     	$params['desc'] = $desc;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 上线活动论坛发贴
     * @link{https://api.douban.com/v2/online/:id/discussions|}
     * @param int $id
     * @param $title	题目	不能为空
     * @param $content	内容	不能为空
     */
     function online_post_message($id,$title,$content)
     {
     	$request_url = "https://api.douban.com/v2/online/".$id."/discussions|";
     	$params['title'] = $title;
     	$params['content'] = $content;
     	return $this->oauth->post($request_url,$params);
     }


     /**
     * 获取用户参加的线上活动列表
     * @link{ https://api.douban.com/v2/online/user_participated/:id}
     * @param int $id
     * @param boolean $exclude_expired	是否包括过期活动	true，false，默认为包含过期
     */
     function online_user_takein_list($id,$exclude_expired = true)
     {
     	$request_url = "https://api.douban.com/v2/online/user_participated/".$id;
     	return $this->oauth->get($request_url,$params);
     }


     /**
     * 获取用户创建的线上活动列表
     * @link {https://api.douban.com/v2/online/user_created/:id}
     * @param int $id
     */
     function online_user_create_list($id)
     {
     	$request_url = "https://api.douban.com/v2/online/user_created/".$id;
     	return $this->oauth->get($request_url);
     }


     //论坛API V2
     /**
     * 获取讨论
     * @link{https://api.douban.com/v2/discussion/:id}
     * @param int $id
     */
     function discussion_getbyid($id)
     {
     	$request_url = "https://api.douban.com/v2/discussion/".$id;
     	return $this->oauth->get($request_url);
     }


     /**
     * 更新讨论
     * @link {https://api.douban.com/v2/discussion/:id}
     * @param int $id
     * @param $title	题目	不能为空
     * @param $content	内容	不能为空
     */
     function discussion_updateone($id,$title,$content)
     {
     	$request_url = "https://api.douban.com/v2/discussion/".$id;
     	$params['title'] = $title;
     	$params['content'] = $content;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 删除讨论
     * @link{https://api.douban.com/v2/discussion/:id}
     * @param int $id
     */
     function discussion_deleteone($id)
     {
     	$request_url = "https://api.douban.com/v2/discussion/".$id;
     	return $this->oauth->delete($request_url);
     }


     /**
     * 新发讨论
     * @link {https://api.douban.com/v2/target/:id/discussions}
     * @param int $id
     * @param $title
     * @param $content
     */
     function discussion_newpost($id,$title,$content)
     {
     	$request_url = "https://api.douban.com/v2/target/".$id."/discussions";
     	$params['title'] = $title;
     	$params['content'] = $content;
     	return $this->oauth->post($request_url,$params);
     }

     /**
     * 获取讨论列表
     * @link { https://api.douban.com/v2/target/:id/discussions}
     * @param int $id
     */
     function discussion_commitlist($id)
     {
     	$request_url = "https://api.douban.com/v2/target/".$id."/discussions";
     	return $this->oauth->get($request_url);
     }

     /**
     * 获取讨论回复列表或者回复一条
     * @link {https://api.douban.com/v2/discussion/:id/comments}
     * @param int $id
     * @param $method = POST or GET default POST,表示发表评论，GET表示获取列表
     */
     function discussion_getlist_commit($id,$method = 'POST',$title = null,$content = null)
     {
         $request_url = "https://api.douban.com/v2/discussion/".$id."/comments";
         if($method == 'POST')
         {
         	$params['title'] = $title;
         	$params['content'] = $content;
         	return $this->oauth->post($request_url,$params);
         }elseif ($method == 'GET') {
         	return $this->oauth->get($request_url);
         }
     }

     /**
     * 获取讨论单条回复或者删除
     * @link{https://api.douban.com/v2/discussion/:id/comment/:id}
     * @param int $discussion_id
     * @param int $commit_id
     * @param $method GET/DELETE
     */
     function discussion_getsingle_delete($discussion_id,$commit_id,$method='GET')
     {
     	$request_url = "https://api.douban.com/v2/discussion/".$discussion_id."/comment/".$commit_id;
     	if($method == 'GET')
     	{
     		return $this->oauth->get($request_url);
     	}elseif ($method == 'DELETE') {
     		return $this->oauth->delete($request_url);
     	}
     }



     //回复Api V2
     /**
     * 获取回复列表
     * @link{https://api.douban.com/v2/target/:id/comments}
     * @param int $id
     */
     function comments_byid($id)
     {
     	$request_url = "https://api.douban.com/v2/target/".$id."/comments";
     	return $this->oauth->get($request_url);
     }


     /**
     * 新发讨论
     * @link{https://api.douban.com/v2/target/:id/comments}
     * @param int $id
     * @param $content	回复内容	必传
     */
     function comments_newpost($id,$content)
     {
     	$request_url = "https://api.douban.com/v2/target/".$id."/comments";
     	$params['content'] = $content;
     	return $this->oauth->post($request_url,$params);
     }


     /**
     * 获取单条回复
     * @link{https://api.douban.com/v2/target/:id/comment/:id}
     * @param int $targetid
     * @param int $commitid
     */
     function comments_get_single($targetid,$commitid)
     {
     	$request_url = "https://api.douban.com/v2/target/".$targetid."/comment/".$commitid;
     	return $this->oauth->get($request_url);
     }

     /**
     * 删除回复
      * @link{https://api.douban.com/v2/target/:id/comment/:id}
     * @param int $targetid
     * @param int $commitid
     */
     function comments_delete_single($targetid,$commitid)
     {
     	$request_url = "https://api.douban.com/v2/target/".$targetid."/comment/".$commitid;
     	return $this->oauth->delete($request_url);
     }

}
