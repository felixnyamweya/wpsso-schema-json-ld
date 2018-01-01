<?php
/**
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE WPSSO SCHEMA JSON-LD
 * MARKUP (WPSSO JSON) PRO APPLICATION, YOU AGREE TO BE BOUND BY THE TERMS OF
 * ITS LICENSE AGREEMENT.
 * 
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: https://wpsso.com/wp-content/plugins/wpsso-schema-json-ld/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT, PLEASE DO NOT
 * INSTALL, RUN, COPY, OR OTHERWISE USE THE WPSSO SCHEMA JSON-LD MARKUP (WPSSO
 * JSON) PRO APPLICATION.
 * 
 * Copyright 2016-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonGplHeadWebPage' ) ) {

	class WpssoJsonGplHeadWebPage {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_webpage' => array(
					'json_data_https_schema_org_webpage' => 5,	// $json_data, $mod, $mt_og, $type_id, $is_main
					'json_data_https_schema_org_blogposting' => 5,	// $json_data, $mod, $mt_og, $type_id, $is_main
				),
			) );
		}

		public function filter_json_data_https_schema_org_webpage( $json_data, $mod, $mt_og, $type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();
			$lca = $this->p->cf['lca'];

			// blogposting is a child of article
			// use the article image size and add the headline property
			if ( $this->p->schema->is_schema_type_child( $type_id, 'article' ) ) {

				$org_logo_key = 'org_banner_url';		// use a banner for all article sub-types
				$size_name = $lca.'-schema-article';		// same size, but minimum width is 696px

				/**
				 * Property:
				 * 	headline
				 */
				$headline_max_len = $this->p->cf['head']['limit_max']['schema_article_headline_len'];
				$ret['headline'] = $this->p->page->get_title( $headline_max_len, '...', $mod );

			} else {
				$org_logo_key = 'org_logo_url';
				$size_name = $this->p->cf['lca'].'-schema';
			}

			/**
			 * Property:
			 * 	datePublished
			 * 	dateModified
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
				'datePublished' => 'article:published_time',
				'dateModified' => 'article:modified_time',
			) );

			/**
			 * Property:
			 *	inLanguage
			 */
			$ret['inLanguage'] = SucomUtil::get_locale( $mod );

			/**
			 * Property:
			 *	publisher as https://schema.org/Organization
			 */
			if ( ! empty( $mod['obj'] ) ) {
				// get_options() returns null if an index key is not found
				$org_id = $mod['obj']->get_options( $mod['id'], 'schema_pub_org_id' );	// null, 'none', 'site', or number (including 0)
				if ( null === $org_id ) {
					$org_id = 'site';	// default
				}
			} else {
				$org_id = 'site';	// default
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'publisher / organization id is '.$org_id );
			}

		 	/**
			 * $org_id can be 'none', 'site', or a number (including 0).
		 	 * $logo_key can be 'org_logo_url' or 'org_banner_url' (600x60px image) for Articles.
			 * do not provide localized option names - the method will fetch the localized values.
			 */
			WpssoSchema::add_single_organization_data( $ret['publisher'], $mod, $org_id, $org_logo_key, false );	// $list_element = false

			/**
			 * Property:
			 *	author as https://schema.org/Person
			 *	contributor as https://schema.org/Person
			 */
			WpssoSchema::add_author_coauthor_data( $ret, $mod );

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 *	video as https://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $size_name );

			// check only published posts or other non-post objects
			if ( $mod['post_status'] === 'publish' || ! $mod['is_post'] ) {
				foreach ( array( 'image' ) as $prop_name ) {
					if ( empty( $ret[$prop_name] ) ) {
						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'creativework '.$prop_name.' value is empty and required' );
						}
						if ( $this->p->notice->is_admin_pre_notices() ) {	// skip if notices already shown
							$this->p->notice->err( $this->p->msgs->get( 'notice-missing-schema-'.$prop_name ) );
						}
					}
				}
			}

			/**
			 * Property:
			 *	commentCount
			 *	comment as https://schema.org/Comment
			 */
			WpssoJsonSchema::add_comment_list_data( $ret, $mod );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}

