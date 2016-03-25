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
Tested Up To: 4.5
Stable Tag: 1.5.0

WPSSO extension to add complete Schema JSON-LD markup (BlogPosting, Article, Place, Product, etc.) for Google and Pinterest.

== Description ==

<p><img src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png" width="256" height="256" style="width:33%;min-width:128px;max-width:256px;float:left;margin:0 40px 20px 0;" /><strong>Are you looking for better <em>Schema</em>, <em>Structured Data</em> and <em>Rich Snippet</em> markup?</strong></p>

<p><strong>Are you missing or does your theme offer incorrect / incomplete Schema markup?</strong></p>

<p><strong>Are you getting warnings in Google's <a href="https://developers.google.com/structured-data/testing-tool/">Structured Data Testing Tool</a>?</strong></p>

<p>WPSSO Schema JSON-LD (WPSSO JSON) <a href="http://json-ld.org/">includes Schema JSON-LD markup as a stand-alone block of information</a> &mdash; independant of any Schema markup in your theme templates.</p>

<p>WPSSO Schema JSON-LD (WPSSO JSON) works in conjunction with the <a href="https://wordpress.org/plugins/wpsso/">WordPress Social Sharing Optimization (WPSSO)</a> plugin, extending its features and options to include <em>complete</em> Schema JSON-LD / Rich Snippet markup in webpage headers for Google Search, Pinterest, and others.</p>

= Available in Multiple Languages =

* English (US)
* French (France)
* More to come...

= Quick List of Features =

**WPSSO JSON Free / Basic Features**

Includes support for Automattic's [Accelerated Mobile Pages (AMP)](https://wordpress.org/plugins/amp/) plugin.

Adds Google / Schema JSON-LD markup:

* Schema Type [schema.org/BlogPosting](http://schema.org/BlogPosting)
* Schema Type [schema.org/WebPage](http://schema.org/WebPage)

> * URL
> * Name (Title)
> * Description
> * DatePublished
> * DateModified
> * Author as [schema.org/Person](http://schema.org/Person)
> 	* Author URL from Profile
> 	* Author Name
> 	* Author Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Author Image URL
> 		* Author Image Width
> 		* Author Image Height
> * Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height
> * Video as [schema.org/VideoObject](http://schema.org/VideoObject) ([WPSSO Pro](https://wpsso.com/extend/plugins/wpsso/) required)
> 	* Video URL
> 	* Video Name (Title)
> 	* Video Description
> 	* Video FileFormat
> 	* Video Width
> 	* Video Height
> 	* Video Duration
> 	* Video UploadDate
> 	* Video ThumbnailUril
> 	* Video EmbedUrl
> 	* Video Thumbnail as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Thumbnail URL
> 		* Thumbnail Width
> 		* Thumbnail Height

**WPSSO JSON Pro / Power-User Features**

Adds Google / Schema JSON-LD markup:

* Schema Type [schema.org/Article](http://schema.org/Article)
* Schema Type [schema.org/NewsArticle](http://schema.org/NewsArticle)
* Schema Type [schema.org/TechArticle](http://schema.org/TechArticle)
 
> * URL
> * Name (Title)
> * Headline
> * Description
> * DatePublished
> * DateModified
> * Author as [schema.org/Person](http://schema.org/Person)
> 	* Author URL from Profile
> 	* Author Name
> 	* Author Description
> 	* Author Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Author Image URL
> 		* Author Image Width
> 		* Author Image Height
> * Publisher as [schema.org/Organization](http://schema.org/Organization)
> 	* URL
> 	* Name
> 	* Description
> 	* Logo Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Image URL
> 		* Image Width
> 		* Image Height
> * Article Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height
> * Article Video as [schema.org/VideoObject](http://schema.org/VideoObject) ([WPSSO Pro](https://wpsso.com/extend/plugins/wpsso/) required)
> 	* Video URL
> 	* Video Name (Title)
> 	* Video Description
> 	* Video FileFormat
> 	* Video Width
> 	* Video Height
> 	* Video Duration
> 	* Video UploadDate
> 	* Video ThumbnailUril
> 	* Video EmbedUrl
> 	* Video Thumbnail as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Thumbnail URL
> 		* Thumbnail Width
> 		* Thumbnail Height

* Schema Type [schema.org/Organization](http://schema.org/Organization)
 
> * URL
> * Name
> * Description
> * Logo Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height

* Schema Type [schema.org/Person](http://schema.org/Person)
 
> * URL from Profile
> * Name
> * Description
> * Person Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height

* Schema Type [schema.org/Place](http://schema.org/Place) ([WPSSO PLM](https://wordpress.org/plugins/wpsso-plm/) extension required)

> * URL
> * Name
> * Description
> * Geo Location as [schema.org/GeoCoordinates](http://schema.org/GeoCoordinates)
> 	* elevation
> 	* latitude
> 	* longitude
> * Place Address as [schema.org/PostalAddress](http://schema.org/PostalAddress)
> 	* streetAddress
> 	* postOfficeBoxNumber
> 	* addressLocality
> 	* addressRegion
> 	* postalCode
> 	* addressCountry
> * Place Image as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height

* Schema Type [schema.org/Product](http://schema.org/Product) (eCommerce plugin usually required)
 
> * URL
> * Name
> * Description
> * SKU
> * Offers as [schema.org/Offer](http://schema.org/Offer)
> 	* Availability
> 	* Price
> 	* PriceCurrency
> * Rating as [schema.org/AggregateRating](http://schema.org/AggregateRating)
> 	* RatingValue
> 	* RatingCount
> 	* WorstRating
> 	* BestRating
> 	* ReviewCount
> * Product Image(s) as [schema.org/ImageObject](http://schema.org/ImageObject)
> 	* Image URL
> 	* Image Width
> 	* Image Height
> * Product Video as [schema.org/VideoObject](http://schema.org/VideoObject) ([WPSSO Pro](https://wpsso.com/extend/plugins/wpsso/) required)
> 	* Video URL
> 	* Video Name
> 	* Video Description
> 	* Video FileFormat
> 	* Video Width
> 	* Video Height
> 	* Video Duration
> 	* Video UploadDate
> 	* Video ThumbnailUril
> 	* Video EmbedUrl
> 	* Video Thumbnail as [schema.org/ImageObject](http://schema.org/ImageObject)
> 		* Thumbnail URL
> 		* Thumbnail Width
> 		* Thumbnail Height

* Schema Type [schema.org/WebSite](http://schema.org/WebSite)
 
> * URL
> * Name
> * Description
> * PotentialAction as [schema.org/SearchAction](http://schema.org/SearchAction)
> 	* Target
> 	* Query-Input

= Extends the WPSSO Social Plugin =

The WordPress Social Sharing Optimization (WPSSO) plugin is required to use the WPSSO JSON extension.

You can use the Free version of WPSSO JSON with *both* the Free and Pro versions of WPSSO, but the [WPSSO JSON Pro](http://wpsso.com/extend/plugins/wpsso-schema-json-ld/) version requires the use of the [WPSSO Pro](http://wpsso.com/extend/plugins/wpsso/) version as well (for its Video and Product information, along with more advanced features).

Purchase the [WPSSO Schema JSON-LD (WPSSO JSON) Pro](http://wpsso.com/extend/plugins/wpsso-schema-json-ld/) extension (includes a *No Risk 30 Day Refund Policy*).

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

<blockquote id="changelog_top_info">
<p>New versions of the plugin are released approximately every week (more or less). 
New features are added, tested, and released incrementally, instead of grouping them together in a major version release. 
When minor bugs fixes and/or code improvements are applied, new versions are also released.
This release schedule keeps the code stable and reliable, at the cost of more frequent updates.</p>
</blockquote>

= Free / Basic Version Repository =

* [GitHub](https://github.com/SurniaUlula/wpsso-schema-json-ld)
* [WordPress.org](https://wordpress.org/plugins/wpsso-schema-json-ld/developers/)

= Changelog / Release Notes =

**Version 1.5.0 (2016/03/25)**

Official announcement: N/A

* *New Features*
	* Added a new "Schema JSON-LD" settings page under the SSO menu.
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Tested with WordPress v4.5-RC1-37079.

**Version 1.4.1 (2016/03/23)**

Official announcement: N/A

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* Modified the SucomWebpage `get_caption()`, `get_title()`, and `get_description()` method arguments to use the new `$mod` array.

**Version 1.4.0 (2016/03/20)**

Official announcement: [WPSSO Update - AMP Support and Several Core Changes](http://wpsso.com/2016/03/20/wpsso-update-amp-support-and-several-core-changes/)

> Required update for WordPress Social Sharing Optimization (WPSSO) version 3.28.1.

* *New Features*
	* Added support for Automattic's [Accelerated Mobile Pages (AMP)](https://wordpress.org/plugins/amp/) plugin. See [the AMP project README on GitHub](https://github.com/Automattic/amp-wp/blob/master/readme.md) for details on modifying AMP plugin settings with custom filters.
* *Improvements*
	* Added the "publisher" property to the WebPage and BlogPosting markup.
	* Moved the "inLanguage" to individual schema type modules.
	* Includes the video preview image first (if one is found).
	* Improved the Social Settings options display (Free version).
* *Bugfixes*
	* None
* *Developer Notes*
	* None

== Upgrade Notice ==

= 1.5.0 =

2016/03/25 - Added a new "Schema JSON-LD" settings page under the SSO menu. Tested with WordPress v4.5-RC1-37079.

= 1.4.1 =

2016/03/23 - Modified several SucomWebage method arguments to use the new $mod array.

= 1.4.0 =

2016/03/20 - Required update for WPSSO v3.28.1. Improved the Social Settings options display (Free version). Added support for Automattic's AMP plugin.

