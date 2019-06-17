<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonActions' ) ) {

	class WpssoJsonActions {

		protected $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( is_admin() ) {

				$this->p->util->add_plugin_actions( $this, array(
					'admin_post_head' => 1,
				) );
			}
		}

		public function action_admin_post_head( $mod ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			} elseif ( $this->p->check->pp( 'wpssojson', true, $this->p->avail[ '*' ][ 'p_dir' ] ) ) {
				return;
			}

			$urls          = $this->p->cf[ 'plugin' ][ 'wpssojson' ][ 'url' ];
			$page_type_id  = $this->p->schema->get_mod_schema_type( $mod, $get_schema_id = true );
			$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );
			$filter_name   = $this->p->schema->get_json_data_filter( $mod, $page_type_url );
			$notice_key    = 'no_filter_' . $filter_name . '_' . $mod[ 'name' ] . '_' . $mod[ 'id' ];

			if ( has_filter( $filter_name ) ) {
				return;
			}

			$warn_msg = '<p class="top"><em>' . __( 'This notice is only shown to users with Administrative privileges.', 'wpsso-schema-json-ld' ) . '</em></p>';
			
			$warn_msg .= '<p>';

			$warn_msg .= sprintf( __( 'The Free / Standard version of WPSSO JSON does not include support for the Schema type <a href="%1$s">%1$s</a> - only the basic Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $page_type_url ) . ' ';
				
			$warn_msg .= sprintf( __( 'The <a href="%1$s">Premium version of WPSSO JSON</a> includes a wide selection of supported Schema types, including the Schema type <a href="%2$s">%2$s</a>.', 'wpsso-schema-json-ld' ), $urls['purchase'], $page_type_url ) . ' ';
				
			$warn_msg .= sprintf( __( 'If this Schema type is an important classification for your content, you should consider purchasing the Premium version.', 'wpsso-schema-json-ld' ), $page_type_url );

			$warn_msg .= '</p>';

			$this->p->notice->warn( $warn_msg, $user_id = null, $notice_key, $dismiss_time = true );
		}
	}
}
