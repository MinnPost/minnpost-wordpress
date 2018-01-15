<?php
/**
 * Plugin Name: LH Multipart Email
 * Plugin URI: https://lhero.org/portfolio/lh-multipart-email/
 * Description: Makes all html emails Html and plain text multipart emails
 * Version: 1.01
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com/
 * Text Domain: lh_multipart_email
*/


if (!class_exists('LH_multipart_email_plugin')) {

class LH_multipart_email_plugin {


//http://wordpress.stackexchange.com/a/191974
//http://stackoverflow.com/a/2564472

public function phpmailer_init( $phpmailer ){
  if( ($phpmailer->ContentType == 'text/html') && empty( $phpmailer->AltBody )) {

if (!class_exists('Html2Text\Html2Text') && !class_exists('Html2Text\Html2TextException')){

require_once("includes/Html2Text.php");
require_once("includes/Html2TextException.php");

}


$phpmailer->AltBody = Html2Text\Html2Text::convert($phpmailer->Body);




}
}



public function __construct() {


add_action( 'phpmailer_init', array($this,"phpmailer_init"), 1000, 1 );


}


}



$lh_multipart_email_instance = new LH_multipart_email_plugin();

}

?>