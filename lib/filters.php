<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonFilters' ) ) {

	class WpssoJsonFilters {

		protected $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			add_filter( 'amp_post_template_metadata', array( $this, 'filter_amp_post_template_metadata' ), 9000, 2 );

			$crawler_name = empty( $this->p->avail['*']['vary_ua'] ) ? 'none' : SucomUtil::get_crawler_name();

			if ( $crawler_name === 'pinterest' ) {
				// Pinterest does not read JSON-LD markup.
			} else {
				$this->p->util->add_plugin_filters( $this, array(
					'add_schema_head_attributes'       => '__return_false',
					'add_schema_meta_array'            => '__return_false',
					'add_schema_noscript_array'        => '__return_false',
					'json_data_https_schema_org_thing' => 5,
				), -1000 );	// Make sure we run first.
			}

			$this->p->util->add_plugin_filters( $this, array(
				'get_md_defaults' => 2,
			) );

			if ( is_admin() ) {

				$this->p->util->add_plugin_actions( $this, array(	// Admin actions.
					'admin_post_head' => 1,
				) );

				$this->p->util->add_plugin_filters( $this, array(	// Admin filters.
					'option_type'               => 2,
					'save_post_options'         => 4,
					'post_cache_transient_keys' => 4,
					'pub_google_rows'           => 2,
					'messages_tooltip_meta'     => 2,
				) );

				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 4,
					'status_pro_features' => 4,
				), 10, 'wpssojson' );	// Hook to wpssojson filters.
			}
		}

		/**
		 * Remove AMP json data to prevent duplicate Schema JSON-LD markup.
		 */
		public function filter_amp_post_template_metadata( $metadata, $post_obj ) {
			return array();
		}

		/**
		 * Common filter for all Schema types.
		 *
		 * Adds the url, name, description, and if true, the main entity property. 
		 * Does not add images, videos, author or organization markup since this will
		 * depend on the Schema type (Article, Product, Place, etc.).
		 */
		public function filter_json_data_https_schema_org_thing( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'common json data filters' );
			}

			$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );

			$ret = WpssoSchema::get_schema_type_context( $page_type_url );

			/**
			 * Property:
			 *	additionalType
			 */
			$ret['additionalType'] = array();

			if ( is_object( $mod['obj'] ) ) {

				$mod_opts = $mod['obj']->get_options( $mod['id'] );

				if ( is_array( $mod_opts ) ) {	// Just in case.
					foreach ( SucomUtil::preg_grep_keys( '/^schema_addl_type_url_[0-9]+$/', $mod_opts ) as $addl_type_url ) {
						if ( filter_var( $addl_type_url, FILTER_VALIDATE_URL ) !== false ) {	// Just in case.
							$ret['additionalType'][] = $addl_type_url;
						}
					}
				}
			}

			$ret['additionalType'] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_additionaltype',
				$ret['additionalType'], $mod, $mt_og, $page_type_id, $is_main );

			if ( empty( $ret['additionalType'] ) ) {
				unset( $ret['additionalType'] );
			}

			/**
			 * Property:
			 *	url
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array( 'url' => 'og:url' ) );

			/**
			 * Property:
			 *	sameAs
			 */
			$ret['sameAs'] = array();

			if ( is_object( $mod['obj'] ) ) {

				$mod_opts = $mod['obj']->get_options( $mod['id'] );

				$ret['sameAs'][] = $this->p->util->get_canonical_url( $mod );

				if ( $mod['is_post'] ) {

					/**
					 * Add the permalink, which may be different than the shared URL and the canonical URL.
					 */
					$ret['sameAs'][] = get_permalink( $mod['id'] );

					/**
					 * Add the shortlink / short URL, but only if the link rel shortlink tag is enabled.
					 */
					$add_link_rel_shortlink = empty( $this->p->options['add_link_rel_shortlink'] ) ? false : true; 

					if ( apply_filters( $this->p->lca . '_add_link_rel_shortlink', $add_link_rel_shortlink, $mod ) ) {

						$ret['sameAs'][] = wp_get_shortlink( $mod['id'], 'post' );

						/**
						 * Some themes and plugins have been known to hook the WordPress 'get_shortlink' filter 
						 * and return an empty URL to disable the WordPress shortlink meta tag. This breaks the 
						 * WordPress wp_get_shortlink() function and is a violation of the WordPress theme 
						 * guidelines.
						 *
						 * This method calls the WordPress wp_get_shortlink() function, and if an empty string 
						 * is returned, calls an unfiltered version of the same function.
						 *
						 * $context = 'blog', 'post' (default), 'media', or 'query'
						 */
						$ret['sameAs'][] = SucomUtilWP::wp_get_shortlink( $mod['id'], 'post' );
					}
				}

				/**
				 * Add the shortened URL for posts (which may be different to the shortlink), terms, and users.
				 */
				if ( ! empty( $this->p->options['plugin_shortener'] ) && $this->p->options['plugin_shortener'] !== 'none' ) {
					if ( ! empty( $mt_og['og:url'] ) ) {	// Just in case.
						$ret['sameAs'][] = apply_filters( $this->p->lca . '_get_short_url',
							$mt_og['og:url'], $this->p->options['plugin_shortener'], $mod, $mod['name'] );
					}
				}

				/**
				 * Get additional sameAs URLs from the post/term/user custom meta.
				 */
				if ( is_array( $mod_opts ) ) {	// Just in case
					foreach ( SucomUtil::preg_grep_keys( '/^schema_sameas_url_[0-9]+$/', $mod_opts ) as $url ) {
						$ret['sameAs'][] = SucomUtil::esc_url_encode( $url );
					}
				}

				/**
				 * Sanitize the sameAs array - make sure URLs are valid and remove any duplicates.
				 */
				if ( ! empty( $ret['sameAs'] ) ) {

					$added_urls = array();

					foreach ( $ret['sameAs'] as $num => $url ) {

						if ( empty( $url ) ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value is empty' );
							}

						} elseif ( $ret['url'] === $url ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value is "url" property (' . $url . ')' );
							}

						} elseif ( isset( $added_urls[$url] ) ) {	// Already added.

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value already added (' . $url . ')' );
							}

						} elseif ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value is not valid (' . $url . ')' );
							}

						} else {	// Mark the url as already added and get the next url.

							$added_urls[$url] = true;

							continue;	// Get the next url.
						}

						unset( $ret['sameAs'][$num] );	// Remove the duplicate / invalid url.
					}

					$ret['sameAs'] = array_values( $ret['sameAs'] );	// Reindex / renumber the array.
				}
			}


			$ret['sameAs'] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_sameas',
				$ret['sameAs'], $mod, $mt_og, $page_type_id, $is_main );

			if ( empty( $ret['sameAs'] ) ) {
				unset( $ret['sameAs'] );
			}

			/**
			 * Property:
			 *	name
			 *	alternateName
			 */
			$ret['name'] = $this->p->page->get_title( 0, '', $mod, true, false, true, 'schema_title', false );

			$ret['alternateName'] = $this->p->page->get_title( $this->p->options['og_title_len'], '...', $mod, true, false, true, 'schema_title_alt' );

			if ( $ret['name'] === $ret['alternateName'] ) {
				unset( $ret['alternateName'] );
			}

			/**
			 * Property:
			 *	description
			 */
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'getting schema description with custom meta fallback: schema_desc, seo_desc, og_desc' );
			}

			$md_idx = array( 'schema_desc', 'seo_desc', 'og_desc' );

			$ret['description'] = $this->p->page->get_description( $this->p->options['schema_desc_len'], '...', $mod, true, false, true, $md_idx );

			/**
			 * Property:
			 *	potentialAction
			 */
			$ret['potentialAction'] = array();

			$ret['potentialAction'] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_potentialaction',
				$ret['potentialAction'], $mod, $mt_og, $page_type_id, $is_main );

			if ( empty( $ret['potentialAction'] ) ) {
				unset( $ret['potentialAction'] );
			}

			/**
			 * Get additional Schema properties from the optional post content shortcode.
			 */
			if ( $mod['is_post'] ) {

				$content = get_post_field( 'post_content', $mod['id'] );

				if ( empty( $content ) ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'post_content for post id ' . $mod['id'] . ' is empty' );
					}

				} elseif ( isset( $this->p->sc['schema'] ) && is_object( $this->p->sc['schema'] ) ) {	// Is the schema shortcode class loaded.

					if ( has_shortcode( $content, WPSSOJSON_SCHEMA_SHORTCODE_NAME ) ) {	// Does the content have a schema shortcode.

						$content_data = $this->p->sc['schema']->get_content_json_data( $content );

						if ( ! empty( $content_data ) ) {
							$ret = WpssoSchema::return_data_from_filter( $ret, $content_data );
						}
					}
				}
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'common json data filters' );
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		public function filter_get_md_defaults( $md_defs, $mod ) {

			$timezone = get_option( 'timezone_string' );

			$schema_type = $this->p->schema->get_mod_schema_type( $mod, true, false );	// $ret_schema_id = true, $use_mod_opts = false

			$def_currency = empty( $this->p->options['plugin_def_currency'] ) ?
				'USD' : $this->p->options['plugin_def_currency'];

			$review_item_type = empty( $this->p->options['schema_review_item_type'] ) ?
				'none' : $this->p->options['schema_review_item_type'];

			$schema_md_defs = array(
				'schema_type'                        => $schema_type,
				'schema_title'                       => '',
				'schema_title_alt'                   => '',
				'schema_desc'                        => '',
				'schema_pub_org_id'                  => 'site',			// Creative Work Publisher
				'schema_headline'                    => '',			// Creative Work Headline
				'schema_course_provider_id'          => 'none',			// Course Provider 
				'schema_event_start_date'            => '',			// Event Start Date
				'schema_event_start_time'            => 'none',			// Event Start Time
				'schema_event_start_timezone'        => $timezone,		// Event Start Timezone
				'schema_event_end_date'              => '',			// Event End Date
				'schema_event_end_time'              => 'none',			// Event End Time
				'schema_event_end_timezone'          => '',			// Event End Timezone
				'schema_event_offers_start_date'     => '',			// Event Start Date
				'schema_event_offers_start_time'     => 'none',			// Offers Start Time
				'schema_event_offers_start_timezone' => $timezone,		// Offers Start Timezone
				'schema_event_offers_end_date'       => '',			// Offers End Date
				'schema_event_offers_end_time'       => 'none',			// Offers End Time
				'schema_event_offers_end_timezone'   => '',			// Offers End Timezone
				'schema_event_org_id'                => 'none',			// Event Organizer
				'schema_event_perf_id'               => 'none',			// Event Performer
				'schema_howto_prep_days'             => 0,			// How-To Preparation Time (Days)
				'schema_howto_prep_hours'            => 0,			// How-To Preparation Time (Hours)
				'schema_howto_prep_mins'             => 0,			// How-To Preparation Time (Mins)
				'schema_howto_prep_secs'             => 0,			// How-To Preparation Time (Secs)
				'schema_howto_total_days'            => 0,			// How-To Total Time (Days)
				'schema_howto_total_hours'           => 0,			// How-To Total Time (Hours)
				'schema_howto_total_mins'            => 0,			// How-To Total Time (Mins)
				'schema_howto_total_secs'            => 0,			// How-To Total Time (Secs)
				'schema_howto_yield'                 => '',			// How-To Yield
				'schema_job_title'                   => '',
				'schema_job_org_id'                  => 'none',			// Hiring Organization
				'schema_job_location_id'             => 'none',			// Job Location
				'schema_job_salary'                  => '',			// Base Salary
				'schema_job_salary_currency'         => $def_currency,		// Base Salary Currency
				'schema_job_salary_period'           => 'year',			// Base Salary per Year, Month, Week, Hour
				'schema_job_empl_type_full_time'     => 0,
				'schema_job_empl_type_part_time'     => 0,
				'schema_job_empl_type_contractor'    => 0,
				'schema_job_empl_type_temporary'     => 0,
				'schema_job_empl_type_intern'        => 0,
				'schema_job_empl_type_volunteer'     => 0,
				'schema_job_empl_type_per_diem'      => 0,
				'schema_job_empl_type_other'         => 0,
				'schema_job_expire_date'             => '',
				'schema_job_expire_time'             => 'none',
				'schema_job_expire_timezone'         => '',
				'schema_org_org_id'                  => 'none',			// Organization
				'schema_person_id'                   => 'none',			// Person
				'schema_recipe_cook_method'          => '',			// Recipe Cooking Method
				'schema_recipe_course'               => '',			// Recipe Course
				'schema_recipe_cuisine'              => '',			// Recipe Cuisine
				'schema_recipe_prep_days'            => 0,			// Recipe Preparation Time (Days)
				'schema_recipe_prep_hours'           => 0,			// Recipe Preparation Time (Hours)
				'schema_recipe_prep_mins'            => 0,			// Recipe Preparation Time (Mins)
				'schema_recipe_prep_secs'            => 0,			// Recipe Preparation Time (Secs)
				'schema_recipe_cook_days'            => 0,			// Recipe Cooking Time (Days)
				'schema_recipe_cook_hours'           => 0,			// Recipe Cooking Time (Hours)
				'schema_recipe_cook_mins'            => 0,			// Recipe Cooking Time (Mins)
				'schema_recipe_cook_secs'            => 0,			// Recipe Cooking Time (Secs)
				'schema_recipe_total_days'           => 0,			// How-To Total Time (Days)
				'schema_recipe_total_hours'          => 0,			// How-To Total Time (Hours)
				'schema_recipe_total_mins'           => 0,			// How-To Total Time (Mins)
				'schema_recipe_total_secs'           => 0,			// How-To Total Time (Secs)
				'schema_recipe_nutri_serv'           => '',			// Serving Size
				'schema_recipe_nutri_cal'            => '',			// Calories
				'schema_recipe_nutri_prot'           => '',			// Protein
				'schema_recipe_nutri_fib'            => '',			// Fiber
				'schema_recipe_nutri_carb'           => '',			// Carbohydrates
				'schema_recipe_nutri_sugar'          => '',			// Sugar
				'schema_recipe_nutri_sod'            => '',			// Sodium
				'schema_recipe_nutri_fat'            => '',			// Fat
				'schema_recipe_nutri_trans_fat'      => '',			// Trans Fat
				'schema_recipe_nutri_sat_fat'        => '',			// Saturated Fat
				'schema_recipe_nutri_unsat_fat'      => '',			// Unsaturated Fat
				'schema_recipe_nutri_chol'           => '',			// Cholesterol
				'schema_recipe_yield'                => '',			// Recipe Yield
				'schema_review_item_type'            => $review_item_type,	// Reviewed Item Type
				'schema_review_item_name'            => '',			// Reviewed Item Name
				'schema_review_item_url'             => '',			// Reviewed Item URL
				'schema_review_item_image_url'       => '',			// Reviewed Item Image URL
				'schema_review_rating'               => '0.0',			// Reviewed Item Rating
				'schema_review_rating_from'          => '1',			// Reviewed Item Rating (From)
				'schema_review_rating_to'            => '5',			// Reviewed Item Rating (To)
				'schema_review_rating_alt_name'      => '',			// Reviewed Item Rating Alternate Name
			);

			foreach ( range( 0, WPSSO_SCHEMA_ADDL_TYPE_URL_MAX - 1, 1 ) as $key_num ) {
				$schema_md_defs['schema_addl_type_url_' . $key_num] = '';
			}

			foreach ( range( 0, WPSSO_SCHEMA_SAMEAS_URL_MAX - 1, 1 ) as $key_num ) {
				$schema_md_defs['schema_sameas_url_' . $key_num] = '';
			}

			return array_merge( $md_defs, $schema_md_defs );
		}

		public function action_admin_post_head( $mod ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$urls          = $this->p->cf['plugin']['wpssojson']['url'];
			$page_type_id  = $this->p->schema->get_mod_schema_type( $mod, true );	// $ret_schema_id is true.
			$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );
			$filter_name   = $this->p->schema->get_json_data_filter( $mod, $page_type_url );
			$warn_msg      = '';
			$notice_key    = false;

			if ( has_filter( $filter_name ) ) {
				return;
			}

			if ( ! $this->p->check->pp( 'wpssojson', true, $this->p->avail['*']['p_dir'] ) ) {

				$warn_msg = sprintf( __( 'The Free / Standard version of WPSSO JSON does not include support for the Schema type <a href="%1$s">%1$s</a> &mdash; only the basic Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $page_type_url ) . ' ';
				
				$warn_msg .= sprintf( __( 'The <a href="%1$s">Pro version of WPSSO JSON</a> includes a wide selection of supported Schema types, including the Schema type <a href="%2$s">%2$s</a>.', 'wpsso-schema-json-ld' ), $urls['purchase'], $page_type_url ) . ' ';
				
				$warn_msg .= sprintf( __( 'If this Schema type is an important classification for your content, you should consider purchasing the Pro version.', 'wpsso-schema-json-ld' ), $page_type_url );

				$notice_key = 'no_filter_' . $filter_name . '_' . $mod['name'] . '_' . $mod['id'];
			}

			if ( ! empty( $warn_msg ) ) {
				$this->p->notice->warn( '<p class="top"><em>' . __( 'This notice is only shown to users with Administrative privileges.',
					'wpsso-schema-json-ld' ) . '</em></p><p>' . $warn_msg . '</p>', null, $notice_key, true );
			}
		}

		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {
				return $type;
			} elseif ( strpos( $base_key, 'schema_' ) !== 0 ) {
				return $type;
			}

			switch ( $base_key ) {
				case 'schema_event_offer_name':
				case 'schema_howto_step':
				case 'schema_howto_supply':
				case 'schema_howto_tool':
				case 'schema_howto_yield':		// How-To Makes
				case 'schema_job_title':
				case 'schema_job_currency':
				case 'schema_person_job_title':
				case 'schema_recipe_cook_method':
				case 'schema_recipe_course':
				case 'schema_recipe_cuisine':
				case 'schema_recipe_ingredient':
				case 'schema_recipe_instruction':
				case 'schema_recipe_nutri_serv':
				case 'schema_recipe_yield':		// Recipe Makes
				case 'schema_review_item_name':
				case 'schema_review_rating_alt_name':
					return 'one_line';
					break;
				case 'schema_type':
				case 'schema_event_offer_currency':
				case 'schema_event_offer_avail':
				case 'schema_review_item_type':
					return 'not_blank';
					break;
				case 'schema_event_offer_price':
				case 'schema_howto_prep_days':		// How-To Preparation Time
				case 'schema_howto_prep_hours':
				case 'schema_howto_prep_mins':
				case 'schema_howto_prep_secs':
				case 'schema_howto_total_days':		// How-To Total Time
				case 'schema_howto_total_hours':
				case 'schema_howto_total_mins':
				case 'schema_howto_total_secs':
				case 'schema_recipe_prep_days':		// Recipe Preparation Time
				case 'schema_recipe_prep_hours':
				case 'schema_recipe_prep_mins':
				case 'schema_recipe_prep_secs':
				case 'schema_recipe_cook_days':		// Recipe Cooking Time
				case 'schema_recipe_cook_hours':
				case 'schema_recipe_cook_mins':
				case 'schema_recipe_cook_secs':
				case 'schema_recipe_total_days':	// Recipe Total Time
				case 'schema_recipe_total_hours':
				case 'schema_recipe_total_mins':
				case 'schema_recipe_total_secs':
				case 'schema_recipe_nutri_cal':
				case 'schema_recipe_nutri_prot':
				case 'schema_recipe_nutri_fib':
				case 'schema_recipe_nutri_carb':
				case 'schema_recipe_nutri_sugar':
				case 'schema_recipe_nutri_sod':
				case 'schema_recipe_nutri_fat':
				case 'schema_recipe_nutri_sat_fat':
				case 'schema_recipe_nutri_unsat_fat':
				case 'schema_recipe_nutri_chol':
				case 'schema_review_rating':
				case 'schema_review_rating_from':
				case 'schema_review_rating_to':
					return 'blank_num';	// Must be numeric (blank or zero is ok).
					break;
				case 'schema_review_item_url':
				case 'schema_review_item_image_url':
					return 'url';
					break;
				case 'schema_job_salary':
					return 'blank_num';
					break;
			}

			return $type;
		}

		public function filter_save_post_options( $md_opts, $post_id, $rel_id, $mod ) {

			$md_defs = $this->filter_get_md_defaults( array(), $mod );	// Only get the schema options.

			/**
			 * Check for default recipe values.
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^schema_recipe_(prep|cook|total)_(days|hours|mins|secs)$/', $md_opts ) as $md_idx => $value ) {

				$md_opts[$md_idx] = (int) $value;

				if ( $md_opts[$md_idx] === $md_defs[$md_idx] ) {
					unset( $md_opts[$md_idx] );
				}
			}

			/**
			 * If the review rating is 0, remove the review rating options.
			 * If we have a review rating, then make sure there's a from/to as well.
			 */
			if ( empty( $md_opts['schema_review_rating'] ) ) {
				foreach ( array( 'schema_review_rating', 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_idx ) {
					unset( $md_opts[$md_idx] );
				}
			} else {
				foreach ( array( 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_idx ) {
					if ( empty( $md_opts[$md_idx] ) && isset( $md_defs[$md_idx] ) ) {
						$md_opts[$md_idx] = $md_defs[$md_idx];
					}
				}
			}

			foreach ( array( 'schema_event_start', 'schema_event_end' ) as $md_pre ) {

				/**
				 * Unset date / time if same as the default value.
				 */
				foreach ( array( 'date', 'time', 'timezone' ) as $md_ext ) {

					if ( isset( $md_opts[$md_pre . '_' . $md_ext] ) &&
						( $md_opts[$md_pre . '_' . $md_ext] === $md_defs[$md_pre . '_' . $md_ext] ||
							$md_opts[$md_pre . '_' . $md_ext] === 'none' ) ) {

						unset( $md_opts[$md_pre . '_' . $md_ext] );
					}
				}

				if ( empty( $md_opts[$md_pre . '_date'] ) && empty( $md_opts[$md_pre . '_time'] ) ) {		// No date or time.
					unset( $md_opts[$md_pre . '_timezone'] );
					continue;
				} elseif ( ! empty( $md_opts[$md_pre . '_date'] ) && empty( $md_opts[$md_pre . '_time'] ) ) {	// Date with no time.
					$md_opts[$md_pre . '_time'] = '00:00';
				} elseif ( empty( $md_opts[$md_pre . '_date'] ) && ! empty( $md_opts[$md_pre . '_time'] ) ) {	// Time with no date.
					$md_opts[$md_pre . '_date'] = gmdate( 'Y-m-d', time() );
				}
			}

			foreach ( range( 0, WPSSO_SCHEMA_EVENT_OFFERS_MAX - 1, 1 ) as $key_num ) {

				$have_offer = false;

				foreach ( array( 'schema_event_offer_name', 'schema_event_offer_price' ) as $md_pre ) {
					if ( isset( $md_opts[$md_pre . '_' . $key_num] ) && $md_opts[$md_pre . '_' . $key_num] !== '' ) {
						$have_offer = true;
					}
				}

				if ( ! $have_offer ) {
					unset( $md_opts['schema_event_offer_currency_' . $key_num] );
					unset( $md_opts['schema_event_offer_avail_' . $key_num] );
				}
			}

			return $md_opts;
		}

		public function filter_post_cache_transient_keys( $transient_keys, $mod, $sharing_url, $mod_salt ) {

			/**
			 * Clear the WPSSO Core head meta tags array.
			 */
			$cache_md5_pre = $this->p->lca . '_h_';
			$cache_method = 'WpssoHead::get_head_array';

			$year  = get_the_time( 'Y', $mod['id'] );
			$month = get_the_time( 'm', $mod['id'] );
			$day   = get_the_time( 'd', $mod['id'] );

			$home_url  = home_url( '/' );
			$year_url  = get_year_link( $year );
			$month_url = get_month_link( $year, $month );
			$day_url   = get_day_link( $year, $month, $day );

			foreach ( array( $home_url, $year_url, $month_url, $day_url ) as $url ) {
				$transient_keys[] = array(
					'id'   => $cache_md5_pre . md5( $cache_method . '(url:' . $url . ')' ),
					'pre'  => $cache_md5_pre,
					'salt' => $cache_method . '(url:' . $url . ')',
				);
			}

			/**
			 * Clear term archive page meta tags (and json markup).
			 */
			foreach ( get_post_taxonomies( $mod['id'] ) as $tax_name ) {
				foreach ( wp_get_post_terms( $mod['id'], $tax_name ) as $term ) {
					$transient_keys[] = array(
						'id'   => $cache_md5_pre . md5( $cache_method . '(term:' . $term->term_id . '_tax:' . $tax_name . ')' ),
						'pre'  => $cache_md5_pre,
						'salt' => $cache_method . '(term:' . $term->term_id . '_tax:' . $tax_name . ')',
					);
				}
			}

			/**
			 * Clear author archive page meta tags (and json markup).
			 */
			$author_id = get_post_field( 'post_author', $mod['id'] );

			$transient_keys[] = array(
				'id'   => $cache_md5_pre . md5( $cache_method . '(user:' . $author_id . ')' ),
				'pre'  => $cache_md5_pre,
				'salt' => $cache_method . '(user:' . $author_id . ')',
			);

			return $transient_keys;
		}

		/**
		 * Filter the SSO > General > Google / Schema tab options.
		 */
		public function filter_pub_google_rows( $table_rows, $form ) {
			foreach ( array_keys( $table_rows ) as $key ) {
				switch ( $key ) {
					/**
					 * Keep these rows.
					 */
					case 'schema_knowledge_graph':
					case 'schema_home_person_id':
						break;
					/**
					 * Remove these rows.
					 */
					case 'subsection_google_schema':
					case ( strpos( $key, 'schema_' ) === 0 ? true : false ):
						unset( $table_rows[$key] );
						break;
				}
			}
			return $table_rows;
		}

		public function filter_messages_tooltip_meta( $text, $idx ) {

			if ( strpos( $idx, 'tooltip-meta-schema_' ) !== 0 ) {
				return $text;
			}

			switch ( $idx ) {

				case 'tooltip-meta-schema_type':

					$text = __( 'Select a Schema item type that best describes the main content of this webpage.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_addl_type_url':

					$text = sprintf( __( 'Additional (and optional) type URLs for the item, typically used to specify more precise types from an external vocabulary in microdata syntax. For example, an additional Schema type URL for a product item could be http://www.productontology.org/id/Hammer (see %s for more examples).', 'wpsso-schema-json-ld' ), '<a href="http://www.productontology.org/">The Product Types Ontology</a>' );

				 	break;

				case 'tooltip-meta-schema_sameas_url':

					$text = __( 'Additional (and optional) webpage reference URLs that unambiguously indicate the item\'s identity. For example, the URL of the item\'s Wikipedia page, Wikidata entry, IMDB page, official website, etc.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_pub_org_id':

					$text = __( 'Select a publisher for the Schema Article item type and/or its sub-type (NewsArticle, TechArticle, etc).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_headline':

					$text = __( 'A custom headline for the Schema CreativeWork item type and/or its sub-type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_course_provider_id':

					$text = __( 'Select an organizer for the course service provider, service operator, or service performer (ie. the goods producer).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_org_id':

					$text = __( 'Select an organizer for the event.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_perf_id':

					$text = __( 'Select a performer for the event.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_start':

					$text = __( 'Select the event start date and time.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_end':

					$text = __( 'Select the event end date and time.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_offers_start':

					$text = __( 'The date and time when tickets go on sale.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_offers_end':

					$text = __( 'The date and time when tickets are no longer on sale.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_offers':

					$text = __( 'One or more offers for the event, including the offer name, price and currency.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_steps':

					$text = __( 'A list of steps to complete this How-To.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_supplies':

					$text = __( 'A list of supplies that are consumed when completing this How-To.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_tools':

					$text = __( 'A list of tools or objects that are required to complete this How-To.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_prep_time':
				case 'tooltip-meta-schema_recipe_prep_time':

					$text = __( 'The total time it takes to prepare the items before executing the instruction steps.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_total_time':
				case 'tooltip-meta-schema_recipe_total_time':

					$text = __( 'The total time required to perform the all instructions (including any preparation time).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_yield':

					$text = __( 'The quantity made when following these How-To instructions (example: "a paper airplane", "10 personalized candles", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_title':

					$text = __( 'The title of this job, which may be different than the WordPress post / page title.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_org_id':

					$text = __( 'Optionally select a different organization for the hiring organization.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_location_id':

					$text = __( 'Optionally select a different place / location for the job location.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_salary':

					$text = __( 'Optionally provide details on the base salary. The base salary must be numeric, like 120000, 50.00, etc. Do not use spaces, commas, or currency symbols, as these are not valid numeric values.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_empl_type':

					$text = sprintf( __( 'Check one or more Google approved employment types (see <a href="%s">Google\'s Job Posting guidelines</a> for more information).', 'wpsso-schema-json-ld' ), 'https://developers.google.com/search/docs/data-types/job-postings' );

				 	break;

				case 'tooltip-meta-schema_job_expire':

					$text = __( 'Select a job posting expiration date and time. If a job posting never expires, or you do not know when the job will expire, do not select an expiration date and time.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_org_org_id':

					$text = __( 'Optionally select a different organization for the Schema Organization item type and/or its sub-type (Airline, Corporation, School, etc). Select "[None]" to use the default organization details.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_person_id':

					$role_label_transl = _x( 'Person', 'user role', 'wpsso' );	// Use the wpsso translation domain.

					$text = sprintf( __( 'Select a person from the list of eligible WordPress users &mdash; to be included in this list, a user must be members of the WordPress "%s" role.', 'wpsso-schema-json-ld' ), $role_label_transl );

				 	break;

				case 'tooltip-meta-schema_question_desc':

			 		$text = __( 'An optional heading / description of the question and it\'s answer.', 'wpsso-schema-json-ld' ) . ' ';
					
					$text .= __( 'If the question is part of a larger group of questions on the same subject, then this would be an appropriate field to describe that subject (example: "QA about a Flying Toaster" ).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_person_job_title':

					$text = __( 'A person\'s job title (for example, Financial Manager).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_cook_method':

					$text = __( 'The cooking method used for this recipe (example: Baking, Frying, Steaming, etc.)', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_cook_time':

					$text = __( 'The total time it takes to cook this recipe.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_course':

					$text = __( 'The course name for this recipe (example: Appetizer, Entr&eacute;e, Main Course / Main Dish, Dessert, Side-dish, etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_cuisine':

					$text = __( 'The type of cuisine for this recipe (example: French, Indian, Italian, Japanese, Thai, etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_ingredients':

					$text = __( 'A list of ingredients for this recipe (example: "1 cup flour", "1 tsp salt", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_instructions':

					$text = __( 'A list of instructions for this recipe (example: "beat eggs", "add and mix flour", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_serv':

					$text = __( 'The serving size in volume or mass. A serving size is required to include nutrition information in the Schema recipe markup.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_cal':

					$text = __( 'The number of calories per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_prot':

					$text = __( 'The number of grams of protein per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_fib':

					$text = __( 'The number of grams of fiber per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_carb':

					$text = __( 'The number of grams of carbohydrates per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_sugar':

					$text = __( 'The number of grams of sugar per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_sod':

					$text = __( 'The number of milligrams of sodium per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_fat':

					$text = __( 'The number of grams of fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_trans_fat':

					$text = __( 'The number of grams of trans fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_sat_fat':

					$text = __( 'The number of grams of saturated fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_unsat_fat':

					$text = __( 'The number of grams of unsaturated fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_chol':

					$text = __( 'The number of milligrams of cholesterol per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_yield':

					$text = __( 'The quantity or servings made by this recipe (example: "5 servings", "Serves 4-6", "Yields 10 burgers", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_item_type':

					$text = __( 'Select a Schema type that best describes the subject being reviewed.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_item_name':

					$text = __( 'The official and/or model name for the subject being reviewed.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_item_url':

					$text = __( 'A webpage URL for the subject being reviewed.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_item_image_url':

					$text = __( 'An image URL showing the subject being reviewed.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_rating':

					$text = __( 'A rating for the subject being reviewed, along with the low/high rating scale (default is 1 to 5).', 'wpsso-schema-json-ld' );

				case 'tooltip-meta-schema_review_rating_alt_name':

					$text = __( 'An alternate name / description for the rating value (example: False, Misleading, Accurate, etc.).', 'wpsso-schema-json-ld' );

				 	break;

			}

			return $text;
		}

		/**
		 * Hooked to 'wpssojson_status_gpl_features'.
		 */
		public function filter_status_gpl_features( $features, $ext, $info, $pkg ) {

			foreach ( $info['lib']['gpl'] as $sub => $libs ) {

				if ( $sub === 'admin' ) { // Skip status for admin menus and tabs.
					continue;
				}

				foreach ( $libs as $id_key => $label ) {

					list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );

					if ( $pkg['pp'] && ! empty( $info['lib']['pro'][$sub][$id] ) ) {
						continue;
					}

					$classname = SucomUtil::sanitize_classname( 'wpssojsongpl' . $sub . $id, false );	// $underscore is false.

					$features[$label] = array( 'status' => class_exists( $classname ) ? 'on' : 'off' );
				}
			}
			return $this->filter_common_status_features( $features, $ext, $info, $pkg );
		}

		/**
		 * Hooked to 'wpssojson_status_pro_features'.
		 */
		public function filter_status_pro_features( $features, $ext, $info, $pkg ) {
			return $this->filter_common_status_features( $features, $ext, $info, $pkg );
		}

		private function filter_common_status_features( $features, $ext, $info, $pkg ) {
			foreach ( $features as $key => $arr ) {
				if ( preg_match( '/^\(([a-z\-]+)\) (Schema Type .+) \((.+)\)$/', $key, $match ) ) {
					$features[$key]['label'] = $match[2] . ' (' . $this->p->schema->count_schema_type_children( $match[3] ) . ')';
				}
			}
			return $features;
		}
	}
}

