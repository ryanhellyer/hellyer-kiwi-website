Note: This is simply a fork of the Grunion plugin found in the JetPack plugin. I am maintaining it for use on my own site, but will not be adding any features unless I can easily copy them over from JetPack.


=== Easy Contact Forms ===
Contributors: ryanhellyer
Tags: contact, form, contact form, plugins, plugin, email, WordPress.com
Stable tag: 1.0
Requires at least: 4.1
Tested up to: 4.2

Add a contact form to any post, page or text widget. Messages will be sent to any email address you choose.

== Description ==

Add a contact form to any post or page by inserting `[contact-form]` in the post.  Messages will be sent to the post's author or any email address you choose.

Or add a contact form ta a text widget.  Messages will be sent to the email address set in your Settings -> General admin panel or any email address you choose.

Your email address is never shown, and the sender never learns it (unless you reply to the email).

This plugin is based on the JetPack plugin, which was based on the original Grunion plugin used on WordPress.com.


= Configuration =

The `[contact-form]` shortcode has the following parameters:

* `to`: A comma separated list of email addresses to which the messages will be sent.
  If you leave this blank: contact forms in posts and pages will send messages to the post or page's author; and
  contact forms in text widgets will send messages to the email address set in Settings -> General.

  Example: `[contact-form to="you@me.com"]`

  Example: `[contact-form to="you@me.com,me@you.com,us@them.com"]`

* `subject`: The e-mail subject of the message defaults to `[{Blog Title}] {Sidebar}` for text widgets
  and `[{Blog Title}] {Post Title}` for posts and pages. Set your own default with the subject option.

  Example: `[contact-form subject="My Contact Form"]`

* `show_subject`: You can allow the user to edit the subject by showing a new field on the form. The
  field will be populated with the default subject or the subject you have set with the previous option.

  Example: `[contact-form subject="My Contact Form" show_subject="yes"]`

== Frequently Asked Questions ==

= What about spam? Will I get a lot from the contact form? =

If you have [Akismet](http://akismet.com/) installed on your blog, you shouldn't get much spam.
All the messages people send to you through the contact form will be filtered through Akismet.

= Anyone can put whatever they want in the name and email boxes. How can I know who's really sending the message? =

If a logged member of your site sends you a message, the end of the email will let you know that the message was sent by a verified user.
Otherwise, you can't trust anything... just like a blog comment.

Anonymity is both a curse and a blessing :)

= My blog has multiple authors. Who gets the email? =

By default, the email is sent to the author of the post with the contact form in it. So each author on your blog can have his or her own contact form.

In the contact form shortcode, you can specify what email address(es) messages should be sent to with the `to` parameter.

= Great! But how will my visitors know who they're sending a message to? =

Just make the title of your post "Contact Mary" or put "Hey, drop John a line with the form below" in the body of your post.

== Changelog ==

= 1.0 =
Initial fork from JetPack
