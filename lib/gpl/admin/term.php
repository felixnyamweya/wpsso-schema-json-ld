<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonGplAdminTerm' ) ) {

	class WpssoJsonGplAdminTerm {

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

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// Timer begin.
			}

			$schema_types        = $this->p->schema->get_schema_types_select( null, true ); // $add_none is true.
			$addl_type_max       = SucomUtil::get_const( 'WPSSO_SCHEMA_ADDL_TYPE_URL_MAX', 5 );
			$sameas_max          = SucomUtil::get_const( 'WPSSO_SCHEMA_SAMEAS_URL_MAX', 5 );
			$og_title_max_len    = $this->p->options['og_title_len'];
			$schema_desc_max_len = $this->p->options['schema_desc_len'];

			$def_schema_title     = $this->p->page->get_title( 0, '', $mod, true, false, true, 'og_title', false );
			$def_schema_title_alt = $this->p->page->get_title( $og_title_max_len, '...', $mod, true, false, true, 'og_title' );
			$def_schema_desc      = $this->p->page->get_description( $schema_desc_max_len, '...', $mod );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark( 'setup post form variables' );	// Timer end.
			}

			/**
			 * Remove the default schema rows so we can append a whole new set.
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
					'tr_class' => $def_schema_title === $def_schema_title_alt ? 'hide_in_basic' : '',	// Hide if titles are the same.
					'label' => _x( 'Schema Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_title_alt', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( $def_schema_title_alt, 'wide' ),
				),
				'schema_desc' => array(
					'label' => _x( 'Schema Description', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_desc', 'td_class' => 'blank',
					'content' => $form->get_no_textarea_value( $def_schema_desc, '', '', $schema_desc_max_len ),
				),
				'schema_type' => array(
					'label' => _x( 'Schema Item Type', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_type', 'td_class' => 'blank',
					'content' => $form->get_no_select( 'schema_type', $schema_types, 'schema_type', '', true, true, 'unhide_rows' ),
				),
				'schema_addl_type_url' => array(
					'tr_class' => $form->get_css_class_hide_prefix( 'basic', 'schema_addl_type_url' ),
					'label' => _x( 'Microdata Type URLs', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_addl_type_url', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide', '', '', 2 ),
				),
				'schema_sameas_url' => array(
					'tr_class' => $form->get_css_class_hide_prefix( 'basic', 'schema_sameas_url' ),
					'label' => _x( 'Same-As URLs', 'option label', 'wpsso-schema-json-ld' ),
					'th_class' => 'medium', 'tooltip' => 'meta-schema_sameas_url', 'td_class' => 'blank',
					'content' => $form->get_no_input_value( '', 'wide', '', '', 2 ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod );

			return SucomUtil::get_after_key( $table_rows, 'subsection_schema',
				'', '<td colspan="2">'.$this->p->msgs->get( 'pro-feature-msg', 
					array( 'lca' => 'wpssojson' ) ).'</td>' );
		}
	}
}
