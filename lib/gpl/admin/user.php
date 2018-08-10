<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonGplAdminUser' ) ) {

	class WpssoJsonGplAdminUser {

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

			$dots      = '...';
			$r_cache   = true;
			$do_encode = true;

			$sameas_max          = SucomUtil::get_const( 'WPSSO_SCHEMA_SAMEAS_URL_MAX', 5 );
			$og_title_max_len    = $this->p->options['og_title_len'];
			$schema_desc_max_len = $this->p->options['schema_desc_len'];

			$def_schema_title     = $this->p->page->get_title( 0, '', $mod, $r_cache, false, $do_encode, 'og_title' );
			$def_schema_title_alt = $this->p->page->get_title( $og_title_max_len, $dots, $mod, $r_cache, false, $do_encode, 'og_title' );
			$def_schema_desc      = $this->p->page->get_description( $schema_desc_max_len, $dots, $mod, $r_cache, false, $do_encode, array( 'seo_desc', 'og_desc' ) );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// Timer end.
			}

			/**
			 * Save and re-use the existing Schema Description field from WPSSO Core if available.
			 */
			$schema_desc_row = isset( $table_rows['schema_desc'] ) ? array( 'table_row' => $table_rows['schema_desc'] ) : array(
				'label' => _x( 'Profile Page Description', 'option label', 'wpsso-schema-json-ld' ),
				'th_class' => 'medium', 'tooltip' => 'meta-schema_desc', 'td_class' => 'blank',
				'content' => $form->get_no_textarea_value( $def_schema_desc, '', '', $schema_desc_max_len ),
			);

			/**
			 * Remove the default schema rows so we can append a whole new set with a different order.
			 */
			foreach ( array( 'subsection_schema', 'schema_desc' ) as $key ) {
				if ( isset( $table_rows[$key] ) ) {
					unset ( $table_rows[$key] );
				}
			}

			$form_rows = array(
				'subsection_schema' => array(
					'td_class' => 'subsection', 'header' => 'h4',
					'label' => _x( 'Structured Data / Schema Markup', 'metabox title', 'wpsso-schema-json-ld' )
				),
				'schema_title' => array(
					'label' => _x( 'Schema Item Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_title', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( $def_schema_title, 'wide' ),
				),
				'schema_title_alt' => array(
					'label' => _x( 'Schema Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_title_alt', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( $def_schema_title_alt, 'wide' ),
				),
				'schema_desc' => $schema_desc_row,
				'schema_sameas_url' => array(
					'label' => _x( 'Other Profile Page URLs', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_sameas_url', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide', '', '', 2 ),
				),

				/**
				 * Schema Person
				 */
				'subsection_person' => array(
					'td_class' => 'subsection', 'header' => 'h5',
					'label' => _x( 'Person Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_person_job_title' => array(
					'label' => _x( 'Job Title', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_person_job_title', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide' ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod );

			return SucomUtil::get_after_key( $table_rows, 'subsection_schema', '',
				'<td colspan="2">' . $this->p->msgs->get( 'pro-feature-msg', array( 'lca' => 'wpssojson' ) ) . '</td>' );
		}
	}
}
