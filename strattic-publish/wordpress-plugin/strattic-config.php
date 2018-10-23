<?php

if ( ! defined( 'STRATTIC_CLOUDFRONT_URL' ) ) {
	define( 'STRATTIC_CLOUDFRONT_URL', '$cloudfront_url' );
}

if ( ! defined( 'STRATTIC_CLOUDFRONT_ID' ) ) {
	define( 'STRATTIC_CLOUDFRONT_ID', '$cloudfront_id' );
}

if ( ! defined( 'STRATTIC_HOME_URL' ) ) {
	define( 'STRATTIC_HOME_URL', '$home_url' );
}

if ( ! defined( 'STRATTIC_S3_BUCKET' ) ) {
	define( 'STRATTIC_S3_BUCKET', '$s3_bucket' );
}

if ( ! defined( 'STRATTIC_EMAIL' ) ) {
	define( 'STRATTIC_EMAIL', 'support@strattic.com' );
}

if ( ! defined( 'STRATTIC_USER' ) ) {
	define( 'STRATTIC_USER', '$user' );
}

if ( ! defined( 'STRATTIC_PASSWORD' ) ) {
	define( 'STRATTIC_PASSWORD', '$password' );
}

if ( ! defined( 'STRATTIC_DIRECTORY' ) ) {
	define( 'STRATTIC_DIRECTORY', '$directory' );
}

#Defaults to /usr/local/bin/strattic-publish/ . Used only for deploy script.
if ( ! defined( 'STRATTIC_WORDPRESS_PLUGIN_DIR' ) ) {
	define( 'STRATTIC_WORDPRESS_PLUGIN_DIR', '$wordpress_plugin_dir' );
}

if ( ! defined( 'STRATTIC_DEBUG' ) ) {
	define( 'STRATTIC_DEBUG', 'false' );
}
