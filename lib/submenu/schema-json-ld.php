<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
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
			add_meta_box( $this->pagehook.'_schema_json_ld', 
				_x( 'Schema JSON-LD Markup', 'metabox title', 'wpsso-schema-json-ld' ),
					array( &$this, 'show_metabox_schema_json_ld' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_schema_json_ld() {
			$lca = $this->p->cf['lca'];
			$metabox_id = 'schema_json_ld';
			$tabs = apply_filters( $lca.'_'.$metabox_id.'_tabs', array( 
				'props' => _x( 'Schema Properties', 'metabox tab', 'wpsso-schema-json-ld' ),
				'types' => _x( 'Schema Types', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );
			$table_rows = array();
			foreach ( $tabs as $key => $title ) {
				$table_rows[$key] = apply_filters( $lca.'_'.$metabox_id.'_'.$key.'_rows', 
					$this->get_table_rows( $metabox_id, $key ), $this->form );
			}
			$this->p->util->do_metabox_tabs( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $key ) {
			$table_rows = array();
			switch ( $metabox_id.'-'.$key ) {
				case 'schema_json_ld-props':

					$table_rows['site_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Website Name',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_name', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'site_name', $this->p->options ),
						'long_name', '', 0, get_bloginfo( 'name', 'display' ) ).'</td>';

					$table_rows['schema_alt_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Website Alternate Name',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_alt_name', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'schema_alt_name', $this->p->options ),
						'long_name' ).'</td>';

					$table_rows['site_desc'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Website Description',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_desc', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_textarea( SucomUtil::get_key_locale( 'site_desc', $this->p->options ),
						'', '', 0, get_bloginfo( 'description', 'display' ) ).'</td>';

					$table_rows['schema_logo_url'] = $this->form->get_th_html( 
						'<a href="https://developers.google.com/structured-data/customize/logos">'.
						_x( 'Organization Logo URL', 'option label', 'wpsso-schema-json-ld' ).'</a>',
							'', 'schema_logo_url', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'schema_logo_url', $this->p->options ), 'wide' ).'</td>';

					$table_rows['schema_banner_url'] = $this->form->get_th_html( _x( 'Organization Banner URL',
						'option label', 'wpsso-organization' ), '', 'schema_banner_url', array( 'is_locale' => true ) ).
					'<td>'.$this->form->get_input( SucomUtil::get_key_locale( 'schema_banner_url', $this->p->options ), 'wide' ).'</td>';

					$table_rows['schema_img_max'] = $this->form->get_th_html( _x( 'Maximum Images to Include',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_img_max' ).
					'<td>'.$this->form->get_select( 'schema_img_max', 
						range( 0, $this->p->cf['form']['max_media_items'] ), 'short', '', true ).
					( empty( $this->form->options['og_vid_prev_img'] ) ?
						'' : ' <em>'._x( 'video preview images are enabled (and included first)',
							'option comment', 'wpsso-schema-json-ld' ).'</em>' ).'</td>';

					$table_rows['schema_img'] = $this->form->get_th_html( _x( 'Schema Image Dimensions',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_img_dimensions' ).
					'<td>'.$this->form->get_input_image_dimensions( 'schema_img' ).'</td>';	// $use_opts = false

					$table_rows['schema_desc_len'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Maximum Description Length',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_desc_len' ).
					'<td>'.$this->form->get_input( 'schema_desc_len', 'short' ).' '.
						_x( 'characters or less', 'option comment', 'wpsso-schema-json-ld' ).'</td>';

					$table_rows['schema_author_name'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Author / Person Name Format',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_author_name' ).
					'<td>'.$this->form->get_select( 'schema_author_name', 
						$this->p->cf['form']['user_name_fields'] ).'</td>';

					break;

				case 'schema_json_ld-types':

					$schema_types = $this->p->schema->get_schema_types_select( null, true );	// $add_none = true

					foreach ( array( 
						'home_index' => _x( 'Item Type for Blog Front Page', 'option label', 'wpsso-schema-json-ld' ),
						'home_page' => _x( 'Item Type for Static Front Page', 'option label', 'wpsso-schema-json-ld' ),
						'archive_page' => _x( 'Item Type for Archive Page', 'option label', 'wpsso-schema-json-ld' ),
						'user_page' => _x( 'Item Type for User / Author Page', 'option label', 'wpsso-schema-json-ld' ),
						'search_page' => _x( 'Item Type for Search Results Page', 'option label', 'wpsso-schema-json-ld' ),
					) as $type_name => $type_label ) {
						switch ( $type_name ) {
							case 'archive_page':
							case 'user_page':
							case 'search_page':
								$tr = '<tr class="hide_in_basic">';
								break;
							default:
								$tr = '';
								break;
						}
						$table_rows['schema_type_for_'.$type_name] = $tr.
						$this->form->get_th_html( $type_label, '', 'schema_type_for_'.$type_name ).
						'<td>'.$this->form->get_select( 'schema_type_for_'.$type_name, $schema_types, 'schema_type' ).'</td>';
					}

					$schema_by_ptn = '';
					foreach ( $this->p->util->get_post_types( 'object' ) as $pt ) {
						$schema_by_ptn .= '<p>'.$this->form->get_select( 'schema_type_for_'.$pt->name,
							$schema_types, 'schema_type' ).' for '.$pt->label.'</p>'."\n";
					}

					$table_rows['schema_type_for_ptn'] = $this->form->get_th_html( _x( 'Item Type by Post Type',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_type_for_ptn' ).
					'<td>'.$schema_by_ptn.'</td>';

					$table_rows['schema_review_item_type'] = '<tr class="hide_in_basic">'.
					$this->form->get_th_html( _x( 'Default Reviewed Item Type', 
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_review_item_type' ).
					'<td>'.$this->form->get_select( 'schema_review_item_type', $schema_types, 'schema_type' ).'</td>';

					break;
			}
			return $table_rows;
		}
	}
}

?>
