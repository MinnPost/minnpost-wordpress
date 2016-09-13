<?php

	if ( !function_exists( 'jordy_meow_flattr' ) ) {
		if ( !defined( 'WP_HIDE_DONATION_BUTTONS' ) )
			add_action( 'admin_head', 'jordy_meow_flattr', 1 );
		function jordy_meow_flattr () {
			?>
				<script type="text/javascript">
					/* <![CDATA[ */
					    (function() {
					        var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
					        s.type = 'text/javascript';
					        s.async = true;
					        s.src = '//api.flattr.com/js/0.6/load.js?mode=auto&uid=TigrouMeow&popout=0';
					        t.parentNode.insertBefore(s, t);
					    })();
					/* ]]> */
				</script>
			<?php
		}
		function by_jordy_meow( $hide_ads = false ) {
			echo '<div><span style="font-size: 13px; position: relative; top: -6px;">Developed by <a style="text-decoration: none;" target="_blank" href="http://apps.meow.fr">Jordy Meow</a></span>';
			if ( !defined( 'WP_HIDE_DONATION_BUTTONS' ) && !$hide_ads )
				echo ' <a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" title="Jordy Meow" href="http://profiles.wordpress.org/TigrouMeow/"></a>';
			echo '</div>';
		}
	}

	if ( !function_exists( 'jordy_meow_donation' ) ) {
		function jordy_meow_donation( $showWPE = true ) {
			if ( defined( 'WP_HIDE_DONATION_BUTTONS' ) && WP_HIDE_DONATION_BUTTONS == true )
				return;
			if ( $showWPE ) {
				echo '<a style="float: right;" target="_blank" href="http://www.shareasale.com/u.cfm?D=339321&U=767054&M=41388%20">
				<img src="' . trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'media-file-renamer/img') . 'wpengine.png" height="60" border="0" /></a>';
			}
		}
	}

	if ( !function_exists('jordy_meow_footer') ) {
		function jordy_meow_footer() {
			?>
			<div style=" color: #32595E; border: 1px solid #DFDFDF; position: absolute;margin-right: 20px;right: 0px;left: 0px;font-family: Tahoma;z-index: 10;background: white;margin-top: 15px;font-size: 7px;padding: 0px 10px;">
			<p style="font-size: 11px; font-family: Tahoma;"><b>This plugin is actively developed and maintained by <a href='http://www.meow.fr'>Jordy Meow</a></b>.<br />More of my tools are available on <a target='_blank' href="http://apps.meow.fr">Meow Apps</a> and my website is <a target='_blank' href='http://jordymeow.com'>Jordy Meow's Offbeat Guide to Japan</a>.
			</div>
			<?php
		}
	}
?>
