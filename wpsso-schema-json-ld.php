<?php
/*
 * Plugin Name: WPSSO Schema JSON-LD Markup
 * Plugin Slug: wpsso-schema-json-ld
 * Text Domain: wpsso-schema-json-ld
 * Domain Path: /languages
 * Plugin URI: https://wpsso.com/extend/plugins/wpsso-schema-json-ld/
 * Assets URI: https://surniaulula.github.io/wpsso-schema-json-ld/assets/
 * Author: JS Morisset
 * Author URI: https://surniaulula.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Description: WPSSO extension to add Schema JSON-LD / SEO markup for Articles, Events, Local Business, Products, Recipes, Reviews + many more.
 * Requires At Least: 3.7
 * Tested Up To: 4.8
 * Version: 1.15.2-dev.1
 * 
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *	{major}		Major structural code changes / re-writes or incompatible API changes.
 *	{minor}		New functionality was added or improved in a backwards-compatible manner.
 *	{bugfix}	Backwards-compatible bug fixes or small improvements.
 *	{stage}.{level}	Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 * 
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJson' ) ) {

	class WpssoJson {

		public $p;			// Wpsso
		public $reg;			// WpssoJsonRegister
		public $filters;		// WpssoJsonFilters

		private static $instance;
		private static $have_min = true;	// have minimum wpsso version

		public function __construct() {

			require_once ( dirname( __FILE__ ).'/lib/config.php' );
			WpssoJsonConfig::set_constants( __FILE__ );
			WpssoJsonConfig::require_libs( __FILE__ );	// includes the register.php class library
			$this->reg = new WpssoJsonRegister();		// activate, deactivate, uninstall hooks

			if ( is_admin() ) {
				add_action( 'admin_init', array( __CLASS__, 'required_check' ) );
				add_action( 'wpsso_init_textdomain', array( __CLASS__, 'wpsso_init_textdomain' ) );
			}

			add_filter( 'wpsso_get_config', array( &$this, 'wpsso_get_config' ), 20, 2 );
			add_action( 'wpsso_init_options', array( &$this, 'wpsso_init_options' ), 100 );
			add_action( 'wpsso_init_objects', array( &$this, 'wpsso_init_objects' ), 100 );
			add_action( 'wpsso_init_plugin', array( &$this, 'wpsso_init_plugin' ), 100 );
		}

		public static function &get_instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new self;
			return self::$instance;
		}

		public static function required_check() {
			if ( ! class_exists( 'Wpsso' ) ) {
				add_action( 'all_admin_notices', array( __CLASS__, 'required_notice' ) );
			}
		}

		// also called from the activate_plugin method with $deactivate = true
		public static function required_notice( $deactivate = false ) {
			self::wpsso_init_textdomain();
			$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
			$die_msg = __( '%1$s is an extension for the %2$s plugin &mdash; please install and activate the %3$s plugin before activating %4$s.',
				'wpsso-schema-json-ld' );
			$err_msg = __( 'The %1$s extension requires the %2$s plugin &mdash; please install and activate the %3$s plugin.',
				'wpsso-schema-json-ld' );
			if ( $deactivate === true ) {
				if ( ! function_exists( 'deactivate_plugins' ) ) {
					require_once trailingslashit( ABSPATH ).'wp-admin/includes/plugin.php';
				}
				deactivate_plugins( $info['base'], true );	// $silent = true
				wp_die( '<p>'.sprintf( $die_msg, $info['name'], $info['req']['name'], $info['req']['short'], $info['short'] ).'</p>' );
			} else {
				echo '<div class="notice notice-error error"><p>'.
					sprintf( $err_msg, $info['name'], $info['req']['name'], $info['req']['short'] ).'</p></div>';
			}
		}

		public static function wpsso_init_textdomain() {
			load_plugin_textdomain( 'wpsso-schema-json-ld', false, 'wpsso-schema-json-ld/languages/' );
		}

		public function wpsso_get_config( $cf, $plugin_version = 0 ) {
			$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];

			if ( version_compare( $plugin_version, $info['req']['min_version'], '<' ) ) {
				self::$have_min = false;
				return $cf;
			}

			return SucomUtil::array_merge_recursive_distinct( $cf, WpssoJsonConfig::$cf );
		}

		public function wpsso_init_options() {
			if ( method_exists( 'Wpsso', 'get_instance' ) ) {
				$this->p =& Wpsso::get_instance();
			} else {
				$this->p =& $GLOBALS['wpsso'];
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( self::$have_min ) {
				$this->p->avail['p_ext']['json'] = true;
				foreach ( array( 'gpl', 'pro' ) as $lib ) {
					foreach ( array( 'head', 'prop' ) as $sub ) {
						if ( ! isset( WpssoJsonConfig::$cf['plugin']['wpssojson']['lib'][$lib][$sub] ) ||
							! is_array( WpssoJsonConfig::$cf['plugin']['wpssojson']['lib'][$lib][$sub] ) ) {
							continue;
						}
						foreach ( WpssoJsonConfig::$cf['plugin']['wpssojson']['lib'][$lib][$sub] as $id_key => $label ) {
							list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );
							$this->p->avail[$sub][$id] = true;
						}
					}
				}
			} else {
				$this->p->avail['p_ext']['json'] = false;	// just in case
			}
		}

		public function wpsso_init_objects() {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( self::$have_min ) {
				$this->filters = new WpssoJsonFilters( $this->p );
				$this->schema = new WpssoJsonSchema( $this->p );
			}
		}

		public function wpsso_init_plugin() {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! self::$have_min ) {
				return $this->min_version_notice();	// stop here
			}
		}

		private function min_version_notice() {
			$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
			$wpsso_version = $this->p->cf['plugin']['wpsso']['version'];

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( $info['name'].' requires '.$info['req']['short'].' v'.
					$info['req']['min_version'].' or newer ('.$wpsso_version.' installed)' );
			}

			if ( is_admin() ) {
				$this->p->notice->err( sprintf( __( 'The %1$s extension v%2$s requires %3$s v%4$s or newer (v%5$s currently installed).',
					'wpsso-schema-json-ld' ), $info['name'], $info['version'], $info['req']['short'],
						$info['req']['min_version'], $wpsso_version ) );
			}
		}
	}

        global $wpssojson;
	$wpssojson =& WpssoJson::get_instance();
}

?>
