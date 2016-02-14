=== WPSSO Schema JSON-LD ===
Plugin Name: WPSSO Schema JSON-LD (WPSSO JSON)
Plugin Slug: wpsso-schema-json-ld
Text Domain: wpsso-schema-json-ld
Domain Path: /languages
Contributors: jsmoriss
Donate Link: https://wpsso.com/
Tags: wpsso, schema, json
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Requires At Least: 3.1
Tested Up To: 4.4.2
Stable Tag: 1.0.0

WPSSO extension to add Schema JSON-LD markup in webpage headers for Google, Pinterest, etc.

== Description ==

<p><img src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png" width="256" height="256" style="width:33%;min-width:128px;max-width:256px;float:left;margin:0 40px 20px 0;" /></p>

WPSSO Schema JSON-LD (WPSSO JSON) works in conjunction with the [WordPress Social Sharing Optimization (WPSSO)](https://wordpress.org/plugins/wpsso/) plugin, extending its features with additional settings pages, tabs, and options to automatically publish content on social websites.

= Quick List of Features =

**WPSSO JSON Free / Basic Features**

Adds Google / Schema JSON-LD markup for Post / Page item types:

* [schema.org/Blog](http://schema.org/Blog) and [schema.org/WebPage](http://schema.org/WebPage)
	* URL
	* Title
	* Description
	* DatePublished
	* DateModified
	* Author as [schema.org/Person](http://schema.org/Person)
		* Author URL
		* Author Name
		* Author Image as [schema.org/ImageObject](http://schema.org/ImageObject)
			* Author Image URL
			* Author Image Width
			* Author Image Height
	* Post / Page Image(s) as [schema.org/ImageObject](http://schema.org/ImageObject)
		* Image URL
		* Image Width
		* Image Height

**WPSSO JSON Pro / Power-User Features**

Adds Google / Schema JSON-LD markup for Post / Page item types:

* [schema.org/Article](http://schema.org/Article)
* [schema.org/Place](http://schema.org/Place) ([WPSSO PLM](https://wordpress.org/plugins/wpsso-plm/) extension required)
	* Place Address as [schema.org/PostalAddress](http://schema.org/PostalAddress)
		* streetAddress
		* postOfficeBoxNumber
		* addressLocality
		* addressRegion
		* postalCode
		* addressCountry
	* Geo Location as [schema.org/GeoCoordinates](http://schema.org/GeoCoordinates)
		* elevation
		* latitude
		* longitude
* [schema.org/Product](http://schema.org/Product) (eCommerce plugin often required)
	* Rating as [schema.org/AggregateRating](http://schema.org/AggregateRating)
		* ratingvalue
		* ratingcount
		* worstrating
		* bestrating
		* reviewcount

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

**Version 1.0.0 (TBD)**

* *New Features*
	* None
* *Improvements*
	* None
* *Bugfixes*
	* None
* *Developer Notes*
	* None

== Upgrade Notice ==

= 1.0.0 =

TBD - Initial release.

