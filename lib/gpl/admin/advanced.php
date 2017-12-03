<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonGplAdminAdvanced' ) ) {

	class WpssoJsonGplAdminAdvanced {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'plugin_cache_rows' => 3,		// $table_rows, $form, $network
			), 25 );
		}

		// filter priority must be more than 20
		public function filter_plugin_cache_rows( $table_rows, $form, $network = false ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$lca = $this->p->cf['lca'];
			$cache_md5_pre = $lca.'_j_';
			$cache_opt_key = $this->p->cf['wp']['transient'][$cache_md5_pre]['opt_key'];

			SucomUtil::add_after_key( $table_rows, 'plugin_types_cache_exp', array(
				$cache_opt_key => $form->get_th_html( _x( 'Schema Post Data Cache Expiry',
					'option label', 'wpsso-schema-json-ld' ), null, $cache_opt_key ).
				'<td nowrap class="blank">'.$this->p->options[$cache_opt_key].' '.
				_x( 'seconds (0 to disable)', 'option comment', 'wpsso-schema-json-ld' ).'</td>'.
				WpssoAdmin::get_option_site_use( $cache_opt_key, $form, $network ),
			) );

			return $table_rows;
		}
	}
}

