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
			), 5 );
		}

		public function filter_add_schema_meta_array( $bool ) {
			return false;
		}

		public function filter_data_http_schema_org_item_type( $data, $use_post, $obj, $mt_og, $post_id, $author_id, $head_type ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];

			if ( ! apply_filters( $lca.'_add_schema_item_type_json', true ) ) {
				if ( $this->p->debug->enabled )
					$this->p->debug->log( 'exiting early: item type schema data disabled' );
				return $data;
			}

			$data = WpssoSchema::get_item_type_context( $head_type );
			$og_type = $mt_og['og:type'];	// used to get product:rating:* values

			if ( ! empty( $mt_og['og:url'] ) )
				$data['url'] = $mt_og['og:url'];

			if ( ! empty( $mt_og['og:title'] ) )
				$data['name'] = $mt_og['og:title'];

			$data['description'] = $this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
				'...', $use_post, true, true, true, 'schema_desc' );	// custom meta = schema_desc

			switch ( $head_type ) {
				case 'http://schema.org/Blog':
				case 'http://schema.org/WebPage':

					if ( ! empty( $mt_og['article:published_time'] ) )
						$data['datepublished'] = $mt_og['article:published_time'];

					if ( ! empty( $mt_og['article:modified_time'] ) )
						$data['datemodified'] = $mt_og['article:modified_time'];

					WpssoSchema::add_single_person_data( $data, '', $author_id );

					if ( isset( $mt_og['og:image'] ) && 
						is_array( $mt_og['og:image'] ) )
							WpssoSchema::add_image_list_data( $data, 'image', $mt_og['og:image'], 'og:image' );
					break;
			}

			if ( ! empty( $mt_og[$og_type.':rating:average'] ) &&
				( ! empty( $mt_og[$og_type.':rating:count'] ) || 
					! empty( $mt_og[$og_type.':review:count'] ) ) ) {

				$data['aggregateRating'] = array(
					'@context' => 'http://schema.org',
					'@type' => 'AggregateRating',
				);

				foreach ( array(
					'ratingvalue' => 'rating:average',
					'ratingcount' => 'rating:count',
					'worstrating' => 'rating:worst',
					'bestrating' => 'rating:best',
					'reviewcount' => 'review:count',
				) as $ar_key => $og_key )
					if ( isset( $mt_og[$og_type.':'.$og_key] ) )
						$data['aggregateRating'][$ar_key] = $mt_og[$og_type.':'.$og_key];
			}

			return $data;
		}
	}
}

?>
