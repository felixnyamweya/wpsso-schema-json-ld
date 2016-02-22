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
				'json_data_http_schema_org_item_type' => 8,
			), -100 );	// make sure we run first

			if ( is_admin() ) {
				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 3,
					'status_pro_features' => 3,
				), 10, 'wpssojson' );
				$this->p->util->add_plugin_filters( $this, array(
					'pub_google_rows' => 2,
				) );
			}
		}

		public function filter_pub_google_rows( $rows, $form ) {
			unset ( $rows['schema_add_noscript'] );
			return $rows;
		}

		public function filter_json_data_http_schema_org_item_type( $json_data, 
			$use_post, $obj, $mt_og, $post_id, $author_id, $head_type, $main_entity ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$ret = WpssoSchema::get_item_type_context( $head_type );

			WpssoSchema::add_data_prop_from_og( $ret, $mt_og, array(
				'url' => 'og:url',
				'name' => 'og:title',
			) );

			$ret['description'] = $this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
				'...', $use_post, true, true, true, 'schema_desc' );	// custom meta = schema_desc

			if ( $main_entity )
				WpssoSchema::add_main_entity_data( $ret, $ret['url'] );

			switch ( $head_type ) {
				case 'http://schema.org/BlogPosting':
				case 'http://schema.org/WebPage':

					WpssoSchema::add_data_prop_from_og( $ret, $mt_og, array(
						'datepublished' => 'article:published_time',
						'datemodified' => 'article:modified_time',
					) );

					if ( $author_id > 0 )
						WpssoSchema::add_single_person_data( $ret['author'],
							$author_id, true );	// list_element = true

					$size_name = $this->p->cf['lca'].'-schema';
					$og_image = $this->p->og->get_all_images( 1, $size_name, $post_id, true, 'schema' );

					if ( empty( $og_image ) && 
						SucomUtil::is_post_page( $use_post ) )
							$og_image = $this->p->media->get_default_image( 1, $size_name, true );

					WpssoSchema::add_image_list_data( $ret['image'], $og_image, 'og:image' );

					break;
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}

		// hooked to 'wpssojson_status_gpl_features'
		public function filter_status_gpl_features( $features, $lca, $info ) {
			foreach ( array( 
				'Itemtype BlogPosting',
				'Itemtype WebPage',
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
				if ( strpos( $key, 'Itemtype ' ) === 0 )
					$features[$key]['tooltip'] = __( 'Adds Schema JSON-LD markup for Posts, Pages, Media, and Custom Post Types with a matching Schema item type.', 'wpsso-schema-json-ld' );
				elseif ( strpos( $key, 'Property ' ) === 0 )
					$features[$key]['tooltip'] = __( 'Adds Schema JSON-LD markup for matching item type properties.', 'wpsso-schema-json-ld' );
			}
			return $features;
		}
	}
}

?>
