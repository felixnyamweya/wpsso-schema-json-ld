<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonGplAdminPost' ) ) {

	class WpssoJsonGplAdminPost {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'post_text_rows' => 4,	// $table_rows, $form, $head, $mod
			) );
		}

		public function filter_post_text_rows( $table_rows, $form, $head, $mod ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// timer begin
			}

			$schema_types = $this->p->schema->get_schema_types_select();	// $add_none = true
			$title_max_len = $this->p->options['og_title_len'];
			$desc_max_len = $this->p->options['schema_desc_len'];
			$headline_max_len = WpssoJsonConfig::$cf['schema']['article']['headline']['max_len'];
			$auto_draft_msg = sprintf( __( 'Save a draft version or publish the %s to update this value.',
				'wpsso-schema-json-ld' ), SucomUtil::titleize( $mod['post_type'] ) );
			$days_sep = ' '._x( 'days', 'option comment', 'wpsso-schema-json-ld' ).', ';
			$hours_sep = ' '._x( 'hours', 'option comment', 'wpsso-schema-json-ld' ).', ';
			$mins_sep = ' '._x( 'mins', 'option comment', 'wpsso-schema-json-ld' ).', ';
			$secs_sep = ' '._x( 'secs', 'option comment', 'wpsso-schema-json-ld' );

			/*
			 * Organization variables.
			 */
			$org_names = array( 'none' => '[None]', 'site' => _x( 'Website Organization',
				'option value', 'wpsso-schema-json-ld' ) );
			$perf_names = array( 'none' => '[None]' );

			if ( ! empty( $this->p->cf['plugin']['wpssoorg'] ) && empty( $this->p->cf['plugin']['wpssoorg']['version'] ) ) {
				$org_info = $this->p->cf['plugin']['wpssoorg'];
				$org_req_msg = ' <em><a href="'.$org_info['url']['home'].'" target="_blank">'.
					sprintf( _x( '%s extension required', 'option comment', 'wpsso-schema-json-ld' ),
						$org_info['short'] ).'</a></em>';
			} else {
				$org_req_msg = '';
			}

			/*
			 * Javascript classes to hide/show rows by selected schema type.
			 */
			$schema_type_tr_class = array(
				'article' => $this->p->schema->get_children_css_class( 'article', 'hide_schema_type' ),
				'event' => $this->p->schema->get_children_css_class( 'event', 'hide_schema_type' ),
				'local.business' => $this->p->schema->get_children_css_class( 'local.business', 'hide_schema_type' ),
				'organization' => $this->p->schema->get_children_css_class( 'organization', 'hide_schema_type' ),
				'recipe' => $this->p->schema->get_children_css_class( 'recipe', 'hide_schema_type' ),
				'review' => $this->p->schema->get_children_css_class( 'review', 'hide_schema_type' ),
			);

			/*
			 * Remove the default schema rows so we can append a whole new set.
			 */
			foreach ( array( 'subsection_schema', 'schema_desc' ) as $key ) {
				if ( isset( $table_rows[$key] ) ) {
					unset ( $table_rows[$key] );
				}
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// timer end
			}

			$form_rows = array(
				'subsection_schema' => array(
					'td_class' => 'subsection',
					'header' => 'h4',
					'label' => _x( 'Google Structured Data / Schema Markup', 'metabox title', 'wpsso-schema-json-ld' )
				),
				/*
				 * All Schema Types
				 */
				'schema_title' => array(
					'label' => _x( 'Schema Item Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_title', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_input_value( $this->p->webpage->get_title( $title_max_len,
						'...', $mod ), 'wide' ),
				),
				'schema_desc' => array(
					'label' => _x( 'Schema Description', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_desc', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_textarea_value( $this->p->webpage->get_description( $desc_max_len, 
						'...', $mod ), '', '', $desc_max_len ),
				),
				'schema_is_main' => array(
					'label' => _x( 'Main Entity of Page', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_is_main', 'td_class' => 'blank',
					'content' => $form->get_no_checkbox( 'schema_is_main' ),
				),
				'schema_type' => array(
					'label' => _x( 'Schema Item Type', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_type', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_type', $schema_types,
						'long_name', '', true, $form->defaults['schema_type'], 'unhide_rows' ),
				),
				/*
				 * Schema Article
				 */
				'subsection_article' => array(
					'tr_class' => $schema_type_tr_class['article'],
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Article Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_pub_org_id' => array(
					'tr_class' => $schema_type_tr_class['article'],
					'label' => _x( 'Article Publisher', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_pub_org_id', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_pub_org_id', $org_names, 'long_name' ).$org_req_msg,
				),
				'schema_headline' => array(
					'tr_class' => $schema_type_tr_class['article'],
					'label' => _x( 'Article Headline', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_headline', 'td_class' => 'blank',
					'no_auto_draft' => true,
					'content' => $form->get_no_input_value( $this->p->webpage->get_title( $headline_max_len, '...', $mod ), 'wide' ),
				),
				/*
				 * Schema Event
				 */
				'subsection_event' => array(
					'tr_class' => $schema_type_tr_class['event'],
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Event Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_event_org_id' => array(
					'tr_class' => $schema_type_tr_class['event'],
					'label' => _x( 'Event Organizer', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_event_org_id', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_event_org_id', $org_names, 'long_name' ).$org_req_msg,
				),
				'schema_event_perf_id' => array(
					'tr_class' => $schema_type_tr_class['event'],
					'label' => _x( 'Event Performer', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_event_perf_id', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_event_perf_id', $perf_names, 'long_name' ).$org_req_msg,
				),
				/*
				 * Schema Organization
				 */
				'subsection_organization' => array(
					'tr_class' => $schema_type_tr_class['organization'].' '.$schema_type_tr_class['local.business'],
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Organization Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_org_org_id' => array(
					'tr_class' => $schema_type_tr_class['organization'].' '.$schema_type_tr_class['local.business'],
					'label' => _x( 'Organization', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_org_org_id', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_org_org_id', $org_names, 'long_name' ).$org_req_msg,
				),
				/*
				 * Schema Recipe
				 */
				'subsection_recipe' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Recipe Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_course' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Course', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_course', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_cuisine' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Cuisine', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_cuisine', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_yield' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Quantity', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_yield', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_prep_time' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Preperation Time', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_prep_time', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '0', 'short' ).$days_sep.
						$form->get_no_input_value( '0', 'short' ).$hours_sep.
						$form->get_no_input_value( '0', 'short' ).$mins_sep.
						$form->get_no_input_value( '0', 'short' ).$secs_sep,
				),
				'schema_recipe_cook_time' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Cooking Time', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_cook_time', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '0', 'short' ).$days_sep.
						$form->get_no_input_value( '0', 'short' ).$hours_sep.
						$form->get_no_input_value( '0', 'short' ).$mins_sep.
						$form->get_no_input_value( '0', 'short' ).$secs_sep,
				),
				'schema_recipe_total_time' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Total Time', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_total_time', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '0', 'short' ).$days_sep.
						$form->get_no_input_value( '0', 'short' ).$hours_sep.
						$form->get_no_input_value( '0', 'short' ).$mins_sep.
						$form->get_no_input_value( '0', 'short' ).$secs_sep,
				),
				'schema_recipe_ingredients' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Ingredients', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_ingredients', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_instructions' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Recipe Instructions', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_instructions', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide' ),
				),
				'subsection_recipe_nutrition' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Nutrition Information per Serving', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_serv' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Serving Size', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_serv', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'long_name required' ),
				),
				'schema_recipe_nutri_cal' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Calories', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_cal', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'calories', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_prot' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Protein', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_prot', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of protein', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_fib' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Fiber', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_fib', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of fiber', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_carb' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Carbohydrates', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_carb', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of carbohydrates', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_sugar' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Sugar', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_sugar', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of sugar', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_sod' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Sodium', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_sod', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'milligrams of sodium', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_fat' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Fat', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_fat', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_sat_fat' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Saturated Fat', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_sat_fat', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of saturated fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_unsat_fat' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Unsaturated Fat', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_unsat_fat', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of unsaturated fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_trans_fat' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Trans Fat', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_trans_fat', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'grams of trans fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_chol' => array(
					'tr_class' => $schema_type_tr_class['recipe'],
					'label' => _x( 'Cholesterol', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_recipe_nutri_chol', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'medium' ).' '.
						_x( 'milligrams of cholesterol', 'option comment', 'wpsso-schema-json-ld' ),
				),
				/*
				 * Schema Review
				 */
				'subsection_review' => array(
					'tr_class' => $schema_type_tr_class['review'],
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Review Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_review_item_type' => array(
					'tr_class' => $schema_type_tr_class['review'],
					'label' => _x( 'Subject Type', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_review_item_type', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_review_item_type', $schema_types, 'long_name' ),
				),
				'schema_review_item_name' => array(
					'tr_class' => $schema_type_tr_class['review'],
					'label' => _x( 'Subject Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_review_item_name', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide' ),
				),
				'schema_review_item_url' => array(
					'tr_class' => $schema_type_tr_class['review'],
					'label' => _x( 'Subject Webpage URL', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_review_item_url', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide' ),
				),
				'schema_review_item_image_url' => array(
					'tr_class' => $schema_type_tr_class['review'],
					'label' => _x( 'Subject Image URL', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_review_item_image_url', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide' ),
				),
				// included as schema.org/Rating, not schema.org/aggregateRating
				'schema_review_rating' => array(
					'tr_class' => $schema_type_tr_class['review'],
					'label' => _x( 'Review Rating', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_review_rating', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( $form->defaults['schema_review_rating'], 'short' ).
						' '._x( 'from', 'option comment', 'wpsso-schema-json-ld' ).' '.
							$form->get_no_input_value( $form->defaults['schema_review_rating_from'], 'short' ).
						' '._x( 'to', 'option comment', 'wpsso-schema-json-ld' ).' '.
							$form->get_no_input_value( $form->defaults['schema_review_rating_to'], 'short' ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod, $auto_draft_msg );

			return SucomUtil::get_after_key( $table_rows, 'subsection_schema',
				'', '<td colspan="2">'.$this->p->msgs->get( 'pro-feature-msg', 
					array( 'lca' => 'wpssojson' ) ).'</td>' );
		}
	}
}

?>
