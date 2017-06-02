<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2017 Jean-Sebastien Morisset (https://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonSchema' ) ) {

	class WpssoJsonSchema {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
		}

		/*
		 * Called by CollectionPage and ProfilePage
		 */
		public static function add_parts_data( &$json_data, $mod, $mt_og, $type_id, $is_main ) {

			$wpsso =& Wpsso::get_instance();
			$parts_added = 0;
			$posts_mods = array();
			
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding parts data' );	// begin timer
			}

			/*
			 * $type_id is false for parts to prevent recursion of main loop posts.
			 */
			if ( $type_id !== false && ( is_home() || is_archive() || is_search() ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'using query loop to get posts' );
				}

				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						global $post;
						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'getting mod for post id '.$post->ID );
						}
						$posts_mods[] = $wpsso->m['util']['post']->get_mod( $post->ID );
					}
					wp_reset_postdata();
				}

				$posts_per_page = apply_filters( $wpsso->cf['lca'].'_posts_per_page', 
					get_option( 'posts_per_page' ), $mod );

				if ( count( $posts_mods ) > $posts_per_page ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'slicing posts_mods array from '.
							count( $posts_mods ).' to '.$posts_per_page.' elements' );
					}
					$posts_mods = array_slice( $posts_mods, 0, $posts_per_page );
				}
			/*
			 * Get first page of posts for this term / user archive page.
			 * If the module is a post, then return all children of that post.
			 */
			} elseif ( method_exists( $mod['obj'], 'get_posts_mods' ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'using module object to get posts' );
				}
				$posts_mods = $mod['obj']->get_posts_mods( $mod );
			}

			if ( ! empty( $posts_mods ) ) {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'posts_mods array has '.count( $posts_mods ).' elements' );
				}

				foreach ( $posts_mods as $post_mod ) {
					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->mark( 'post id '.$post_mod['id'].' part' );	// begin timer
					}

					// set the reference url for admin notices
					if ( is_admin() ) {
						$sharing_url = $wpsso->util->get_sharing_url( $post_mod );
						$previous_url = $wpsso->notice->set_reference_url( $sharing_url );
					}

					$post_mt_og = array();
					$post_mt_og = $wpsso->og->get_array( $post_mod, $post_mt_og );
					$json_data['hasPart'][] = $wpsso->schema->get_json_data( $post_mod,
						$post_mt_og, false, true );	// $page_type_id = false, $is_main = true

					// restore the previous reference url for admin notices
					if ( is_admin() ) {
						$wpsso->notice->set_reference_url( $previous_url );
					}

					$parts_added++;

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->mark( 'post id '.$post_mod['id'].' part' );	// end timer
					}
				}
			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'posts_mods array is empty' );
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding parts data' );	// end timer
			}

			return $parts_added;
		}

		public static function add_media_data( &$json_data, $mod, $mt_og, $size_name = null, $add_video = true ) {

			$wpsso =& Wpsso::get_instance();
			
			/*
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			$og_image = array();

			$prev_count = 0;
			$max = $wpsso->util->get_max_nums( $mod, 'schema' );

			if ( empty( $size_name ) ) {
				$size_name = $wpsso->cf['lca'].'-schema';
			}

			/*
			 * Include video preview images first.
			 */
			if ( ! empty( $mt_og['og:video'] ) && is_array( $mt_og['og:video'] ) ) {
				// prevent duplicates - exclude text/html videos
				foreach ( $mt_og['og:video'] as $num => $og_video ) {
					if ( isset( $og_video['og:video:type'] ) &&
						$og_video['og:video:type'] !== 'text/html' ) {
						if ( SucomUtil::get_mt_media_url( $og_video, 'og:image' ) )
							$prev_count++;
						$og_image[] = SucomUtil::preg_grep_keys( '/^og:image/', $og_video );
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

			$og_image = array_merge( $og_image, $wpsso->og->get_all_images( $max['schema_img_max'],
				$size_name, $mod, true, 'schema' ) );

			if ( ! empty( $og_image ) )
				$images_added = WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );
			else $images_added = 0;

			if ( ! $images_added && $mod['is_post'] ) {
				$og_image = $wpsso->media->get_default_image( 1, $size_name, true );
				$images_added = WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );
			}

			if ( ! $images_added )
				unset( $json_data['image'] );	// prevent null assignment

			/*
			 * Property:
			 *	video as https://schema.org/VideoObject
			 *
			 * Allow the video property to be skipped -- some schema types (organization, 
			 * for example) do not include a video property.
			 */
			if ( $add_video && ! empty( $mt_og['og:video'] ) )
				WpssoJsonSchema::add_video_list_data( $json_data['video'], $mt_og['og:video'], 'og:video' );
		}

		public static function add_comment_list_data( &$json_data, $mod ) {
			if ( ! $mod['is_post'] || ! $mod['id'] || ! comments_open( $mod['id'] ) )
				return;

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
				foreach ( $og_video as $video )
					$videos_added += self::add_single_video_data( $json_data, $video, $prefix, true );	// list_element = true
			} elseif ( is_array( $og_video ) )
				$videos_added += self::add_single_video_data( $json_data, $og_video, $prefix, true );		// list_element = true

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
				if ( ! WpssoSchema::add_single_image_data( $ret['thumbnail'], $opts, 'og:image', false ) ) {	// list_element = false
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
	}
}

?>
