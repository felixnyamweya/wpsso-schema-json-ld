<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonSubmenuSchemaJsonLd' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoJsonSubmenuSchemaJsonLd extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->menu_lib = $lib;
			$this->menu_ext = $ext;
		}

		// called by the extended WpssoAdmin class
		protected function add_meta_boxes() {
			$this->maybe_show_language_notice();

			add_meta_box( $this->pagehook.'_schema_json_ld', 
				_x( 'Schema JSON-LD Markup', 'metabox title', 'wpsso-schema-json-ld' ),
					array( &$this, 'show_metabox_schema_json_ld' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_schema_json_ld() {

			$metabox_id = 'schema_json_ld';

			$tabs = apply_filters( $this->p->lca.'_'.$metabox_id.'_tabs', array( 
				'props' => _x( 'Schema Properties', 'metabox tab', 'wpsso-schema-json-ld' ),
				'types' => _x( 'Schema Types', 'metabox tab', 'wpsso-schema-json-ld' ),
				'knowledge_graph' => _x( 'Knowledge Graph', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );

			$table_rows = array();

			foreach ( $tabs as $key => $title ) {
				$table_rows[$key] = apply_filters( $this->p->lca.'_'.$metabox_id.'_'.$key.'_rows', 
					$this->get_table_rows( $metabox_id, $key ), $this->form );
			}

			$this->p->util->do_metabox_tabs( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $key ) {

			$table_rows = array();

			switch ( $metabox_id.'-'.$key ) {

				case 'schema_json_ld-props':

					$table_rows['site_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'WebSite Name',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_name', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'site_name', $this->p->options ),
						'long_name', '', 0, get_bloginfo( 'name', 'display' ) ).'</td>';

					$table_rows['site_name_alt'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'WebSite Alternate Name',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_name_alt', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'site_name_alt', $this->p->options ),
						'long_name' ).'</td>';

					$table_rows['site_desc'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'WebSite Description',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_desc', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_textarea( SucomUtil::get_key_locale( 'site_desc', $this->p->options ),
						'', '', 0, get_bloginfo( 'description', 'display' ) ).'</td>';

					$this->add_schema_item_props_table_rows( $table_rows );

					break;

				case 'schema_json_ld-types':

					$schema_types = $this->p->schema->get_schema_types_select( null, true );	// $add_none = true

					$this->add_schema_item_types_table_rows( $table_rows, array(
						'schema_type_for_archive_page' => 'hide_in_basic',	// hide in basic view
						'schema_type_for_user_page' => 'hide_in_basic',		// hide in basic view
						'schema_type_for_search_page' => 'hide_in_basic',	// hide in basic view
					), $schema_types );

					$table_rows['schema_review_item_type'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Default Reviewed Item Type', 
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_review_item_type' ).
					'<td>'.$this->form->get_select( 'schema_review_item_type', $schema_types, 'schema_type' ).'</td>';

					break;

				case 'schema_json_ld-knowledge_graph':

					$this->add_schema_knowledge_graph_table_rows( $table_rows );

					break;
			}

			return $table_rows;
		}
	}
}

