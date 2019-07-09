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

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$dots               = '...';
			$read_cache         = true;
			$no_hashtags        = false;
			$maybe_hashtags     = true;
			$do_encode          = true;
			$schema_desc_md_key = array( 'seo_desc', 'og_desc' );

			/**
			 * Select option arrays.
			 */
			$schema_types = $this->p->schema->get_schema_types_select( null, $add_none = true );

			/**
			 * Maximum option lengths.
			 */
			$og_title_max_len    = $this->p->options['og_title_max_len'];
			$schema_desc_max_len = $this->p->options[ 'schema_desc_max_len' ];

			/**
			 * Default option values.
			 */
			$def_schema_title     = $this->p->page->get_title( 0, '', $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_title_alt = $this->p->page->get_title( $og_title_max_len, $dots, $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_desc      = $this->p->page->get_description( $schema_desc_max_len, $dots, $mod, $read_cache, $no_hashtags, $do_encode, $schema_desc_md_key );

			/**
			 * Remove and re-create.
			 */
			unset( $table_rows[ 'subsection_schema' ] );
			unset( $table_rows[ 'schema_desc' ] );

			/**
			 * Metabox form rows.
			 */
			$form_rows = array(
				'subsection_schema' => array(
					'td_class' => 'subsection',
					'header'   => 'h4',
					'label'    => _x( 'Structured Data / Schema Markup', 'metabox title', 'wpsso-schema-json-ld' )
				),

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
				'wpssojson-pro-feature-msg' => array(
					'table_row' => '<td colspan="2">' . $this->p->msgs->get( 'pro-feature-msg', array( 'lca' => 'wpssojson' ) ) . '</td>',
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
				'schema_desc' => array(
					'no_auto_draft' => true,
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Description', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_desc',
					'content'       => $form->get_no_textarea_value( $def_schema_desc, '', '', $schema_desc_max_len ),
				),
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

			return $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod );
		}
	}
}
