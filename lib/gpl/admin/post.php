<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonGplAdminPost' ) ) {

	class WpssoJsonGplAdminPost {

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->util->add_plugin_filters( $this, array( 
				'post_header_rows' => 3,
			) );
		}

		public function filter_post_header_rows( $rows, $form, $head_info ) {
			$post_status = get_post_status( $head_info['post_id'] );
			$post_type = get_post_type( $head_info['post_id'] );
			$td_save_draft = '<td class="blank"><em>'.
				sprintf( __( 'Save a draft version or publish the %s to update this value.',
					'wpsso' ), $head_info['ptn'] ).'</em></td>';
			$disable_article = isset( $head_info['og:type'] ) &&
				$head_info['og:type'] === 'article' ? false : true;

			if ( $post_status == 'auto-draft' )
				$rows = SucomUtil::insert_before_key( $rows, 'schema_desc',
					'schema_headline', $this->p->util->get_th( _x( 'Google / Schema Headline',
						'option label', 'wpsso-schema-json-ld' ), 'medium', 'meta-schema_headline', $head_info ).
							$td_save_draft );
			else $rows = SucomUtil::insert_before_key( $rows, 'schema_desc',
				'schema_headline', $this->p->util->get_th( _x( 'Google / Schema Headline',
					'option label', 'wpsso-schema-json-ld' ), 'medium', 'meta-schema_headline', $head_info ). 
				'<td class="blank">'.$this->p->webpage->get_title( $this->p->options['og_title_len'],
					'...', true, true, false, true, 'none' ).'</td>' );	// $use_post = true, $md_idx = 'none'

			return $rows;
		}
	}
}

?>
