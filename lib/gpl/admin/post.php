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
					'wpsso-schema-json-ld' ), $head_info['ptn'] ).'</em></td>';

			// move the schema description down
			unset ( $rows['schema_desc'] );

			$rows[] = '<td></td><td class="subsection top"><h4>'.
				_x( 'Google / Schema JSON-LD',
					'metabox title', 'wpsso-schema-json-ld' ).'</h4></td>';

			$headline_max_len = WpssoJsonConfig::$cf['schema']['article']['headline']['max_len'];
			if ( $post_status == 'auto-draft' )
				$rows['schema_headline'] = $this->p->util->get_th( _x( 'Article Headline',
					'option label', 'wpsso-schema-json-ld' ), 'medium', 'meta-schema_headline', $head_info ).
						$td_save_draft;
			else $rows['schema_headline'] = $this->p->util->get_th( _x( 'Article Headline',
					'option label', 'wpsso-schema-json-ld' ), 'medium', 'meta-schema_headline', $head_info ). 
				'<td class="blank">'.$this->p->webpage->get_title( $headline_max_len, '...', true ).'</td>';

			if ( $post_status == 'auto-draft' )
				$rows['schema_desc'] = $this->p->util->get_th( _x( 'Description',
					'option label', 'wpsso-schema-json-ld' ), 'medium', 'meta-schema_desc', $head_info ).
						$td_save_draft;
			else $rows['schema_desc'] = $this->p->util->get_th( _x( 'Description',
					'option label', 'wpsso-schema-json-ld' ), 'medium', 'meta-schema_desc', $head_info ).
				'<td class="blank">'.$this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
					'...', true ).'</td>';
	
			return $rows;
		}
	}
}

?>
