<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonSchema' ) ) {

	class WpssoJsonSchema {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			if ( $this->p->debug->enabled )
				$this->p->debug->mark();
		}

		public static function add_author_and_media_data( &$json_data, &$use_post, &$post_obj, &$mt_og, &$post_id, &$user_id ) {

			$wpsso = Wpsso::get_instance();
			
			/*
			 * Property:
			 *	author as http://schema.org/Person
			 */
			if ( $user_id > 0 )
				WpssoSchema::add_single_person_data( $json_data['author'], $user_id, true );

			/*
			 * Property:
			 *	image as http://schema.org/ImageObject
			 */
			$size_name = $wpsso->cf['lca'].'-schema';
			$og_image = $wpsso->og->get_all_images( 1, $size_name, $post_id, true, 'schema' );

			if ( empty( $og_image ) && 
				SucomUtil::is_post_page( $use_post ) )
					$og_image = $wpsso->media->get_default_image( 1, $size_name, true );

			if ( ! empty( $og_image ) )
				WpssoSchema::add_image_list_data( $json_data['image'], $og_image, 'og:image' );

			/*
			 * Property:
			 *	video as http://schema.org/VideoObject
			 */
			if ( ! empty( $mt_og['og:video'] ) )
				WpssoJsonSchema::add_video_list_data( $json_data['video'], $mt_og['og:video'], 'og:video' );
		}

		// pass a single or two dimension video array in $og_video
		public static function add_video_list_data( &$json_data, &$og_video, $opt_pre = 'og:video' ) {

			if ( isset( $og_video[0] ) && is_array( $og_video[0] ) ) {				// 2 dimensional array
				foreach ( $og_video as $video )
					self::add_single_video_data( $json_data, $video, $opt_pre, true );	// list_element = true

			} elseif ( is_array( $og_video ) )
				self::add_single_video_data( $json_data, $og_video, $opt_pre, true );		// list_element = true
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
		public static function add_single_video_data( &$json_data, &$opts, $opt_pre = 'og:video', $list_element = true ) {

			$wpsso = Wpsso::get_instance();

			if ( empty( $opts ) || ! is_array( $opts ) ) {
				if ( $wpsso->debug->enabled )
					$wpsso->debug->log( 'exiting early: options array is empty or not an array' );
				return false;
			}

			if ( empty( $opts[$opt_pre] ) && empty( $opts[$opt_pre.':secure_url'] ) ) {
				if ( $wpsso->debug->enabled )
					$wpsso->debug->log( 'exiting early: '.$opt_pre.' and '.
						$opt_pre.':secure_url values are empty' );
				return false;
			}

			$ret = array(
				'@context' => 'http://schema.org',
				'@type' => 'VideoObject',
				'url' => esc_url( empty( $opts[$opt_pre.':secure_url'] ) ?	// prefer secure_url if available
					$opts[$opt_pre] :
					$opts[$opt_pre.':secure_url']
				),
			);

			WpssoSchema::add_data_prop_from_og( $ret, $opts, array(
				'name' => $opt_pre.':title',
				'description' => $opt_pre.':description',
				'fileFormat' => $opt_pre.':type',
				'width' => $opt_pre.':width',
				'height' => $opt_pre.':height',
				'duration' => $opt_pre.':duration',
				'uploadDate' => $opt_pre.':upload_date',
				'thumbnailUrl' => $opt_pre.':thumbnail_url',
				'embedUrl' => $opt_pre.':embed_url',
			) );

			if ( $opts[$opt_pre.':has_image'] )
				if ( ! WpssoSchema::add_single_image_data( $ret['thumbnail'], $opts, 'og:image', false ) )	// list_element = false
					unset( $ret['image'] );

			if ( empty( $list_element ) )
				$json_data = $ret;
			else $json_data[] = $ret;	// add an item to the list

			return true;
		}

	}
}

?>
