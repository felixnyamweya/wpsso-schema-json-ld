<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonStdAdminTerm' ) ) {

	class WpssoJsonStdAdminTerm {

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'term_edit_rows' => 4,
			) );
		}

		public function filter_term_edit_rows( $table_rows, $form, $head, $mod ) {

			$dots           = '...';
			$read_cache     = true;
			$no_hashtags    = false;
			$maybe_hashtags = true;
			$do_encode      = true;

			/**
			 * Select option arrays.
			 */
			$schema_types = $this->p->schema->get_schema_types_select( null, $add_none = true );

			/**
			 * Maximum option lengths.
			 */
			$og_title_max_len = $this->p->options['og_title_max_len'];

			/**
			 * Default option values.
			 */
			$def_schema_title     = $this->p->page->get_title( 0, '', $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_title_alt = $this->p->page->get_title( $og_title_max_len, $dots, $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );

			/**
			 * Save and remove specific rows so we can append a whole new set with a different order.
			 */
			$saved_table_rows = array();

			foreach ( array( 'subsection_schema', 'schema_desc' ) as $key ) {

				if ( isset( $table_rows[ $key ] ) ) {

					$saved_table_rows[ $key ] = $table_rows[ $key ];

					unset( $table_rows[ $key ] );
				}
			}

			$form_rows = array(
				'subsection_schema' => '',	// Placeholder.

				/**
				 * All Schema Types
				 */
				'schema_type' => array(
					'th_class' => 'medium',
					'label'    => _x( 'Schema Type', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_type',
					'content'  => $form->get_select( 'schema_type', $schema_types,
						'schema_type', '', true, false, true, 'on_change_unhide_rows' ),
				),
				'schema_title' => array(
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Name / Title', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_title',
					'content'  => $form->get_no_input_value( $def_schema_title, 'wide' ),
				),
				'schema_title_alt' => array(
					'tr_class' => $def_schema_title === $def_schema_title_alt ? 'hide_in_basic' : '',	// Hide if titles are the same.
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_title_alt',
					'content'  => $form->get_no_input_value( $def_schema_title_alt, 'wide' ),
				),
				'schema_desc' => '',	// Placeholder.
				'schema_addl_type_url' => array(
					'tr_class' => $form->get_css_class_hide_prefix( 'basic', 'schema_addl_type_url' ),
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Microdata Type URLs', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_addl_type_url',
					'content'  => $form->get_no_input_value( '', 'wide', '', '', $repeat = 2 ),
				),
				'schema_sameas_url' => array(
					'tr_class' => $form->get_css_class_hide_prefix( 'basic', 'schema_sameas_url' ),
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Same-As URLs', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_sameas_url',
					'content'  => $form->get_no_input_value( '', 'wide', '', '', $repeat = 2 ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod );

			/**
			 * Restore the saved rows.
			 */
			foreach ( $saved_table_rows as $key => $value ) {
				$table_rows[ $key ] = $saved_table_rows[ $key ];
			}

			SucomUtil::add_after_key( $table_rows, 'subsection_schema', '', '<td colspan="2">' .
				$this->p->msgs->get( 'pro-feature-msg', array( 'lca' => 'wpssojson' ) ) . '</td>' );

			return $table_rows;
		}
	}
}
