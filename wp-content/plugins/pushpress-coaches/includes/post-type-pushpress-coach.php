<?php
/*
* Coach Custom Post type
*/
if ( ! class_exists( 'Coach_Post_Type' ) ) {
	class Coach_Post_Type {
		function __construct() {
			/**
			* Create Coach CPT
			*
			* Function Name: coach_init
			**/
			add_action( 'init', array( $this, 'coach_init' ) );

			/**
			* Add Meta Box to Coach CPT
			*
			* Function Name: coach_meta_box_add
			**/
			add_action( 'add_meta_boxes', array( $this, 'coach_meta_box_add' ) );

			/**
			* Save data from meta box
			*
			* Function Name: coach_metabox_save
			*
			* @param array  $coach_box : Holds parameters to be passed to add_meta_box function
			* @param int    $post_id : Contains ID of post.
			* @param int 	$old : Old values of inputs saved in database
			* @param int 	$new : New values of inputs to be updated in database
			*
			*/
			add_action( 'save_post', array( $this, 'coach_metabox_save' ) );

			/**
			* Updating messages
			*
			* Function Name: coach_set_messages
			*
			* @param var  $message : Holds message to be displayed
			**/
			add_filter( 'post_updated_messages', array( $this, 'coach_set_messages' ) );

			/**
			* Create company name & client location field in Coach post
			*
			* Function Name: add_thumbnail_column
			**/
			add_filter( 'manage_edit-coaches_columns', array( $this, 'add_thumbnail_column'), 10, 1 );
			add_action( 'manage_coaches_posts_custom_column', array( $this, 'display_thumbnail' ), 10, 1 );
			add_filter( 'enter_title_here',array( $this, 'coach_default_title' ));
			add_filter( 'pre_get_posts' , array( $this, 'hide_coach_template'),900);            
			

			/**
			* Rename Featured image title text
			*
			* Function Name: client_coach_change_image_box
			**/
			add_action( 'do_meta_boxes', array( $this,'client_coach_change_image_box' ) );

			global $coach_box;
			$prefix = 'coach_';
			$coach_box = array(
				'id' => 'coachid',
				'title' => 'Coach Information',
				'page' => 'coaches',
				'context' => 'coach',
				'priority' => 'high',
				'fields' => array(
					array(
						'name' => 'Sort Position',
						'desc' => 'Coach Listing Page position',
						'id' => $prefix . 'sort_order',
						'type' => 'number',
						'std' => '1'
					),
					array(
						'name' => 'First Name',
						'desc' => 'Enter first name.',
						'id' => $prefix . 'first_name',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Last Name',
						'desc' => 'Enter last name.',
						'id' => $prefix . 'last_name',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Title',
						'desc' => 'Head Coach',
						'id' => $prefix . 'title',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Phone',
						'desc' => 'Coach Phone Number',
						'id' => $prefix . 'phone',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Email',
						'desc' => 'Coach Email Address',
						'id' => $prefix . 'email',
						'type' => 'email',
						'std' => ''
					),
					array(
						'name' => 'Facebook',
						'desc' => 'Coach Facebook profile URL',
						'id' => $prefix . 'facebook',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Twitter',
						'desc' => 'Coach Twitter handle - DO NOT enter @ before your handle.',
						'id' => $prefix . 'twitter',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Instagram',
						'desc' => 'Coach Instagram handle - DO NOT enter @ before your handle.',
						'id' => $prefix . 'instagram',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Web Site / URL',
						'desc' => 'Coach Website',
						'id' => $prefix . 'website',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'List Page Introduction',
						'desc' => 'Enter a short (140 character) introduction for your coach.  If present, this will be used on the coach listing page.',
						'id' => $prefix . 'intro',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => 'Feats of Strength / Endurance',
						'desc' => 'Add up to 6 feats of strength. They could also be skills or general things you are proud of. Maybe you got a great score in a workout, can do a sub 5min mile, can deadlift 500lbs etc.',
						'id' => $prefix . 'feats_of_strength',
						'type' => 'textarea',
						'std' => ''
					),
					array(
						'name' => 'Qualifications',
						'desc' => 'Add qualifications you are proud of. Start with the most important ones first. Don\'t include your ceramics degree, its not relevant here.',
						'id' => $prefix . 'qualifications',
						'type' => 'textarea',
						'std' => ''
					),
					array(
						'name' => 'About',
						'desc' => 'Share your story, emphasize what you’ve learned along your life’s journey and how you’ve morphed into the coach you are today.',
						'id' => $prefix . 'about',
						'type' => 'textarea',
						'std' => ''
					),
					array(
						'name' => 'Turning Point',
						'desc' => 'What’s your purpose for coaching? Who are you impassioned to serve? State it clearly so your readers know whether or not you are the right coach for them. By the time your readers get to the end of your bio, they should be either hitting the back button because you’re the wrong coach for them, or signing up to join.',
						'id' => $prefix . 'turning_point',
						'type' => 'textarea',
						'std' => ''
					),
					array(
						'name' => 'Passion & Motivation',
						'desc' => 'What’s your purpose for coaching? Who are you impassioned to serve? State it clearly so your readers know whether or not you are the right coach for them. Whats your motivation?',
						'id' => $prefix . 'motivation',
						'type' => 'textarea',
						'std' => ''
					)
				)
			);
		}

		/**
		* Create Coach CPT
		*
		* Function Name: coach_init
		**/
		public function coach_init() {

			$labels_coach = array(
				'name' 					=> __('Coaches','dts'),
				'singular_name' 		=> __('Coaches','dts'),
				'add_new' 				=> __('Add Coach','dts'),
				'add_new_item' 			=> __('Add New Coach','dts'),
				'edit_item' 			=> __('Edit Coach','dts'),
				'new_item' 				=> __('New Coach','dts'),
				'all_items' 			=> __('All Coaches','dts'),
				'search_items' 			=> __('Search Coaches','dts'),
				'not_found' 			=> __('No Coaches found','dts'),
				'not_found_in_trash' 	=> __('No Coaches found in Trash','dts'),
				'parent_item_colon'		=> '',
				'menu_name' 			=> __('Coaches','dts')
			);
			$argscoach = array(
				'labels' 			=> $labels_coach,
				'public' 			=> true,
				'show_ui' 			=> true,
				'rewrite' 			=> array( 'slug' => 'coaches' ),
				'capability_type' 	=> 'post',
				'menu_icon' 		=> 'dashicons-id',
				'hierarchical' 		=> true,
				'supports' 			=> array( 'title', 'editor', 'thumbnail' ),
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' =>true,
				'show_in_admin_bar' => true,
				'has_archive' => true,
				'query_var'             => true,
			);

			register_post_type( 'coaches', $argscoach );
		}

		/**
		* Add Meta Box to Coach CPT
		*
		* Function Name: coach_meta_box_add
		**/
		public function coach_meta_box_add() {
			global $coach_box;
			add_meta_box( $coach_box['id'], $coach_box['title'], array( $this, 'coach_meta_box_cb' ), 'coaches', 'normal','high' );
		}

		/**
		* Callback Function of Coach Meta Box
		*
		* Function Name: coach_meta_box_cb
		**/
		public function coach_meta_box_cb() {
			global $coach_box, $post;

			echo '<input type="hidden" name="coach_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
			echo '<table class="form-table">';
			foreach ( $coach_box['fields'] as $field ) {
				// get current post meta data
				$coach_check_value = get_post_meta( $post->ID, $field['id'], true );
				echo '<tr class="form-field">',
						'<th scope="row"><label for="', $field['id'], '">', _e($field['name'],'aiinfo'), '</label></th>',
						'<td>';
				switch ( $field['type'] ) {
					case 'text':
						echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $coach_check_value ? $coach_check_value : $field['std'], '" />', '<br />', $field['desc'];
						break;
					case 'number':
						echo '<input type="number" name="', $field['id'], '" id="', $field['id'], '" value="', $coach_check_value ? $coach_check_value : $field['std'], '" />', '<br />', $field['desc'];
						break;
					case 'twitter-text':
						echo '<input type="text" maxlength="140" name="', $field['id'], '" id="', $field['id'], '" value="', $coach_check_value ? $coach_check_value : $field['std'], '" />', '<br />', $field['desc'];
						break;
					case 'email':
						echo '<input type="email" name="', $field['id'], '" id="', $field['id'], '" value="', $coach_check_value ? $coach_check_value : $field['std'], '" />', '<br />', $field['desc'];
						break;
					 case 'textarea':
						echo '<textarea placeholder="', $field['desc'], '" style="height:100px;" name="', $field['id'], '" id="', $field['id'], '">', $coach_check_value ? $coach_check_value : $field['std'], '</textarea>';
						break;
					 case 'checkbox':
						echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '" '.checked($coach_check_value,"on" ).'/>', $field['desc'];
						break;

				}
				echo '</td><td>',
					'</td></tr>';
			}
			echo '</table>';
		}

		/**
		* Save data from meta box
		*
		* Function Name: coach_metabox_save
		*
		* @param array  $coach_box : Holds parameters to be passed to add_meta_box function
		* @param int    $post_id : Contains ID of post.
		* @param int 	$old : Old values of inputs saved in database
		* @param int 	$new : New values of inputs to be updated in database
		*
		*/
		public function coach_metabox_save( $post_id ) {
			global $coach_box;

			// verify nonce
			if ( isset($_POST['coach_meta_box_nonce']) && !wp_verify_nonce( $_POST['coach_meta_box_nonce'], basename(__FILE__) ) )
				{
				return $post_id;
			}

			// check autosave
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// check permissions
			if ( 'page' == $_POST['post_type'] ) {
				if (!current_user_can('edit_page', $post_id)) {
					return $post_id;
				}
			} elseif (!current_user_can('edit_post', $post_id)) {
				return $post_id;
			}

			foreach ( $coach_box['fields'] as $field ) {
				$old = get_post_meta( $post_id, $field['id'], true );
				$new = $_POST[$field['id']];
				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ('' == $new && $old) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
			}
		}

		/**
		* Create company name & client location field in Coach post
		*
		* Function Name: add_thumbnail_column
		**/
		public function add_thumbnail_column( $columns ) {
			unset($columns['wpex_post_thumbs']);
			unset($columns['wpseo-score']);
			unset($columns['wpseo-links']);
			unset($columns['wpseo-score-readability']);

			$column_coach = array( 'sort_order' => __('Sort Order','coaches' ));
			$columns = array_slice( $columns, 0, 1, true ) + array_slice( $columns, 1, 1, true ) + $column_coach + array_slice( $columns, 1, NULL, true );
			return $columns;
		}

		function display_thumbnail( $column ) {
			global $post;

			switch ( $column ) {
				case 'sort_order':
					echo  $sort_order = get_post_meta($post->ID, 'coach_sort_order', true);
					break;

			}
		}

		function hide_coach_template($query) {
			global $pagenow;

			if( 'edit.php' != $pagenow || !$query->is_admin )
				return $query;

			$user = wp_get_current_user();
			$roles = wp_get_current_user()->roles;


			if (!in_array("administrator", $roles)) { 
				$post_status = array( 'publish', 'pending', 'draft', 'future' );
				$query->set("post_status", $post_status);	    			
			}
			return $query;
		}

		/**
		* Updating messages
		*
		* Function Name: coach_set_messages
		*
		* @param var  $message : Holds message to be displayed
		**/
		public function coach_set_messages($messages) {

			global $post, $post_ID;
			$post_type = get_post_type( $post_ID );

			$obj = get_post_type_object( $post_type );
			$singular = $obj->labels->singular_name;

			$messages[$post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __($singular.' updated. <a href="%s">View '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __($singular .'Custom field updated.'),
			3 => __($singular.'Custom field deleted.'),
			4 => __($singular.' updated.'),
			5 => isset($_GET['revision']) ? sprintf( __($singular.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __($singular.' published. <a href="%s">View '.strtolower($singular).'</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Page saved.'),
			8 => sprintf( __($singular.' submitted. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __($singular.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.strtolower($singular).'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __($singular.' draft updated. <a target="_blank" href="%s">Preview '.strtolower($singular).'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			);
			return $messages;
		}

		/**
		* Rename Featured image title text
		*
		* Function Name: client_coach_change_image_box
		**/
		public function client_coach_change_image_box() {
			remove_meta_box( 'postimagediv', 'coaches', 'side' );
			add_meta_box( 'postimagediv', __('Client Image (150px * 150px)'), 'post_thumbnail_meta_box', 'coaches', 'side', 'low' );
		}

		public function coach_default_title( $title ){
			$screen = get_current_screen();
			if ( 'coaches' == $screen->post_type ){
				$title = 'Enter the name';
			}
			return $title;
		}

	}
	new Coach_Post_Type;


}
