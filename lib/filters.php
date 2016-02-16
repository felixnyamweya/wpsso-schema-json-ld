<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonFilters' ) ) {

	class WpssoJsonFilters {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$this->p->util->add_plugin_filters( $this, array(
				'add_schema_head_attributes' => '__return_false',
				'add_schema_meta_array' => '__return_false',
				'data_http_schema_org_item_type' => 7,
			), -100 );

			if ( is_admin() ) {
				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 3,
					'status_pro_features' => 3,
				), 10, 'wpssojson' );
			}
		}

		public function filter_add_schema_meta_array( $bool ) {
			return false;
		}

		// create basic JSON-LD markup for the item type (url, name, description)
		public function filter_data_http_schema_org_item_type( $data, $use_post, $obj, $mt_og, $post_id, $author_id, $head_type ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			if ( $this->p->debug->enabled )
				$this->p->debug->log( 'head_type: '.$head_type );

			$lca = $this->p->cf['lca'];
			$data = WpssoSchema::get_item_type_context( $head_type );	// init the JSON-LD data array

			if ( ! empty( $mt_og['og:url'] ) )
				$data['url'] = $mt_og['og:url'];

			if ( ! empty( $mt_og['og:title'] ) )
				$data['name'] = $mt_og['og:title'];

			$data['description'] = $this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
				'...', $use_post, true, true, true, 'schema_desc' );	// custom meta = schema_desc

			switch ( $head_type ) {
				case 'http://schema.org/BlogPosting':
				case 'http://schema.org/WebPage':

					if ( ! empty( $mt_og['article:published_time'] ) )
						$data['datepublished'] = $mt_og['article:published_time'];

					if ( ! empty( $mt_og['article:modified_time'] ) )
						$data['datemodified'] = $mt_og['article:modified_time'];

					if ( $author_id > 0 )
						WpssoSchema::add_single_person_data( $data['author'],
							$author_id, true );	// list_element = true

					if ( isset( $mt_og['og:image'] ) && 
						is_array( $mt_og['og:image'] ) )
							WpssoSchema::add_image_list_data( $data['image'],
								$mt_og['og:image'], 'og:image' );

					break;
			}

			return $data;
		}

		// hooked to 'wpssojson_status_gpl_features'
		public function filter_status_gpl_features( $features, $lca, $info ) {
			foreach ( array( 
				'Item Type BlogPosting',
				'Item Type WebPage',
			) as $key )
				$features[$key]['status'] = 'on';
			return $this->add_status_schema_tooltips( $features, $lca, $info );
		}

		// hooked to 'wpssojson_status_pro_features'
		public function filter_status_pro_features( $features, $lca, $info ) {
			return $this->add_status_schema_tooltips( $features, $lca, $info );
		}

		private function add_status_schema_tooltips( $features, $lca, $info ) {
			foreach ( $features as $key => $arr ) {
				if ( strpos( $key, 'Item Type ' ) === 0 )
					$features[$key]['tooltip'] = __( 'Adds Schema JSON-LD markup for Posts, Pages, Media, and Custom Post Types with a matching Schema item type.', 'wpsso-schema-json-ld' );
				elseif ( strpos( $key, 'Property ' ) === 0 )
					$features[$key]['tooltip'] = __( 'Adds Schema JSON-LD markup for matching item type properties.', 'wpsso-schema-json-ld' );
			}
			return $features;
		}
	}
}

?>
