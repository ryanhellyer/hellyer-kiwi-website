<?php

/**
 * Add newsletter code via shortcode.
 * This is necessary so that it can be used in a text widget.
 */
add_shortcode( 'varnish_newsletter_signup', 'varnish_newsletter_signup_shortcode' );
function varnish_newsletter_signup_shortcode() {
	return '
<!--[if lte IE 8]>
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
<![endif]-->
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
<span id="457184bd-0901-4b19-b9df-643ce223d3fd" class="newsletter-signup-shortcode">
<script>
  hbspt.forms.create({ 
    portalId: \'209523\',
    formId: \'457184bd-0901-4b19-b9df-643ce223d3fd\',
    target: \'.newsletter-signup-shortcode\'
  });
</script>
</span>

';

}

/**
 * Need to add shortcode support to the text widget to make this work.
 */
add_filter( 'widget_text','do_shortcode' );
