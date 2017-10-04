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
		private $sc_names = array();
		private $sc_depth = 0;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			foreach ( range( 0, WPSSOJSON_SCHEMA_SHORTCODE_DEPTH ) as $depth ) {
				$this->sc_names[] = WPSSOJSON_SCHEMA_SHORTCODE_NAME.
					( $depth ? WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR.$depth : '' );
			}

			add_filter( 'no_texturize_shortcodes', array( &$this, 'exclude_from_wptexturize' ) );
			add_filter( 'sucom_strip_shortcodes_preg', array( &$this, 'strip_shortcodes_preg' ) );

			$this->add_shortcode();

			$this->p->util->add_plugin_actions( $this, array( 
				'text_filter_before' => 1,
				'text_filter_after' => 1,
			) );
		}

		public function exclude_from_wptexturize( $shortcodes ) {
			foreach ( $this->sc_names as $sc_name ) {
				$shortcodes[] = $sc_name;
			}
			return $shortcodes;
		}

		public function strip_shortcodes_preg( $preg_array ) {
			$preg_array[] = '/\[\/?'.
				WPSSOJSON_SCHEMA_SHORTCODE_NAME.
				WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR.
				'[0-9]+[^\]]*\]/';
			return $preg_array;
		}

		public function action_text_filter_before( $filter_name ) {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'filter_name' => $filter_name,
				) );
			}
			return $this->add_shortcode();
		}

		public function action_text_filter_after( $filter_name ) {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_args( array( 
					'filter_name' => $filter_name,
				) );
			}
			return $this->remove_shortcode();
		}

		public function add_shortcode() {
			if ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
				$sc_added = false;
				foreach ( $this->sc_names as $sc_name ) {
					if ( ! shortcode_exists( $sc_name ) ) {
						$sc_added = true;
        					add_shortcode( $sc_name, array( &$this, 'do_shortcode' ) );
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( '['.$sc_name.'] schema shortcode added' );
						}
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'cannot add ['.$sc_name.'] schema shortcode - shortcode already exists' );
					}
				}
				return $sc_added;
			}
			return false;
		}

		public function remove_shortcode() {
			if ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
				$sc_removed = false;
				foreach ( $this->sc_names as $sc_name ) {
					if ( shortcode_exists( $sc_name ) ) {
						$sc_removed = true;
						remove_shortcode( $sc_name );
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( '['.$sc_name.'] schema shortcode removed' );
						}
					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'cannot remove ['.$sc_name.'] schema shortcode - shortcode does not exist' );
					}
				}
				return $sc_removed;
			}
			return false;
		}

		public function do_shortcode( $atts, $content, $sc_name ) {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
			if ( $this->set_data ) {
				/*
				 * When a schema type id is selected, a prop attribute value must be specified as well.
				 */
				if ( ! empty( $atts['type'] ) && empty( $atts['prop'] ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( $sc_name.' shortcode with type is missing a prop attribute value' );
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
						$this->p->debug->log( $sc_name.' shortcode with content is missing a type attribute value' );
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
									$this->p->debug->log( $sc_name.' shortcode type "'.$value.'" is not a recognized value' );
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

							if ( WPSSOJSON_SCHEMA_SHORTCODE_SINGLE_CONTENT ) {
								$prop_content = preg_replace( '/\['.WPSSOJSON_SCHEMA_SHORTCODE_NAME.
									WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR.'[0-9]+[^\]]*\].*\[\/'.
									WPSSOJSON_SCHEMA_SHORTCODE_NAME.
									WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR.'[0-9]+[^\]]*\]/s', '', $content );
							} else {
								$prop_content =& $content;
							}

							if ( ! isset( $this->data_ref[$prop_name]['description'] ) ) {
								$this->data_ref[$prop_name]['description'] = $this->p->util->cleanup_html_tags( $prop_content );
							}

							$og_videos = $this->p->media->get_content_videos( 1, false, false, $prop_content );
							if ( ! empty( $og_videos ) ) {
								WpssoJsonSchema::add_video_list_data( $this->data_ref[$prop_name]['video'], $og_videos, 'og:video' );
							}

							$size_name = $this->p->cf['lca'].'-schema';
							$og_images = $this->p->media->get_content_images( 1, $size_name, false, false, false, $prop_content );
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
				$atts_string = '';
				foreach ( $atts as $key => $value ) {
					$atts_string .= $key.'="'.$value.'" ';
				}

				// fix extra paragraph prefix / suffix from wpautop
				$content = preg_replace( '/(^<\/p>|<p>$)/', '', $content );

				$content = do_shortcode( $content );

				// show attributes in html comment for debugging
				$content = '<!-- '.$sc_name.' shortcode: '.$atts_string.'-->'.
						$content.'<!-- /'.$sc_name.' shortcode -->';

				return $content;
			}
		}

		public function get_json_data( $content, &$json_data = array(), $increment = false ) {
			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
			if ( ! empty( $content ) ) {
				if ( $increment ) {
					$this->sc_depth++;
				} else {
					$this->set_data = true;
				}

				/*
				 * If we already have a position / depth for additions to the json_data array,
				 * save it so we can return here after calling do_shortcode().
				 */
				if ( isset( $this->data_ref ) ) {
					$this->prev_ref[$this->sc_depth] =& $this->data_ref;
				}

				/*
				 * Set the current position / depth in the json_data array for new additions.
				 */
				$this->data_ref =& $json_data;

				do_shortcode( $content );

				/*
				 * If we have a previous position / depth saved, restore that position so later 
				 * shortcode additions can be added from this position / depth.
				 */
				if ( isset( $this->prev_ref[$this->sc_depth] ) ) {
					$this->data_ref =& $this->prev_ref[$this->sc_depth];
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
