<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonShortcodeSchema' ) ) {

	class WpssoJsonShortcodeSchema {

		private $p;
		private $save_data = false;
		private $json_data = array();

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->add();
		}

		public function add() {
			if ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
        			add_shortcode( WPSSOJSON_SCHEMA_SHORTCODE_NAME, array( &$this, 'shortcode' ) );
				$this->p->debug->log( '['.WPSSOJSON_SCHEMA_SHORTCODE_NAME.'] sharing shortcode added' );
			}
		}

		public function remove() {
			if ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
				remove_shortcode( WPSSOJSON_SCHEMA_SHORTCODE_NAME );
				$this->p->debug->log( '['.WPSSOJSON_SCHEMA_SHORTCODE_NAME.'] sharing shortcode removed' );
			}
		}

		public function shortcode( $atts, $content = null ) {

			if ( is_feed() ) {
				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: no schema in rss feeds'  );
				}
				return $content;
			}

			$atts_string = '';
			foreach ( $atts as $key => $value ) {
				$atts_string .= $key.'="'.$value.'" ';
			}

			if ( $this->save_data ) {
				// when a type is selected, a prop attribute value must be specified as well
				if ( ! empty( $atts['type'] ) && empty( $atts['prop'] ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'schema shortcode with type is missing a prop attribute value' );
					}
					if ( $this->p->notice->is_admin_pre_notices() ) {
						$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
						$err_msg = __( '%1$s %2$s shortcode with a type value of "%3$s" is missing the required prop attribute value.',
							'wpsso-schema-json-ld' );
						$this->p->notice->err( sprintf( $err_msg, $info['short'], WPSSOJSON_SCHEMA_SHORTCODE_NAME, $atts['type'] ) );
					}
				} elseif ( ! empty( $content ) && empty( $atts['type'] ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'schema shortcode with content is missing a type attribute value' );
					}
					if ( $this->p->notice->is_admin_pre_notices() ) {
						$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
						$err_msg = __( '%1$s %2$s shortcode with a description value is missing the required type attribute value.',
							'wpsso-schema-json-ld' );
						$this->p->notice->err( sprintf( $err_msg, $info['short'], WPSSOJSON_SCHEMA_SHORTCODE_NAME ) );
					}
				} else {
					$prop_name = '';
					$temp_data = array();
					foreach ( $atts as $key => $value ) {
						if ( $key === 'prop' ) {
							$prop_name = $value;
						} elseif ( $key === 'type' ) {
							if ( filter_var( $value, FILTER_VALIDATE_URL ) !== false ) {
								$type_url = $value;
							} else {
								$type_url = $this->p->schema->get_schema_type_url( $value );
							}
							$temp_data = WpssoSchema::get_schema_type_context( $type_url, $temp_data );
						} else {
							$temp_data[$key] = $value;
						}
					}
					if ( ! empty( $prop_name ) ) {
						if ( ! empty( $content ) ) {
							$temp_data['description'] = $this->p->util->cleanup_html_tags( $content );
						}
						$this->json_data[$prop_name] = $temp_data;
					} else {
						$this->json_data = SucomUtil::array_merge_recursive_distinct( $this->json_data, $temp_data );
					}
				}
			}

			// fix the wpautop extra paragraph prefix / suffix
			$content = preg_replace( '/(^<\/p>|<p>$)/', '', $content );

			return '<!-- wpssojson-schema-shortcode: '.$atts_string.'-->'.
				$content.'<!-- /wpssojson-schema-shortcode -->';
		}

		public function set_save_data( $bool ) {
			$this->save_data = $bool;
		}

		public function get_json_data() {
			$temp_data = $this->json_data;
			$this->save_data = false;	// reset to default value
			$this->json_data = array();	// reset to default value
			return $temp_data;
		}
	}
}

?>
