<?php
// Allow List - returns false if not found
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class chkauthorizenet extends be_module {
	public $searchname = 'authorizenet';
	public $searchlist = array(
			'198.241.207.102',
			'198.241.206.93',
			'198.241.206.93',
			'198.241.207.102',
			'198.241.207.97',
			'198.241.206.88',
			'198.241.207.105',
			'198.241.206.96',
			'198.241.207.104',
			'198.241.206.95',
			'198.241.206.95',
			'198.241.207.104',
			'198.241.206.93',
			'198.241.207.102',
			'198.241.206.38',
			'198.241.207.38',
			
			
			
// sandbox
			'198.241.206.25',
			'198.241.207.84',
			'198.241.206.22',
			'198.241.206.22',
			'198.241.207.86',
			'198.241.206.38',
			'198.241.207.38'

		);
}

?>