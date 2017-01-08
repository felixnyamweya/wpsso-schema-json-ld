=== WPSSO Schema JSON-LD Markup - Schema / Structured Data / Rich Snippet Markup ===
Plugin Name: WPSSO Schema JSON-LD Markup (WPSSO JSON)
Plugin Slug: wpsso-schema-json-ld
Text Domain: wpsso-schema-json-ld
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Donate Link: https://wpsso.com/extend/plugins/wpsso-schema-json-ld/?utm_source=wpssojson-readme-donate
Assets URI: https://surniaulula.github.io/wpsso-schema-json-ld/assets/
Tags: seo, json, json-ld, ld+json, schema, schema.org, structured data, rich snippets, news seo, local seo, local business, pinterest, article, product, products, event, events, rating, ratings, review, recipe, recipes, amp, co-authors, co-authors plus, video object, video schema, video seo, search engine optimization, optimize, knowledge graph, aggregate rating
Contributors: jsmoriss
Requires At Least: 3.7
Tested Up To: 4.7
Stable Tag: 1.11.6-1

WPSSO extension to add complete Schema JSON-LD markup (Article, Event, Organization, Place / Business, Product, Recipe, and more).

== Description ==

<p><img src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png" width="256" height="256" style="width:33%;min-width:128px;max-width:256px;float:left;margin:0 40px 20px 0;" /><strong>Features <em>accurate</em> and <em>comprehensive</em> Schema / Structured Data markup</strong> &mdash; including images, videos, organization (publisher), person (author and co-authors), product variations, product ratings, recipe details, event information, collection pages, and much more.</p>

<p><strong>Override missing or innaccurate Schema markup in your theme templates.</strong></p>

<p><strong>Select a another Schema than <a href="https://schema.org/BlogPosting">BlogPosting</a> for <a href="https://wordpress.org/plugins/amp/">AMP</a> webpages.</strong></p>

<p><strong>Include all WooCommerce product images and variations the Schema Product markup!</strong> (Pro version)</p>

<p><strong>Provides unique Schema markup and optimization features for Pinterest</strong> &mdash; Pinterest does not (currently) read the standard JSON-LD format. WPSSO and its WPSSO JSON extension include special provisions to provide Schema meta tags for Pinterest, along with methods to avoid conflicts between incompatible Pinterest and Facebook Open Graph meta tags.</p>

<blockquote>
<p><strong>Prerequisite</strong> &mdash; WPSSO Schema JSON-LD Markup (WPSSO JSON) is an extension for the <a href="https://wordpress.org/plugins/wpsso/">WordPress Social Sharing Optimization (WPSSO)</a> plugin, which <em>automatically</em> creates complete and accurate meta tags and Schema markup for Social Sharing Optimization (SSO) and Search Engine Optimization (SEO).</p>
</blockquote>

= Quick List of Features =

**WPSSO JSON Free / Basic Features**

Extends the features of WPSSO Free or Pro.

Includes support for Automattic's [Accelerated Mobile Pages (AMP)](https://wordpress.org/plugins/amp/) plugin.

Includes contributor markup for [Co-Authors Plus](https://wordpress.org/plugins/co-authors-plus/) authors and guest authors (requires [WPSSO Pro](https://wpsso.com/) to retrieve co-author information).

Adds an additional "Schema Markup" settings page to the SSO menu:

* Website Alternate Name
* Organization Logo Image URL
* Organization Banner (600x60) URL
* Maximum Images to Include
* Schema Image Dimensions
* Maximum Description Length
* Author / Person Name Format
* Item Type for Blog Home Page
* Item Type for Static Home Page
* Item Type for Archive Page
* Item Type for User / Author Page
* Item Type for Search Results Page
* Item Type by Post Type (for Posts, Pages, Media, and custom post types).
* Recipe Ingredients Custom Field

Adds Schema / Structured Data JSON-LD markup for:

* Schema Type [schema.org/BlogPosting](https://schema.org/BlogPosting)
* Schema Type [schema.org/WebPage](https://schema.org/WebPage)

= Quick List of Features (Continued) =

**WPSSO JSON Pro / Power-User Features**

Extends the features of WPSSO Pro.

Adds additional custom options in the Social Settings metabox:

* Schema Item Name (aka Title)
* Schema Description
* Main Entity of Page
* Schema Item Type
* Article Publisher
* Article Headline
* Event Organizer
* Event Performer
* Recipe Preparation Time 
* Recipe Cooking Time 
* Recipe Total Time 
* Recipe Total Calories 
* Recipe Quantity 
* Recipe Ingredients 
* Reviewed Item Type 
* Reviewed Item URL 
* Reviewed Item Rating 

Adds Schema / Structured Data JSON-LD markup for:

* Schema Type [schema.org/CreativeWork](https://schema.org/CreativeWork)
	* Schema Type [schema.org/Article](https://schema.org/Article)
		* Schema Type [schema.org/BlogPosting](https://schema.org/BlogPosting)
		* Schema Type [schema.org/NewsArticle](https://schema.org/NewsArticle)
		* Schema Type [schema.org/Report](https://schema.org/Report)
		* Schema Type [schema.org/ScholarlyArticle](https://schema.org/ScholarlyArticle)
		* Schema Type [schema.org/SocialMediaPosting](https://schema.org/SocialMediaPosting)
		* Schema Type [schema.org/TechArticle](https://schema.org/TechArticle)
	* Schema Type [schema.org/Book](https://schema.org/Book)
	* Schema Type [schema.org/Blog](https://schema.org/Blog) (includes hasPart property with posts)
	* Schema Type [schema.org/Recipe](https://schema.org/Recipe)
	* Schema Type [schema.org/Review](https://schema.org/Review)
	* Schema Type [schema.org/WebPage](https://schema.org/WebPage)
		* Schema Type [schema.org/AboutPage](https://schema.org/AboutPage)
		* Schema Type [schema.org/CheckoutPage](https://schema.org/CheckoutPage)
		* Schema Type [schema.org/CollectionPage](https://schema.org/CollectionPage) (includes hasPart property with posts)
		* Schema Type [schema.org/ContactPage](https://schema.org/ContactPage)
		* Schema Type [schema.org/ItemPage](https://schema.org/ItemPage)
		* Schema Type [schema.org/ProfilePage](https://schema.org/ProfilePage) (includes hasPart property with posts)
		* Schema Type [schema.org/QAPage](https://schema.org/QAPage)
		* Schema Type [schema.org/SearchResultsPage](https://schema.org/SearchResultsPage) (includes hasPart property with posts)
	* Schema Type [schema.org/WebSite](https://schema.org/WebSite)
* Schema Type [schema.org/Event](https://schema.org/Event)
	* Schema Type [schema.org/BusinessEvent](https://schema.org/BusinessEvent)
	* Schema Type [schema.org/ChildrensEvent](https://schema.org/ChildrensEvent)
	* Schema Type [schema.org/DanceEvent](https://schema.org/DanceEvent)
	* Schema Type [schema.org/DeliveryEvent](https://schema.org/DeliveryEvent)
	* Schema Type [schema.org/EducationEvent](https://schema.org/EducationEvent)
	* Schema Type [schema.org/ExhibitionEvent](https://schema.org/ExhibitionEvent)
	* Schema Type [schema.org/Festival](https://schema.org/Festival)
	* Schema Type [schema.org/FoodEvent](https://schema.org/FoodEvent)
	* Schema Type [schema.org/LiteraryEvent](https://schema.org/LiteraryEvent)
	* Schema Type [schema.org/MusicEvent](https://schema.org/MusicEvent)
	* Schema Type [schema.org/PublicationEvent](https://schema.org/PublicationEvent)
	* Schema Type [schema.org/SaleEvent](https://schema.org/SaleEvent)
	* Schema Type [schema.org/ScreeningEvent](https://schema.org/ScreeningEvent)
	* Schema Type [schema.org/SocialEvent](https://schema.org/SocialEvent)
	* Schema Type [schema.org/SportsEvent](https://schema.org/SportsEvent)
	* Schema Type [schema.org/TheaterEvent](https://schema.org/TheaterEvent)
	* Schema Type [schema.org/VisualArtsEvent](https://schema.org/VisualArtsEvent)
* Schema Type [schema.org/Organization](https://schema.org/Organization)
	* Schema Type [schema.org/Airline](https://schema.org/Airline)
	* Schema Type [schema.org/Corporation](https://schema.org/Corporation)
	* Schema Type [schema.org/EducationalOrganization](https://schema.org/EducationalOrganization)
		* Schema Type [schema.org/CollegeOrUniversity](https://schema.org/CollegeOrUniversity)
		* Schema Type [schema.org/ElementarySchool](https://schema.org/ElementarySchool)
		* Schema Type [schema.org/MiddleSchool](https://schema.org/MiddleSchool)
		* Schema Type [schema.org/Preschool](https://schema.org/Preschool)
		* Schema Type [schema.org/School](https://schema.org/School)
	* Schema Type [schema.org/GovernmentOrganization](https://schema.org/GovernmentOrganization)
	* Schema Type [schema.org/MedicalOrganization](https://schema.org/MedicalOrganization)
		* Schema Type [schema.org/Dentist](https://schema.org/Dentist)
		* Schema Type [schema.org/Hospital](https://schema.org/Hospital)
		* Schema Type [schema.org/Pharmacy](https://schema.org/Pharmacy)
		* Schema Type [schema.org/Physician](https://schema.org/Physician)
	* Schema Type [schema.org/NGO](https://schema.org/NGO)
	* Schema Type [schema.org/PerformingGroup](https://schema.org/PerformingGroup)
		* Schema Type [schema.org/DanceGroup](https://schema.org/DanceGroup)
		* Schema Type [schema.org/MusicGroup](https://schema.org/MusicGroup)
		* Schema Type [schema.org/PerformingGroup](https://schema.org/PerformingGroup)
		* Schema Type [schema.org/TheaterGroup](https://schema.org/TheaterGroup)
	* Schema Type [schema.org/SportsOrganization](https://schema.org/SportsOrganization)
		* Schema Type [schema.org/SportsTeam](https://schema.org/SportsTeam)
* Schema Type [schema.org/Person](https://schema.org/Person)
* Schema Type [schema.org/Place](https://schema.org/Place) ([WPSSO PLM](https://wordpress.org/plugins/wpsso-plm/) extension required to manage Place / Location information)
	* Schema Type [schema.org/AdministrativeArea](https://schema.org/AdministrativeArea)
	* Schema Type [schema.org/CivicStructure](https://schema.org/CivicStructure)
	* Schema Type [schema.org/Landform](https://schema.org/Landform)
	* Schema Type [schema.org/LandmarksOrHistoricalBuildings](https://schema.org/LandmarksOrHistoricalBuildings)
	* Schema Type [schema.org/LocalBusiness](https://schema.org/LocalBusiness)
		* Schema Type [schema.org/AnimalShelter](https://schema.org/AnimalShelter)
		* Schema Type [schema.org/AutomotiveBusiness](https://schema.org/AutomotiveBusiness)
		* Schema Type [schema.org/ChildCare](https://schema.org/ChildCare)
		* Schema Type [schema.org/DryCleaningOrLaundry](https://schema.org/DryCleaningOrLaundry)
		* Schema Type [schema.org/EmergencyService](https://schema.org/EmergencyService)
		* Schema Type [schema.org/EmploymentAgency](https://schema.org/EmploymentAgency)
		* Schema Type [schema.org/EntertainmentBusiness](https://schema.org/EntertainmentBusiness)
		* Schema Type [schema.org/FinancialService](https://schema.org/FinancialService)
		* Schema Type [schema.org/FoodEstablishment](https://schema.org/FoodEstablishment)
			* Schema Type [schema.org/Bakery](https://schema.org/Bakery)
			* Schema Type [schema.org/BarOrPub](https://schema.org/BarOrPub)
			* Schema Type [schema.org/Brewery](https://schema.org/Brewery)
			* Schema Type [schema.org/CafeOrCoffeeShop](https://schema.org/CafeOrCoffeeShop)
			* Schema Type [schema.org/FastFoodRestaurant](https://schema.org/FastFoodRestaurant)
			* Schema Type [schema.org/IceCreamShop](https://schema.org/IceCreamShop)
			* Schema Type [schema.org/Restaurant](https://schema.org/Restaurant)
			* Schema Type [schema.org/Winery](https://schema.org/Winery)
		* Schema Type [schema.org/GovernmentOffice](https://schema.org/GovernmentOffice)
		* Schema Type [schema.org/HealthAndBeautyBusiness](https://schema.org/HealthAndBeautyBusiness)
		* Schema Type [schema.org/HomeAndConstructionBusiness](https://schema.org/HomeAndConstructionBusiness)
			* Schema Type [schema.org/GeneralContractor](https://schema.org/GeneralContractor)
			* Schema Type [schema.org/HVACBusiness](https://schema.org/HVACBusiness)
			* Schema Type [schema.org/HousePainter](https://schema.org/HousePainter)
			* Schema Type [schema.org/Locksmith](https://schema.org/Locksmith)
			* Schema Type [schema.org/MovingCompany](https://schema.org/MovingCompany)
			* Schema Type [schema.org/Plumber](https://schema.org/Plumber)
			* Schema Type [schema.org/RoofingContractor](https://schema.org/RoofingContractor)
		* Schema Type [schema.org/InternetCafe](https://schema.org/InternetCafe)
		* Schema Type [schema.org/LegalService](https://schema.org/LegalService)
		* Schema Type [schema.org/Library](https://schema.org/Library)
		* Schema Type [schema.org/LodgingBusiness](https://schema.org/LodgingBusiness)
		* Schema Type [schema.org/MedicalOrganization](https://schema.org/MedicalOrganization)
		* Schema Type [schema.org/ProfessionalService](https://schema.org/ProfessionalService)
		* Schema Type [schema.org/RadioStation](https://schema.org/RadioStation)
		* Schema Type [schema.org/RealEstateAgent](https://schema.org/RealEstateAgent)
		* Schema Type [schema.org/RecyclingCenter](https://schema.org/RecyclingCenter)
		* Schema Type [schema.org/SelfStorage](https://schema.org/SelfStorage)
		* Schema Type [schema.org/ShoppingCenter](https://schema.org/ShoppingCenter)
		* Schema Type [schema.org/SportsActivityLocation](https://schema.org/SportsActivityLocation)
		* Schema Type [schema.org/Store](https://schema.org/Store)
			* Schema Type [schema.org/AutoPartsStore](https://schema.org/AutoPartsStore)
			* Schema Type [schema.org/BikeStore](https://schema.org/BikeStore)
			* Schema Type [schema.org/BookStore](https://schema.org/BookStore)
			* Schema Type [schema.org/ClothingStore](https://schema.org/ClothingStore)
			* Schema Type [schema.org/ComputerStore](https://schema.org/ComputerStore)
			* Schema Type [schema.org/ConvenienceStore](https://schema.org/ConvenienceStore)
			* Schema Type [schema.org/DepartmentStore](https://schema.org/DepartmentStore)
			* Schema Type [schema.org/ElectronicsStore](https://schema.org/ElectronicsStore)
			* Schema Type [schema.org/Florist](https://schema.org/Florist)
			* Schema Type [schema.org/FurnitureStore](https://schema.org/FurnitureStore)
			* Schema Type [schema.org/GardenStore](https://schema.org/GardenStore)
			* Schema Type [schema.org/GroceryStore](https://schema.org/GroceryStore)
			* Schema Type [schema.org/HardwareStore](https://schema.org/HardwareStore)
			* Schema Type [schema.org/HobbyShop](https://schema.org/HobbyShop)
			* Schema Type [schema.org/HomeGoodsStore](https://schema.org/HomeGoodsStore)
			* Schema Type [schema.org/JewelryStore](https://schema.org/JewelryStore)
			* Schema Type [schema.org/LiquorStore](https://schema.org/LiquorStore)
			* Schema Type [schema.org/MensClothingStore](https://schema.org/MensClothingStore)
			* Schema Type [schema.org/MobilePhoneStore](https://schema.org/MobilePhoneStore)
			* Schema Type [schema.org/MovieRentalStore](https://schema.org/MovieRentalStore)
			* Schema Type [schema.org/MusicStore](https://schema.org/MusicStore)
			* Schema Type [schema.org/OfficeEquipmentStore](https://schema.org/OfficeEquipmentStore)
			* Schema Type [schema.org/OutletStore](https://schema.org/OutletStore)
			* Schema Type [schema.org/PawnShop](https://schema.org/PawnShop)
			* Schema Type [schema.org/PetStore](https://schema.org/PetStore)
			* Schema Type [schema.org/ShoeStore](https://schema.org/ShoeStore)
			* Schema Type [schema.org/SportingGoodsStore](https://schema.org/SportingGoodsStore)
			* Schema Type [schema.org/TireShop](https://schema.org/TireShop)
			* Schema Type [schema.org/ToyStore](https://schema.org/ToyStore)
			* Schema Type [schema.org/WholesaleStore](https://schema.org/WholesaleStore)
		* Schema Type [schema.org/TelevisionStation](https://schema.org/TelevisionStation)
		* Schema Type [schema.org/TouristInformationCenter](https://schema.org/TouristInformationCenter)
		* Schema Type [schema.org/TravelAgency](https://schema.org/TravelAgency)
	* Schema Type [schema.org/Residence](https://schema.org/Residence)
	* Schema Type [schema.org/TouristAttraction](https://schema.org/TouristAttraction)
* Schema Type [schema.org/Product](https://schema.org/Product) (supported e-Commerce plugin required)

= Markup Examples =

* [Markup Example for a Restaurant](http://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-restaurant/) using the WPSSO PLM extension to manage the Place / Location information (address, geo coordinates, business hours – daily and seasonal, restaurant menu URL, and accepts reservation values).
* [Markup Example for a Tech Article](http://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-tech-article/) published on surniaulula.com.
* [Markup Example for a WooCommerce Product](http://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-woocommerce-product/), including its name, description, images, videos, sku, price, availability, ratings, colors, category, width, height, weight, all product variations, and much more.

= Extends the WPSSO Plugin =

The WordPress Social Sharing Optimization (WPSSO) plugin is required to use the WPSSO JSON extension.

Use the Free version of WPSSO JSON with *both* the Free and Pro versions of WPSSO. The [WPSSO JSON Pro extension](https://wpsso.com/extend/plugins/wpsso-schema-json-ld/?utm_source=wpssojson-readme-extends) (along with all WPSSO Pro extensions) requires the [WPSSO Pro plugin](https://wpsso.com/extend/plugins/wpsso/?utm_source=wpssojson-readme-extends) as well.

[Purchase the WPSSO Schema JSON-LD Markup (WPSSO JSON) Pro extension](https://wpsso.com/extend/plugins/wpsso-schema-json-ld/?utm_source=wpssojson-readme-purchase) (includes a *No Risk 30 Day Refund Policy*).

== Installation ==

= Install and Uninstall =

* [Install the Plugin](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/installation/install-the-plugin/)
* [Uninstall the Plugin](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/installation/uninstall-the-plugin/)

== Frequently Asked Questions ==

= Frequently Asked Questions =

* None

== Other Notes ==

= Additional Documentation =

* [Developer Resources](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/)
	* [Filters](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/filters/)
		* [Filter Examples](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/)
			* [Assign a Custom Field Value to a Schema Property](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/assign-a-custom-field-value-to-a-schema-property/)
			* [Modify the aggregateRating Property](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/modify-the-aggregaterating-property/)
			* [Modify the VideoObject Name and Description](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/modify-the-videoobject-name-and-description/)
		* [Filters by Name](https://wpsso.com/codex/plugins/wpsso-schema-json-ld/notes/developer/filters/by-name/)

== Screenshots ==

01. (Pro version) Social Settings metabox on Posts, Pages, Taxonomy / Terms, and User editing pages &mdash; Allows you to modify the default Schema type, title, headline (for Articles), description, image, video, preview an example share, preview the meta tags, and validate the webpage markup with online tools.
02. (Pro version) A selection of Google Structured Data / Schema Markup options are shown based on the Schema Item Type. This example shows the Schema Item Type https://schema.org/Review, along with its reviewed item type (product, restaurant, book, etc.), the item URL, and the item rating.
03. (Pro version) An example of WPSSO JSON's Schema Recipe markup on Pinterest.
04. (Pro version) Google's Structured Data Testing Tool showing results for an example of the Schema Item Type https://schema.org/TechArticle.

== Changelog ==

= Free / Basic Version Repository =

* [GitHub](https://surniaulula.github.io/wpsso-schema-json-ld/)
* [WordPress.org](https://wordpress.org/plugins/wpsso-schema-json-ld/developers/)

= Version Numbering Scheme =

Version components: `{major}.{minor}.{bugfix}-{stage}{level}`

* {major} = Major code changes / re-writes or significant feature changes.
* {minor} = New features / options were added or improved.
* {bugfix} = Bugfixes or minor improvements.
* {stage}{level} = dev &lt; a (alpha) &lt; b (beta) &lt; rc (release candidate) &lt; # (production).

Note that the production stage level can be incremented on occasion for simple text revisions and/or translation updates. See [PHP's version_compare()](http://php.net/manual/en/function.version-compare.php) documentation for additional information on "PHP-standardized" version numbering.

= Changelog / Release Notes =

**Version 1.11.6-1 (2017/01/08)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Added a 'plugins_loaded' action hook to load the plugin text domain.

**Version 1.11.5-1 (2016/12/12)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Added a check for maximum posts per page in WpssoJsonSchema add_parts_data() to limit the number of posts included.
	* Added a potentialAction property and filter to the Schema for https://schema.org/Thing.

**Version 1.11.4-3 (2016/12/05)**

* *New Features*
	* None
* *Improvements*
	* Added the product variation URL to notices when checking for product variation images.
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.11.3-1 (2016/11/25)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Refactored the min_version_notice() method and moved variables to config class.

**Version 1.11.2-1 (2016/11/17)**

* *New Features*
	* None
* *Improvements*
	* Added date based archive URLs when clearing the post cache.
	* Added a "Reference URL" link to notice messages when generating JSON-LD for archive pages (users and terms).
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.11.1-1 (2016/11/12)**

* *New Features*
	* None
* *Improvements*
	* Added an "Item Type for Blog Home Page" option for non-static home pages.
	* Added a new 'hasPart' property with a list of posts for the Schema Blog, CollectionPage, ProfilePage, and SearchResultsPage markup.
* *Bugfixes*
	* None
* *Developer Notes*
	* Added a new WpssoJsonSchema add_parts_data() method to fetch posts for the current term / user archive page.
	* Renamed the WpssoSchema get_head_item_type() method to get_mod_schema_type().
	* Renamed the WpssoSchema get_item_type_context() method to get_schema_type_context().
	* Added a hook to the 'wpsso_post_cache_transients' filter in order to clear the term transient cache on post update. 
	* Updated 'wpsso_post_cache_transients' filter arguments for WPSSO v3.37.2-1.

== Upgrade Notice ==

= 1.11.6-1 =

(2017/01/08) Added a 'plugins_loaded' action hook to load the plugin text domain.

= 1.11.5-1 =

(2016/12/12) Added a check for maximum posts per page in WpssoJsonSchema add_parts_data() to limit the number of posts included. Added a potentialAction property and filter to the Schema for https://schema.org/Thing.

= 1.11.4-3 =

(2016/12/05) Added the product variation URL to notices when checking for product variation images.

