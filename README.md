# Theme Check with Exclusions

This is a forked and slightly modified copy of the [Theme Check](https://github.com/WordPress/theme-check) plugin by [Samuel Wood](https://profiles.wordpress.org/otto42). It adds the ability to exclude directories from your theme that you do not want checked ([some common files and directories](https://github.com/tdmalone/theme-check-tdmalone/blob/master/checkbase.php#L162) are excluded by default).

Note that you probably don't want to use this functionality when you're getting ready to submit the theme to the WordPress theme repo (because you really _shouldn't_ have these files and directories there!). But it's great for development, when you likely need them!

**[See the original plugin's readme.txt here](readme.txt).**

## Installation

This plugin is not in the WordPress plugin repository. Because that would be confusing!

Instead, install it directly to your site.

With wp-cli:

    wp plugin install --activate https://github.com/tdmalone/theme-check-tdmalone/archive/master.zip

Manually:

* Download [this ZIP file](https://github.com/tdmalone/theme-check-tdmalone/archive/master.zip)
* Extract it into your `wp-content/plugins` directory
* Rename the plugin directory - if you like - to remove the `-master` from it (but don't set it just to `theme-check`, unless you want updates from the WordPress plugin repo to override it with the original plugin)
* Activate the plugin on your Plugins page in the WordPress admin

## Usage

To use the plugin, just head to _Appearance -> Theme Check_ in your WordPress admin. It's in the same place as the original plugin.

This plugin, however, automatically excludes [some common files and directories](https://github.com/tdmalone/theme-check-tdmalone/blob/master/checkbase.php#L162) that you might have sitting around during development.

You can modify these exclusions if you wish, with a filter in your theme:

    add_filter( 'tm_theme_check_exclusions', function( $exclusions ) {

      // Add a directory.
      $exclusions[] = 'some-directory/';

      // Remove a directory.
      unset( $exclusions[ array_keys( $exclusions, 'node_modules/' )[0] ] );

      return $exclusions;

    });

All files/folders provided are considered relative to the theme root, whether or not you include a leading slash.

## License

[GPLv2](LICENSE).
