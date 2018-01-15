=== LH Multipart Email ===
Contributors: shawfactor
Donate link: http://lhero.org/plugins/lh-multipart-email/
Tags: email, text email, html email, spam, spamassassin
Requires at least: 3.1
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Decreases the chance of your legit emails being marked as spam by providing a text alternative within the one email.

== Description ==

This is a plugin you need but probably don't realise. It does one thing very well and very simply. For every HTML email sent by WordPress it will provide a text alternative (within the one email).

99% of all email clients will just show the HTML version of the email. The other 1% can't properly display HTML and will show the plain text email. Without this plugin, they would have had nothing to show. The other major benefit of this plugin come from the fact that will reduce the chances of the emails you send ending up in the recipients spam folder.

If you have more question about the why's of this plugin, take a look at the FAQ.


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


== Changelog ==


**1.00 April 29, 2017**  
Initial release.

**1.01 July 25, 2017**  
Class check.