<h1>WPSSO Schema JSON-LD</h1><h3>Complete Schema / Rich Snippet Markup for Google and Pinterest</h3>

<table>
<tr><th align="right" valign="top" nowrap>Plugin Name</th><td>WPSSO Schema JSON-LD (WPSSO JSON)</td></tr>
<tr><th align="right" valign="top" nowrap>Summary</th><td>WPSSO extension to add complete Schema JSON-LD markup (BlogPosting, Article, Place, Product, etc.) for Google and Pinterest.</td></tr>
<tr><th align="right" valign="top" nowrap>Stable Version</th><td>1.0.1</td></tr>
<tr><th align="right" valign="top" nowrap>Requires At Least</th><td>WordPress 3.1</td></tr>
<tr><th align="right" valign="top" nowrap>Tested Up To</th><td>WordPress 4.4.2</td></tr>
<tr><th align="right" valign="top" nowrap>Contributors</th><td>jsmoriss</td></tr>
<tr><th align="right" valign="top" nowrap>Website URL</th><td><a href="https://wpsso.com/">https://wpsso.com/</a></td></tr>
<tr><th align="right" valign="top" nowrap>License</th><td><a href="http://www.gnu.org/licenses/gpl.txt">GPLv3</a></td></tr>
<tr><th align="right" valign="top" nowrap>Tags / Keywords</th><td>wpsso, schema, structured data, json, json-ld, ld+json, rich snippets, article, product</td></tr>
</table>

<h2>Description</h2>

<p align="center"><img src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png" width="256" height="256" /></p><p><strong>Are you looking for better <em>Schema</em>, <em>Structured Data</em> and <em>Rich Snippet</em> markup?</strong></p>

<p>WPSSO Schema JSON-LD (WPSSO JSON) works in conjunction with the <a href="https://wordpress.org/plugins/wpsso/">WordPress Social Sharing Optimization (WPSSO)</a> plugin, extending its features with additional options to include complete Schema JSON-LD / Rich Snippet markup in webpage headers for Google Search, Pinterest, and other social / search engine crawlers.</p>

<h4>Available in Multiple Languages</h4>

<ul>
<li>English (US)</li>
<li>French (France)</li>
<li>More to come...</li>
</ul>

<h4>Quick List of Features</h4>

<p><strong>WPSSO JSON Free / Basic Features</strong></p>

<p>Adds Google / Schema JSON-LD markup for Post / Page item types:</p>

<ul>
<li><a href="http://schema.org/BlogPosting">schema.org/BlogPosting</a> and <a href="http://schema.org/WebPage">schema.org/WebPage</a>

<ul>
<li>URL</li>
<li>Name</li>
<li>Description</li>
<li>DatePublished</li>
<li>DateModified</li>
<li>Author as <a href="http://schema.org/Person">schema.org/Person</a>

<ul>
<li>Author URL</li>
<li>Author Name</li>
<li>Author Image as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Author Image URL</li>
<li>Author Image Width</li>
<li>Author Image Height</li>
</ul></li>
</ul></li>
<li>Post / Page Image(s) as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Image URL</li>
<li>Image Width</li>
<li>Image Height</li>
</ul></li>
</ul></li>
</ul>

<p><strong>WPSSO JSON Pro / Power-User Features</strong></p>

<p>Adds Google / Schema JSON-LD markup for Post / Page item types:</p>

<ul>
<li><a href="http://schema.org/Article">schema.org/Article</a>

<ul>
<li>URL</li>
<li>Name</li>
<li>Headline</li>
<li>Description</li>
<li>DatePublished</li>
<li>DateModified</li>
<li>Author as <a href="http://schema.org/Person">schema.org/Person</a>

<ul>
<li>Author URL</li>
<li>Author Name</li>
<li>Author Image as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Author Image URL</li>
<li>Author Image Width</li>
<li>Author Image Height</li>
</ul></li>
</ul></li>
<li>Publisher as <a href="http://schema.org/Organization">schema.org/Organization</a>

<ul>
<li>URL</li>
<li>Name</li>
<li>Description</li>
<li>Logo Image as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Image URL</li>
<li>Image Width</li>
<li>Image Height</li>
</ul></li>
</ul></li>
<li>Article Image(s) as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Image URL</li>
<li>Image Width</li>
<li>Image Height</li>
</ul></li>
</ul></li>
<li><a href="http://schema.org/Place">schema.org/Place</a> (<a href="https://wordpress.org/plugins/wpsso-plm/">WPSSO PLM</a> extension required)

<ul>
<li>URL</li>
<li>Name</li>
<li>Description</li>
<li>Geo Location as <a href="http://schema.org/GeoCoordinates">schema.org/GeoCoordinates</a>

<ul>
<li>elevation</li>
<li>latitude</li>
<li>longitude</li>
</ul></li>
<li>Place Address as <a href="http://schema.org/PostalAddress">schema.org/PostalAddress</a>

<ul>
<li>streetAddress</li>
<li>postOfficeBoxNumber</li>
<li>addressLocality</li>
<li>addressRegion</li>
<li>postalCode</li>
<li>addressCountry</li>
</ul></li>
<li>Place Image(s) as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Image URL</li>
<li>Image Width</li>
<li>Image Height</li>
</ul></li>
</ul></li>
<li><a href="http://schema.org/Product">schema.org/Product</a> (eCommerce plugin usually mandatory)

<ul>
<li>URL</li>
<li>Name</li>
<li>Description</li>
<li>SKU</li>
<li>Offers as <a href="http://schema.org/Offer">schema.org/Offer</a>

<ul>
<li>Availability</li>
<li>Price</li>
<li>PriceCurrency</li>
</ul></li>
<li>Rating as <a href="http://schema.org/AggregateRating">schema.org/AggregateRating</a>

<ul>
<li>RatingValue</li>
<li>RatingCount</li>
<li>WorstRating</li>
<li>BestRating</li>
<li>ReviewCount</li>
</ul></li>
<li>Product Image(s) as <a href="http://schema.org/ImageObject">schema.org/ImageObject</a>

<ul>
<li>Image URL</li>
<li>Image Width</li>
<li>Image Height</li>
</ul></li>
</ul></li>
</ul>

<h4>Uses the WPSSO Framework</h4>

<p>The WordPress Social Sharing Optimization (WPSSO) plugin is required to use the WPSSO JSON extension. You can use the Free version of WPSSO JSON with <em>both</em> the Free and Pro versions of WPSSO, but <a href="http://wpsso.com/extend/plugins/wpsso-schema-json-ld/">WPSSO JSON Pro</a> requires the use of the <a href="http://wpsso.com/extend/plugins/wpsso/">WPSSO Pro</a> plugin as well. <a href="http://wpsso.com/extend/plugins/wpsso-schema-json-ld/">Purchase the WPSSO Schema JSON-LD (WPSSO JSON) Pro extension</a> (includes a <em>No Risk 30 Day Refund Policy</em>).</p>


<h2>Installation</h2>

<h4>Install and Uninstall</h4>

<ul>
<li><a href="http://wpsso.com/codex/plugins/wpsso-schema-json-ld/installation/install-the-plugin/">Install the Plugin</a></li>
<li><a href="http://wpsso.com/codex/plugins/wpsso-schema-json-ld/installation/uninstall-the-plugin/">Uninstall the Plugin</a></li>
</ul>


<h2>Frequently Asked Questions</h2>

<h4>Frequently Asked Questions</h4>


<h2>Other Notes</h2>

<h3>Other Notes</h3>
<h4>Additional Documentation</h4>

