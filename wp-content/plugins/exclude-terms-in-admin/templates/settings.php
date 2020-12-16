<form method="post" action="options.php">
	<?php
		settings_fields( $section ) . do_settings_sections( $section );
		submit_button( esc_html__( 'Save settings', 'exclude-terms-admin' ) );
	?>
</form>
