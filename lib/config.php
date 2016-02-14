<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonConfig' ) ) {

	class WpssoJsonConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssojson' => array(
					'version' => '1.0.0',	// plugin version
					'short' => 'WPSSO JSON',
					'name' => 'WPSSO Schema JSON-LD (WPSSO JSON)',
					'desc' => 'WPSSO extension to add complete Schema JSON-LD markup in webpage headers for Google and Pinterest.',
					'slug' => 'wpsso-schema-json-ld',
					'base' => 'wpsso-schema-json-ld/wpsso-schema-json-ld.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-schema-json-ld',
					'domain_path' => '/languages',
					'img' => array(
						'icon_small' => 'images/icon-128x128.png',
						'icon_medium' => 'images/icon-256x256.png',
					),
					'url' => array(
						// wordpress
						'download' => 'https://wordpress.org/plugins/wpsso-schema-json-ld/',
						'review' => 'https://wordpress.org/support/view/plugin-reviews/wpsso-schema-json-ld?filter=5&rate=5#postform',
						'readme' => 'https://plugins.svn.wordpress.org/wpsso-schema-json-ld/trunk/readme.txt',
						'wp_support' => 'https://wordpress.org/support/plugin/wpsso-schema-json-ld',
						// surniaulula
						'update' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/update/',
						'purchase' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/',
						'changelog' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/changelog/',
						'codex' => 'http://wpsso.com/codex/plugins/wpsso-schema-json-ld/',
						'faq' => 'http://wpsso.com/codex/plugins/wpsso-schema-json-ld/faq/',
						'notes' => '',
						'feed' => 'http://wpsso.com/category/application/wordpress/wp-plugins/wpsso-schema-json-ld/feed/',
						'pro_support' => 'http://wpsso-schema-json-ld.support.wpsso.com/',
					),
					'lib' => array(
						'gpl' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
						),
						'pro' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
							'head' => array(
								'article' => 'Item Type Article',
								'place' => 'Item Type Place',
								'product' => 'Item Type Product',
							),
							'prop' => array(
								'rating' => 'Property AggregateRating',
							),
						),
					),
				),
			),
		);

		public static function set_constants( $plugin_filepath ) { 
			define( 'WPSSOJSON_FILEPATH', $plugin_filepath );						
			define( 'WPSSOJSON_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'WPSSOJSON_PLUGINSLUG', self::$cf['plugin']['wpssojson']['slug'] );		// wpsso-sp
			define( 'WPSSOJSON_PLUGINBASE', self::$cf['plugin']['wpssojson']['base'] );		// wpsso-sp/wpsso-sp.php
			define( 'WPSSOJSON_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
		}

		public static function require_libs( $plugin_filepath ) {

			require_once( WPSSOJSON_PLUGINDIR.'lib/register.php' );
			require_once( WPSSOJSON_PLUGINDIR.'lib/filters.php' );

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		// gpl / pro library loader
		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {
			if ( $ret === false && ! empty( $filespec ) ) {
				$filepath = WPSSOJSON_PLUGINDIR.'lib/'.$filespec.'.php';
				if ( file_exists( $filepath ) ) {
					require_once( $filepath );
					if ( empty( $classname ) )
						return 'wpssojson'.str_replace( array( '/', '-' ), '', $filespec );
					else return $classname;
				}
			}
			return $ret;
		}
	}
}

?>
