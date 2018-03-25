<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2018 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonSchema' ) ) {

	class WpssoJsonSchema {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
		}

		/**
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

			/**
			 * Sanity checks.
			 */
			if ( empty( $page_type_id ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: page_type_id is empty' );
				}
				return $posts_count;
			} elseif ( empty( $prop_name_type_ids ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: prop_name_type_ids is empty' );
				}
				return $posts_count;
			}

			/**
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

			/**
			 * Begin timer.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding posts data' );	// begin timer
			}

			/**
			 * Set the page number and the posts per page values.
			 */
			if ( ! isset( $posts_per_page_max ) ) {	// only set the value once
				$posts_per_page_max = SucomUtil::get_const( 'WPSSO_SCHEMA_POSTS_PER_PAGE_MAX', 10 );
			}

			global $wpsso_paged;
			$wpsso_paged = 1;
			$post_mods = array();

			if ( false === $posts_per_page ) {	// get the default if no argument provided
				$posts_per_page = get_option( 'posts_per_page' );
			}

			if ( $posts_per_page > $posts_per_page_max ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'setting posts_per_page '.$posts_per_page.' to maximum of '.$posts_per_page_max );
				}
				$posts_per_page = $posts_per_page_max;
			}

			$posts_per_page = (int) apply_filters( $wpsso->lca.'_posts_per_page', $posts_per_page, $mod );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'posts_per_page after filter is '.$posts_per_page );
			}

			/**
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
						$post_mods[] = $wpsso->m['util']['post']->get_mod( $post->ID );
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
				$post_mods = $mod['obj']->get_posts_mods( $mod, $posts_per_page, $wpsso_paged );
			} else {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: no source to get posts mods' );
					$wpsso->debug->mark( 'adding posts data' );	// end timer
				}
				unset( $wpsso_paged );	// unset the forced page number
				return $posts_count;
			}

			if ( empty( $post_mods ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: post_mods array is empty' );
					$wpsso->debug->mark( 'adding posts data' );	// end timer
				}
				unset( $wpsso_paged );	// unset the forced page number
				return $posts_count;
			}

			$post_mods = apply_filters( $wpsso->lca.'_json_post_mods', $post_mods, $mod, $page_type_id, $is_main );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'post_mods array has '.count( $post_mods ).' elements' );
			}

			/**
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

				foreach ( $post_mods as $post_mod ) {

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
							$wpsso->debug->log( 'getting single mod data for post id '.$post_mod['id'] );
						}

						$post_data = WpssoSchema::get_single_mod_data( $post_mod, false, $page_type_id );	// $mt_og = false

						if ( empty( $post_data ) ) {	// prevent null assignment
							$wpsso->debug->log( 'single mod data for post id '.$post_mod['id'].' is empty' );
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
			unset( $added_page_type_ids[$page_type_id] );

			/**
			 * End timer.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding posts data' );	// end timer
			}

			return $posts_count;
		}

		public static function add_media_data( &$json_data, $mod, $mt_og, $size_name = null, $add_video = true ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			$og_images = array();
			$prev_count = 0;
			$max = $wpsso->util->get_max_nums( $mod, 'schema' );

			if ( empty( $size_name ) ) {
				$size_name = $wpsso->lca . '-schema';
			}

			/**
			 * Include video preview images first.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'getting preview image(s)' );
			}
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
						$wpsso->debug->log( $prev_count.' preview images found (schema_img_max adjusted to '.$max['schema_img_max'].')' );
					}
				}
			}

			/**
			 * All other images.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'adding all image(s)' );
			}

			$og_images = array_merge( $og_images, $wpsso->og->get_all_images( $max['schema_img_max'], $size_name, $mod, true, 'schema' ) );

			if ( ! empty( $og_images ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding images to json data' );
				}
				$images_added = WpssoSchema::add_og_image_list_data( $json_data['image'], $og_images, 'og:image' );
			} else {
				$images_added = 0;
			}

			if ( ! $images_added && $mod['is_post'] ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding default image to json data' );
				}
				$og_images = $wpsso->media->get_default_images( 1, $size_name, true );
				$images_added = WpssoSchema::add_og_image_list_data( $json_data['image'], $og_images, 'og:image' );
			}

			if ( ! $images_added ) {
				unset( $json_data['image'] );	// prevent null assignment
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( $images_added.' images added' );
			}

			/**
			 * Property:
			 *	video as https://schema.org/VideoObject
			 *
			 * Allow the video property to be skipped -- some schema types (organization, for example) do not include a video property.
			 */
			if ( $add_video ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding all video(s)' );
				}

				if ( ! empty( $mt_og['og:video'] ) ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'adding videos to json data' );
					}
					$videos_added = WpssoJsonSchema::add_video_list_data( $json_data['video'], $mt_og['og:video'], 'og:video' );
				} else {
					$videos_added = 0;
				}

				if ( ! $videos_added ) {
					unset( $json_data['video'] );	// prevent null assignment
				}

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( $videos_added.' videos added' );
				}

			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'skipping videos: add_video argument is false' );
			}

			/**
			 * Redefine mainEntityOfPage property for Attachment pages.
			 *
			 * If this is an attachment page, and the post mime_type is a known media type (image, video, or audio),
			 * then set the first media array element mainEntityOfPage to the page url, and set the page mainEntityOfPage
			 * property to false (so it doesn't get defined later).
			 */
			$main_prop = $mod['is_post'] && $mod['post_type'] === 'attachment' ? preg_replace( '/\/.*$/', '', $mod['post_mime'] ) : '';

			$main_prop = apply_filters( $wpsso->lca.'_json_media_main_prop', $main_prop, $mod );

			if ( ! empty( $main_prop ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( $mod['name'].' id '.$mod['id'].' '.$main_prop.' property is main entity' );
				}

				if ( ! empty( $json_data[$main_prop] ) && is_array( $json_data[$main_prop] ) ) {

					reset( $json_data[$main_prop] );

					$media_key = key( $json_data[$main_prop] );	// media array key should be '0'

					if ( ! isset( $json_data[$main_prop][$media_key]['mainEntityOfPage'] ) ) {
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'mainEntityOfPage for '.$main_prop.' key '.$media_key.' = '.$mt_og['og:url'] );
						}
						$json_data[$main_prop][$media_key]['mainEntityOfPage'] = $mt_og['og:url'];
					} elseif ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'mainEntityOfPage for '.$main_prop.' key '.$media_key.' already defined' );
					}

					$json_data['mainEntityOfPage'] = false;
				}
			}
		}

		public static function add_comment_list_data( &$json_data, $mod ) {

			if ( ! $mod['is_post'] || ! $mod['id'] || ! comments_open( $mod['id'] ) ) {
				return;
			}

			$json_data['commentCount'] = get_comments_number( $mod['id'] );

			/**
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

			$wpsso =& Wpsso::get_instance();

			$comments_added = 0;

			if ( $comment_id && $cmt = get_comment( $comment_id ) ) {

				$comments_added++;

				// if not adding a list element, inherit the existing schema type url (if one exists)
				if ( ! $list_element && ( $comment_type_url = WpssoSchema::get_data_type_url( $json_data ) ) !== false ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'using inherited schema type url = '.$comment_type_url );
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

		/**
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
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: '.$prefix.' URL values are empty' );
				}
				return 0;	// return count of videos added
			}

			// if not adding a list element, inherit the existing schema type url (if one exists)
			list( $video_type_id, $video_type_url ) = WpssoSchema::get_single_type_id_url( $json_data, false, false, 'video.object', $list_element );

			$ret = WpssoSchema::get_schema_type_context( $video_type_url, array(
				'url' => esc_url_raw( $media_url ),
			) );

			WpssoSchema::add_data_itemprop_from_assoc( $ret, $opts, array(
				'name' => $prefix.':title',
				'description' => $prefix.':description',
				'fileFormat' => $prefix.':type',	// mime type
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

			if ( ! empty( $opts[$prefix.':tag'] ) ) {
				if ( is_array( $opts[$prefix.':tag'] ) ) {
					$ret['keywords'] = implode( ', ', $opts[$prefix.':tag'] );
				} else {
					$ret['keywords'] = $opts[$prefix.':tag'];
				}
			}

			if ( empty( $list_element ) ) {
				$json_data = $ret;
			} else {
				$json_data[] = $ret;	// add an item to the list
			}

			return 1;	// return count of videos added
		}
	}
}

