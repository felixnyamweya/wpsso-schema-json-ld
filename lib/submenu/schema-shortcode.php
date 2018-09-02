<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonSubmenuSchemaShortcode' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoJsonSubmenuSchemaShortcode extends WpssoAdmin {

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

		protected function add_plugin_hooks() {
			$this->p->util->add_plugin_filters( $this, array(
				'action_buttons' => 1,
			) );
		}

		// called by the extended WpssoAdmin class
		protected function add_meta_boxes() {
			add_meta_box( $this->pagehook.'_schema_shortcode',
				_x( 'Schema Shortcode', 'metabox title', 'wpsso-schema-json-ld' ),
					array( $this, 'show_metabox_schema_shortcode' ), $this->pagehook, 'normal' );
		}

		public function filter_action_buttons( $action_buttons ) {
			unset( $action_buttons[0] );
			return $action_buttons;
		}

		public function show_metabox_schema_shortcode() {
			echo '<table class="sucom-settings '.$this->p->lca.' html-content-metabox">';
			echo '<tr><td>';
			echo $this->get_config_url_content( 'wpssojson', 'html/shortcode.html' );
			echo '</td></tr></table>';
		}
	}
}

