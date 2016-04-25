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

			add_filter( 'amp_post_template_metadata', 
				array( &$this, 'filter_amp_post_template_metadata' ), 9000, 2 );

			$this->p->util->add_plugin_filters( $this, array(
				'add_schema_head_attributes' => '__return_false',
				'add_schema_meta_array' => '__return_false',
				'add_schema_noscript_array' => '__return_false',
				'json_data_http_schema_org' => 7,			// $json_data, $use_post, $mod, $mt_og, $user_id, $head_type, $is_main
				'json_data_http_schema_org_webpage' => array( 
					'json_data_http_schema_org_webpage' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
					'json_data_http_schema_org_blogposting' => 5,	// $json_data, $use_post, $mod, $mt_og, $user_id
				),
			), -100 );	// make sure we run first

			if ( is_admin() ) {
				$this->p->util->add_plugin_actions( $this, array(
					'admin_post_header' => 1,			// $mod
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'get_md_defaults' => 2,				// $def_opts, $mod
					'pub_google_rows' => 2,				// $table_rows, $form
				) );
				$this->p->util->add_plugin_filters( $this, array(
					'status_gpl_features' => 3,			// $features, $lca, $info
					'status_pro_features' => 3,			// $features, $lca, $info
				), 10, 'wpssojson' );
			}
		}

		public function filter_amp_post_template_metadata( $metadata, $post_obj ) {
			return array();	// remove the AMP json data to prevent duplicate JSON-LD blocks
		}

		/*
		 * Common filter for all Schema types.
		 *
		 * Adds the url, name, description, and if true, the main entity property. 
		 * Does not add images, videos, author or organization markup since this will
		 * depend on the Schema type (Article, Product, Place, etc.).
		 */
		public function filter_json_data_http_schema_org( $json_data, $use_post, $mod, $mt_og, $user_id, $head_type, $is_main ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$lca = $this->p->cf['lca'];
			$ret = WpssoSchema::get_item_type_context( $head_type );

			/*
			 * Property:
			 *	url
			 */
			WpssoSchema::add_data_prop_from_og( $ret, $mt_og, array( 'url' => 'og:url' ) );

			/*
			 * Property:
			 *	name
			 *
			 * get_title( $textlen = 70, $trailing = '', $use_post = false, $use_cache = true,
			 *	$add_hashtags = false, $encode = true, $md_idx = 'og_title' ) {
			 */
			$ret['name'] = $this->p->webpage->get_title( $this->p->options['og_title_len'], 
				'...', $mod, true, false, true, 'schema_title' );

			/*
			 * Property:
			 *	description
			 *
			 * get_description( $textlen = 156, $trailing = '...', $use_post = false, $use_cache = true,
			 *	$add_hashtags = true, $encode = true, $md_idx = 'og_desc' )
			 */
			$ret['description'] = $this->p->webpage->get_description( $this->p->options['schema_desc_len'], 
				'...', $mod, true, false, true, 'schema_desc' );

			/*
			 * Property:
			 *	mainEntityOfPage as http://schema.org/WebPage
			 */
			if ( $is_main )
				WpssoSchema::add_main_entity_data( $ret, $ret['url'] );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}

		/*
		 * Common filter for WebPage and the BlogPosting Schema types.
		 * 
		 * Adds the date published, date modified, author, and image properties.
		 */
		public function filter_json_data_http_schema_org_webpage( $json_data, $use_post, $mod, $mt_og, $user_id ) {
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();

			$ret = array();
			$lca = $this->p->cf['lca'];

			/*
			 * Property:
			 * 	datepublished
			 * 	datemodified
			 */
			WpssoSchema::add_data_prop_from_og( $ret, $mt_og, array(
				'datepublished' => 'article:published_time',
				'datemodified' => 'article:modified_time',
			) );

			/*
			 * Property:
			 *	inLanguage
			 */
			$ret['inLanguage'] = get_locale();

			/*
			 * Property:
			 *	publisher as http://schema.org/Organization
			 */
			WpssoSchema::add_single_organization_data( $ret['publisher'], $mod, 'schema_logo_url', false );	// $list_element = false

			/*
			 * Property:
			 *	author as http://schema.org/Person
			 */
			if ( $user_id > 0 )
				WpssoSchema::add_single_person_data( $ret['author'], $user_id, true );

			/*
			 * Property:
			 *	image as http://schema.org/ImageObject
			 *	video as http://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $use_post, $mod, $mt_og, $user_id );

			return WpssoSchema::return_data_from_filter( $json_data, $ret );
		}

		public function action_admin_post_header( $mod ) {
			if ( current_user_can( 'manage_options' ) ) {
				$type_id = $this->p->schema->get_head_item_type( $mod, true );
				$type_url = $this->p->schema->get_item_type_value( $type_id );
				$has_filter = $this->p->schema->has_json_data_filter( $mod, $type_url );

				if ( ! $has_filter ) {
					$filter_name = $this->p->schema->get_json_data_filter( $mod, $type_url );
					$urls = $this->p->cf['plugin']['wpssojson']['url'];

					// the period in the $type_id matches the pound sign in the lib name as well ;-)
					$head_lib_count = count( SucomUtil::preg_grep_keys( '/^'.$type_id.'(:.*)?$/',
						$this->p->cf['plugin']['wpssojson']['lib']['pro']['head'] ) );

					if ( $this->p->check->aop( 'wpssojson', true, $this->p->is_avail['aop'] ) ) {
						$msg_id = 'no_filter_pro_'.$filter_name;
						$msg_txt = sprintf( __( 'WPSSO JSON Pro does not include specific / customized support for the Schema type <a href="%1$s">%1$s</a> &mdash; only the Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $type_url ).' '.sprintf( __( 'Developers may wish to hook the \'%1$s\' filter to further customize the default JSON-LD data array, and include additional properties.', 'wpsso-schema-json-ld' ), $filter_name ).' '.sprintf( __( 'You are also invited to <a href="%1$s">request the addition of this Schema type</a> for a future release of WPSSO JSON Pro. ;-)', 'wpsso-schema-json-ld' ), $urls['pro_support'], $type_url );
					} elseif ( $head_lib_count > 0 ) {
						$msg_id = 'filter_in_pro_'.$filter_name;
						$msg_txt = sprintf( __( 'The Free / Basic version of WPSSO JSON does not include support for the Schema type <a href="%1$s">%1$s</a> &mdash; only the basic Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $type_url ).' '.sprintf( 'The Pro version of WPSSO JSON provides an evolving list of supported Schema types, including the Schema type <a href="%1$s">%1$s</a>.', $type_url ).' '.sprintf( __( 'You may consider <a href="%1$s">purchasing the Pro version of WPSSO JSON</a> if the Schema type <a href="%2$s">%2$s</a> is an important classification for your content.', 'wpsso-schema-json-ld' ), $urls['purchase'], $type_url );
					} else {
						$msg_id = 'no_filter_gpl_'.$filter_name;
						$msg_txt = sprintf( __( 'The Free / Basic version of WPSSO JSON does not include support for the Schema type <a href="%1$s">%1$s</a> &mdash; only the basic Schema properties <em>url</em>, <em>name</em>, and <em>description</em> will be included in the Schema JSON-LD markup.', 'wpsso-schema-json-ld' ), $type_url ).' '.sprintf( 'The <a href="%1$s">Pro version of WPSSO JSON</a> provides an evolving list of supported Schema types &mdash; you may want to check if Schema type <a href="%2$s">%2$s</a> is available with the Pro version. If it isn\'t supported yet, you are invited to <a href="%3$s">request its addition</a>. ;-)', $urls['purchase'], $type_url, $urls['pro_support'] );
					}
					$this->p->notice->err( '<em>'.__( 'This notice is only shown to users with Administrative privileges.', 'wpsso-schema-json-ld' ).'</em><p>'.$msg_txt.'</p>', true, true, $msg_id, true );
				}
			}
		}

		public function filter_get_md_defaults( $def_opts, $mod ) {
			return array_merge( $def_opts, array(
				'schema_is_main' => 1,
				'schema_type' => $this->p->schema->get_head_item_type( $mod, true, false ),	// $return_id = true, $use_mod_opts = false
				'schema_title' => '',
				'schema_headline' => '',
				'schema_desc' => '',
			) );
		}

		public function filter_pub_google_rows( $table_rows, $form ) {
			foreach ( array_keys( $table_rows ) as $key ) {
				switch ( $key ) {
					case 'schema_add_noscript':
					case 'schema_social_json':
						break;
					case 'subsection_google_schema':
					case ( strpos( $key, 'schema_' ) === 0 ? true : false ):
						unset( $table_rows[$key] );
						break;
				}
			}
			return $table_rows;
		}

		// hooked to 'wpssojson_status_gpl_features'
		public function filter_status_gpl_features( $features, $lca, $info ) {
			foreach ( array( 
				'Type BlogPosting',
				'Type WebPage',
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
				if ( strpos( $key, 'Type ' ) === 0 )
					$features[$key]['tooltip'] = __( 'Adds Schema JSON-LD markup for Posts, Pages, Media, and Custom Post Types with a matching Schema item type.', 'wpsso-schema-json-ld' );
				elseif ( strpos( $key, 'Property ' ) === 0 )
					$features[$key]['tooltip'] = __( 'Adds Schema JSON-LD markup for matching item type properties.', 'wpsso-schema-json-ld' );
			}
			return $features;
		}
	}
}

?>
