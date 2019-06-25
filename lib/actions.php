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
			} elseif ( $this->p->check->pp( 'wpssojson' ) ) {
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

			$pro_transl     = _x( $this->p->cf[ 'dist' ][ 'pro' ], 'distribution name', 'wpsso' );
			$std_transl     = _x( $this->p->cf[ 'dist' ][ 'std' ], 'distribution name', 'wpsso' );

			$warn_msg = '<p class="top"><em>' . __( 'This notice is only shown to users with Administrative privileges.', 'wpsso-schema-json-ld' ) . '</em></p>';
			
			$warn_msg .= '<p>';

			$warn_msg .= sprintf( __( 'The WPSSO JSON %1$s add-on does not include support for the Schema type <a href="%2$s">%2$s</a>.', 'wpsso-schema-json-ld' ), $std_transl, $page_type_url ) . ' ';

			// translators: %1$s is the purchase URL, %2$s is the word "Premium", %3$s is the schema.org type URL.
			$warn_msg .= sprintf( __( 'The <a href="%1$s">WPSSO JSON %2$s add-on</a> includes an extensive selection of supported Schema types, including the Schema type <a href="%3$s">%3$s</a>.', 'wpsso-schema-json-ld' ), $urls[ 'purchase' ], $pro_transl, $page_type_url ) . ' ';

			// translators: %1$s is the word "Premium".
			$warn_msg .= sprintf( __( 'If this Schema type is an important classification for your content, you should consider purchasing the %1$s add-on.', 'wpsso-schema-json-ld' ), $pro_transl );

			$warn_msg .= '</p>';

			$this->p->notice->warn( $warn_msg, $user_id = null, $notice_key, $dismiss_time = true );
		}
	}
}
