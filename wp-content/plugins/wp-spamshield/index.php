<?php
/**
 *  index.php
 *  Version: 20160817.01
 *  Author: Red Sand
 *  http://www.redsandmarketing.com/plugins/
 *
 *  This script keeps search engines, bots, and unwanted visitors from viewing your private plugin directory contents.
 *
 *  You can avoid the need for pages like this by adding a single line of code to the beginning of your .htaccess file:
 *
 *		## Add the following line to the beginning of your .htaccess for security and SEO.
 *		Options -Indexes
 *		## This will turn off indexes so your site won't reveal contents of directories that don't have an index file.
 *
 */

@ini_set('display_errors', 0);
error_reporting(0);

/* We're going to block bad bots/visitors and redirect human visitors to the website root. */
idx_getenv();
$new_url	= idx_get_site_url();
$code		= '403';
$status		= 'Forbidden';
if( !headers_sent() ) {
	if(function_exists('header_remove')){@header_remove('Cache-Control');@header_remove('Last-Modified');@header_remove('ETag');}
	header('X-Robots-Tag: none',TRUE);
	header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$status); /* Must use THIS version if using in conjunction with "Refresh". Allows you to set status before redirect. */
	header('Refresh: 0; url='.$new_url,TRUE);
	/* Using 'Refresh:' instead of 'Location:' allows you to set 403 status code first. */
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $code.' - '.$status; ?></title>
<meta name="description" content="" /><meta name="keywords" content="" />
<meta name="ROBOTS" content="NONE" />
<link rel="canonical" href="<?php echo $site_url; ?>" />
</head>
<body>
<?php
echo str_repeat( '&nbsp;', 512 );
?>

</body>
</html><?php
exit();

function idx_getenv( $e = FALSE, $add_vars = array() ) {
	global $_IDX_ENV;
	if( empty( $_IDX_ENV ) || !is_array( $_IDX_ENV ) ) { $_IDX_ENV = array(); }
	$_IDX_ENV = (array) $_IDX_ENV + (array) $_ENV;
	$vars = array( 'REMOTE_ADDR', 'SERVER_ADDR', 'LOCAL_ADDR', 'HTTP_HOST', 'SERVER_NAME', );
	$vars = !empty( $add_vars ) ? (array) $vars + (array) $add_vars : $vars;
	if( !empty( $e ) ) { $vars[] = $e; }
	foreach( $vars as $i => $v ) {
		if( empty( $_IDX_ENV[$v] ) ) { $_IDX_ENV[$v] = $_ENV[$v] = ''; if( function_exists( 'getenv' ) ) { $_IDX_ENV[$v] = $_ENV[$v] = @getenv($v); } }
	}
	return FALSE !== $e ? $_IDX_ENV[$e] : $_IDX_ENV;
}

function idx_get_site_url() {
	$url  = idx_is_ssl() ? 'https://' : 'http://';
	$url .= idx_get_server_name();
	return $url;
}

function idx_is_ssl() {
	if( !empty( $_SERVER['HTTPS'] )						&& 'off'	!==	$_SERVER['HTTPS'] )						{ return TRUE; }
	if( !empty( $_SERVER['SERVER_PORT'] )				&& '443'	==	$_SERVER['SERVER_PORT'] )				{ return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] )	&& 'https'	===	$_SERVER['HTTP_X_FORWARDED_PROTO'] )	{ return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] )		&& 'off'	!==	$_SERVER['HTTP_X_FORWARDED_SSL'] )		{ return TRUE; }
	return FALSE;
}

function idx_get_server_name() {
	global $_IDX_ENV;
	if(		!empty( $_SERVER['HTTP_HOST'] ) )		{ $server_name = $_SERVER['HTTP_HOST']; }
	elseif(	!empty( $_IDX_ENV['HTTP_HOST'] ) )		{ $server_name = $_SERVER['HTTP_HOST'] = $_IDX_ENV['HTTP_HOST']; }
	elseif(	!empty( $_SERVER['SERVER_NAME'] ) )		{ $server_name = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME']; }
	elseif(	!empty( $_IDX_ENV['SERVER_NAME'] ) )	{ $server_name = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $_IDX_ENV['SERVER_NAME']; }
	return !empty( $server_name ) && '.' !== trim( $server_name ) ? strtolower( $server_name ) : '';
}
