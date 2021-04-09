=== LH Multipart Email ===
Contributors: shawfactor, y0uri
Donate link: http://lhero.org/portfolio/lh-multipart-email/
Tags: email, text email, html email, spam, spamassassin
Requires at least: 5.5
Tested up to: 5.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Decreases the chance of your legit emails being marked as spam by providing a text alternative within the one email.

== Description ==

This is a plugin you need but probably don't realise. It does one thing very well and very simply. For every HTML email sent by WordPress it will provide a text alternative (within the one email).

99% of all email clients will just show the HTML version of the email. The other 1% can't properly display HTML and will show the plain text email. Without this plugin, they would have had nothing to show. The other major benefit of this plugin come from the fact that will reduce the chances of the emails you send ending up in the recipients spam folder.

If you have more question about the why's of this plugin, take a look at the FAQ.

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-multipart-email/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-multipart-email/).**


== Installation ==

= Installing the plugin =
1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for *Add Plain Text Email* and click "Install now"
1. Alternatively, download the plugin and upload the contents of `add-plain-text-email.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin. Your HTML emails will now automatically have a plain text version attached. 


== Frequently Asked Questions ==

= Why add a plain text version? =
Because it decreases the chance of your *legitimate* email being marked as being spam and thus landing (disappearing) in spam folders.

http://wiki.apache.org/spamassassin/Rules/MIME_HTML_ONLY


= How is this plugin different from Danny van Kooten's Add Plain Text Email plugin? =
Both plugins achieve the same thing and use the same approach, however his simply strips html tags from the html email version to create the text version. In contrast mine uses a more advanced approach using the Jevon Wrights Html2Text library, thus includes links and other useful information whilst still producing a conforming text alternative.

http://wiki.apache.org/spamassassin/Rules/MIME_HTML_ONLY

= Will this mess up my HTML email? =
No, the plugin does not affect the HTML message of emails. 

= How can I tell if this plugin is working? =
Once enabled some email clients will have an option to view emails as text only. Alternatively others (such as gmail) offer a Show Original function where you can view the raw email (and see that your HTML email now also has a text alternative)

= What is something does not work?  =

LH Sortable Tables, and all [https://lhero.org](LocalHero) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones). 

If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfirteen, twentysixteen etc.

If the problem persists pleasse leave a post in the support forum: [https://wordpress.org/support/plugin/lh-multipart-email/](https://wordpress.org/support/plugin/lh-multipart-email/) . I look there regularly and resolve most queries.

= What if I need a feature that is not in the plugin?  =

Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/)


== Changelog ==


**1.00 April 29, 2017**  
Initial release.

**1.01 July 25, 2017**  
Class check.

**1.10 October 17, 2018**  
Singleton and bulk mail support, props y0uri.

**1.11 May 17, 2019**  
Minor security, ie prevent direct access and bump compatability.

**1.12 March 10, 2021**  
Compatible with wordpress 5.5 phpmailer changes.