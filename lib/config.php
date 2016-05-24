<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2016 Jean-Sebastien Morisset (http://surniaulula.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'WpssoJsonConfig' ) ) {

	class WpssoJsonConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssojson' => array(
					'version' => '1.7.2-dev1',		// plugin version
					'opt_version' => '2',		// increment when changing default options
					'short' => 'WPSSO JSON',	// short plugin name
					'name' => 'WPSSO Schema JSON-LD (WPSSO JSON)',
					'desc' => 'WPSSO extension to add complete Schema JSON-LD markup (BlogPosting, Article, Place, Product, etc.) for Google and Pinterest.',
					'slug' => 'wpsso-schema-json-ld',
					'base' => 'wpsso-schema-json-ld/wpsso-schema-json-ld.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-schema-json-ld',
					'domain_path' => '/languages',
					'img' => array(
						'icon_small' => 'images/icon-128x128.png',
						'icon_medium' => 'images/icon-256x256.png',
					),
					'url' => array(
						// wordpress
						'download' => 'https://wordpress.org/plugins/wpsso-schema-json-ld/',
						'review' => 'https://wordpress.org/support/view/plugin-reviews/wpsso-schema-json-ld?filter=5&rate=5#postform',
						'readme' => 'https://plugins.svn.wordpress.org/wpsso-schema-json-ld/trunk/readme.txt',
						'wp_support' => 'https://wordpress.org/support/plugin/wpsso-schema-json-ld',
						// surniaulula
						'update' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/update/',
						'purchase' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/',
						'changelog' => 'http://wpsso.com/extend/plugins/wpsso-schema-json-ld/changelog/',
						'codex' => 'http://wpsso.com/codex/plugins/wpsso-schema-json-ld/',
						'faq' => 'http://wpsso.com/codex/plugins/wpsso-schema-json-ld/faq/',
						'notes' => '',
						'feed' => 'http://wpsso.com/category/application/wordpress/wp-plugins/wpsso-schema-json-ld/feed/',
						'pro_support' => 'http://wpsso-schema-json-ld.support.wpsso.com/',
					),
					'lib' => array(
						// submenu items must have unique keys
						'submenu' => array (
							'schema-json-ld' => 'Schema JSON-LD',
						),
						'gpl' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
						),
						'pro' => array(
							'admin' => array(
								'post' => 'Post Settings',
							),
							'head' => array(
								'article' => '(code) Schema Type Article (article)',
								'foodestablishment' => '(code) Schema Type Food Establishment (food.establishement)',
								'localbusiness' => '(code) Schema Type Local Business (local.business)',
								'organization' => '(code) Schema Type Organization (organization)',
								'person' => '(code) Schema Type Person (person)',
								'place' => '(code) Schema Type Place (place)',
								'product' => '(code) Schema Type Product (product)',
								'website' => '(code) Schema Type Website (website)',
							),
							'prop' => array(
								'rating' => '(code) Property Aggregate Rating',
							),
						),
					),
				),
			),
			'schema' => array(
				'article' => array(
					'headline' => array(
						'max_len' => 110,
					),
				),
				// verified and fully supported schema types
				// used by the WpssoJsonFilters action_admin_post_header() method
				'supported' => array(
					'article' => array( 
						'article' => 'http://schema.org/Article',
						'article.news' => 'http://schema.org/NewsArticle',
						'article.tech' => 'http://schema.org/TechArticle',
						'article.scholarly' => 'http://schema.org/ScholarlyArticle',
					),
					'blog.posting' => 'http://schema.org/BlogPosting',
					'organization' => 'http://schema.org/Organization',
					'person' => 'http://schema.org/Person',
					'place' => array(
						'administrative.area' => 'http://schema.org/AdministrativeArea',
						'civic.structure' => 'http://schema.org/CivicStructure',
						'landform' => 'http://schema.org/Landform',
						'landmarks.or.historical.buildings' => 'http://schema.org/LandmarksOrHistoricalBuildings',
						'local.business' => array( 
							'animal.shelter' => 'http://schema.org/AnimalShelter',
							'automotive.business' => 'http://schema.org/AutomotiveBusiness',
							'child.care' => 'http://schema.org/ChildCare',
							'dry.cleaning.or.laundry' => 'http://schema.org/DryCleaningOrLaundry',
							'emergency.service' => 'http://schema.org/EmergencyService',
							'employement.agency' => 'http://schema.org/EmploymentAgency',
							'entertainment.business' => 'http://schema.org/EntertainmentBusiness',
							'financial.service' => 'http://schema.org/FinancialService',
							'food.establishment' => array( 
								'bakery' => 'http://schema.org/Bakery',
								'bar.or.pub' => 'http://schema.org/BarOrPub',
								'brewery' => 'http://schema.org/Brewery',
								'cafe.or.coffee.shop' => 'http://schema.org/CafeOrCoffeeShop',
								'fast.food.restaurant' => 'http://schema.org/FastFoodRestaurant',
								'food.establishment' => 'http://schema.org/FoodEstablishment',
								'ice.cream.shop' => 'http://schema.org/IceCreamShop',
								'restaurant' => 'http://schema.org/Restaurant',
								'winery' => 'http://schema.org/Winery',
							),
							'government.office' => 'http://schema.org/GovernmentOffice',
							'health.and.beauty.business' => 'http://schema.org/HealthAndBeautyBusiness',
							'home.and.construction.business' => 'http://schema.org/HomeAndConstructionBusiness',
							'internet.cafe' => 'http://schema.org/InternetCafe',
							'legal.service' => 'http://schema.org/LegalService',
							'library' => 'http://schema.org/Library',
							'local.business' => 'http://schema.org/LocalBusiness',
							'lodging.business' => 'http://schema.org/LodgingBusiness',
							'medical.organization' => 'http://schema.org/MedicalOrganization',
							'professional.service' => 'http://schema.org/ProfessionalService',
							'radio.station' => 'http://schema.org/RadioStation',
							'real.estate.agent' => 'http://schema.org/RealEstateAgent',
							'recycling.center' => 'http://schema.org/RecyclingCenter',
							'self.storage' => 'http://schema.org/SelfStorage',
							'shopping.center' => 'http://schema.org/ShoppingCenter',
							'sports.activity.location' => 'http://schema.org/SportsActivityLocation',
							'store' => 'http://schema.org/Store',
							'television.station' => 'http://schema.org/TelevisionStation',
							'tourist.information.center' => 'http://schema.org/TouristInformationCenter',
							'travel.agency' => 'http://schema.org/TravelAgency',
						),
						'place' => 'http://schema.org/Place',
						'residence' => 'http://schema.org/Residence',
						'tourist.attraction' => 'http://schema.org/TouristAttraction',
					),
					'product' => 'http://schema.org/Product',
					'webpage' => 'http://schema.org/WebPage',
					'website' => 'http://schema.org/WebSite',
				),
			),
		);

		public static function get_version() { 
			return self::$cf['plugin']['wpssojson']['version'];
		}

		public static function set_constants( $plugin_filepath ) { 
			define( 'WPSSOJSON_FILEPATH', $plugin_filepath );						
			define( 'WPSSOJSON_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'WPSSOJSON_PLUGINSLUG', self::$cf['plugin']['wpssojson']['slug'] );		// wpsso-sp
			define( 'WPSSOJSON_PLUGINBASE', self::$cf['plugin']['wpssojson']['base'] );		// wpsso-sp/wpsso-sp.php
			define( 'WPSSOJSON_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
		}

		public static function require_libs( $plugin_filepath ) {

			require_once( WPSSOJSON_PLUGINDIR.'lib/register.php' );
			require_once( WPSSOJSON_PLUGINDIR.'lib/filters.php' );
			require_once( WPSSOJSON_PLUGINDIR.'lib/schema.php' );

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {
			if ( $ret === false && ! empty( $filespec ) ) {
				$filepath = WPSSOJSON_PLUGINDIR.'lib/'.$filespec.'.php';
				if ( file_exists( $filepath ) ) {
					require_once( $filepath );
					if ( empty( $classname ) )
						return SucomUtil::sanitize_classname( 'wpssojson'.$filespec );
					else return $classname;
				}
			}
			return $ret;
		}
	}
}

?>
