<?php

class MediaHub_Content_API extends MediaHub_Core {

	/**
	 * Class variables.
	 * 
	 * @access private
	 */
	private $settings_api;
	private $allowed_html = array(
		'iframe'         => array(
			'src'        => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'a'       => array(
			'href'       => array(),
			'title'      => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'div'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'dl'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'dd'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'dt'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'form'     => array(
			'role'       => array(),
			'method'     => array(),
			'action'     => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'input'     => array(
			'placeholder'=> array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'span'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'p'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h1'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h2'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h3'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h4'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h5'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'h6'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'table'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'blockquote'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'small'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'code'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'pre'       => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'tr'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
			'td'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'th'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'thead'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'tfoot'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'style'     => array(
			'type'       => array(),
			'id'         => array(),
			'rel'        => array(),
			'media'      => array(),
			'href'       => array()
		),
		'ul'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'li'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'ol'         => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'img'         => array(
			'src'        => array(),
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'article'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'aside'       => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'header'      => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'nav'        => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'footer'     => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'section'    => array(
			'style'      => array(),
			'class'      => array(),
			'id'         => array()
		),
		'br'     => array(),
		'em'     => array(),
		'i'      => array(),
		'strong' => array(),
		'b'      => array(),
		'u'      => array(),
		'font'   => array()
	);

	/**
	 * Settings page tabs.
	 */
	public $settings_sections;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		$this->settings_sections = array(
			array(
				'id' => 'mhca_options',
				'title' => __( 'General settings', 'mediahub' ),
			),
			array(
				'id' => 'mhca_api_key',
				'title' => __( 'API settings', 'mediahub' ),
			),
			array(
				'id' => 'mhca_help',
				'title' => __( 'Instructions', 'mediahub' ),
			)
		);

		$this->settings_api = new WeDevs_Settings_API;

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Add settings link to Plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_add_settings_link' ) );

		// Register (de)activation hooks
		register_activation_hook( __FILE__, array( $this, 'mh_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'mh_deactivation' ) );

		// Add custom cron schedule
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		add_action( 'mh_event_hook', array( $this, 'get_or_update_notes' ) );

	}

	/**
	 * Initialize admin settings
	 *
	 * @return void
	 */
	public function admin_init() {

		/**
		 * Cron test code
		 *
		 */
		if ( isset( $_GET['testcron'] ) ) {
			$this->get_or_update_notes();
		}

		// Creating new categories when selected on MediaHub settings page
		if ( isset( $_POST['mhca_options']['mediahub_cat2cat'] ) ) {
			$categories = $_POST['mhca_options']['mediahub_cat2cat'];
			foreach( $categories as $key => $category ) {
				if ( ! is_numeric( $category ) ) {
					$category = explode( '|', $category );

					// If "new category" chosen in select box, then create it
					if ( 'new-mediahub-category' == $category[0] ) {
						$category_id = wp_create_category( $category[2] );
						$_POST['mhca_options']['mediahub_cat2cat'][$category[1]] = $category_id;
					}

				}
			}
		}

		// If on admin page, then update posts (automatically run on WP Cron too)
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'mediahub_api' && isset( $_GET['update'] ) && $_GET['update'] == 'yes' ) {
			$this->get_or_update_notes();
		}

		// Implement the admin settings fields (uses a settings fields library)
		$this->settings_api->set_sections( $this->settings_sections );
		$this->settings_api->set_fields( $this->get_settings_fields() );
		$this->settings_api->admin_init();
	}

	/**
	 * Add admin menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_options_page( 'MediaHub API', 'MediaHub API', 'delete_posts', 'mediahub_api', array( $this, 'plugin_page' ) );
	}

	/**
	 * Plugin setting link
	 *
	 * @param  array $links Plugin link array
	 * @return array $links Amended plugin settings link
	 */
	public function plugin_add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=mediahub_api">' . __( 'Settings', 'mediahub' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	public function get_settings_fields() {

		$settings_fields['mhca_options'] = array(
			array(
				'name' => 'mediahub_howto',
				'label' => __( '', 'mediahub' ),
				'desc' => __( 'Categories', 'mediahub' ),
				'type' => 'html'
			)
		);

		// Get the categories
		$categories = $this->mediahub_request( $get = 'section/categories', $query = '' );
		$cat_array = null;

		if ( is_object( $categories ) && $categories->success == 1 && count( $categories->data ) > 0 ){

			foreach ( $categories->data as $id => $object ) {
				$cat_options[ $object->id ] = $object->name;
			}

			// Set $cat_options if no data available yet)
			if ( ! isset( $cat_options ) ) {
				$cat_options = array();
			}

			$cat2cat_array = array(
				'name' => 'mediahub_cat2cat',
				'label' => __( 'Categories', 'mediahub' ),
				'type' => 'cat2cat',
				'options' => $cat_options,
				'default' => 'no',
			);

			$update_array = array(
				'name' => 'mediahub_request_posts',
				'label' => __( 'Update', 'mediahub' ),
				'desc' => __( '<a href="options-general.php?page=mediahub_api&update=yes" class="button">' . __( 'Update', 'mediahub' ) . '</a> <p class="description">Klik op de update button om berichten handmatig op te halen. <br><strong>LET OP: dit is niet de save knop!</strong></p>', 'mediahub' ),
				'type' => 'html'
			);
		}
		else {
			$cat_array = array(
				'name' => 'mediahub_categorieen',
				'label' => __( '<strong>CAUTION:</strong>', 'mediahub' ),
				'desc' => '<strong>' . __( 'First fill all API settings correctly.', 'mediahub' ) . '</strong>',
				'type' => 'html'
			);

			$cat2cat_array = array();

			$update_array = array(
				'name' => 'mediahub_request_posts',
				'label' => '',
				'desc' => '',
				'type' => 'html'
			);
		}

		// Get newsletter groups from API
		$results = $this->mediahub_request( 'newsletters/groups', '', 'GET' );
		$api_groups = array();
		if ( isset( $results ) && isset( $results->data ) ) {
			$results = $results->data;
			if ( is_array( $results ) ) {
				foreach( $results as $key => $result ) {
					$api_groups[$result->id] = $result->name;
				}
			}
		}

		$settings_fields['mhca_options'][] = $cat_array;
		$settings_fields['mhca_options'][] = $update_array;
		$settings_fields['mhca_options'][] = $cat2cat_array;
		$settings_fields['mhca_options'][] = array(
			'name' => 'mediahub_api_group',
			'label' => __( 'API Group', 'mediahub' ),
			'desc' => __( 'Choose the group to which subscribers will be added:', 'mediahub' ),
			'type' => 'select',
			'default' => 'no',
			'options' => $api_groups,
		);
		$settings_fields['mhca_options'][] = array(
			'name' => 'cron_interval',
			'label' => __( 'Checking Interval', 'mediahub' ),
			'desc' => __( 'Choose the time between checking for new posts', 'mediahub' ),
			'type' => 'select',
			'default' => 'no',
			'options' => array(
				'minutes5'  => __( '5 minutes', 'mediahub' ),
				'minutes10' => __( '10 minutes', 'mediahub' ),
				'minutes15' => __( '15 minutes', 'mediahub' ),
			),
		);

		$settings_fields['mhca_api_key'] = array(
			array(
				'name' => 'mediahub_api_url',
				'label' => __( 'API URL', 'mediahub' ),
				'type' => 'text'
			),
			array(
				'name' => 'mediahub_api_openkey',
				'label' => __( 'API Open Key', 'mediahub' ),
				'type' => 'text'
			),
			array(
				'name' => 'mediahub_api_secretkey',
				'label' => __( 'API Secret Key', 'mediahub' ),
				'type' => 'text'
			),
			array(
				'name' => 'mediahub_environment_id',
				'label' => __( 'Environment ID', 'mediahub' ),
				'type' => 'text'
			),

		);

		$help_desc = __( '<p>Below you can see all the themes from The MediaHub<br>Check the desired categories and save the changes with the blue button.<br>Then you can retrieve the messages manually. The first time you do this may take a long time because of the amount of messages that have to be retrieved.</p><p>You only have to update manually the first time and / or optionally as you add a new category.<br>New messages are retrieved automatically after setting up.</p>', 'mediahub' );

		$help_algemeen = __( '<p>Wanneer je de API gegevens correct ingevuld hebt zie je hier een lijst met categorien die je kan kiezen.<br>Vink de gewenste categorien aan en sla daarna de wijziging op met de blauwe knop.<br>Hierna kan je de berichten handmatig ophalen. De eerste keer dat je dit doet kan het lang duren vanwege de hoeveelheid berichten die opgehaald moeten worden.</p><p>Je hoeft het handmatig updaten alleen de eerste keer te doen en/of optioneel als je een nieuwe categorie toevoegd.<br>Nieuwe berichten worden na het instellen automatisch elke 15 minuten opgehaald.</p>', 'mediahub' );

		$settings_fields['mhca_help'] = array(
			array(
				'name' => 'mediahub_help_algemeen',
				'label' => '<strong>' . __( 'General settings', 'mediahub' ) . '</strong>',
				'desc' => $help_algemeen,
				'type' => 'html'
			),
			array(
				'name' => 'mediahub_help_api',
				'label' => '<strong>' . __( 'API settings', 'mediahub' ) . '</strong>',
				'desc' => $help_desc,
				'type' => 'html'
			)
		);

		return $settings_fields;
	}

	/**
	 * Display settings page
	 *
	 * @return string Settings page html
	 */
	function plugin_page() {
		echo '<div class="wrap">';

		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}

	/**
	 * Adds 5, 10 and 15 minute cron schedules.
	 *
	 * @param array $schedules Cron schedule array
	 * @return array $schedules Amended cron schedule array
	 */
	public function cron_schedules( $schedules ) {

		$schedules['minutes5'] = array(
			'interval' => 300,
			'display'  => __( 'Every 5 minutes' )
		);
		$schedules['minutes10'] = array(
			'interval' => 600,
			'display'  => __( 'Every 10 minutes' )
		);
		$schedules['minutes15'] = array(
			'interval' => 900,
			'display'  => __( 'Every 15 minutes' )
		);
		return $schedules;
	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 *
	 * @return void
	 */
	public function mh_activation() {
		wp_schedule_event( current_time ( 'timestamp' ), 'minutes15', 'mh_event_hook' );
	}

	/**
	 * On deactivation, remove all functions from the scheduled action hook.
	 *
	 * @return void
	 */
	public function mh_deactivation() {
		wp_clear_scheduled_hook( 'mh_event_hook' );
	}

	/**
	 * Gets the posts from Mediahub API, inserts/updates posts and media
	 *
	 * @return void
	 */
	public function get_or_update_notes() {

		// Load the necessary files for side loading when using cron
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php'  );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Get Mediahub categories
		if ( $categories = get_option( 'mhca_options', false ) ) {

			// Get ID's from events to compare with note ID's later on
			if( $events = $this->mediahub_request( 'section/categories', $query = 'type=event&' ) ) {
				if ( 'Unauthorized' != $events->error ) {
					$event_ids = array();

					foreach ($events->data as $event_key => $event_value) {
						$event_ids[] = $event_value->id;
					}
				}
			}

			// Loop through found categories
			if ( isset( $categories['mediahub_cat2cat'] ) ) {
				foreach ( $categories['mediahub_cat2cat'] as $term_id => $bool ) {

					// If not set to true (1) then continue, as we aren't meant to be processing that category right now
					if ( 0 == $bool ) {
						continue;
					}

					// Posts in category
					$articles = $this->mediahub_request( $get = 'articles', $query = 'status=1&section_categories=' . $term_id . '&' );
					if ( isset( $articles->data ) ) {
						foreach( $articles->data as $key => $article ) {

							// Work out which status to apply
							if ( 'published' == $article->status->name ) {
								$status = 'publish'; // MediaHub uses different terminology for the status of "publish"
							} elseif ( 'pending' == $article->status->name ) {
								$status = 'publish'; // Pending within MediaHub refers to scheduled posts within WordPress, so we just switch them to use the status of "publish" instead
							} else {
								$status = 'reject'; // If no suitable status found, then just reject it outright
							}

							if ( 'reject' != $status ) {

								// Sanitize data inputs
								$title      = wp_kses_post( $article->revision->title );
								$content    = wp_kses( $article->revision->content, $this->allowed_html, '' );
								$excerpt    = wp_kses_post( $article->revision->excerpt );
								$slug       = sanitize_title( $article->revision->slug );
								$created_on = sanitize_title( $article->revision->created_on );

								// Alter content and/or excerpt depending if or which are empty
								if ( $excerpt != '' && $content != '' ) {
									$content = $excerpt . "\n<!--more-->\n" . $content;
								} elseif ( $excerpt == '' && $content != '' ) {
									$content = $content;
								} elseif ( $excerpt != '' && $content == '' ) {
									$content = $excerpt;
								}

								// Add photos to content
								$images = array();
								if ( isset( $article->media->photo ) ) {
									$media = $article->media->photo;
									foreach( $media as $key => $media_item ) {
										$files = $media_item->file; // Grab all available files (various sizes of same image)
										foreach( $files as $size => $file ) {

											if ( 1920 == $size ) {
												$image = $file->file;
												$images[] = $file->file;
											}
											if ( 300 == $size ) {
												$thumb = $file->file;
												$thumbs[] = $file->file;
											}

										}
									}

									foreach( $images as $k => $image_url ) {
										$content .= '

										<p>
											<a href="' . esc_url( $image_url ) . '">
												<img width="150" height="150" src="' . esc_url( $thumbs[$k] ) . '" data-lazy-src="' . esc_url( $thumbs[$k] ) . '" />
											</a>
										</p>';
									}

								}


								// Check if post already exists
								$args = array(
									'post_type'           => 'post',
									'ignore_sticky_posts' => 1,
									'post_status'         => 'any',
									'posts_per_page'      => self::MAX_POSTS_PER_PAGE,
									'meta_key'            => self::META_KEY,
									'meta_query'          => array(
										array(
											'key'     => self::META_KEY,
											'value'   => $article->revision->id,
											'compare' => '='
										)
									)
								);
								$post_exists = new WP_Query( $args );

								// If no post is found
								if ( count( $post_exists->posts ) == 0 ) {

									// Insert new post
									$post = array(
										'post_name'     => sanitize_title( $title ) . '-' . $article->revision->id,
										'post_title'    => $title,
										'post_date'     => date( 'Y-m-d H:i:s', $created_on ),
										'post_content'  => $content,
										'post_status'   => $status,
										'post_author'   => 1
									);
									$post_id = wp_insert_post( $post );

									// Add meta data
									add_post_meta( $post_id, self::META_KEY,            $article->revision->id, true );
									add_post_meta( $post_id, 'mediahub_date_created',   $article->revision->created_on, true );
									add_post_meta( $post_id, 'mediahub_date_changed',   $article->revision->changed_on, true );
									add_post_meta( $post_id, 'mediahub_date_published', $article->revision->created_on, true );

									// Compare ID with event ID, if true add mediahub_event meta
									if ( in_array( $article->revision->id, $event_ids ) ) {
										update_post_meta( $post_id, 'mediahub_event', true );
									}

									// Set the post category(s)
									$category_id = $categories['mediahub_cat2cat'][$term_id];
									wp_set_post_categories( $post_id, array( $category_id ) );

									// Post tags
									$terms = array();

									if ( isset( $articles->data[$key]->metadata->keyword ) ) {
										$keywords = $articles->data[$key]->metadata->keyword;
										foreach( $keywords as $keyword_id => $object ) {
											$terms[] = strtolower( $object->name ); // Add keywords to post -tags
										}
									}

									if ( isset( $articles->data[$key]->metadata->person ) ) {
										$persons = $articles->data[$key]->metadata->person;
										foreach( $persons as $person_id => $object ) {
											$terms[] = strtolower( $object->name ); // Add person to post -tags
										}
									}

									if ( isset( $articles->data[$key]->metadata->organisation ) ) {
										$organisations = $articles->data[$key]->metadata->organisation;
										foreach( $organisations as $organisation_id => $object ) {
											$terms[] = strtolower( $object->name ); // Add organisation to post -tags
										}

echo 'YES';
print_r( $terms );
echo "\n\n\n\n\n\n.........................\n\n\n\n\n\n\n";
print_r( $articles );
die;

									}

									wp_set_post_terms( $post_id, $terms, 'post_tag', $append = false ); // Finally adding post tags

									// Featured Image
									if ( isset ( $article->media->primary->file ) ) {
										$primary_media = $article->media->primary->file;
										foreach( $primary_media as $key => $featured_image_block ) {
											if ( 800 == $key ) {
												$featured_image = $featured_image_block->file;
											}
										}
									}

									// Sideload the featured image
									if ( isset( $featured_image ) ) {

										// Add 20 seconds to time limit due to GD being slow sometimes
										set_time_limit( 20 );

										$image = media_sideload_image( $featured_image, $post_id, $title );
										// Get attached images
										$images = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
										$images = reset( $images );
										$images = reset( $images );

										// Set first attached image as featured image
										add_post_meta( $post_id, '_thumbnail_id', $images );
									}
									unset( $featured_image );

									// Create gallery
									$images = array();
									if ( isset( $article->media->mediatag ) ) {
										$galleries = $article->media->mediatag;
										foreach( $galleries as $gallery_key => $gallery_data ) {
											$files = $gallery_data->media->primary->file;

											foreach( $files as $size => $file ) {

												if ( 1920 == $size ) {
													$image = $file->file;
													$result = media_sideload_image( $image, $post_id, '' );
													$images[] = $file->file;
												}

											}

										}

										// Add gallery to post content
										$images_uploaded = get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
										$content .= '[gallery columns="2" ids="';
										$gallery_image_ids = array();
										foreach( $images_uploaded as $image_id => $image ) {
											if ( '' == $image->post_title ) { // Gallery images don't come with a post-title, so we are able to use this to determine that the images we are processing are no part of the gallery
												$gallery_image_ids[] = $image_id;
												$content .= $image_id . ',';
											}
										}
										$content .= '"]';

										// Update post with new gallery shortcode(s) added
										$post_update = array(
											'ID'           => $post_id,
											'post_content' => $content,
										);
										wp_update_post( $post_update );

									}

								}

								// If post already exists ...
								else {

									// If the post already exists, we should still add the category to it, as it may have multiple categories
									$category_id = $categories['mediahub_cat2cat'][$term_id];
									$post_id = $post_exists->post->ID;
									wp_set_post_categories( $post_id, array( $category_id ), true );

								}

								unset( $status );
							}
						}
					}
				}
			}
		}
die('REDIRECT HERE');
		wp_redirect( admin_url() . 'options-general.php?page=mediahub_api', 302 );
	}

}

$settings = new MediaHub_Content_API();
