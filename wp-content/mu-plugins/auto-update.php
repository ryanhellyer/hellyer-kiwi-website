<?php

/**
 * Update all the things!
 */
add_filter( 'allow_major_auto_core_updates', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_translation', '__return_true' );

// Allow core nightly updates
add_filter( 'allow_dev_auto_core_updates', '__return_true' );

// Force automatic updates even when SVN or Git folder found
function always_return_false_for_vcs( $checkout, $context ) {
   return false;
}
add_filter( 'automatic_updates_is_vcs_checkout', 'always_return_false_for_vcs', 10, 2 );

