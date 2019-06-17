<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonStdAdminUser' ) ) {

	class WpssoJsonStdAdminUser {

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'user_edit_rows' => 4,
			) );
		}

		public function filter_user_edit_rows( $table_rows, $form, $head, $mod ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// Timer begin.
			}

			$dots           = '...';
			$read_cache     = true;
			$no_hashtags    = false;
			$maybe_hashtags = true;
			$do_encode      = true;

			$og_title_max_len = $this->p->options['og_title_max_len'];

			$def_schema_title     = $this->p->page->get_title( 0, '', $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_title_alt = $this->p->page->get_title( $og_title_max_len, $dots, $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// Timer end.
			}

			/**
			 * Save and remove specific rows so we can append a whole new set with a different order.
			 */
			$saved_table_rows = array();

			foreach ( array( 'subsection_schema', 'schema_desc' ) as $key ) {

				if ( isset( $table_rows[ $key ] ) ) {

					$saved_table_rows[ $key ] = $table_rows[ $key ];

					unset ( $table_rows[ $key ] );
				}
			}

			$form_rows = array(
				'subsection_schema' => '',	// Placeholder.

				/**
				 * All Schema Types
				 */
				'schema_title' => array(
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Name / Title', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_title',
					'content'  => $form->get_no_input_value( $def_schema_title, 'wide' ),
				),
				'schema_title_alt' => array(
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_title_alt',
					'content'  => $form->get_no_input_value( $def_schema_title_alt, 'wide' ),
				),
				'schema_desc' => '',	// Placeholder.
				'schema_sameas_url' => array(
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Other Profile Page URLs', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_sameas_url',
					'content'  => $form->get_no_input_value( '', 'wide', '', '', $repeat = 2 ),
				),

				/**
				 * Schema Person
				 */
				'subsection_person' => array(
					'td_class' => 'subsection',
					'header'   => 'h4',
					'label'    => _x( 'Person Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_person_job_title' => array(
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Job Title', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_person_job_title',
					'content'  => $form->get_no_input_value( '', 'wide' ),
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
