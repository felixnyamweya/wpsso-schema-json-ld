=== WPSSO Schema JSON-LD Markup - Schema / Structured Data / Rich Snippet / SEO Markup ===
Plugin Name: WPSSO Schema JSON-LD Markup
Plugin Slug: wpsso-schema-json-ld
Text Domain: wpsso-schema-json-ld
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-schema-json-ld/assets/
Tags: article seo, local seo, news seo, video seo, local business, json, json-ld, ld+json, schema, structured data, seo, pinterest, aggregate rating, rating, review, recipe, event, product, video schema, knowledge graph, search engine optimization, rich snippets
Contributors: jsmoriss
Requires At Least: 3.7
Tested Up To: 4.8
Stable Tag: 1.14.3

WPSSO extension to add Schema JSON-LD / SEO markup for Articles, Events, Local Business, Products, Recipes, Reviews + many more.

== Description ==

<img class="readme-icon" src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png">

<p><strong>Comprehensive and <em>accurate</em> Schema / Structured Data / SEO markup for Google</strong> &mdash; including images, videos, organization (publisher), person (author and co-authors), product variations, product ratings, recipe details, event information, collection pages, and much more.</p>

<p><strong>Customized Schema markup and optimization features for Pinterest</strong> &mdash; Pinterest does not (currently) read the preferred JSON-LD format. WPSSO and its WPSSO JSON extension include special provisions to provide unique Schema meta tags for Pinterest, along with methods to avoid conflicts between incompatible Pinterest and Facebook Open Graph meta tags.</p>

<p><strong>Select a different Schema than <a href="https://schema.org/BlogPosting">BlogPosting</a> for <a href="https://wordpress.org/plugins/amp/">AMP</a> webpages.</strong></p>

<p><strong>Adds comments and threaded replies to Schema CreativeWork markup and its sub-types</strong> (Article, BlogPosting, WebPage, etc.) for better Google SEO ranking.</p>

<p><strong>Include JSON-LD markup for WooCommerce products</strong> &mdash; including images, variations (weight, size, color, etc), reviews, ratings, and more (Pro version).</p>

<blockquote>
<p><strong>Prerequisite</strong> &mdash; WPSSO Schema JSON-LD Markup is an extension for the <a href="https://wordpress.org/plugins/wpsso/">WPSSO</a> plugin, which <em>automatically</em> generates complete and accurate meta tags + Schema markup from your content for social media optimization (SMO) and SEO.</p>
</blockquote>

= Quick List of Features =

**WPSSO JSON Free / Standard Features**

* Extends the features of WPSSO Free or Pro.
* Includes support for Automattic's [Accelerated Mobile Pages (AMP)](https://wordpress.org/plugins/amp/) plugin.
* Includes contributor markup for [Co-Authors Plus](https://wordpress.org/plugins/co-authors-plus/) authors and guest authors (requires [WPSSO Pro](https://wpsso.com/) to retrieve co-author information).
* Adds an additional "Schema Markup" settings page to the SSO menu:
	* Website Alternate Name
	* Organization Logo Image URL
	* Organization Banner (600x60) URL
	* Maximum Images to Include
	* Schema Image Dimensions
	* Maximum Description Length
	* Author / Person Name Format
	* Item Type for Blog Front Page
	* Item Type for Static Front Page
	* Item Type for Archive Page
	* Item Type for User / Author Page
	* Item Type for Search Results Page
	* Item Type by Post Type (for Posts, Pages, Media, and custom post types).
	* Default Reviewed Item Type
* Adds Schema / Structured Data / SEO JSON-LD markup for:
	* Schema Type [schema.org/BlogPosting](https://schema.org/BlogPosting)
	* Schema Type [schema.org/WebPage](https://schema.org/WebPage)

<img class="readme-example" src="https://surniaulula.github.io/wpsso-schema-json-ld/images/social/wpsso-json-pinterest-recipe-pin-zoomed.png">

= Quick List of Features (Continued) =

**WPSSO JSON Pro / Additional Features**

* Extends the features of WPSSO Pro (requires a licensed WPSSO Pro plugin).
* WPSSO Pro already integrates with many 3rd party plugins and services for additional image, video, e-commerce product details, SEO settings, etc. The following modules are included with the Pro version of WPSSO, and are automatically loaded if/when the supported plugins and/or services are required.
	* **WPSSO Pro Integrated 3rd Party Plugins**
		* All in One SEO Pack
		* bbPress
		* BuddyPress (including Group Forum Topics)
		* Co-Authors Plus (including Guest Authors)
		* Easy Digital Downloads
		* HeadSpace2 SEO
		* NextGEN Gallery
		* MarketPress - WordPress eCommerce
		* Polylang
		* rtMedia for WordPress, BuddyPress and bbPress
		* The Events Calendar
		* The SEO Framework
		* WooCommerce (version 1 and 2)
		* WP eCommerce
		* WordPress REST API (version 2)
		* Yoast SEO (aka WordPress SEO)
		* Yotpo Social Reviews for WooCommerce
	* **WPSSO Pro Integrated Service APIs**
		* Bitly
		* Facebook Embedded Videos
		* Google URL Shortener
		* Gravatar (Author Image)
		* Ow.ly
		* Slideshare Presentations
		* TinyURL
		* Vimeo Videos
		* Wistia Videos
		* Your Own URL Shortener (YOURLS)
		* YouTube Videos and Playlists
* WPSSO JSON Pro includes additional 3rd party integration modules to extend its Schema markup:
	* **WPSSO JSON Pro Integrated 3rd Party Plugins**
		* WP Product Review
		* WP Recipe Maker
		* WP Ultimate Recipe

<img class="readme-example" src="https://surniaulula.github.io/wpsso-schema-json-ld/images/settings/wpsso-json-social-settings.png">

* Adds additional custom options to the Social Settings metabox, displayed or hidden based on the Schema Item Type selected:
	* Schema Item Name (aka Title)
	* Schema Description
	* Main Entity of Page
	* Schema Item Type
	* Additional Type URL
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
	* Recipe Instructions 
	* Recipe Nutrition Information per Serving 
		* Serving Size
		* Calories
		* Protein
		* Fiber
		* Carbohydrates
		* Sugar
		* Sodium
		* Fat
		* Saturated Fat
		* Unsaturated Fat
		* Trans Fat
		* Cholesterol
	* (Review) Subject Type 
	* (Review) Subject Name 
	* (Review) Subject Webpage URL 
	* (Review) Subject Image URL 
	* Review Rating 

<img class="readme-example landscape" src="https://surniaulula.github.io/wpsso-schema-json-ld/images/social/google-testing-tool-results-tech-article.png">

* Adds Schema / Structured Data / SEO JSON-LD markup for:
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
		* Schema Type [schema.org/Game](https://schema.org/Game)
		* Schema Type [schema.org/Movie](https://schema.org/Movie)
		* Schema Type [schema.org/Painting](https://schema.org/Painting)
		* Schema Type [schema.org/Photograph](https://schema.org/Photograph)
		* Schema Type [schema.org/Recipe](https://schema.org/Recipe)
		* Schema Type [schema.org/Review](https://schema.org/Review)
		* Schema Type [schema.org/Sculpture](https://schema.org/Sculpture)
		* Schema Type [schema.org/SoftwareApplication](https://schema.org/SoftwareApplication)
		* Schema Type [schema.org/VisualArtwork](https://schema.org/VisualArtwork)
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
			* Schema Type [schema.org/HighSchool](https://schema.org/HighSchool)
			* Schema Type [schema.org/MiddleSchool](https://schema.org/MiddleSchool)
			* Schema Type [schema.org/Preschool](https://schema.org/Preschool)
			* Schema Type [schema.org/School](https://schema.org/School)
		* Schema Type [schema.org/GovernmentOrganization](https://schema.org/GovernmentOrganization)
		* Schema Type [schema.org/MedicalOrganization](https://schema.org/MedicalOrganization)
			* Schema Type [schema.org/Pharmacy](https://schema.org/Pharmacy)
			* Schema Type [schema.org/Physician](https://schema.org/Physician)
		* Schema Type [schema.org/NGO](https://schema.org/NGO)
		* Schema Type [schema.org/Organization](https://schema.org/Organization)
		* Schema Type [schema.org/PerformingGroup](https://schema.org/PerformingGroup)
			* Schema Type [schema.org/DanceGroup](https://schema.org/DanceGroup)
			* Schema Type [schema.org/MusicGroup](https://schema.org/MusicGroup)
			* Schema Type [schema.org/PerformingGroup](https://schema.org/PerformingGroup)
			* Schema Type [schema.org/TheaterGroup](https://schema.org/TheaterGroup)
		* Schema Type [schema.org/SportsOrganization](https://schema.org/SportsOrganization)
			* Schema Type [schema.org/SportsTeam](https://schema.org/SportsTeam)
	* Schema Type [schema.org/Person](https://schema.org/Person)
	* Schema Type [schema.org/Place](https://schema.org/Place)
		* Schema Type [schema.org/AdministrativeArea](https://schema.org/AdministrativeArea)
		* Schema Type [schema.org/CivicStructure](https://schema.org/CivicStructure)
			* Schema Type [schema.org/Airport](https://schema.org/Airport)
			* Schema Type [schema.org/Aquarium](https://schema.org/Aquarium)
			* Schema Type [schema.org/Beach](https://schema.org/Beach)
			* Schema Type [schema.org/Bridge](https://schema.org/Bridge)
			* Schema Type [schema.org/BusStation](https://schema.org/BusStation)
			* Schema Type [schema.org/BusStop](https://schema.org/BusStop)
			* Schema Type [schema.org/Cemetary](https://schema.org/Cemetary)
			* Schema Type [schema.org/Crematorium](https://schema.org/Crematorium)
			* Schema Type [schema.org/EventVenu](https://schema.org/EventVenu)
			* Schema Type [schema.org/Park](https://schema.org/Park)
			* Schema Type [schema.org/ParkingFacility](https://schema.org/ParkingFacility)
			* Schema Type [schema.org/PerformingArtsTheater](https://schema.org/PerformingArtsTheater)
			* Schema Type [schema.org/PlaceOfWorship](https://schema.org/PlaceOfWorship)
			* Schema Type [schema.org/Playground](https://schema.org/Playground)
			* Schema Type [schema.org/RVPark](https://schema.org/RVPark)
			* Schema Type [schema.org/SubwayStation](https://schema.org/SubwayStation)
			* Schema Type [schema.org/TaxiStand](https://schema.org/TaxiStand)
			* Schema Type [schema.org/TrainStation](https://schema.org/TrainStation)
			* Schema Type [schema.org/Zoo](https://schema.org/Zoo)
		* Schema Type [schema.org/Landform](https://schema.org/Landform)
		* Schema Type [schema.org/LandmarksOrHistoricalBuildings](https://schema.org/LandmarksOrHistoricalBuildings)
		* Schema Type [schema.org/LocalBusiness](https://schema.org/LocalBusiness)
			* Schema Type [schema.org/AnimalShelter](https://schema.org/AnimalShelter)
			* Schema Type [schema.org/AutomotiveBusiness](https://schema.org/AutomotiveBusiness)
				* Schema Type [schema.org/AutoBodyShop](https://schema.org/AutoBodyShop)
				* Schema Type [schema.org/AutoDealer](https://schema.org/AutoDealer)
				* Schema Type [schema.org/AutoPartsStore](https://schema.org/AutoPartsStore)
				* Schema Type [schema.org/AutoRental](https://schema.org/AutoRental)
				* Schema Type [schema.org/AutoRepair](https://schema.org/AutoRepair)
				* Schema Type [schema.org/AutoWash](https://schema.org/AutoWash)
				* Schema Type [schema.org/GasStation](https://schema.org/GasStation)
				* Schema Type [schema.org/MotorcycleDealer](https://schema.org/MotorcycleDealer)
				* Schema Type [schema.org/MotorcycleRepair](https://schema.org/MotorcycleRepair)
			* Schema Type [schema.org/ChildCare](https://schema.org/ChildCare)
			* Schema Type [schema.org/Dentist](https://schema.org/Dentist)
			* Schema Type [schema.org/DryCleaningOrLaundry](https://schema.org/DryCleaningOrLaundry)
			* Schema Type [schema.org/EmergencyService](https://schema.org/EmergencyService)
				* Schema Type [schema.org/FireStation](https://schema.org/FireStation)
				* Schema Type [schema.org/Hospital](https://schema.org/Hospital)
				* Schema Type [schema.org/PoliceStation](https://schema.org/PoliceStation)
			* Schema Type [schema.org/EmploymentAgency](https://schema.org/EmploymentAgency)
			* Schema Type [schema.org/EntertainmentBusiness](https://schema.org/EntertainmentBusiness)
				* Schema Type [schema.org/MovieTheatre](https://schema.org/MovieTheatre)
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
				* Schema Type [schema.org/Electrician](https://schema.org/Electrician)
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
				* Schema Type [schema.org/Campground](https://schema.org/Campground)
			* Schema Type [schema.org/ProfessionalService](https://schema.org/ProfessionalService)
			* Schema Type [schema.org/RadioStation](https://schema.org/RadioStation)
			* Schema Type [schema.org/RealEstateAgent](https://schema.org/RealEstateAgent)
			* Schema Type [schema.org/RecyclingCenter](https://schema.org/RecyclingCenter)
			* Schema Type [schema.org/SelfStorage](https://schema.org/SelfStorage)
			* Schema Type [schema.org/ShoppingCenter](https://schema.org/ShoppingCenter)
			* Schema Type [schema.org/SportsActivityLocation](https://schema.org/SportsActivityLocation)
				* Schema Type [schema.org/StadiumOrArena](https://schema.org/StadiumOrArena)
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
		* Schema Type [auto.schema.org/Vehicle](https://auto.schema.org/Vehicle)
			* Schema Type [auto.schema.org/BusOrCoach](https://auto.schema.org/BusOrCoach)
			* Schema Type [auto.schema.org/Car](https://auto.schema.org/Car)
			* Schema Type [auto.schema.org/Motorcycle](https://auto.schema.org/Motorcycle)
			* Schema Type [auto.schema.org/MotorizedBicycle](https://auto.schema.org/MotorizedBicycle)
			* Schema Type [auto.schema.org/Vehicle](https://auto.schema.org/Vehicle)

= Markup Examples =

* [Markup Example for a Restaurant](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-restaurant/) using the WPSSO PLM extension to manage the Place / Location information (address, geo coordinates, business hours – daily and seasonal, restaurant menu URL, and accepts reservation values).
* [Markup Example for a Tech Article](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-tech-article/) published on surniaulula.com.
* [Markup Example for a WooCommerce Product](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-woocommerce-product/), including its name, description, images, videos, sku, price, condition, availability, ratings, colors, category, width, height, weight, all product variations, and much more.

= Extends the WPSSO Plugin =

<p>The <a href="https://wordpress.org/plugins/wpsso-schema-json-ld/">WPSSO JSON Free extension</a> works with the WPSSO Free or Pro plugin. The <a href="https://wpsso.com/extend/plugins/wpsso-schema-json-ld/?utm_source=wpssojson-readme-extends">WPSSO JSON Pro extension</a> (along with all WPSSO Pro extensions) requires the <a href="https://wpsso.com/extend/plugins/wpsso/?utm_source=wpssojson-readme-extends">WPSSO Pro plugin</a> as well.</p>

[Purchase the WPSSO Schema JSON-LD Markup Pro extension here](https://wpsso.com/extend/plugins/wpsso-schema-json-ld/?utm_source=wpssojson-readme-purchase) (all purchases include a *No Risk 30 Day Refund Policy*).

== Installation ==

= Install and Uninstall =

* [Install the Plugin (Free and Pro version)](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/installation/install-the-plugin/)
* [Uninstall the Plugin](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/installation/uninstall-the-plugin/)

== Frequently Asked Questions ==

= Frequently Asked Questions =

* None

== Other Notes ==

= Additional Documentation =

* [Developer Resources](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/)
	* [Filters](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/)
		* [Filter Examples](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/)
			* [Assign a Custom Field Value to a Schema Property](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/assign-a-custom-field-value-to-a-schema-property/)
			* [Modify the aggregateRating Property](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/modify-the-aggregaterating-property/)
			* [Modify the VideoObject Name and Description](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/modify-the-videoobject-name-and-description/)
		* [Filters by Name](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/by-name/)

== Screenshots ==

01. WPSSO JSON Schema Markup settings page includes options for site name, alternate name, logo, banner, image size, and Schema types for posts, pages, custom post types, etc.
02. WPSSO JSON options in the Social Settings metabox for the Schema type https://schema.org/Article (Pro version).
03. WPSSO JSON options in the Social Settings metabox for the Schema type https://schema.org/Recipe (Pro version).
04. WPSSO JSON options in the Social Settings metabox for the Schema type https://schema.org/Review (Pro version).
05. WPSSO JSON example for the Schema type https://schema.org/Recipe on Pinterest (Pro version).
06. WPSSO JSON example for the Schema type https://schema.org/TechArticle in Google's Structured Data Testing Tool (Pro version).

== Changelog ==

= Free / Basic Version Repository =

* [GitHub](https://surniaulula.github.io/wpsso-schema-json-ld/)
* [WordPress.org](https://wordpress.org/plugins/wpsso-schema-json-ld/developers/)

= Version Numbering =

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes / re-writes or incompatible API changes.
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).

= Changelog / Release Notes =

**Version 1.14.3 (2017/06/07)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Added a return value check when getting the author data for the Schema review property.
* *Developer Notes*
	* None

**Version 1.14.2 (2017/06/06)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Fixed an error from the Google validator by removing the 'video' property for Schema Products.
* *Developer Notes*
	* None

**Version 1.14.1 (2017/05/29)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Fixed detection of weight, width, height attributes for product variations (Pro version).
* *Developer Notes*
	* None

**Version 1.14.0 (2017/05/15)**

* *New Features*
	* None
* *Improvements*
	* Added a new "Additional Type URL" option in the Social Settings metabox for posts, pages, and custom post types.
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.13.9 (2017/04/30)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Fixed inheritance of Schema sub-types when adding Organization markup.
	* Fixed check of variable product for WooCommerce v3.x (Pro version).
* *Developer Notes*
	* Code refactoring to rename the $is_avail array to $avail for WPSSO v3.42.0.
	* Added a Schema type inheritance feature when adding markup for single Schema elements.

**Version 1.13.8 (2017/04/22)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Fixed a null value for the default publisher ID in Schema WebPage and BlogPosting markup.
* *Developer Notes*
	* Renamed the SucomUtil crawler_name() calls to get_crawler_name() for WPSSO v3.41.0.

**Version 1.13.7 (2017/04/16)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Refactored the plugin init filters and moved/renamed the registration boolean from `is_avail[$name]` to `is_avail['p_ext'][$name]`.

**Version 1.13.6 (2017/04/08)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Minor revision to move URLs in the extension config to the main WPSSO plugin config.
	* Dropped the package number from the production version string.

**Version 1.13.5-1 (2017/04/05)**

* *New Features*
	* None
* *Improvements*
	* Updated the plugin icon images and the documentation URLs.
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.13.4-1 (2017/03/31)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Added a 'wpsso_og_add_mt_reviews' filter to return true for WPSSO v3.40.7-1.

**Version 1.13.3-1 (2017/03/25)**

* *New Features*
	* None
* *Improvements*
	* Added a new "Organization" selector in the Social Settings metabox when the Schema Item Type is an Organization.
	* Added the organization filter to the local business filter (local businesses are both places and organizations).
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.13.2-1 (2017/03/15)**

* *New Features*
	* None
* *Improvements*
	* Added comments / replies for each review to the Schema Review markup.
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.13.1-1 (2017/03/10)**

* *New Features*
	* None
* *Improvements*
	* Added support for the "itemCondition" property in the https://schema.org/Product markup (Pro version).
* *Bugfixes*
	* Fixed a variable name conflict in the WP Recipe Maker integration module (Pro version).
* *Developer Notes*
	* None

**Version 1.13.0-1 (2017/03/06)**

* *New Features*
	* Added support for the WP Recipe Maker plugin (Pro version).
	* Added support for the WP Ultimate Recipe plugin (Pro version).
	* Added two new Recipe Information options (Pro version):
		* Recipe Course
		* Recipe Cuisine
	* Added a Recipe Instructions list in the Social Settings metabox (Pro version).
	* Added Nutrition Information per Serving options for the Schema Recipe type (Pro version):
		* Serving Size
		* Calories
		* Protein
		* Fiber
		* Carbohydrates
		* Sugar
		* Sodium
		* Fat
		* Saturated Fat
		* Unsaturated Fat
		* Trans Fat
		* Cholesterol
* *Improvements*
	* None
* *Bugfixes*
	* Fixed an incorrect textdomain value for a few option labels.
* *Developer Notes*
	* Refactored the WP Product Review integration module to standardize the code / methods with the two new recipe modules included in this version.

**Version 1.12.3-1 (2017/02/26)**

* *New Features*
	* Added support for the WP Product Review plugin (Pro version).
* *Improvements*
	* Added new options for the Schema type Review (Pro version):
		* (Review) Subject Name
		* (Review) Subject Image URL
* *Bugfixes*
	* None
* *Developer Notes*
	* None

**Version 1.12.2-1 (2017/02/19)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Renamed a few site related option keys for WPSSO v3.39.9-1:
		* 'og_site_name' =&gt; 'site_name'
		* 'og_site_description' =&gt; 'site_desc'

**Version 1.12.1-1 (2017/02/13)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* Removed the unsupported video property from the Schema Organization markup.
* *Developer Notes*
	* None

**Version 1.12.0-1 (2017/02/08)**

* *New Features*
	* Added comments and replies to the Schema CreativeWork markup and its sub-types (Article, BlogPosting, WebPage, etc.).
* *Improvements*
	* Added a new Schema review property module to include WooCommerce product reviews (Pro version).
* *Bugfixes*
	* None
* *Developer Notes*
	* Added new add_comment_list_data() and add_single_comment_data() methods in the WpssoJsonSchema class.
	* Added a new WpssoJsonProPropReview class in lib/pro/prop/review.php (Pro version).

== Upgrade Notice ==

= 1.14.3 =

(2017/06/07) Added a return value check when getting the author data for the Schema review property.

= 1.14.2 =

(2017/06/06) Fixed an error from the Google validator by removing the 'video' property for Schema Products.

= 1.14.1 =

(2017/05/29) Fixed detection of weight, width, height attributes for product variations (Pro version).

= 1.14.0 =

(2017/05/15) Added a new "Additional Type URL" option in the Social Settings metabox for posts, pages, and custom post types.

= 1.13.9 =

(2017/04/30) Fixed inheritance of Schema sub-types when adding Organization markup. Code refactoring to rename the $is_avail array.

= 1.13.8 =

(2017/04/22) Renamed the SucomUtil crawler_name() calls to get_crawler_name() for WPSSO v3.41.0. Fixed a null value for the default publisher ID in Schema WebPage and BlogPosting markup.

= 1.13.7 =

(2017/04/16) Refactored the plugin init filters and moved/renamed the registration boolean.

= 1.13.6 =

(2017/04/08) Minor revision to move URLs in the extension config to the main WPSSO plugin config.

= 1.13.5-1 =

(2017/04/05) Updated the plugin icon images and the documentation URLs.

= 1.13.4-1 =

(2017/03/31) Added a 'wpsso_og_add_mt_reviews' filter to return true for WPSSO v3.40.7-1.

= 1.13.3-1 =

(2017/03/25) Added a new "Organization" selector in the Social Settings metabox when the Schema Item Type is an Organization.

= 1.13.2-1 =

(2017/03/15) Added comments / replies for each review to the Schema Review markup.

= 1.13.1-1 =

(2017/03/10) Added support for the "itemCondition" property in the https://schema.org/Product markup (Pro version).

= 1.13.0-1 =

(2017/03/06) Added several new recipe options in the Social Settings metabox (Pro version). Added support for the WP Recipe Maker and WP Ultimate Recipe plugins (Pro version).

= 1.12.3-1 =

(2017/02/26) Added new options for the Schema type Review (Pro version). Added support for the WP Product Review plugin (Pro version).

= 1.12.2-1 =

(2017/02/19) Renamed a few site related option keys for WPSSO v3.39.9-1.

= 1.12.1-1 =

(2017/02/13) Removed the unsupported video property from the Schema Organization markup.

= 1.12.0-1 =

(2017/02/08) Added comments and replies to the Schema CreativeWork markup and its sub-types. Added a new Schema review property module to include WooCommerce product reviews (Pro version).

