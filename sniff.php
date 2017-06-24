<?php
/**
 * Perform sniff check.
 *
 * @since 0.1.0
 *
 * @param string $file Path of the files to sniff.
 * @param array  $args Arguments.
 *
 * @return bool
 */
function theme_check_do_sniff( $files, $args = array() ) {

	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	$defaults = array(
		'show_warnings'       => false,
		'raw_output'          => 0,
		'minimum_php_version' => '5.2',
		'standard'            => array( 'WordPress-Theme' ),
		'extensions'          => array( 'php','css', ),
	);

	$args = wp_parse_args( $args, $defaults );

	// Set CLI arguments.
	$values['files']       = $files;
	$values['reportWidth'] = '110';

	if ( 0 === absint( $args['raw_output'] ) ) {
		$values['reports']['json'] = null;
	}

	if ( ! empty( $args['extensions'] ) ) {
		$values['extensions'] = $args['extensions'];
	}

	if ( ! empty( $args['standard'] ) ) {
		$values['standard'] = $args['standard'];
	}
	$values['standard'][] = plugin_dir_path( __FILE__ ) . 'bin/phpcs.xml';

	// Set default standard.
	PHP_CodeSniffer::setConfigData( 'default_standard', 'WordPress-Theme', true );

	// Ignoring warnings when generating the exit code.
	PHP_CodeSniffer::setConfigData( 'ignore_warnings_on_exit', true, true );

	// Show only errors?
	PHP_CodeSniffer::setConfigData( 'show_warnings', absint( $args['show_warnings'] ), true );

	// Ignore unrelated files from the check.
	$values['ignored'] = array(
		'.*/node_modules/.*',
	);

	// Set minimum supported PHP version.
	PHP_CodeSniffer::setConfigData( 'testVersion', $args['minimum_php_version'] . '-7.0', true );

	// Set text domains.
	PHP_CodeSniffer::setConfigData( 'text_domain', implode( ',', $args['text_domains'] ), true );

	// Path to WordPress Theme coding standard.
	PHP_CodeSniffer::setConfigData( 'installed_paths', plugin_dir_path( __FILE__ ) . 'vendor/wp-coding-standards/wpcs/', true );

	// Initialise CodeSniffer.
	$phpcs_cli = new PHP_CodeSniffer_CLI();
	$phpcs_cli->checkRequirements();

	ob_start();
	$num_errors = $phpcs_cli->process( $values );
	$raw_output = ob_get_clean();
	$output = '';

	// Sniff theme files.
	if ( 1 === absint( $args['raw_output'] ) ) {
		if ( ! empty( $raw_output ) ) {
			$output = '<pre>' . esc_html( $raw_output ) . '</pre>';
		}
	} else {
		$output = json_decode( $raw_output );
	} // End if().

	return array(
		$num_errors,
		$output
	);
}

/**
 * Render JSON data in cleaner format.
 *
 * @since 0.1.0
 *
 * @param string $json JSON data.
 */
function theme_check_render_json_report( $json, $theme_dir ) {

	if ( ! isset( $json->files ) ) {
		return;
	}
	?>
	<?php foreach ( $json->files as $file_key => $file ) : ?>
		<?php
		if ( 0 === absint( $file->errors ) && 0 === absint( $file->warnings ) ) {
			continue;
		}
		$file_name = str_replace( $theme_dir, '', $file_key );
		?>
		<ul class="tc-result">
				<li class="report-file-heading-field"><?php printf( esc_html__( 'File: %s','ns-theme-check' ), esc_html( $file_name ) ); ?></li><!-- .report-file-heading-field -->
			<?php if ( ! empty( $file->messages ) && is_array( $file->messages ) ) : ?>
				<ul>
					<?php foreach ( $file->messages as $item ) : ?>
						<?php $row_class = ( 'error' === strtolower( $item->type ) ) ?'item-type-error' : 'item-type-warning'; ?>
						<li class="<?php echo esc_attr( $row_class ); ?>">
							<span class="td-line"><?php printf( esc_html__( 'Line: %d','ns-theme-check' ), absint( $item->line ) ); ?></span>
							<span class="td-type"><?php echo esc_html( $item->type ); ?></span>
							<span class="td-message"><?php echo esc_html( $item->message ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</ul><!-- .report-file-item -->
	<?php endforeach; ?>
	<?php
}
