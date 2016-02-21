=== WPSSO Schema JSON-LD - Complete Schema / Rich Snippet Markup for Google and Pinterest ===
Plugin Name: WPSSO Schema JSON-LD (WPSSO JSON)
Plugin Slug: wpsso-schema-json-ld
Text Domain: wpsso-schema-json-ld
Domain Path: /languages
Contributors: jsmoriss
Donate Link: https://wpsso.com/
Tags: wpsso, schema, structured data, json, json-ld, ld+json, rich snippets, article, product, pinterest, google
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Requires At Least: 3.1
Tested Up To: 4.4.2
Stable Tag: 1.0.1

WPSSO extension to add complete Schema JSON-LD markup (BlogPosting, Article, Place, Product, etc.) for Google and Pinterest.

== Description ==

<p><img src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png" width="256" height="256" style="width:33%;min-width:128px;max-width:256px;float:left;margin:0 40px 20px 0;" /><strong>Are you looking for better <em>Schema</em>, <em>Structured Data</em> and <em>Rich Snippet</em> markup?</strong></p>

<p>WPSSO Schema JSON-LD (WPSSO JSON) works in conjunction with the <a href="https://wordpress.org/plugins/wpsso/">WordPress Social Sharing Optimization (WPSSO)</a> plugin, extending its features with additional options to include complete Schema JSON-LD / Rich Snippet markup in webpage headers for Google Search, Pinterest, and other social / search engine crawlers.</p>

= Available in Multiple Languages =

* English (US)
* French (France)
* More to come...

= Quick List of Features =

**WPSSO JSON Free / Basic Features**

Adds Google / Schema JSON-LD markup:

*Schema type [schema.org/BlogPosting](http://schema.org/BlogPosting) and [schema.org/WebPage](http://schema.org/WebPage)*

> * URL
> * Name
> * Description
> * DatePublished
> * DateModified
> * Author as [schema.org/Person](http://schema.org/Person)
> 	* Author URL
> 	* Author Name
> 	* Author Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Author Image URL
> 		* Author Image Width
> 		* Author Image Height
> * Post / Page Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height

**WPSSO JSON Pro / Power-User Features**

Adds Google / Schema JSON-LD markup:

*Schema type [schema.org/Article](http://schema.org/Article)*

* URL
* Name
* Headline
* Description
* DatePublished
* DateModified
* Author as [schema.org/Person](http://schema.org/Person)
	* Author URL
	* Author Name
	* Author Description
	* Author Image as [schema.org/ImageObject](http://schema.org/ImageObject)
		* Author Image URL
		* Author Image Width
		* Author Image Height
* Publisher as [schema.org/Organization](http://schema.org/Organization)
	* URL
	* Name
	* Description
	* Logo Image as [schema.org/ImageObject](http://schema.org/ImageObject)
		* Image URL
		* Image Width
		* Image Height
* Article Image as [schema.org/ImageObject](http://schema.org/ImageObject)
	* Image URL
	* Image Width
	* Image Height

*Schema type [schema.org/Organization](http://schema.org/Organization)*

* URL
* Name
* Description
* Logo Image as [schema.org/ImageObject](http://schema.org/ImageObject)
	* Image URL
	* Image Width
	* Image Height

*Schema type [schema.org/Person](http://schema.org/Person)*

* URL
* Name
* Description
* Image as [schema.org/ImageObject](http://schema.org/ImageObject)
	* Image URL
	* Image Width
	* Image Height

*Schema type [schema.org/Place](http://schema.org/Place) ([WPSSO PLM](https://wordpress.org/plugins/wpsso-plm/) extension required)*

* URL
* Name
* Description
* Geo Location as [schema.org/GeoCoordinates](http://schema.org/GeoCoordinates)
	* elevation
	* latitude
	* longitude
* Place Address as [schema.org/PostalAddress](http://schema.org/PostalAddress)
	* streetAddress
	* postOfficeBoxNumber
	* addressLocality
	* addressRegion
	* postalCode
	* addressCountry
* Place Image as [schema.org/ImageObject](http://schema.org/ImageObject)
	* Image URL
	* Image Width
	* Image Height

*Schema type [schema.org/Product](http://schema.org/Product) (eCommerce plugin usually mandatory)*

* URL
* Name
* Description
* SKU
* Offers as [schema.org/Offer](http://schema.org/Offer)
	* Availability
	* Price
	* PriceCurrency
* Rating as [schema.org/AggregateRating](http://schema.org/AggregateRating)
	* RatingValue
	* RatingCount
	* WorstRating
	* BestRating
	* ReviewCount
* Product Image(s) as [schema.org/ImageObject](http://schema.org/ImageObject)
	* Image URL
	* Image Width
	* Image Height

*Schema type [schema.org/WebSite](http://schema.org/WebSite)*

* URL
* Name
* Description
* PotentialAction as [schema.org/SearchAction](http://schema.org/SearchAction)
	* Target
	* Query-Input

= Uses the WPSSO Framework =

The WordPress Social Sharing Optimization (WPSSO) plugin is required to use the WPSSO JSON extension. You can use the Free version of WPSSO JSON with *both* the Free and Pro versions of WPSSO, but [WPSSO JSON Pro](http://wpsso.com/extend/plugins/wpsso-schema-json-ld/) requires the use of the [WPSSO Pro](http://wpsso.com/extend/plugins/wpsso/) plugin as well. [Purchase the WPSSO Schema JSON-LD (WPSSO JSON) Pro extension](http://wpsso.com/extend/plugins/wpsso-schema-json-ld/) (includes a *No Risk 30 Day Refund Policy*).

== Installation ==

= Install and Uninstall =

* [Install the Plugin](http://wpsso.com/codex/plugins/wpsso-schema-json-ld/installation/install-the-plugin/)
* [Uninstall the Plugin](http://wpsso.com/codex/plugins/wpsso-schema-json-ld/installation/uninstall-the-plugin/)

== Frequently Asked Questions ==

= Frequently Asked Questions =

== Other Notes ==

= Additional Documentation =

== Screenshots ==

== Changelog ==

= Free / Basic Version Repository =

* [GitHub](https://github.com/SurniaUlula/wpsso-schema-json-ld)
* [WordPress.org](https://wordpress.org/plugins/wpsso-schema-json-ld/developers/)

= Changelog / Release Notes =

**Version 1.0.2 (2016/02/21)**

2016/02/21 - Maintenance release for WordPress Social Sharing Optimization (WPSSO) v3.25.0.

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* None

== Upgrade Notice ==

= 1.0.2 =

2016/02/21 - Maintenance release for WordPress Social Sharing Optimization (WPSSO) v3.25.0.

