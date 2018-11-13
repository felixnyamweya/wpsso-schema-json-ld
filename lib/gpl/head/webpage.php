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

		private $p;

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

		public function filter_json_data_https_schema_org_webpage( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			if ( $this->p->schema->is_schema_type_child( $page_type_id, 'article' ) ) {
				$org_logo_key = 'org_banner_url';
				$size_name    = $this->p->lca . '-schema-article';
			} else {
				$org_logo_key = 'org_logo_url';
				$size_name    = $this->p->lca . '-schema';
			}

			/**
			 * Property:
			 * 	headline
			 */
			$headline_max_len  = $this->p->cf['head']['limit_max']['schema_headline_len'];

			$ret[ 'headline' ] = $this->p->page->get_title( $headline_max_len, '...', $mod );

			/**
			 * Property:
			 *      text
			 */
			$ret[ 'text' ] = $this->p->page->get_the_text( $mod, $read_cache = true, $md_key = 'schema_text' );

			if ( empty( $ret[ 'text' ] ) ) { // Just in case.
				unset( $ret[ 'text' ] );
			}

			/**
			 * Property:
			 *	inLanguage
			 *      copyrightYear
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated
				 * to add more than one value to a property array.
				 */
				foreach ( array(
					'schema_lang'            => 'inLanguage',
					'schema_family_friendly' => 'isFamilyFriendly',
					'schema_copyright_year'  => 'copyrightYear',
				) as $md_key => $prop_name ) {

					$md_val = $mod[ 'obj' ]->get_options( $mod[ 'id' ], $md_key, $filter_opts = true, $def_fallback = true );
	
					if ( $md_val === null || $md_val === '' || $md_val === 'none' ) {
						continue;
					}

					switch ( $prop_name ) {

						case 'isFamilyFriendly':	// Must be a true or false boolean value.
	
							$md_val = empty( $md_val ) ? false : true;

							break;
					}

					$ret[ $prop_name ] = $md_val;
				}
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
			 *      provider
			 *      publisher
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated
				 * to add more than one value to a property array.
				 */
				foreach ( array(
					'schema_pub_org_id'  => 'publisher',
					'schema_prov_org_id' => 'provider',
				) as $md_key => $prop_name ) {
	
					$md_val = $mod[ 'obj' ]->get_options( $mod[ 'id' ], $md_key, $filter_opts = true, $def_fallback = true );
	
					if ( $md_val === null || $md_val === '' || $md_val === 'none' ) {
						continue;
					}
	
					WpssoSchema::add_single_organization_data( $ret[ $prop_name ], $mod, $md_val, $org_logo_key, $list_element = false );
		
					if ( empty( $ret[ $prop_name ] ) ) {	// Just in case.
						unset( $ret[ $prop_name ] );
					}
				}
			}

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

							$notice_key = $mod[ 'name' ] . '-' . $mod[ 'id' ] . '-notice-missing-schema-' . $prop_name;
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
