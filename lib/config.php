<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonConfig' ) ) {

	class WpssoJsonConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssojson' => array(
					'version' => '1.19.3-dev.1',		// plugin version
					'opt_version' => '10',		// increment when changing default options
					'short' => 'WPSSO JSON',	// short plugin name
					'name' => 'WPSSO Schema JSON-LD Markup',
					'desc' => 'WPSSO Core extension to add Schema JSON-LD / SEO markup for Articles, Events, Local Business, Products, Recipes, Reviews + many more.',
					'slug' => 'wpsso-schema-json-ld',
					'base' => 'wpsso-schema-json-ld/wpsso-schema-json-ld.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-schema-json-ld',
					'domain_path' => '/languages',
					'req' => array(
						'short' => 'WPSSO',
						'name' => 'WPSSO Core',
						'min_version' => '3.48.12',
					),
					'img' => array(
						'icons' => array(
							'low' => 'images/icon-128x128.png',
							'high' => 'images/icon-256x256.png',
						),
					),
					'lib' => array(
						// submenu items must have unique keys
						'submenu' => array (
							'schema-json-ld' => 'Schema Markup',
							'schema-shortcode' => 'Schema Shortcode',
						),
						'shortcode' => array(
							'schema' => 'Schema Shortcode',
						),
						'gpl' => array(
							'admin' => array(
								'advanced' => 'Advanced Settings',
								'post' => 'Post Settings',
							),
							'head' => array(
								'webpage' => '(code) Schema Type WebPage (webpage)',
								'webpage#blogposting:no_load' => '(code) Schema Type Blog Posting (blog.posting)',
							),
						),
						'pro' => array(
							'admin' => array(
								'advanced' => 'Advanced Settings',
								'post' => 'Post Settings',
							),
							'head' => array(
								'article' => '(code) Schema Type Article (article)',
								'blog' => '(code) Schema Type Blog (blog)',
								'collectionpage' => '(code) Schema Type Collection Page (webpage.collection)',
								'creativework' => '(code) Schema Type Creative Work (creative.work)',
								'event' => '(code) Schema Type Event (event)',
								'foodestablishment' => '(code) Schema Type Food Establishment (food.establishment)',
								'jobposting' => '(code) Schema Type Job Posting (job.posting)',
								'localbusiness' => '(code) Schema Type Local Business (local.business)',
								'organization' => '(code) Schema Type Organization (organization)',
								'person' => '(code) Schema Type Person (person)',
								'place' => '(code) Schema Type Place (place)',
								'product' => '(code) Schema Type Individual Product (product)',
								'profilepage' => '(code) Schema Type Profile Page (webpage.profile)',
								'recipe' => '(code) Schema Type Recipe (recipe)',
								'review' => '(code) Schema Type Review (review)',
								'searchresultspage' => '(code) Schema Type Search Results Page (webpage.search.results)',
								'webpage' => '(code) Schema Type WebPage (webpage)',
								'website' => '(code) Schema Type WebSite (website)',
							),
							'prop' => array(
								'aggregaterating' => '(code) Property Aggregate Rating',
								'review' => '(code) Property Reviews',
							),
							'job' => array(
								'simplejobboard' => '(plugin) Simple Job Board',
								'wpjobmanager' => '(plugin) WP Job Manager',
							),
							'recipe' => array(
								'wprecipemaker' => '(plugin) WP Recipe Maker',
								'wpultimaterecipe' => '(plugin) WP Ultimate Recipe',
							),
							'review' => array(
								'wpproductreview' => '(plugin) WP Product Review',
							),
						),
					),
				),
			),
			'opt' => array(						// options
				'defaults' => array(
					'plugin_json_post_data_cache_exp' => 1209600,	// Schema Post Data Cache Expiry (2 weeks)
				),	// end of defaults
				'site_defaults' => array(
					'plugin_json_post_data_cache_exp' => 1209600,	// Schema Post Data Cache Expiry (2 weeks)
					'plugin_json_post_data_cache_exp:use' => 'default',
				),	// end of site defaults
			),
			'wp' => array(				// wordpress
				'transient' => array(
					'wpsso_j_' => array(
						'label' => 'Schema Post Data',
						'text_domain' => 'wpsso-schema-json-ld',
						'opt_key' => 'plugin_json_post_data_cache_exp',
						'filter' => 'wpsso_cache_expire_json_post_data',
					),
				),
			),
			'menu' => array(
				'dashicons' => array(
					'schema-shortcode' => 'info',
				),
			),
		);

		public static function get_version( $add_slug = false ) {
			$ext = 'wpssojson';
			$info =& self::$cf['plugin'][$ext];
			return $add_slug ? $info['slug'].'-'.$info['version'] : $info['version'];
		}

		public static function set_constants( $plugin_filepath ) { 
			if ( defined( 'WPSSOJSON_VERSION' ) ) {			// execute and define constants only once
				return;
			}
			define( 'WPSSOJSON_VERSION', self::$cf['plugin']['wpssojson']['version'] );						
			define( 'WPSSOJSON_FILEPATH', $plugin_filepath );						
			define( 'WPSSOJSON_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'WPSSOJSON_PLUGINSLUG', self::$cf['plugin']['wpssojson']['slug'] );		// wpsso-sp
			define( 'WPSSOJSON_PLUGINBASE', self::$cf['plugin']['wpssojson']['base'] );		// wpsso-sp/wpsso-sp.php
			define( 'WPSSOJSON_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );

			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {
			if ( null === $var_const ) {
				$var_const = self::get_variable_constants();
			}
			foreach ( $var_const as $name => $value ) {
				if ( ! defined( $name ) ) {
					define( $name, $value );
				}
			}
		}

		public static function get_variable_constants() {
			$var_const = array();

			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_NAME'] = 'schema';
			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR'] = '_';
			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_DEPTH'] = 3;
			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_SINGLE_CONTENT'] = true;

			foreach ( $var_const as $name => $value ) {
				if ( defined( $name ) ) {
					$var_const[$name] = constant( $name );	// inherit existing values
				}
			}

			return $var_const;
		}

		public static function require_libs( $plugin_filepath ) {

			require_once WPSSOJSON_PLUGINDIR.'lib/register.php';
			require_once WPSSOJSON_PLUGINDIR.'lib/filters.php';
			require_once WPSSOJSON_PLUGINDIR.'lib/schema.php';

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {
			if ( false === $ret && ! empty( $filespec ) ) {
				$filepath = WPSSOJSON_PLUGINDIR.'lib/'.$filespec.'.php';
				if ( file_exists( $filepath ) ) {
					require_once $filepath;
					if ( empty( $classname ) ) {
						return SucomUtil::sanitize_classname( 'wpssojson'.$filespec, false );	// $underscore = false
					} else {
						return $classname;
					}
				}
			}
			return $ret;
		}
	}
}

