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
	}

	public function import_from_discord() {

		require( __DIR__.'/vendor/autoload.php' );

		$discord = new DiscordClient(['token' => DISCORD_UNDIECAR_TOKEN ]); // Token is required

		$channel = $discord->channel->getChannel(
			array( 'channel.id' => DISCORD_PHOTO_CHANNEL_ID )
		);
		$last_message_id = $channel['last_message_id'];

		$messages = $discord->channel->getChannelMessages(['channel.id' => DISCORD_PHOTO_CHANNEL_ID,'before'=> 0,'after'=> 0,'around'=> (int) $last_message_id,'limit'=> 5]);


		$dir = wp_upload_dir();
		$base_dir = $dir['basedir'];

		foreach ( $messages as $key1 => $message ) {
			foreach ( $message['attachments'] as $key2 => $attachment ) {
				$message_id = $message['id'];
				$author = $message['author']['username'];
				$file_name = $attachment['filename'];
				$file_size = $attachment['size'];
				$file_url = $attachment['url'];
				$file_description = sprintf( esc_html__( 'Posted by %s: ', 'undiecar' ), $author ) . esc_html( $message['content'] );

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
