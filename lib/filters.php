<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://wpsso.com/)
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

			$crawler_name = SucomUtil::get_crawler_name();

			add_filter( 'amp_post_template_metadata', 
				array( &$this, 'filter_amp_post_template_metadata' ), 9000, 2 );

			if ( $crawler_name === 'pinterest' ) {
				// pinterest does not read json markup
			} else {
				$this->p->util->add_plugin_filters( $this, array(
					'add_schema_head_attributes' => '__return_false',
					'add_schema_meta_array' => '__return_false',
					'add_schema_noscript_array' => '__return_false',
					'json_data_https_schema_org_thing' => 5,
				), -1000 );	// make sure we run first
			}

			$this->p->util->add_plugin_filters( $this, array(
				'get_md_defaults' => 2,
			) );

			if ( is_admin() ) {
				$this->p->util->add_plugin_actions( $this, array(	// admin actions
					'admin_post_head' => 1,
				) );
				$this->p->util->add_plugin_filters( $this, array(	// admin filters
					'option_type' => 2,
					'post_cache_transient_keys' => 3,
					'pub_google_rows' => 2,
					'save_post_options' => 4,
					'messages_tooltip_meta' => 2,
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 4,
					'status_pro_features' => 4,
				), 10, 'wpssojson' );	// hook to wpssojson filters
			}
		}

		public function filter_amp_post_template_metadata( $metadata, $post_obj ) {
			return array();	// remove the AMP json data to prevent duplicate JSON-LD blocks
		}

		/*
		 * Common filter for all Schema types.
		 *
		 * Adds the url, name, description, and if true, the main entity property. 
		 * Does not add images, videos, author or organization markup since this will
		 * depend on the Schema type (Article, Product, Place, etc.).
		 */
		public function filter_json_data_https_schema_org_thing( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'common json data filter' );
			}

			$lca = $this->p->cf['lca'];
			$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );
			$ret = WpssoSchema::get_schema_type_context( $page_type_url );

			/*
			 * Property:
			 *	additionalType
			 */
			if ( is_object( $mod['obj'] ) ) {
				$mod_opts = $mod['obj']->get_options( $mod['id'] );
				foreach ( SucomUtil::preg_grep_keys( '/^schema_add_type_url_[0-9]+$/', $mod_opts ) as $add_type_url ) {
					if ( filter_var( $add_type_url, FILTER_VALIDATE_URL ) !== false ) {	// just in case
						$ret['additionalType'][] = $add_type_url;
					}
				}
			}

			/*
			 * Property:
			 *	url
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array( 'url' => 'og:url' ) );

			/*
			 * Property:
			 *	name
			 */
			$ret['name'] = $this->p->page->get_title( $this->p->options['og_title_len'], 
				'...', $mod, true, false, true, 'schema_title' );

			/*
			 * Property:
			 *	description
			 */
			$ret['description'] = $this->p->page->get_description( $this->p->options['schema_desc_len'], 
				'...', $mod, true, false, true, 'schema_desc' );

			$action_data = (array) apply_filters( $lca.'_json_prop_https_schema_org_potentialaction',
				array(), $mod, $mt_og, $page_type_id, $is_main );

			if ( ! empty( $action_data ) ) {
				$ret['potentialAction'] = $action_data;
			}

			/*
			 * Get additional Schema properties from the optional post content shortcode.
			 */
			if ( $mod['is_post'] ) {
				$content = get_post_field( 'post_content', $mod['id'] );
				if ( empty( $content ) ) {
					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'post_content for post id '.$mod['id'].' is empty' );
					}
				// are plugin shortcodes enabled
				} elseif ( ! empty( $this->p->options['plugin_shortcodes'] ) ) {
					// is the schema shortcode class loaded
					if ( isset( $this->p->sc['schema'] ) && is_object( $this->p->sc['schema'] ) ) {
						// does the content have a schema shortcode
						if ( has_shortcode( $content, WPSSOJSON_SCHEMA_SHORTCODE_NAME ) ) {
							$content_data = $this->p->sc['schema']->get_json_data( $content );
							if ( ! empty( $content_data ) ) {
								$ret = WpssoSchema::return_data_from_filter( $ret, $content_data );
							}
						}
					}
				}
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'common json data filter' );
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		public function action_admin_post_head( $mod ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$urls = $this->p->cf['plugin']['wpssojson']['url'];
			$page_type_id = $this->p->schema->get_mod_schema_type( $mod, true );	// $get_id = true;
			$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );
			$filter_name = $this->p->schema->get_json_data_filter( $mod, $page_type_url );
			$warn_msg = '';

			if ( has_filter( $filter_name ) ) {
				return;
			}

			if ( ! $this->p->check->aop( 'wpssojson', true, $this->p->avail['*']['p_dir'] ) ) {
				$warn_msg = sprintf( __( 'The Free / Basic version of WPSSO JSON does not include support for the Schema type <a href="%1$s">%1$s</a> &mdash; only the basic Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $page_type_url ).' '.sprintf( __( 'The <a href="%1$s">Pro version of WPSSO JSON</a> includes a wide selection of supported Schema types, including the Schema type <a href="%2$s">%2$s</a>.', 'wpsso-schema-json-ld' ), $urls['purchase'], $page_type_url ).' '.sprintf( __( 'If this Schema is an important classification for your content, you should consider purchasing the Pro version.', 'wpsso-schema-json-ld' ), $page_type_url );
				$dis_key = 'no_filter_'.$filter_name.'_'.$mod['name'].'_'.$mod['id'];
			}

			if ( ! empty( $warn_msg ) ) {
				$this->p->notice->warn( '<em>'.__( 'This notice is only shown to users with Administrative privileges.',
					'wpsso-schema-json-ld' ).'</em><p>'.$warn_msg.'</p>', true, $dis_key, true );	// can be dismissed
			}
		}

		public function filter_option_type( $type, $key ) {
			if ( ! empty( $type ) ) {
				return $type;
			} elseif ( strpos( $key, 'schema_' ) !== 0 ) {
				return $type;
			}
			switch ( $key ) {
				case 'schema_event_offer_name':
				case 'schema_job_title':
				case 'schema_job_currency':
				case 'schema_recipe_course':
				case 'schema_recipe_cuisine':
				case 'schema_recipe_yield':
				case 'schema_recipe_ingredient':
				case 'schema_recipe_instruction':
				case 'schema_review_item_name':
				case 'schema_recipe_nutri_serv':
					return 'one_line';
					break;
				case 'schema_type':
				case 'schema_event_offer_currency':
				case 'schema_event_offer_avail':
				case 'schema_review_item_type':
					return 'not_blank';
					break;
				case 'schema_event_offer_price':
				case 'schema_recipe_prep_days':
				case 'schema_recipe_prep_hours':
				case 'schema_recipe_prep_mins':
				case 'schema_recipe_prep_secs':
				case 'schema_recipe_cook_days':
				case 'schema_recipe_cook_hours':
				case 'schema_recipe_cook_mins':
				case 'schema_recipe_cook_secs':
				case 'schema_recipe_total_days':
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
					return 'blank_num';	// must be numeric (blank or zero is ok)
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

			$md_defs = $this->filter_get_md_defaults( array(), $mod );	// only get the schema options

			// check for default recipe values
			foreach ( SucomUtil::preg_grep_keys( '/^schema_recipe_(prep|cook|total)_(days|hours|mins|secs)$/',
				$md_opts ) as $md_idx => $value ) {

				$md_opts[$md_idx] = (int) $value;
				if ( $md_opts[$md_idx] === $md_defs[$md_idx] ) {
					unset( $md_opts[$md_idx] );
				}
			}

			// if the review rating is 0, remove the review rating options
			if ( empty( $md_opts['schema_review_rating'] ) ) {
				foreach ( array( 'schema_review_rating', 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_idx ) {
					unset( $md_opts[$md_idx] );
				}
			// if we have a review rating, then make sure we have a from/to as well
			} else {
				foreach ( array( 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_idx ) {
					if ( empty( $md_opts[$md_idx] ) && isset( $md_defs[$md_idx] ) ) {
						$md_opts[$md_idx] = $md_defs[$md_idx];
					}
				}
			}

			foreach ( array( 'schema_event_start', 'schema_event_end' ) as $md_pre ) {

				// unset date / time if same as the default value
				foreach ( array( 'date', 'time', 'timezone' ) as $md_ext ) {
					if ( isset( $md_opts[$md_pre.'_'.$md_ext] ) &&
						( $md_opts[$md_pre.'_'.$md_ext] === $md_defs[$md_pre.'_'.$md_ext] ||
							$md_opts[$md_pre.'_'.$md_ext] === 'none' ) ) {
						unset( $md_opts[$md_pre.'_'.$md_ext] );
					}
				}

				if ( empty( $md_opts[$md_pre.'_date'] ) && empty( $md_opts[$md_pre.'_time'] ) ) {
					unset( $md_opts[$md_pre.'_timezone'] );
					continue;
				} elseif ( ! empty( $md_opts[$md_pre.'_date'] ) && empty( $md_opts[$md_pre.'_time'] ) ) {	// date with no time
					$md_opts[$md_pre.'_time'] = '00:00';
				} elseif ( empty( $md_opts[$md_pre.'_date'] ) && ! empty( $md_opts[$md_pre.'_time'] ) ) {	// time with no date
					$md_opts[$md_pre.'_date'] = gmdate( 'Y-m-d', time() );
				}
			}

			foreach ( range( 0, WPSSO_SCHEMA_EVENT_OFFERS_MAX - 1, 1 ) as $key_num ) {
				$have_offer = false;
				foreach ( array( 'schema_event_offer_name', 'schema_event_offer_price' ) as $md_pre ) {
					if ( isset( $md_opts[$md_pre.'_'.$key_num] ) && $md_opts[$md_pre.'_'.$key_num] !== '' ) {
						$have_offer = true;
					}
				}
				if ( ! $have_offer ) {
					unset( $md_opts['schema_event_offer_currency_'.$key_num] );
					unset( $md_opts['schema_event_offer_avail_'.$key_num] );
				}
			}

			return $md_opts;
		}

		public function filter_post_cache_transient_keys( $transient_keys, $mod, $sharing_url ) {

			$md5_pre = $this->p->cf['lca'].'_h_';
			$class_pre = 'Wpsso';
			$method_name = $class_pre.'WpssoHead::get_head_keys';

			$home_url = home_url( '/' );
			$year = get_the_time( 'Y', $mod['id'] );
			$month = get_the_time( 'm', $mod['id'] );
			$day = get_the_time( 'd', $mod['id'] );

			// clear blog home page
			$transient_keys[] = $md5_pre.md5( $method_name.'(url:'.$home_url.')' );

			// clear date based archive pages
			$transient_keys[] = $md5_pre.md5( $method_name.'(url:'.get_year_link( $year ).')' );
			$transient_keys[] = $md5_pre.md5( $method_name.'(url:'.get_month_link( $year, $month ).')' );
			$transient_keys[] = $md5_pre.md5( $method_name.'(url:'.get_day_link( $year, $month, $day ).')' );

			// clear term archive page meta tags (and json markup)
			foreach ( get_post_taxonomies( $mod['id'] ) as $tax_name ) {
				foreach ( wp_get_post_terms( $mod['id'], $tax_name ) as $term ) {
					$transient_keys[] = $md5_pre.md5( $method_name.'(term:'.$term->term_id.'_tax:'.$tax_name.')' );
				}
			}

			// clear author archive page meta tags (and json markup)
			$author_id = get_post_field( 'post_author', $mod['id'] );
			$transient_keys[] = $md5_pre.md5( $method_name.'(user:'.$author_id.')' );

			return $transient_keys;
		}

		public function filter_get_md_defaults( $md_defs, $mod ) {

			$timezone = get_option( 'timezone_string' );

			$schema_type = $this->p->schema->get_mod_schema_type( $mod, true, false );	// $get_id = true, $use_mod_opts = false

			$def_currency = empty( $this->p->options['plugin_def_currency'] ) ?
				'USD' : $this->p->options['plugin_def_currency'];

			$review_item_type = empty( $this->p->options['schema_review_item_type'] ) ?
				'none' : $this->p->options['schema_review_item_type'];

			$schema_md_defs = array(
				'schema_is_main' => 1,
				'schema_type' => $schema_type,
				'schema_title' => '',
				'schema_desc' => '',
				'schema_pub_org_id' => 'site',			// Article Publisher
				'schema_headline' => '',			// Article Headline
				'schema_event_start_date' => '',		// Event Start Date
				'schema_event_start_time' => 'none',		// Event Start Time
				'schema_event_start_timezone' => $timezone,	// Event Start Timezone
				'schema_event_end_date' => '',			// Event End Date
				'schema_event_end_time' => 'none',		// Event End Time
				'schema_event_end_timezone' => '',		// Event End Timezone
				'schema_event_org_id' => 'none',		// Event Organizer
				'schema_event_perf_id' => 'none',		// Event Performer
				'schema_job_org_id' => 'none',			// Hiring Organization
				'schema_job_location_id' => 'none',		// Job Location
				'schema_job_salary' => '',			// Base Salary
				'schema_job_salary_currency' => $def_currency,	// Base Salary Currency
				'schema_job_salary_period' => 'year',		// Base Salary per Year, Month, Week, Hour
				'schema_org_org_id' => 'none',			// Organization
				'schema_recipe_prep_days' => 0,			// Recipe Preperation Time (Days)
				'schema_recipe_prep_hours' => 0,		// Recipe Preperation Time (Hours)
				'schema_recipe_prep_mins' => 0,			// Recipe Preperation Time (Mins)
				'schema_recipe_prep_secs' => 0,			// Recipe Preperation Time (Secs)
				'schema_recipe_cook_days' => 0,			// Recipe Cooking Time (Days)
				'schema_recipe_cook_hours' => 0,		// Recipe Cooking Time (Hours)
				'schema_recipe_cook_mins' => 0,			// Recipe Cooking Time (Mins)
				'schema_recipe_cook_secs' => 0,			// Recipe Cooking Time (Secs)
				'schema_recipe_total_days' => 0,		// Recipe Total Time (Days)
				'schema_recipe_total_hours' => 0,		// Recipe Total Time (Hours)
				'schema_recipe_total_mins' => 0,		// Recipe Total Time (Mins)
				'schema_recipe_total_secs' => 0,		// Recipe Total Time (Secs)
				'schema_recipe_course' => '',			// Recipe Course
				'schema_recipe_cuisine' => '',			// Recipe Cuisine
				'schema_recipe_yield' => '',			// Recipe Yield
				'schema_recipe_nutri_serv' => '',		// Serving Size
				'schema_recipe_nutri_cal' => '',		// Calories
				'schema_recipe_nutri_prot' => '',		// Protein
				'schema_recipe_nutri_fib' => '',		// Fiber
				'schema_recipe_nutri_carb' => '',		// Carbohydrates
				'schema_recipe_nutri_sugar' => '',		// Sugar
				'schema_recipe_nutri_sod' => '',		// Sodium
				'schema_recipe_nutri_fat' => '',		// Fat
				'schema_recipe_nutri_trans_fat' => '',		// Trans Fat
				'schema_recipe_nutri_sat_fat' => '',		// Saturated Fat
				'schema_recipe_nutri_unsat_fat' => '',		// Unsaturated Fat
				'schema_recipe_nutri_chol' => '',		// Cholesterol
				'schema_review_item_type' => $review_item_type,	// Reviewed Item Type
				'schema_review_item_name' => '',		// Reviewed Item Name
				'schema_review_item_url' => '',			// Reviewed Item URL
				'schema_review_item_image_url' => '',		// Reviewed Item Image URL
				'schema_review_rating' => '0.0',		// Reviewed Item Rating
				'schema_review_rating_from' => '1',		// Reviewed Item Rating (from)
				'schema_review_rating_to' => '5',		// Reviewed Item Rating (to)
			);

			return array_merge( $md_defs, $schema_md_defs );
		}

		public function filter_pub_google_rows( $table_rows, $form ) {
			foreach ( array_keys( $table_rows ) as $key ) {
				switch ( $key ) {
					case 'schema_add_noscript':	// keep these rows
					case 'schema_knowledge_graph':
						break;
					case 'subsection_google_schema':	// remove these rows
					case ( strpos( $key, 'schema_' ) === 0 ? true : false ):
						unset( $table_rows[$key] );
						break;
				}
			}
			return $table_rows;
		}

		public function filter_messages_tooltip_meta( $text, $idx ) {
			if ( strpos( $idx, 'tooltip-meta-schema_' ) !== 0 )
				return $text;

			switch ( $idx ) {
				case 'tooltip-meta-schema_is_main':
					$text = __( 'Check this option if the Schema markup describes the main content (aka "main entity") of this webpage.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_type':
					$text = __( 'Select a Schema item type that best describes the main content of this webpage.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_add_type_url':
					$text = sprintf( __( 'Additional (and optional) type URLs for the item, typically used to specify more precise types from an external vocabulary in microdata syntax. For example, an additional Schema type URL for a product item could be http://www.productontology.org/id/Hammer (see %s for more examples).', 'wpsso-schema-json-ld' ), '<a href="http://www.productontology.org/">The Product Types Ontology</a>' );
				 	break;
				case 'tooltip-meta-schema_pub_org_id':
					$text = __( 'Select a publisher for the Schema Article item type and/or its sub-type (NewsArticle, TechArticle, etc).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_headline':
					$text = __( 'A custom headline for the Schema Article item type and/or its sub-type. The headline Schema property is not added for non-Article item types.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_job_org_id':
					$text = __( 'Optionally select a different organization for the hiring organization.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_job_location_id':
					$text = __( 'Optionally select a different address / place / location for the job location.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_job_salary':
					$text = __( 'Optionally provide details on the base salary. The base salary must be numeric, like 120000, 50.00, etc. Do not use spaces, commas, or currency symbols, as these are not valid numeric values.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_org_org_id':
					$text = __( 'Optionally select a different organization for the Schema Organization item type and/or its sub-type (Airline, Corporation, School, etc). Select "[None]" to use the default organization details.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_prep_time':
					$text = __( 'The total time it takes to prepare this recipe.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_cook_time':
					$text = __( 'The total time it takes to cook this recipe.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_total_time':
					$text = __( 'The total time it takes to prepare and cook this recipe.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_course':
					$text = __( 'The course name for this recipe (example: Appetizer, Entr&eacute;e, Main Course / Main Dish, Dessert, Side-dish, etc.).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_cuisine':
					$text = __( 'The type of cuisine for this recipe (example: French, Indian, Italian, Japanese, Thai, etc.).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_yield':
					$text = __( 'The quantity or servings made by this recipe (example: "5 servings", "Serves 4-6", "Yields 10 burgers", etc.).', 'wpsso-schema-json-ld' );
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
				case 'tooltip-meta-schema_event_org_id':
					$text = __( 'Select an organizer for the event.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_event_perf_id':
					$text = __( 'Select a performer for the event.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_event_start':
					$text = __( 'Select the start date and time for the event.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_event_end':
					$text = __( 'Select the end date and time for the event.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_event_offers':
					$text = __( 'One or more offers for the event, including the offer name, price and currency.', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_ingredients':
					$text = __( 'A list of ingredients for this recipe (example: "1 cup flour", "1 tsp salt", etc.).', 'wpsso-schema-json-ld' );
				 	break;
				case 'tooltip-meta-schema_recipe_instructions':
					$text = __( 'A list of instructions for this recipe (example: "beat eggs", "add and mix flour", etc.).', 'wpsso-schema-json-ld' );
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
				 	break;
			}
			return $text;
		}

		// hooked to 'wpssojson_status_gpl_features'
		public function filter_status_gpl_features( $features, $lca, $info, $pkg ) {
			foreach ( $info['lib']['gpl'] as $sub => $libs ) {
				if ( $sub === 'admin' ) // skip status for admin menus and tabs
					continue;
				foreach ( $libs as $id_key => $label ) {
					list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );
					$classname = SucomUtil::sanitize_classname( 'wpssojsongpl'.$sub.$id, false );	// $underscore = false
					$features[$label] = array( 'status' => class_exists( $classname ) ? 'on' : 'off' );
				}
			}
			return $this->filter_common_status_features( $features, $lca, $info, $pkg );
		}

		// hooked to 'wpssojson_status_pro_features'
		public function filter_status_pro_features( $features, $lca, $info, $pkg ) {
			return $this->filter_common_status_features( $features, $lca, $info, $pkg );
		}

		private function filter_common_status_features( $features, $lca, $info, $pkg ) {
			foreach ( $features as $key => $arr )
				if ( preg_match( '/^\(([a-z\-]+)\) (Schema Type .+) \((.+)\)$/', $key, $match ) )
					$features[$key]['label'] = $match[2].' ('.$this->p->schema->count_schema_type_children( $match[3] ).')';
			return $features;
		}
	}
}

?>
