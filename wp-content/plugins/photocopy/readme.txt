=== Photocopy ===
Contributors: ryanhellyer
Donate link: http://pixopoint.com/donate/
Tags: backup, media, uploads, s3
Requires at least: 3.4
Stable tag: 1.0

Backs up your media instantly to Amazon S3

== Description ==

Creates an immediate backup of your media to Amazon S3 as soon as you upload it. Vital for when you never want to risk losing your files.

Can also be useful for off-loading your uploads externally to free up disk space on your main site (additional CDN plugin required to make use of it in this fashion)

== Installation ==

Set the Amazon S3 access key, secret key and your chosen bucket name in your wp-config.php file (or anywhere which executes prior to 'init'). You will need to obtain this information from Amazon directly.

<code>
define( 'AWS_ACCESS_KEY', 'JKEFSLDGNSJKGBSDGJ');
define( 'AWS_SECRET_KEY', 'LKafk+kalfskaKDSDGQWRLZVXMLKEOInsdgsd' );
define( 'AWS_BUCKET_NAME', 'ryans-personal-bucket' );
</code>

Now, when you upload an image via the WordPress media uploader, the files will be automatically backed up to Amazon S3 :)

For more information, visit the <a href="http://pixopoint.com/products/photocopy/">Photocopy plugin page</a>.

<small>Note: This plugin requires Curl to be installed on your server. This is due to the S3 PHP class by Donovan Schönknecht which is used in this plugin. Technically, the WordPress http API could be used instead of directly performing http requests via Curl, but I didn't want to mess with the original class which seems to work very well, and so running Curl on your server is a requirement for use with this plugin.</small>

== Frequently Asked Questions ==

Check out the FAQ on the <a href="http://pixopoint.com/products/photocopy/">Photocopy plugin</a> page.


== Changelog ==

= 1.0.1 (11/11/2012) =
* Fixed bug which prevented non-image attachments from upload

= 1.0 (16/8/2012) =
* Initial plugin creation

<small>Any beta/alpha versions to be released in future, will be posted for download on the <a href="http://pixopoint.com/products/photocopy/">Photocopy plugin page</a>.</small>


== Credits ==

* <a href="http://obenland.it/">Konstantin Obenland</a> - Assisted with the development of the code used for intermediate sized images<br />
* Kyle Matthews - Reported bug which prevented non-image attachments from uploading<br />
