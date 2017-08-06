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
		private $set_data = false;
		private $data_ref = null;
		private $prev_ref = null;
		private $sc_depth = 0;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			add_filter( 'no_texturize_shortcodes', array( &$this, 'exclude_from_wptexturize' ) );
			add_filter( 'sucom_strip_shortcodes_preg', array( &$this, 'strip_shortcodes_preg' ) );

			$this->add();
		}

		public function exclude_from_wptexturize( $shortcodes ) {
			foreach ( range( 0, WPSSOJSON_SCHEMA_SHORTCODE_DEPTH ) as $depth ) {
				$sc_name = WPSSOJSON_SCHEMA_SHORTCODE_NAME.( $depth ? '-'.$depth : '' );
				$shortcodes[] = $sc_name;
			}
			return $shortcodes;
		}

		public function strip_shortcodes_preg( $preg_array ) {
			$preg_array[] = '/\[\/?'.WPSSOJSON_SCHEMA_SHORTCODE_NAME.'-[0-9]+[^\]]*\]/';
			return $preg_array;
		}

		public function add() {
			if ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
				foreach ( range( 0, WPSSOJSON_SCHEMA_SHORTCODE_DEPTH ) as $depth ) {
					$sc_name = WPSSOJSON_SCHEMA_SHORTCODE_NAME.( $depth > 0 ? '-'.$depth : '' );
        				add_shortcode( $sc_name, array( &$this, 'shortcode' ) );
					$this->p->debug->log( '['.$sc_name.'] schema shortcode added' );
				}
			}
		}

		public function remove() {
			if ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
				foreach ( range( 0, WPSSOJSON_SCHEMA_SHORTCODE_DEPTH ) as $depth ) {
					$sc_name = WPSSOJSON_SCHEMA_SHORTCODE_NAME.( $depth > 0 ? '-'.$depth : '' );
					remove_shortcode( $sc_name );
					$this->p->debug->log( '['.$sc_name.'] schema shortcode removed' );
				}
			}
		}

		public function shortcode( $atts, $content, $sc_name ) {

			if ( $this->set_data ) {
				/*
				 * When a schema type id is selected, a prop attribute value must be specified as well.
				 */
				if ( ! empty( $atts['type'] ) && empty( $atts['prop'] ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'schema shortcode with type is missing a prop attribute value' );
					}
					if ( $this->p->notice->is_admin_pre_notices() ) {
						$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
						$err_msg = __( '%1$s %2$s shortcode with a type value of "%3$s" is missing the required prop attribute value.',
							'wpsso-schema-json-ld' );
						$this->p->notice->err( sprintf( $err_msg, $info['short'], $sc_name, $atts['type'] ) );
					}
				/*
				 * When there's content (for a description), the schema type id must be specified -
				 * otherwise it would apply to the main schema, where there is already a description.
				 */
				} elseif ( ! empty( $content ) && empty( $atts['type'] ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'schema shortcode with content is missing a type attribute value' );
					}
					if ( $this->p->notice->is_admin_pre_notices() ) {
						$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
						$err_msg = __( '%1$s %2$s shortcode with a description value is missing the required type attribute value.',
							'wpsso-schema-json-ld' );
						$this->p->notice->err( sprintf( $err_msg, $info['short'], $sc_name ) );
					}
				} else {
					$type_url = '';
					$prop_name = '';
					$temp_data = array();

					foreach ( $atts as $key => $value ) {
						// ignore @context, @type, etc. attribute keys
						if ( strpos( $key, '@' ) === 0 ) {
							continue;
						// save the property name to add the new json array
						} elseif ( $key === 'prop' ) {
							$prop_name = $value;
						// get the @context and @type for the new json array
						} elseif ( $key === 'type' ) {
							if ( filter_var( $value, FILTER_VALIDATE_URL ) !== false ) {
								$type_url = $value;
							} else {
								$type_url = $this->p->schema->get_schema_type_url( $value );
							}
							if ( empty( $type_url ) ) {
								if ( $this->p->debug->enabled ) {
									$this->p->debug->log( 'schema shortcode type "'.$value.'" is not a recognized value' );
								}
								if ( $this->p->notice->is_admin_pre_notices() ) {
									$info = WpssoJsonConfig::$cf['plugin']['wpssojson'];
									$err_msg = __( '%1$s %2$s shortcode type attribute "%3$s" is not a recognized value.',
										'wpsso-schema-json-ld' );
									$this->p->notice->err( sprintf( $err_msg, $info['short'], $sc_name, $value ) );
								}
							} else {
								$temp_data = WpssoSchema::get_schema_type_context( $type_url, $temp_data );
							}
						// all other attribute keys are assumed to be schema property names
						} else {
							$temp_data[$key] = $value;
						}
					}

					if ( $prop_name ) {

						if ( ! isset( $this->data_ref[$prop_name] ) || ! is_array( $this->data_ref[$prop_name] ) ) {
							$this->data_ref[$prop_name] = array();
						}
						$this->data_ref[$prop_name] = SucomUtil::array_merge_recursive_distinct( $this->data_ref[$prop_name], $temp_data );

						if ( ! empty( $content ) ) {

							if ( ! isset( $this->data_ref[$prop_name]['description'] ) ) {
								$this->data_ref[$prop_name]['description'] = $this->p->util->cleanup_html_tags( $content );
							}

							$og_videos = $this->p->media->get_content_videos( 1, false, false, $content );
							if ( ! empty( $og_videos ) ) {
								WpssoJsonSchema::add_video_list_data( $this->data_ref[$prop_name]['video'], $og_videos, 'og:video' );
							}

							$size_name = $this->p->cf['lca'].'-schema';
							$og_images = $this->p->media->get_content_images( 1, $size_name, false, false, false, $content );
							if ( ! empty( $og_images ) ) {
								WpssoSchema::add_image_list_data( $this->data_ref[$prop_name]['image'], $og_images, 'og:image' );
							}

							$this->get_json_data( $content, $this->data_ref[$prop_name], true );
						}
					} else {
						$this->data_ref = SucomUtil::array_merge_recursive_distinct( $this->data_ref, $temp_data );
					}
				}
				return '';

			} else {
				// fix extra paragraph prefix / suffix from wpautop
				$content = preg_replace( '/(^<\/p>|<p>$)/', '', $content );
	
				$atts_string = '';
				foreach ( $atts as $key => $value ) {
					$atts_string .= $key.'="'.$value.'" ';
				}
	
				// show attributes in comment for debugging
				return '<!-- wpssojson-schema-shortcode: '.$atts_string.'-->'.
					$content.'<!-- /wpssojson-schema-shortcode -->';
			}
		}

		public function get_json_data( $content, &$json_data = array(), $increment = false ) {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
			if ( ! empty( $content ) ) {
				if ( $increment ) {
					$this->sc_depth++;
					$sc_name = WPSSOJSON_SCHEMA_SHORTCODE_NAME.'-'.$this->sc_depth;
				} else {
					$this->set_data = true;
					$sc_name = WPSSOJSON_SCHEMA_SHORTCODE_NAME;
				}
				if ( has_shortcode( $content, $sc_name ) ) {
					if ( isset( $this->data_ref ) ) {
						$this->prev_ref[$this->sc_depth] =& $this->data_ref;
					}
					$this->data_ref =& $json_data;
					do_shortcode( $content );
					if ( isset( $this->prev_ref[$this->sc_depth] ) ) {
						$this->data_ref =& $this->prev_ref[$this->sc_depth];
					}
				}
				if ( $increment ) {
					$this->sc_depth--;
				} else {
					$this->set_data = false;
				}
			}
			return $json_data;
		}
	}
}

?>
