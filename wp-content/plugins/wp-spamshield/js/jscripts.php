<?php
/**
 *  WP-SpamShield Dynamic JS File
 *  Version: 1.9.15
 */

/* Security Check - BEGIN */
if(!empty($_GET)||FALSE!==strpos($_SERVER['REQUEST_URI'],'?')||!empty($_SERVER['QUERY_STRING'])){
	@header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403);
	die('ERROR: This resource will not function with a query string. Remove the query string from the URL and try again.');
}
js_wpss_getenv(FALSE,array('REQUEST_METHOD'));global $_WPSS_ENV;
$request_method=(!empty($_SERVER['REQUEST_METHOD']))?$_SERVER['REQUEST_METHOD']:$_WPSS_ENV['REQUEST_METHOD'];
$request_method=(!empty($request_method))?js_wpss_casetrans('upper',$request_method):'';
if(empty($request_method)){$request_method='';}
if(($request_method!='GET'&&$request_method!='HEAD')){
	@header('Allow: GET,HEAD',TRUE);
	@header($_SERVER['SERVER_PROTOCOL'].' 405 Method Not Allowed',TRUE,405);
	die('ERROR: This resource does not accept requests of that type.');
}
/* Security Check - END */

/* Timer Start */
$start_time=microtime(TRUE);
$current_dt=time();	/* Site entry time - timestamp */

/* SET INITIAL VARS */
date_default_timezone_set('UTC');
if(!defined('WPSS_SERVER_NAME')){define('WPSS_SERVER_NAME',js_wpss_get_server_name());}
if(!defined('WPSS_EOL')){$eol=(defined('PHP_EOL')&&("\n"===PHP_EOL||"\r\n"===PHP_EOL))?PHP_EOL:js_wpss_eol();define('WPSS_EOL',$eol);}
if(!defined('WPSS_DS')){$ds=(defined('DIRECTORY_SEPARATOR')&&('/'===DIRECTORY_SEPARATOR||'\\'===DIRECTORY_SEPARATOR))?DIRECTORY_SEPARATOR:js_wpss_ds();define('WPSS_DS',$ds);}
function js_wpss_eol(){return (js_wpss_casetrans('lower',substr(PHP_OS,0,3))==='win')?"\r\n":"\n";}
function js_wpss_ds(){return (js_wpss_casetrans('lower',substr(PHP_OS,0,3))==='win')?'\\':'/';}

/* SESSION CHECK AND FUNCTIONS - BEGIN */
global $session_id,$is_https,$this_url;
$session_id=@session_id();
$is_https=js_wpss_is_https();
$this_url=js_wpss_get_url();
if(empty($session_id)&&!headers_sent()){@session_start();$session_id=@session_id();}
if(!defined('WPSS_SERVER_NAME_NODOT')){$server_name_nodot=str_replace('.','',WPSS_SERVER_NAME);define('WPSS_SERVER_NAME_NODOT',$server_name_nodot);}
if(!defined('WPSS_HASH_ALT')){$alt_prefix=js_wpss_md5(WPSS_SERVER_NAME_NODOT);define('WPSS_HASH_ALT',$alt_prefix);}
if(!defined('WPSS_SITE_URL')&&!empty($_SESSION['wpss_site_url_'.WPSS_HASH_ALT])){$site_url=$_SESSION['wpss_site_url_'.WPSS_HASH_ALT];define('WPSS_SITE_URL',$site_url);}
if(defined('WPSS_SITE_URL')&&!defined('WPSS_HASH')){$hash_prefix=js_wpss_md5(WPSS_SITE_URL);define('WPSS_HASH',$hash_prefix);}
elseif(!empty($_SESSION)&&!empty($_COOKIE)&&!defined('WPSS_HASH')){
	foreach($_COOKIE as $ck_name => $ck_v){
		if(preg_match("~^comment_author_([a-z0-9]{32})$~i",$ck_name,$matches)){define('WPSS_HASH',$matches[1]);break;}
	}
}
$lang_ck_key='UBR_LANG';$lang_ck_val='default';
$current_ip=$_SERVER['REMOTE_ADDR'];
$current_pt=(!empty($_SERVER['REMOTE_PORT']))?$_SERVER['REMOTE_PORT']:'';
$current_ua=js_wpss_get_user_agent();
$current_mt=$start_time;	/* Site entry time - microtime */
/* SESSION CHECK AND FUNCTIONS - END */

if(defined('WPSS_HASH')&&!empty($_SESSION)){
	/* IP, PAGE HITS, PAGES VISITED HISTORY - BEGIN */
	/* Initial IP Address when visitor first comes to site */
	$key_pages_hist 	='wpss_jscripts_referers_history_'.WPSS_HASH;
	$key_hits_per_page	='wpss_jscripts_referers_history_count_'.WPSS_HASH;
	$key_total_page_hits='wpss_page_hits_js_'.WPSS_HASH;
	$key_ip_hist 		='wpss_jscripts_ip_history_'.WPSS_HASH;
	$key_pt_hist 		='wpss_jscripts_pt_history_'.WPSS_HASH;
	$key_init_ip		='wpss_user_ip_init_'.WPSS_HASH;
	$key_init_pt		='wpss_user_pt_init_'.WPSS_HASH;
	$key_init_ua		='wpss_user_agent_init_'.WPSS_HASH;
	$key_init_mt		='wpss_time_init_'.WPSS_HASH;
	$key_init_dt		='wpss_timestamp_init_'.WPSS_HASH;
	$ck_key_init_dt		='NCS_INENTIM'; /* Initial Entry Time */
	if(empty($_SESSION[$key_init_ip])){$_SESSION[$key_init_ip]=$current_ip;}
	if(empty($_SESSION[$key_init_pt])){$_SESSION[$key_init_pt]=$current_pt;}
	if(empty($_SESSION[$key_init_ua])){$_SESSION[$key_init_ua]=$current_ua;}
	if(empty($_SESSION[$key_init_mt])){$_SESSION[$key_init_mt]=$current_mt;}
	if(empty($_SESSION[$key_init_dt])){$_SESSION[$key_init_dt]=$current_dt;}
	/* Set Cookie */
	if(empty($_COOKIE[$ck_key_init_dt])){$new_visit=TRUE;}
	/* IP History - Lets see if they change IP's */
	if(empty($_SESSION[$key_ip_hist])){$_SESSION[$key_ip_hist]=array();$_SESSION[$key_ip_hist][]=$current_ip;}
	if($current_ip!=$_SESSION[$key_init_ip]||!js_wpss_in_array($current_ip,$_SESSION[$key_ip_hist])){$_SESSION[$key_ip_hist][]=$current_ip;}
	/* Port History - Can be telling when spammers change IP's */
	if(empty($_SESSION[$key_pt_hist])){$_SESSION[$key_pt_hist]=array();$_SESSION[$key_pt_hist][]=$current_pt;}
	if($current_pt!=$_SESSION[$key_init_pt]||!js_wpss_in_array($current_pt,$_SESSION[$key_pt_hist])){$_SESSION[$key_pt_hist][]=$current_pt;}
	/* Page hits - this page is more reliable than main if caching is on, so we'll keep a separate count */
	if(empty($_SESSION[$key_total_page_hits])){$_SESSION[$key_total_page_hits]=0;}
	++$_SESSION[$key_total_page_hits];
	/* Referrer History - More reliable way to keep a list of pages, than using main */
	if(empty($_SESSION[$key_pages_hist])){$_SESSION[$key_pages_hist]=array();}
	if(empty($_SESSION[$key_hits_per_page])){$_SESSION[$key_hits_per_page]=array();}
	if(!empty($_SERVER['HTTP_REFERER'])){
		$current_ref=trim(strip_tags($_SERVER['HTTP_REFERER']));
		$key_last_ref='wpss_jscripts_referer_last_'.WPSS_HASH;
		$_SESSION[$key_pages_hist][]=$current_ref;
		if(!isset($_SESSION[$key_hits_per_page][$current_ref])){$_SESSION[$key_hits_per_page][$current_ref]=1;}
		++$_SESSION[$key_hits_per_page][$current_ref];
		/* Last Referrer */
		if(empty($_SESSION[$key_last_ref])){$_SESSION[$key_last_ref]='';}
		$_SESSION[$key_last_ref]=$current_ref;
	}
	/* Initial Referrer - Where Visitor Entered Site // External Referrer --> Landing Page */
	$key_first_ref='wpss_referer_init_'.WPSS_HASH;
	if(empty($_SESSION[$key_first_ref])&&!empty($_COOKIE['JCS_INENREF'])&&FALSE===strpos($_COOKIE['JCS_INENREF'],WPSS_SERVER_NAME)){$_SESSION[$key_first_ref]=$_COOKIE['JCS_INENREF'];}
	/* IP, PAGE HITS, PAGES VISITED HISTORY - END */

	/* AUTHOR, EMAIL, URL HISTORY - BEGIN */

	/* Keep history of Author, Author Email, and Author URL in case they keep changing */
	/* This will expose spammer behavior patterns */

	/* Comment Author */
	$key_auth_hist='wpss_author_history_'.WPSS_HASH;$key_comment_auth='comment_author_'.WPSS_HASH;
	if(empty($_SESSION[$key_auth_hist])){
		$_SESSION[$key_auth_hist]=array();
		if(!empty($_COOKIE[$key_comment_auth])){$_SESSION[$key_comment_auth]=$_COOKIE[$key_comment_auth];$_SESSION[$key_auth_hist][]=$_COOKIE[$key_comment_auth];}
	}elseif(!empty($_COOKIE[$key_comment_auth])){$_SESSION[$key_comment_auth]=$_COOKIE[$key_comment_auth];}
	/* Comment Author Email */
	$key_email_hist='wpss_author_email_history_'.WPSS_HASH;$key_comment_email='comment_author_email_'.WPSS_HASH;
	if(empty($_SESSION[$key_email_hist])){
		$_SESSION[$key_email_hist]=array();
		if(!empty($_COOKIE[$key_comment_email])){$_SESSION[$key_comment_email]=$_COOKIE[$key_comment_email];$_SESSION[$key_email_hist][]=$_COOKIE[$key_comment_email];}
	}elseif(!empty($_COOKIE[$key_comment_email])){$_SESSION[$key_comment_email]=$_COOKIE[$key_comment_email];}
	/* Comment Author URL */
	$key_auth_url_hist='wpss_author_url_history_'.WPSS_HASH;$key_comment_url='comment_author_url_'.WPSS_HASH;
	if(empty($_SESSION[$key_auth_url_hist])){
		$_SESSION[$key_auth_url_hist]=array();
		if(!empty($_COOKIE[$key_comment_url])){$_SESSION[$key_comment_url]=$_COOKIE[$key_comment_url];$_SESSION[$key_auth_url_hist][]=$_COOKIE[$key_comment_url];}
	}elseif(!empty($_COOKIE[$key_comment_url])){$_SESSION[$key_comment_url]=$_COOKIE[$key_comment_url];}
	/* AUTHOR, EMAIL, URL HISTORY - END */

	/* SESSION USER BLACKLIST CHECK - BEGIN */
	if(!empty($_SESSION['wpss_clear_blacklisted_user_'.WPSS_HASH])){$cl_sbluck=TRUE;unset($_SESSION['wpss_blacklisted_user_'.WPSS_HASH]);}
	elseif(!empty($_SESSION['wpss_blacklisted_user_'.WPSS_HASH])&&empty($_COOKIE[$lang_ck_key])){$sbluck=TRUE;}
	elseif(!empty($_COOKIE[$lang_ck_key])&&$_COOKIE[$lang_ck_key]==$lang_ck_val){$_SESSION['wpss_blacklisted_user_'.WPSS_HASH]=TRUE;}
	/* SESSION USER BLACKLIST CHECK - END */
}

/* STANDARD FUNCTIONS - BEGIN */
function js_wpss_getenv($e=FALSE,$add_vars=array()){
	global $_WPSS_ENV;
	if(empty($_WPSS_ENV)||!is_array($_WPSS_ENV)){$_WPSS_ENV=array();}
	$_WPSS_ENV=(array)$_WPSS_ENV+(array)$_ENV;
	$vars=array('REMOTE_ADDR','SERVER_ADDR','LOCAL_ADDR','HTTP_HOST','SERVER_NAME',);$vars=!empty($add_vars)?(array)$vars+(array)$add_vars:$vars;
	if(!empty($e)){$vars[]=$e;}
	foreach($vars as $i => $v){if(empty($_WPSS_ENV[$v])){$_WPSS_ENV[$v]=$_ENV[$v]='';if(function_exists('getenv')){$_WPSS_ENV[$v]=$_ENV[$v]=@getenv($v);}}}
	return FALSE!==$e?$_WPSS_ENV[$e]:$_WPSS_ENV;
}
function js_wpss_md5($str){
	return function_exists('hash')?hash('md5',$str):md5($str);
}
function js_wpss_microtime(){
	$t=microtime(TRUE);if(empty($t)){$t=time();}return $t;
}
function js_wpss_timer($start=NULL,$end=NULL,$show_seconds=FALSE,$precision=8){
	if(empty($start)||empty($end)){$start=0;$end=0;}
	$total_time=$end-$start;
	$total_time_for=number_format($total_time,$precision);
	if(!empty($show_seconds)){$total_time_for .=' seconds';}
	return $total_time_for;
}
function js_wpss_get_user_agent(){
	return !empty($_SERVER['HTTP_USER_AGENT'])?js_wpss_sanitize_string($_SERVER['HTTP_USER_AGENT']):'';
}
function js_wpss_is_https(){
	if(!empty($_SERVER['HTTPS'])&&'off'!==$_SERVER['HTTPS']){return TRUE;}
	if(!empty($_SERVER['SERVER_PORT'])&&'443'==$_SERVER['SERVER_PORT']){return TRUE;}
	if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])&&'https'===$_SERVER['HTTP_X_FORWARDED_PROTO']){return TRUE;}
	if(!empty($_SERVER['HTTP_X_FORWARDED_SSL'])&&'off'!==$_SERVER['HTTP_X_FORWARDED_SSL']){return TRUE;}
	return FALSE;
}
function js_wpss_get_server_addr(){
	global $_WPSS_ENV;if(!empty($_SERVER['SERVER_ADDR'])){$server_addr=$_SERVER['SERVER_ADDR'];}
	elseif(!empty($_WPSS_ENV['SERVER_ADDR'])){$server_addr=$_SERVER['SERVER_ADDR']=$_WPSS_ENV['SERVER_ADDR'];}
	elseif(!empty($_SERVER['LOCAL_ADDR'])){$server_addr=$_SERVER['SERVER_ADDR']=$_SERVER['LOCAL_ADDR'];}
	elseif(!empty($_WPSS_ENV['LOCAL_ADDR'])){$server_addr=$_SERVER['SERVER_ADDR']=$_SERVER['LOCAL_ADDR']=$_WPSS_ENV['LOCAL_ADDR'];}
	return !empty($server_addr)?$server_addr:'';
}
function js_wpss_get_server_name(){
	global $_WPSS_ENV;if(!empty($_SERVER['HTTP_HOST'])){$server_name=$_SERVER['HTTP_HOST'];}
	elseif(!empty($_WPSS_ENV['HTTP_HOST'])){$server_name=$_SERVER['HTTP_HOST']=$_WPSS_ENV['HTTP_HOST'];}
	elseif(!empty($_SERVER['SERVER_NAME'])){$server_name=$_SERVER['HTTP_HOST']=$_SERVER['SERVER_NAME'];}
	elseif(!empty($_WPSS_ENV['SERVER_NAME'])){$server_name=$_SERVER['HTTP_HOST']=$_SERVER['SERVER_NAME']=$_WPSS_ENV['SERVER_NAME'];}
	return !empty($server_name)&&'.'!==trim($server_name)?js_wpss_casetrans('lower',$server_name):'';
}
function js_wpss_get_url(){
	global $is_https;
	$server_name=(defined('WPSS_SERVER_NAME'))?WPSS_SERVER_NAME:@js_wpss_get_server_name();
	$scheme=($is_https)?'https://':'http://';
	return $scheme.$server_name.$_SERVER['REQUEST_URI'];
}
function js_wpss_get_ck_dir(){
	global $this_url;
	$path		= __FILE__;
	$jscripts	= WPSS_DS.'wp-content'.WPSS_DS.'plugins'.WPSS_DS.'wp-spamshield'.WPSS_DS.'js'.WPSS_DS.'jscripts.php';
	$path_guess	= str_replace( $jscripts, '', $path );
	$wp_cnf		= $path_guess.WPSS_DS.'wp-config.php';
	$ck_dir		= ( file_exists( $wp_cnf ) && !empty( $_SERVER['DOCUMENT_ROOT'] ) ) ? str_replace( WPSS_DS, '/', str_replace( $_SERVER['DOCUMENT_ROOT'], '', $path_guess ) . WPSS_DS ) : '/';
	if( FALSE !== strpos( $ck_dir, 'public_html' ) ) {
		$arr	= (array) explode( 'public_html', $ck_dir );
		$ck_dir	= trim( (string) end( $arr ) );
	}
	return ( FALSE !== strpos( $this_url, WPSS_SERVER_NAME.$ck_dir ) && FALSE === strpos( $ck_dir, 'public_html' ) && FALSE === strpos( $ck_dir, '/htdocs/' ) && FALSE === strpos( $ck_dir, WPSS_SERVER_NAME ) ) ? $ck_dir : '/';
}
function js_wpss_strlen($str){
	return function_exists('mb_strlen')?mb_strlen($str,'UTF-8'):strlen($str);
}
function js_wpss_count_words( $str ) {
	return ( empty( $str ) || 0 === js_wpss_strlen( trim( $str ) ) ) ? 0 : count( explode( ' ', $str ) );
}
function js_wpss_casetrans($type,$str){
	switch ($type){
		case 'upper':
			return function_exists('mb_strtoupper')?mb_strtoupper($str,'UTF-8'):strtoupper($str);
		case 'lower':
			return function_exists('mb_strtolower')?mb_strtolower($str,'UTF-8'):strtolower($str);
		case 'ucfirst':
			if(function_exists('mb_strtoupper')&&function_exists('mb_substr')){
				$strtmp=mb_strtoupper(mb_substr($str,0,1,'UTF-8'),'UTF-8'). mb_substr($str,1,NULL,'UTF-8');
				return js_wpss_strlen($str)===js_wpss_strlen($strtmp)?$strtmp:ucfirst($str);
			}else{return ucfirst($str);}
		case 'ucwords':
			return function_exists('mb_convert_case')?mb_convert_case($str,MB_CASE_TITLE,'UTF-8'):ucwords($str);
		default:
			return $str;
	}
}
function js_wpss_sanitize_string($str){
	$str=trim(addslashes(htmlentities(stripslashes(strip_tags($str)))));
	return str_replace(chr(0),'',$str);
}
function js_wpss_in_array($needle,$haystack) {
	$haystack_flip=array_flip($haystack);
	return (isset($haystack_flip[$needle]));
}
/* STANDARD FUNCTIONS - END */

/* SET COOKIE VALUES - BEGIN */
if(empty($session_id)){$session_id=@session_id();}
$DATE_C_YYMM	=date( 'ym' );
$SJECT			='SJECT'.$DATE_C_YYMM;
$CKON			='CKON'.$DATE_C_YYMM;
$ck_key_phrase	='wpss_ckkey_'.WPSS_SERVER_NAME_NODOT.'_'.$session_id;
$ck_val_phrase	='wpss_ckval_'.WPSS_SERVER_NAME_NODOT.'_'.$session_id;
$ck_key 		=js_wpss_md5($ck_key_phrase);
$ck_val 		=js_wpss_md5($ck_val_phrase);
$ck_dir			=js_wpss_get_ck_dir();
$ck_sec			=($is_https)?'secure':'';
$jq_key_phrase	='wpss_jqkey_'.WPSS_SERVER_NAME_NODOT.'_'.$session_id;
$jq_val_phrase	='wpss_jqval_'.WPSS_SERVER_NAME_NODOT.'_'.$session_id;
$jq_key 		=js_wpss_md5($jq_key_phrase);
$jq_val 		=js_wpss_md5($jq_val_phrase);
/* SET COOKIE VALUES - END */

/* Last thing before headers sent */
$_SESSION['wpss_sess_status']='on';
if(!empty($current_ref)&&preg_match("~([&\?])form\=response$~i",$current_ref)&&!empty($_SESSION[$key_comment_auth])){
	@setcookie($key_comment_auth,$_SESSION[$key_comment_auth],0,$ck_dir,NULL,$is_https);
	if(!empty($_SESSION[$key_comment_email])){@setcookie($key_comment_email,$_SESSION[$key_comment_email],0,$ck_dir,NULL,$is_https);}
	if(!empty($_SESSION[$key_comment_url])){@setcookie($key_comment_url,$_SESSION[$key_comment_url],0,$ck_dir,NULL,$is_https);}
}
if(!empty($new_visit)){
	@setcookie($ck_key_init_dt,$current_dt,$current_dt+3600,$ck_dir,WPSS_SERVER_NAME,$is_https,TRUE); /* 1 hour */
}
if(!empty($cl_sbluck)){
	@setcookie($lang_ck_key,$lang_ck_val,$current_dt-31536000,$ck_dir,WPSS_SERVER_NAME,$is_https); /* -1 year (deletes cookie)*/
	unset($_SESSION['wpss_clear_blacklisted_user_'.WPSS_HASH],$_SESSION['wpss_blacklisted_user_'.WPSS_HASH]);
}elseif(!empty($sbluck)){
	@setcookie($lang_ck_key,$lang_ck_val,$current_dt+60*60*24*365*10,$ck_dir,WPSS_SERVER_NAME,$is_https,TRUE); /* 10 years */
}
@setcookie($ck_key,$ck_val,$current_dt+60*60*4,$ck_dir,WPSS_SERVER_NAME,$is_https,TRUE); /* 4 hours - Keep this line as backstop for cache control on browsers with aggressive caching: Safari, etc. */
@setcookie(js_wpss_casetrans('lower',$CKON),js_wpss_casetrans('lower',$SJECT.'_'.strrev(uniqid())),$current_dt+60*5,$ck_dir,WPSS_SERVER_NAME,$is_https,TRUE); /* 5 minutes - Cache control - setting cookies turns off Varnish caching for this script */
/* Control caching */
if(function_exists('header_remove')){@header_remove('Cache-Control');@header_remove('Last-Modified');@header_remove('ETag');}
@header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0, proxy-revalidate, s-maxage=0, no-transform',TRUE); /* HTTP/1.1 - Tell browsers and proxies not to cache this */
@header('Surrogate-Control: no-cache, must-revalidate, max-age=0',TRUE); /* Tell surrogates (gateway caches/reverse proxies) not to cache this */
@header('Pragma: no-cache',TRUE); /* HTTP 1.0 */
@header('Expires: Sat, 26 Jul 1997 05:00:00 GMT',TRUE); /* Date in the past */
@header('Vary: *',TRUE); /* Force no caching */
@header('Content-Type: application/javascript; charset=UTF-8',TRUE);
@header('X-Robots-Tag: none',TRUE);
/* 1e3 = 1000 */
$content = "function wpss_set_ckh(n,v,e,p,d,s){var t=new Date;t.setTime(t.getTime());if(e){e=e*1e3}var u=new Date(t.getTime()+e);document.cookie=n+'='+escape(v)+(e?';expires='+u.toGMTString()+';max-age='+e/1e3+';':'')+(p?';path='+p:'')+(d?';domain='+d:'')+(s?';secure':'')}function wpss_init_ckh(){wpss_set_ckh('".$ck_key."','".$ck_val."','". (60*60*4) ."','".$ck_dir."','".WPSS_SERVER_NAME."','".$ck_sec."');wpss_set_ckh('".$SJECT."','".$CKON."','". (60*60*1) ."','".$ck_dir."','".WPSS_SERVER_NAME."','".$ck_sec."');}wpss_init_ckh();jQuery(document).ready(function($){var h=\"form[method='post']\";\$(h).submit(function(){\$('<input>').attr('type','hidden').attr('name','".$jq_key."').attr('value','".$jq_val."').appendTo(h);return true;})});"."\n";

/* Timer */
$end_time=microtime(TRUE);
$total_time=js_wpss_timer($start_time,$end_time,FALSE,6);
$js_generated="// Generated in: ".$total_time.' seconds'.WPSS_EOL;
if(empty($total_time)||$total_time==0||$total_time>.5){$js_generated.="// ERROR: There is an error in your server configuration or website setup.".WPSS_EOL;} /* Time = 0.00000 or too long indicate server/config error */
$content .= $js_generated;
echo $content;
