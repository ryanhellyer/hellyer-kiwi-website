<?php
/*
Plugin Name: Undiecar Discord
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: 
Author: Ryan Hellyer
Version: 1.0
Author URI: https://geek.hellyer.kiwi/

Copyright (c) 2018 - 2016 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


//define( 'DISCORD_PHOTO_CHANNEL_ID', xxxxxxxxxx );
//define( 'DISCORD_UNDIECAR_TOKEN', xxxxxxxxxx );


use RestCord\DiscordClient;


class Undiecar_Discord {

	/**
	 * Class constructor
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		add_action( 'cron_discord_task', array( $this, 'import_from_discord' ) );
if ( isset( $_GET['test_discord'] ) ) {add_action( 'admin_init', array( $this, 'import_from_discord' ) );}
	}

	public function import_from_discord() {

		require( __DIR__.'/vendor/autoload.php' );

		$discord = new DiscordClient(['token' => DISCORD_UNDIECAR_TOKEN ]); // Token is required

		$channel = $discord->channel->getChannel(
			array( 'channel.id' => DISCORD_PHOTO_CHANNEL_ID )
		);
		$last_message_id = $channel['last_message_id'];

		$messages = $discord->channel->getChannelMessages(['channel.id' => DISCORD_PHOTO_CHANNEL_ID,'before'=> 0,'after'=> 0,'around'=> (int) $last_message_id,'limit'=> 100]);

		$dir = wp_upload_dir();
		$base_dir = $dir['basedir'];

		foreach ( $messages as $key1 => $message ) {

			// Import each embedded video as a new video post
			foreach ( $message[ 'embeds' ] as $key3 => $embed ) {

				if ( 'video' === $embed[ 'type' ] ) {

					$video_url     = esc_url(  $embed[ 'url' ] );

					$post_title = '';
					if ( isset( $embed[ 'title' ] ) ) {
						$post_title    = esc_html( 'Video:', 'undiecar' ) . ' ' . esc_html( $embed[ 'title' ] );
					}

					$description = '';
					if ( isset( $embed[ 'description' ] ) ) {
						$description   = esc_html( $embed[ 'description' ] );
					}

					$author = '';
					if ( isset( $embed[ 'author' ][ 'name' ]) ) {
						$author        = esc_html( $embed[ 'author' ][ 'name' ] );
					}

					$channel = '';
					if ( isset( $embed[ 'author' ][ 'url' ] ) ) {
						$channel       = esc_url(  $embed[ 'author' ][ 'url' ] );
					}

					$thumbnail_url = '';
					if ( isset( $embed[ 'thumbnail' ][ 'url' ] ) ) {
						$thumbnail_url = esc_url(  $embed[ 'thumbnail' ][ 'url' ] );
					}

					$provider = '';
					if ( isset( $embed[ 'provider' ][ 'name' ] ) ) {
						$provider      = esc_html( $embed[ 'provider' ][ 'name' ] );
					}

					$content = wp_kses_post(
						wpautop( $description ) .
						wpautop(
							sprintf(
								esc_html__( 'The following video was kindly created by  %s.', 'undiecar' ),
								'<a href="' . esc_url( $channel ) . '">' . esc_html( $author ) . '</a>'
							)
						) .
						'</p>' . 
						"\n\n" .$video_url . "\n\n"
					);
print_r( $content );die;
					// Create video post - if it doesn't already exist
					$post_slug = sanitize_title( $post_title );
					$existing_post = get_page_by_path( $post_slug, OBJECT, 'video' );

					if ( empty( $existing_post ) ) {

						$post_id = wp_insert_post(
							array(
								'post_name'    => $post_slug,
								'post_title'   => $post_title,
								'post_content' => $content,
								'post_status'  => 'publish',
								'post_type'    => 'video',
							)
						);

						require_once( ABSPATH . 'wp-admin/includes/media.php' );
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						$attachment_id = media_sideload_image( $thumbnail_url, null, $post_title, 'id' );
						$result = set_post_thumbnail( $post_id, $attachment_id );

					}

				}

			}

//die;
			// Import the attached images
			foreach ( $message['attachments'] as $key2 => $attachment ) {
				$message_id = $message['id'];
				$author = $message['author']['username'];
				$file_name = $attachment['filename'];
				$file_size = $attachment['size'];
				$file_url = $attachment['url'];
				$file_description = esc_html( $message['content'] );

				// Check the attachment doesn't already exist
				$args = array(
					'posts_per_page' => 1,
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'meta_key'               => 'discord_message_id',
					'meta_value'             => $message_id,
					'no_found_rows'          => true,  // useful when pagination is not needed.
					'update_post_meta_cache' => false, // useful when post meta will not be utilized.
					'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
					'fields'                 => 'ids'
				);
				$attachments = new WP_Query( $args );
				if ( ! isset( $attachments->posts[0] ) ) {

					require_once( ABSPATH . 'wp-admin/includes/media.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/image.php' );

					$attachment_id = media_sideload_image( $file_url, null, $file_description, 'id' );

					// Get new file name (may have changed on uploading)
					$file_name = basename ( get_attached_file( $attachment_id ) );

					update_post_meta( $attachment_id, 'gallery', true );
					update_post_meta( $attachment_id, 'discord_message_id', $message_id );

				}

			}

		}

	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 */
	public function activation() {

		// Schedule the Cron task
		$first_run_time = current_time ( 'timestamp' ) + 60;
		wp_schedule_event( $first_run_time, 'hourly', 'cron_discord_task' );
	}

	/**
	 * On deactivation, remove all functions from the scheduled action hook.
	 */
	public function deactivation() {
		wp_clear_scheduled_hook( 'cron_discord_task' );
	}

}
new Undiecar_Discord;
