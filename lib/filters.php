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
				'json_data_http_schema_org_webpage' => array( 		// method name to call
					'json_data_http_schema_org_webpage' => 6,	// filter name to hook
					'json_data_http_schema_org_blogposting' => 6,
				),
			), -100 );	// make sure we run first

			if ( is_admin() ) {
				$this->p->util->add_plugin_actions( $this, array(
					'admin_post_header' => 3,
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'get_meta_defaults' => 2,
					'pub_google_rows' => 2,
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 3,
					'status_pro_features' => 3,
				), 10, 'wpssojson' );
			}
		}

		/*
		 * Common filter for all Schema types.
		 *
		 * Adds the url, name, description, and if true, the main entity property. 
		 */
		public function filter_json_data_http_schema_org_item_type( $json_data, 
			$use_post, $post_obj, $mt_og, $post_id, $author_id, $head_type, $is_main ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$ret = WpssoSchema::get_item_type_context( $head_type );

			WpssoSchema::add_data_prop_from_og( $ret, $mt_og, array( 'url' => 'og:url' ) );

			// get_title( $textlen = 70, $trailing = '', $use_post = false, $use_cache = true,
			//	$add_hashtags = false, $encode = true, $md_idx = 'og_title', $src_id = '' ) {
			$ret['name'] = $this->p->webpage->get_title( $this->p->options['og_title_len'], 
				'...', $use_post, true, false, true, 'schema_title' );

			// get_description( $textlen = 156, $trailing = '...', $use_post = false, $use_cache = true,
			//	$add_hashtags = true, $encode = true, $md_idx = 'og_desc', $src_id = '' )
			$ret['description'] = $this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
				'...', $use_post, true, false, true, 'schema_desc' );

			if ( $is_main )
				WpssoSchema::add_main_entity_data( $ret, $ret['url'] );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}

		/*
		 * Common filter for WebPage and BlogPosting Schema types.
		 * 
		 * Adds the date published, date modified, author, and image properties.
		 */
		public function filter_json_data_http_schema_org_webpage( $json_data, 
			$use_post, $post_obj, $mt_og, $post_id, $author_id ) {

			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();
			$lca = $this->p->cf['lca'];

			WpssoSchema::add_data_prop_from_og( $ret, $mt_og, array(
				'datepublished' => 'article:published_time',
				'datemodified' => 'article:modified_time',
			) );

			WpssoJsonSchema::add_author_media_data( $ret, $use_post, $post_id, $author_id );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}

		public static function add_author_media_data( &$json_data, $use_post, $post_id, $author_id ) {

			$wpsso = Wpsso::get_instance();

			if ( $author_id > 0 )
				WpssoSchema::add_single_person_data( $json_data['author'], $author_id, true );	// list_element = true

			$size_name = $wpsso->cf['lca'].'-schema';
			$og_image = $wpsso->og->get_all_images( 1, $size_name, $post_id, true, 'schema' );

			if ( empty( $og_image ) && 
				SucomUtil::is_post_page( $use_post ) )
					$og_image = $wpsso->media->get_default_image( 1, $size_name, true );

			WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );
		}

		public function action_admin_post_header( $post_id, $ptn, $post_obj ) {
			if ( current_user_can( 'manage_options' ) ) {
				$item_type = $this->p->schema->get_head_item_type( $post_id, $post_obj );

				if ( ! $this->p->schema->has_json_data_filter( $item_type ) ) {
					$filter_name = $this->p->schema->get_json_data_filter( $item_type );
					$msg_id = 'no_filter_for_'.$filter_name;

					$this->p->notice->err( '<em>'.__( 'This notice is only shown to users with Administrative privileges.', 'wpsso-schema-json-ld' ).'</em><br/><br/>'.sprintf( __( 'WPSSO JSON does not include specific / customized support for the Schema type <a href="%1$s">%1$s</a> &mdash; the Schema properties URL, Name, and Description will be added by default.', 'wpsso-schema-json-ld' ), $item_type ).' '.sprintf( __( 'Developers may hook the \'%1$s\' filter to further customize the default JSON-LD data array.', 'wpsso-schema-json-ld' ), $filter_name ), true, true, $msg_id, true );
				}
			}
		}

		public function filter_get_meta_defaults( $def_opts, $mod_name ) {
			$def_opts = array_merge( $def_opts, array(
				'schema_is_main' => 1,
				'schema_type' => $this->p->schema->get_head_item_type( false, false, true, false ),	// $ret_key = true, $use_mod = false
				'schema_title' => '',
				'schema_headline' => '',
			) );
			return $def_opts;
		}

		public function filter_pub_google_rows( $rows, $form ) {
			unset ( $rows['schema_add_noscript'] );
			return $rows;
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
