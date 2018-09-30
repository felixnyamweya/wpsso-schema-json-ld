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
					array( $this, 'show_metabox_schema_json_ld' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_schema_json_ld() {

			$metabox_id = 'schema_json_ld';

			$tabs = apply_filters( $this->p->lca.'_'.$metabox_id.'_tabs', array( 
				'props'           => _x( 'Schema Properties', 'metabox tab', 'wpsso-schema-json-ld' ),
				'types'           => _x( 'Schema Types', 'metabox tab', 'wpsso-schema-json-ld' ),
				'knowledge_graph' => _x( 'Knowledge Graph', 'metabox tab', 'wpsso-schema-json-ld' ),
				'meta_defaults'   => _x( 'Meta Defaults', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );

			$table_rows = array();

			foreach ( $tabs as $tab_key => $title ) {
				$table_rows[$tab_key] = apply_filters( $this->p->lca.'_'.$metabox_id.'_'.$tab_key.'_rows', 
					$this->get_table_rows( $metabox_id, $tab_key ), $this->form );
			}

			$this->p->util->do_metabox_tabbed( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id.'-'.$tab_key ) {

				case 'schema_json_ld-props':

					$atts_locale = array( 'is_locale' => true );

					$def_site_name = get_bloginfo( 'name', 'display' );
					$def_site_desc = get_bloginfo( 'description', 'display' );

					$site_name_key     = SucomUtil::get_key_locale( 'site_name', $this->form->options );
					$site_name_alt_key = SucomUtil::get_key_locale( 'site_name_alt', $this->form->options );
					$site_desc_key     = SucomUtil::get_key_locale( 'site_desc', $this->form->options );

					$table_rows['site_name'] = $this->form->get_tr_hide( 'basic', $site_name_key ).
					$this->form->get_th_html( _x( 'WebSite Name', 'option label', 'wpsso-schema-json-ld' ), '', 'site_name', $atts_locale ).
					'<td>'.$this->form->get_input( $site_name_key, 'long_name', '', 0, $def_site_name ).'</td>';

					$table_rows['site_name_alt'] = $this->form->get_tr_hide( 'basic', $site_name_alt_key ).
					$this->form->get_th_html( _x( 'WebSite Alternate Name', 'option label', 'wpsso-schema-json-ld' ), '', 'site_name_alt', $atts_locale ).
					'<td>'.$this->form->get_input( $site_name_alt_key, 'long_name' ).'</td>';

					$table_rows['site_desc'] = $this->form->get_tr_hide( 'basic', $site_desc_key ).
					$this->form->get_th_html( _x( 'WebSite Description', 'option label', 'wpsso-schema-json-ld' ), '', 'site_desc', $atts_locale ).
					'<td>'.$this->form->get_textarea( $site_desc_key, '', '', 0, $def_site_desc ).'</td>';

					$this->add_schema_item_props_table_rows( $table_rows );

					break;

				case 'schema_json_ld-types':

					$schema_types = $this->p->schema->get_schema_types_select( null, true );	// $add_none = true

					/**
					 * Show all by default, except for the archive, user, and search types.
					 */
					$this->add_schema_item_types_table_rows( $table_rows, array(
						'schema_type_for_user_page' => 'basic',
						'schema_type_for_search_page' => 'basic',
						'schema_type_for_archive_page' => 'basic',
					), $schema_types );

					break;

				case 'schema_json_ld-knowledge_graph':

					$this->add_schema_knowledge_graph_table_rows( $table_rows );

					break;

				case 'schema_json_ld-meta_defaults':

					/**
					 * Default currency.
					 */
					$table_rows['plugin_def_currency'] = '' .
					$this->form->get_th_html( _x( 'Default Currency', 'option label', 'wpsso' ), '', 'plugin_def_currency' ).
					'<td>'.$this->form->get_select( 'plugin_def_currency', SucomUtil::get_currencies() ).'</td>';

					break;
			}

			return $table_rows;
		}
	}
}
