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
					'json_data_https_schema_org_webpage'     => 5,
					'json_data_https_schema_org_blogposting' => 5,
				),
			) );
		}

		public function filter_json_data_https_schema_org_webpage( $json_data, $mod, $mt_og, $type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			/**
			 * The blogposting type is a sub-type of article. Use the article image size and add the headline property.
			 */
			if ( $this->p->schema->is_schema_type_child( $type_id, 'article' ) ) {

				$org_logo_key    = 'org_banner_url';                       // Use a banner for all article sub-types.
				$size_name       = $this->p->lca . '-schema-article';      // Same size, but minimum width is 696px.
				$title_max_len   = $this->p->cf['head']['limit_max']['schema_article_headline_len'];
				$ret['headline'] = $this->p->page->get_title( $title_max_len, '...', $mod );

			} else {
				$org_logo_key = 'org_logo_url';
				$size_name    = $this->p->lca . '-schema';
			}

			/**
			 * Property:
			 *      text
			 */
			$ret['text'] = $this->p->page->get_the_text( $mod );

			if ( empty( $ret['text'] ) ) { // Just in case.
				unset( $ret['text'] );
			}

			/**
			 * Property:
			 *      inLanguage - only valid for CreativeWork and Event
			 */
			$ret['inLanguage'] = SucomUtil::get_locale( $mod );

			if ( empty( $ret['inLanguage'] ) ) { // Just in case.
				unset( $ret['inLanguage'] );
			}

			/**
			 * Property:
			 *      datePublished
			 *      dateModified
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
				'datePublished' => 'article:published_time',
				'dateModified'  => 'article:modified_time',
			) );

			/**
			 * Property:
			 *      publisher as https://schema.org/Organization
			 */
			if ( ! empty( $mod['obj'] ) ) {

				/**
				 * The get_options() method returns null if an index key is not found.
				 * Return values are null, 'none', 'site', or number (including 0).
				 */
				$org_id = $mod['obj']->get_options( $mod['id'], 'schema_pub_org_id', $filter_opts = true );

			} else {
				$org_id = null;
			}

			if ( null === $org_id ) {
				$org_id = empty( $this->p->options[ 'schema_def_pub_org_id' ] ) ? 'site' : $this->p->options[ 'schema_def_pub_org_id' ];
			}

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'publisher / organization id is ' . $org_id );
			}

			/**
			 * $org_id can be 'none', 'site', or a number (including 0).
			 * $logo_key can be 'org_logo_url' or 'org_banner_url' (600x60px image) for Articles.
			 * do not provide localized option names - the method will fetch the localized values.
			 */
			WpssoSchema::add_single_organization_data( $ret['publisher'], $mod, $org_id, $org_logo_key, false ); // $list_element = false.

			/**
			 * Property:
			 *      author as https://schema.org/Person
			 *      contributor as https://schema.org/Person
			 */
			WpssoSchema::add_author_coauthor_data( $ret, $mod );

			/**
			 * Property:
			 *      image as https://schema.org/ImageObject
			 *      video as https://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $size_name );

			/**
			 * Check only published posts or other non-post objects.
			 */
			if ( 'publish' === $mod['post_status'] || ! $mod['is_post'] ) {

				foreach ( array( 'image' ) as $prop_name ) {

					if ( empty( $ret[ $prop_name ] ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'creativework ' . $prop_name . ' value is empty and required' );
						}

						if ( $this->p->notice->is_admin_pre_notices() ) { // Skip if notices already shown.

							$notice_key = $mod['name'] . '-' . $mod['id'] . '-notice-missing-schema-' . $prop_name;
							$error_msg  = $this->p->msgs->get( 'notice-missing-schema-' . $prop_name );

							$this->p->notice->err( $error_msg, null, $notice_key );
						}
					}
				}
			}

			/**
			 * Property:
			 *      commentCount
			 *      comment as https://schema.org/Comment
			 */
			WpssoJsonSchema::add_comment_list_data( $ret, $mod );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
