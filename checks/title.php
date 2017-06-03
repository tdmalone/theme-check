<?php
/**
 * Checks for the title:
 * Are there <title> and </title> tags?
 * Is there a call to wp_title()?
 * There can't be any hardcoded text in the <title> tag.
 *
 * See: http://make.wordpress.org/themes/guidelines/guidelines-theme-check/
 */
class Title_Checks implements themecheck {
    protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );

		// Look for add_theme_support( 'title-tag' ) first
		$titletag = true;
		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]title-tag#', $php ) ) {
			$this->error[] = '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('No reference to <strong>add_theme_support( "title-tag" )</strong> was found in the theme.', 'theme-check' );
			$titletag = false;
			$ret = false;
		}

		// Check whether there is a call to wp_title()
		checkcount();
		if ( 0 <= strpos( $php, 'wp_title(' ) && !$titletag ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check').'</span>: ' . __( 'The theme must not call to <strong>wp_title()</strong>.', 'theme-check' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Title_Checks;
