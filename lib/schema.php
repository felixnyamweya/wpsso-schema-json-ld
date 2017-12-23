<?php

/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonSchema' ) ) {

	class WpssoJsonSchema {

		private $p;
		private static $cache_exp_secs = null;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
		}

		/*
		 * Called by Blog, CollectionPage, ProfilePage, and SearchResultsPage.
		 *
		 * Examples:
		 *
		 *	$prop_name_type_ids = array( 'mentions' => false )
		 *	$prop_name_type_ids = array( 'blogPosting' => 'blog.posting' )
		 */
		public static function add_posts_data( array &$json_data, array $mod, array $mt_og, $page_type_id, $is_main,
			array $prop_name_type_ids, $posts_per_page = false ) {

			static $added_page_type_ids = array();
			static $posts_per_page_max = null;

			$wpsso =& Wpsso::get_instance();
			$posts_count = 0;

			/*
			 * Sanity check - must have at least $page_type_id and $prop_name_type_ids.
			 */
			if ( empty( $page_type_id ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: page_type_id is empty' );
				}
				return $posts_count;
			} elseif ( empty( $prop_name_type_ids ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: prop_name_types is empty' );
				}
				return $posts_count;
			}

			/*
			 * Prevent recursion - i.e. webpage.collection in webpage.collection, etc.
			 */
			if ( isset( $added_page_type_ids[$page_type_id] ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: preventing recursion of page_type_id '.$page_type_id );
				}
				return $posts_count;
			} else {
				$added_page_type_ids[$page_type_id] = true;
			}

			/*
			 * Begin timer.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding posts data' );	// begin timer
			}

			/*
			 * Set the page number and the posts per page values.
			 */
			if ( ! isset( $posts_per_page_max ) ) {	// only set the value once
				$posts_per_page_max = SucomUtil::get_const( 'WPSSO_SCHEMA_POSTS_PER_PAGE_MAX', 10 );
			}

			global $wpsso_paged;
			$wpsso_paged = 1;
			$posts_mods = array();

			if ( $posts_per_page === false ) {	// get the default if no argument provided
				$posts_per_page = get_option( 'posts_per_page' );
			}

			$posts_per_page = (int) apply_filters( $wpsso->lca.'_posts_per_page', $posts_per_page, $mod );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'posts_per_page after filter is '.$posts_per_page );
			}

			if ( $posts_per_page > $posts_per_page_max ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'setting posts_per_page '.$posts_per_page.' to maximum of '.$posts_per_page_max );
				}
				$posts_per_page = $posts_per_page_max;
			}

			/*
			 * Get the mod array for all posts.
			 */
			if ( $is_main && ( $mod['is_home_index'] || ! is_object( $mod['obj'] ) ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'using query loop to get posts mods' );
				}
				$post_count = 0;
				if ( have_posts() ) {
					while ( have_posts() ) {
						$post_count++;
						the_post();
						global $post;
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'getting mod for post id '.$post->ID );
						}
						$posts_mods[] = $wpsso->m['util']['post']->get_mod( $post->ID );
						if ( $post_count >= $posts_per_page ) {
							break;	// stop here
						}
					}
					rewind_posts();
				}
			} elseif ( is_object( $mod['obj'] ) && method_exists( $mod['obj'], 'get_posts_mods' ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'using module object to get posts mods' );
				}
				$posts_mods = $mod['obj']->get_posts_mods( $mod, $posts_per_page, $wpsso_paged );
			} else {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: no source to get posts mods' );
					$wpsso->debug->mark( 'adding posts data' );	// end timer
				}
				unset( $wpsso_paged );	// unset the forced page number
				return $posts_count;
			}

			if ( empty( $posts_mods ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: posts_mods array is empty' );
					$wpsso->debug->mark( 'adding posts data' );	// end timer
				}
				unset( $wpsso_paged );	// unset the forced page number
				return $posts_count;
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'posts_mods array has '.count( $posts_mods ).' elements' );
			}

			/*
			 * Set the Schema properties.
			 */
			foreach ( $prop_name_type_ids as $prop_name => $prop_type_ids ) {

				if ( empty( $prop_type_ids ) ) {		// false or empty array - allow any schema type
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'any schema type is allowed for prop_name '.$prop_name );
					}
					$prop_type_ids = array( 'any' );

				} elseif ( is_string( $prop_type_ids ) ) {	// convert value to an array
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'only schema type '.$prop_type_ids.' allowed for prop_name '.$prop_name );
					}
					$prop_type_ids = array( $prop_type_ids );

				} elseif ( ! is_array( $prop_type_ids ) ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'skipping prop_name '.$prop_name.': value must be false, string, or array of schema types' );
					}
					continue;
				}

				if ( empty( $json_data[$prop_name] ) ) {
					$json_data[$prop_name] = array();
				} elseif ( ! is_array( $json_data[$prop_name] ) ) {	// convert single value to an array
					$json_data[$prop_name] = array( $json_data[$prop_name] );
				}

				$prop_name_count = count( $json_data[$prop_name] );	// initialize the posts counter

				foreach ( $posts_mods as $post_mod ) {

					$add_post_data = false;

					foreach ( $prop_type_ids as $family_member_id ) {

						if ( $family_member_id === 'any' ) {
							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'accepting post id '.$post_mod['id'].': any schema type is allowed' );
							}
							$add_post_data = true;
							break;	// stop here
						}

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'getting schema type for post id '.$post_mod['id'] );
						}

						$mod_type_id = $wpsso->schema->get_mod_schema_type( $post_mod, true );	// $get_id = true

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'checking if schema type '.$mod_type_id.' is child of '.$family_member_id );
						}

						$mod_is_child = $wpsso->schema->is_schema_type_child( $mod_type_id, $family_member_id );

						if ( $mod_is_child ) {

							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'accepting post id '.$post_mod['id'].': '.$mod_type_id.' is child of '.$family_member_id );
							}
							$add_post_data = true;
							break;	// stop here

						} elseif ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'post id '.$post_mod['id'].' schema type '.$mod_type_id.' not a child of '.$family_member_id );
						}
					}

					if ( $add_post_data ) {

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'getting single post data for post id '.$post_mod['id'] );
						}

						$post_data = self::get_single_post_data( $post_mod, false, $page_type_id );	// $mt_og = false

						if ( empty( $post_data ) ) {	// prevent null assignment
							$wpsso->debug->log( 'single post data for post id '.$post_mod['id'].' is empty' );
							continue;	// get the next post mod
						}

						$posts_count++;
						$prop_name_count++;

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'adding post id '.$post_mod['id'].' to '.$prop_name.' as array element #'.$prop_name_count );
						}

						$json_data[$prop_name][] = $post_data;	// add the post data

						if ( $prop_name_count >= $posts_per_page ) {
							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'stopping here: maximum posts per page of '.$posts_per_page.' reached' );
							}
							break;	// stop here
						}
					} elseif ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'skipping post id '.$post_mod['id'].' for prop_name '.$prop_name );
					}
				}

				$filter_name = SucomUtil::sanitize_hookname( $wpsso->lca.'_json_prop_https_schema_org_'.$prop_name );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'applying filter '.$filter_name );
				}

				$json_data[$prop_name] = (array) apply_filters( $filter_name, $json_data[$prop_name], $mod, $mt_og, $page_type_id, $is_main );

				if ( empty( $json_data[$prop_name] ) ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'json data prop_name '.$prop_name.' is empty' );
					}
					unset( $json_data[$prop_name] );
				}
			}

			unset( $wpsso_paged );

			/*
			 * End timer.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding posts data' );	// end timer
			}

			return $posts_count;
		}

		public static function add_media_data( &$json_data, $mod, $mt_og, $size_name = null, $add_video = true ) {

			$wpsso =& Wpsso::get_instance();
			
			/*
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			$og_images = array();
			$prev_count = 0;
			$max = $wpsso->util->get_max_nums( $mod, 'schema' );

			if ( empty( $size_name ) ) {
				$size_name = $wpsso->lca.'-schema';
			}

			/*
			 * Include video preview images first.
			 */
			if ( ! empty( $mt_og['og:video'] ) && is_array( $mt_og['og:video'] ) ) {
				// prevent duplicates - exclude text/html videos
				foreach ( $mt_og['og:video'] as $num => $og_video ) {
					if ( isset( $og_video['og:video:type'] ) && $og_video['og:video:type'] !== 'text/html' ) {
						if ( SucomUtil::get_mt_media_url( $og_video, 'og:image' ) ) {
							$prev_count++;
						}
						$og_images[] = SucomUtil::preg_grep_keys( '/^og:image/', $og_video );
					}
				}
				if ( $prev_count > 0 ) {
					$max['schema_img_max'] -= $prev_count;
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( $prev_count.
							' video preview images found (og_img_max adjusted to '.
								$max['schema_img_max'].')' );
					}
				}
			}

			$og_images = array_merge( $og_images, $wpsso->og->get_all_images( $max['schema_img_max'],
				$size_name, $mod, true, 'schema' ) );

			if ( ! empty( $og_images ) ) {
				$images_added = WpssoSchema::add_og_image_list_data( $json_data['image'], $og_images, 'og:image' );
			} else {
				$images_added = 0;
			}

			if ( ! $images_added && $mod['is_post'] ) {
				$og_images = $wpsso->media->get_default_images( 1, $size_name, true );
				$images_added = WpssoSchema::add_og_image_list_data( $json_data['image'], $og_images, 'og:image' );
			}

			if ( ! $images_added ) {
				unset( $json_data['image'] );	// prevent null assignment
			}

			/*
			 * Property:
			 *	video as https://schema.org/VideoObject
			 *
			 * Allow the video property to be skipped -- some schema types (organization, 
			 * for example) do not include a video property.
			 */
			if ( $add_video && ! empty( $mt_og['og:video'] ) ) {
				WpssoJsonSchema::add_video_list_data( $json_data['video'], $mt_og['og:video'], 'og:video' );
			}
		}

		public static function add_comment_list_data( &$json_data, $mod ) {

			if ( ! $mod['is_post'] || ! $mod['id'] || ! comments_open( $mod['id'] ) ) {
				return;
			}

			$json_data['commentCount'] = get_comments_number( $mod['id'] );

			/*
			 * Only get parent comments. The add_single_comment_data() method 
			 * will recurse and add the children.
			 */
			$comments = get_comments( array(
				'post_id' => $mod['id'],
				'status' => 'approve',
				'parent' => 0,	// don't get replies
				'order' => 'DESC',
				'number' => get_option( 'page_comments' ),	// limit number of comments
			) );

			if ( is_array( $comments ) ) {
				foreach( $comments as $num => $cmt ) {
					$comments_added = self::add_single_comment_data( $json_data['comment'], $mod, $cmt->comment_ID );
					if ( ! $comments_added ) {
						unset( $json_data['comment'] );
					}
				}
			}
		}

		public static function add_single_comment_data( &$json_data, $mod, $comment_id, $list_element = true ) {
			$comments_added = 0;

			if ( $comment_id && $cmt = get_comment( $comment_id ) ) {

				$comments_added++;

				// if not adding a list element, inherit the existing schema type url (if one exists)
				if ( ! $list_element && ( $comment_type_url = WpssoSchema::get_data_type_url( $json_data ) ) !== false ) {
					if ( $ngfb->debug->enabled ) {
						$ngfb->debug->log( 'using inherited schema type url = '.$comment_type_url );
					}
				} else {
					$comment_type_url = 'https://schema.org/Comment';
				}

				$ret = WpssoSchema::get_schema_type_context( $comment_type_url, array(
					'url' => get_comment_link( $cmt->comment_ID ),
					'dateCreated' => mysql2date( 'c', $cmt->comment_date_gmt ),
					'description' => get_comment_excerpt( $cmt->comment_ID ),
					'author' => WpssoSchema::get_schema_type_context( 'https://schema.org/Person', array(
						'name' => $cmt->comment_author,
					) ),
				) );

				$children = get_comments( array(
					'post_id' => $mod['id'],
					'status' => 'approve',
					'parent' => $cmt->comment_ID,	// get the children
					'order' => 'DESC',
					'number' => get_option( 'page_comments' ),	// limit number of comments
				) );

				if ( is_array( $children ) ) {
					foreach( $children as $num => $child ) {
						$children_added = self::add_single_comment_data( $ret['comment'], $mod, $child->comment_ID );
						if ( ! $children_added ) {
							unset( $ret['comment'] );
						} else {
							$comments_added += $children_added;
						}
					}
				}

				if ( empty( $list_element ) ) {
					$json_data = $ret;
				} else {
					$json_data[] = $ret;	// add an item to the list
				}
			}

			return $comments_added;	// return count of comments added
		}

		/*
		 * Provide a single or two-dimension video array in $og_video.
		 */
		public static function add_video_list_data( &$json_data, $og_video, $prefix = 'og:video' ) {
			$videos_added = 0;

			if ( isset( $og_video[0] ) && is_array( $og_video[0] ) ) {						// 2 dimensional array
				foreach ( $og_video as $video ) {
					$videos_added += self::add_single_video_data( $json_data, $video, $prefix, true );	// list_element = true
				}
			} elseif ( is_array( $og_video ) ) {
				$videos_added += self::add_single_video_data( $json_data, $og_video, $prefix, true );		// list_element = true
			}

			return $videos_added;	// return count of videos added
		}

		/* pass a single dimension video array in $opts
		 *
		 * example $opts array:
		 *
		 *	Array (
		 *		[og:video:title] => An Example Title
		 *		[og:video:description] => An example description...
		 *		[og:video:secure_url] => https://vimeo.com/moogaloop.swf?clip_id=150575335&autoplay=1
		 *		[og:video:url] => http://vimeo.com/moogaloop.swf?clip_id=150575335&autoplay=1
		 *		[og:video:type] => application/x-shockwave-flash
		 *		[og:video:width] => 1280
		 *		[og:video:height] => 544
		 *		[og:video:embed_url] => https://player.vimeo.com/video/150575335?autoplay=1
		 *		[og:video:has_image] => 1
		 *		[og:image:secure_url] => https://i.vimeocdn.com/video/550095036_1280.jpg
		 *		[og:image] =>
		 *		[og:image:width] => 1280
		 *		[og:image:height] => 544
		 *	)
		 */
		public static function add_single_video_data( &$json_data, $opts, $prefix = 'og:video', $list_element = true ) {

			$wpsso =& Wpsso::get_instance();

			if ( empty( $opts ) || ! is_array( $opts ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: options array is empty or not an array' );
				}
				return 0;	// return count of videos added
			}

			$media_url = SucomUtil::get_mt_media_url( $opts, $prefix );

			if ( empty( $media_url ) ) {
				if ( $ngfb->debug->enabled ) {
					$ngfb->debug->log( 'exiting early: '.$prefix.' URL values are empty' );
				}
				return 0;	// return count of videos added
			}

			// if not adding a list element, inherit the existing schema type url (if one exists)
			if ( ! $list_element && ( $video_type_url = WpssoSchema::get_data_type_url( $json_data ) ) !== false ) {
				if ( $ngfb->debug->enabled ) {
					$ngfb->debug->log( 'using inherited schema type url = '.$video_type_url );
				}
			} else {
				$video_type_url = 'https://schema.org/VideoObject';
			}

			$ret = WpssoSchema::get_schema_type_context( $video_type_url, 
				array( 'url' => esc_url( $media_url ),
			) );

			WpssoSchema::add_data_itemprop_from_assoc( $ret, $opts, array(
				'name' => $prefix.':title',
				'description' => $prefix.':description',
				'fileFormat' => $prefix.':type',
				'width' => $prefix.':width',
				'height' => $prefix.':height',
				'duration' => $prefix.':duration',
				'uploadDate' => $prefix.':upload_date',
				'thumbnailUrl' => $prefix.':thumbnail_url',
				'embedUrl' => $prefix.':embed_url',
			) );

			if ( ! empty( $opts[$prefix.':has_image'] ) ) {
				if ( ! WpssoSchema::add_og_single_image_data( $ret['thumbnail'], $opts, 'og:image', false ) ) {	// list_element = false
					unset( $ret['thumbnail'] );
				}
			}

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;	// add an item to the list
			}

			return 1;	// return count of videos added
		}

		public static function get_single_post_data( array $mod, $mt_og, $page_type_id ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			if ( ! $mod['is_post'] || ! $mod['id'] ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: not a post $mod or post id is empty' );
				}
				return false;
			}

			$cache_index = self::get_mod_cache_index( $mod, $page_type_id );
			$cache_data = self::get_mod_cache_data( $mod, $cache_index );

			if ( isset( $cache_data[$cache_index] ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: returning single post cache data' );
				}
				return $cache_data[$cache_index];	// stop here
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'get single post id '.$mod['id'].' data' );	// begin timer
			}

			// set reference values for admin notices
			if ( is_admin() ) {
				$sharing_url = $wpsso->util->get_sharing_url( $mod );
				$wpsso->notice->set_ref( $sharing_url, $mod, __( 'adding schema for post object', 'wpsso-schema-json-ld' ) );
			}

			if ( ! is_array( $mt_og ) ) {
				$mt_og = $wpsso->og->get_array( $mod, $mt_og = array() );
			}

			$cache_data[$cache_index] = $wpsso->schema->get_json_data( $mod, $mt_og, false, true );	// $page_type_id = false

			// restore previous reference values for admin notices
			if ( is_admin() ) {
				$wpsso->notice->unset_ref( $sharing_url );
			}

			self::save_mod_cache_data( $mod, $cache_data );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'get single post id '.$mod['id'].' data' );	// end timer
			}

			return $cache_data[$cache_index];
		}

		public static function get_mod_cache_index( $mixed, $page_type_id ) {

			$cache_index = 'page_type_id:'.$page_type_id;

			if ( $mixed !== false ) {
				$cache_index .= '_locale:'.SucomUtil::get_locale( $mixed );
			}

			if ( SucomUtil::is_amp() ) {
				$cache_index .= '_amp:true';
			}

			return $cache_index;
		}

		/*
		 * Returns an associative array of json data. The $cache_index argument is used for 
		 * quality control - making sure the $cache_index json data is an array (if it exists).
		 */
		public static function get_mod_cache_data( $mod, $cache_index ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$cache_md5_pre = $wpsso->lca.'_j_';

			if ( ! isset( self::$cache_exp_secs ) ) {	// filter cache expiration if not already set
				$cache_exp_filter = $wpsso->cf['wp']['transient'][$cache_md5_pre]['filter'];
				$cache_opt_key = $wpsso->cf['wp']['transient'][$cache_md5_pre]['opt_key'];
				self::$cache_exp_secs = (int) apply_filters( $cache_exp_filter, $wpsso->options[$cache_opt_key] );
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'cache expire = '.self::$cache_exp_secs );
			}

			if ( self::$cache_exp_secs > 0 ) {

				$cache_salt = 'WpssoJsonSchema::get_mod_cache_data('.SucomUtil::get_mod_salt( $mod ).')';
				$cache_id = $cache_md5_pre.md5( $cache_salt );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'cache salt = '.$cache_salt );
					$wpsso->debug->log( 'cache id = '.$cache_id );
					$wpsso->debug->log( 'cache index = '.$cache_index );
				}

				$cache_data = get_transient( $cache_id );

				if ( isset( $cache_data[$cache_index] ) ) {
					if ( is_array( $cache_data[$cache_index] ) ) {	// just in case
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'cache index data found in array from transient' );
						}
						return $cache_data;	// stop here
					} else {
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'cache index data not an array (unsetting index)' );
						}
						unset( $cache_data[$cache_index] );	// just in case
						return $cache_data;	// stop here
					}
				} else {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'cache index not in transient' );
					}
					return $cache_data;	// stop here
				}
			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'transient cache is disabled' );
			}

			return false;
		}

		public static function save_mod_cache_data( $mod, $cache_data ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$cache_md5_pre = $wpsso->lca.'_j_';

			if ( ! isset( self::$cache_exp_secs ) ) {	// filter cache expiration if not already set
				$cache_exp_filter = $wpsso->cf['wp']['transient'][$cache_md5_pre]['filter'];
				$cache_opt_key = $wpsso->cf['wp']['transient'][$cache_md5_pre]['opt_key'];
				self::$cache_exp_secs = (int) apply_filters( $cache_exp_filter, $wpsso->options[$cache_opt_key] );
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'cache expire = '.self::$cache_exp_secs );
			}

			if ( self::$cache_exp_secs > 0 ) {

				$cache_salt = 'WpssoJsonSchema::get_mod_cache_data('.SucomUtil::get_mod_salt( $mod ).')';
				$cache_id = $cache_md5_pre.md5( $cache_salt );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'cache salt = '.$cache_salt );
					$wpsso->debug->log( 'cache id = '.$cache_id );
				}

				// update the cached array and maintain the existing transient expiration time
				$expires_in_secs = SucomUtil::update_transient_array( $cache_id, $cache_data, self::$cache_exp_secs );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'cache data saved to transient cache (expires in '.$expires_in_secs.' seconds)' );
				}
			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'transient cache is disabled' );
			}

			return false;
		}
	}
}

