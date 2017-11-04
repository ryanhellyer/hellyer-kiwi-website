<?php

/**
 * Get audio posts as formatted array.
 *
 * @param   int    $current_post_id   The ID for the current page
 * @global  array  $audio_posts       An array of all audio post data. This data is kept in the array to be used as a cache for if function is reused.
 * @return  array  An array of all audio post data
 */
function arousingaudio_get_posts( $current_post_id = null ) {
	global $audio_posts;

	// If already set, then just spit it back out again (since it's been cached in the global)
	if ( isset( $audio_posts ) ) {
		return $audio_posts;
	}

	// Create query
	$audio_query = new WP_Query(
		array(
			'posts_per_page' => 100,
			'no_found_rows'  => true,
			'post_type'      => 'audio',
		)
	);

	// Loop through and generate the required array
	if ( $audio_query->have_posts() ) {
		$audio_posts = array();
		while ( $audio_query->have_posts() ) {
			$audio_query->the_post();

			$audio_file_id = get_post_meta( get_the_ID(), '_audio_file_id', true );
			$audio_file_meta = wp_get_attachment_metadata( $audio_file_id );
			$slug = get_post_field( 'post_name' );

			$terms = wp_get_post_terms( get_the_ID(), 'genre' );
			$posts_terms = array();
			foreach ( $terms as $key => $term ) {
				$posts_terms[] = array(
					'name'  => esc_html( $term->name ),
					'slug'  => esc_html( $term->slug ),
				);
			}

			if ( defined( 'TEST' ) ) {
				$title =  substr( 'Title ' . md5( get_the_ID() ) ,0,10 );
				$excerpt = 'Excerpt '. substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 );
				$content = 'Content ' . $audio_posts[ $slug ]['excerpt'] . $audio_posts[ $slug ]['excerpt'] . $audio_posts[ $slug ]['excerpt'] . $audio_posts[ $slug ]['excerpt'] . $audio_posts[ $slug ]['excerpt'] . $audio_posts[ $slug ]['excerpt'] . $audio_posts[ $slug ]['excerpt'];
			} else {
				$title   = get_the_title();
				$excerpt = get_the_excerpt();
				$content = apply_filters( 'the_content', get_the_content() );
			}


			if ( isset( $audio_file_meta[ 'length' ] ) ) {
				$length = $audio_file_meta[ 'length' ];
			} else {
				$length = 0;
			}

			if ( isset( $audio_file_meta[ 'sample_rate' ] ) ) {
				$sample_rate = $audio_file_meta[ 'sample_rate' ];
			} else {
				$sample_rate = 0;
			}

			if ( isset( $audio_file_meta[ 'channels' ] ) ) {
				$channels = $audio_file_meta[ 'channels' ];
			} else {
				$channels = 0;
			}

			$audio_posts[] = array(
				'id'             => get_the_ID(),
				'slug'           => $slug,
				'title'          => $title,
				'excerpt'        => $excerpt,
				'content'        => $content,
				'thumbs_up'      => arousingaudio_get_ratings( 'up', 'both', get_the_ID() ),
				'thumbs_down'    => arousingaudio_get_ratings( 'down', 'both', get_the_ID() ),
				'post_type'      => get_post_type( get_the_ID() ),
				'genre-terms'    => $posts_terms,

				// May not be needed, just dumping here in case they're useful later
				'length'         => (string) absint( $length ),
				'sample_rate'    => (string) absint( $sample_rate ),
				'audio_channels' => (string) absint( $channels ),
			);

		}
	}

	return $audio_posts;
}


/**
 * Get audio posts as formatted array.
 *
 * @param   int     $id    The ID for the post
 * @return  array   The data required for building a particular post
 */
function arousingaudio_get_post( $id ) {

	$the_query = new WP_Query(
		array(
			'p'         => absint( $id ),
			'post_type' => array( 'any' ),
		)
	);

	if ( $the_query->have_posts() ) {

		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$data[ 'slug' ]        = sanitize_title( get_post_field( 'post_name' ) );
			$data[ 'title' ]       = esc_html( get_the_title() );
			$data[ 'content' ]     = apply_filters( 'the_content', get_the_content() );

if ( defined( 'TEST' ) && 'audio' == get_post_type( get_the_ID() ) ) {
	$data['title'] =  substr( 'Title ' . md5( get_the_ID() ) ,0,10 );
	$bla = substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 ) . ' ' . substr( 'Title ' . md5( get_the_ID() ) ,0,8 );
	$data['content'] = 'Content ' . $bla .$bla.$bla .$bla;
}
			$data['content'] = $data['content'];

			$data[ 'thumbs_up' ]   = arousingaudio_get_ratings( 'up', 'both', get_the_ID() );
			$data[ 'thumbs_down' ] = arousingaudio_get_ratings( 'down', 'both', get_the_ID() );
			$data[ 'post_type' ]   = get_post_type( get_the_ID() );

			// May not be needed, just dumping here in case they're useful later
			$data[ 'length' ]         = (string) absint( $audio_file_meta[ 'length' ] );
			$data[ 'sample_rate' ]    = (string) absint( $audio_file_meta[ 'sample_rate' ] );
			$data[ 'audio_channels' ] = (string) absint( $audio_file_meta[ 'channels' ] );

			if ( 'audio' == get_post_type() ) {
				$data[ 'audio' ] = true;
			}

			// Get comments section as a big HTML string
			ob_start();
			global $withcomments;
			$withcomments = 1;
			if ( comments_open() || '0' != get_comments_number() ) {
				comments_template( '', true );
			}
			$comments = ob_get_contents();
			ob_end_clean();

			$data[ 'comments' ] = $comments;

	    }
	}

	return $data;
}

function arousingaudio_get_ratings( $direction, $_logged_in = false, $id ) {

	if ( 'up' == $direction ) {
		$value = 1;
	} else {
		$value = 0;
	}

	$ratings = array();
	if ( false == $_logged_in || "both" == $_logged_in ) {
		$ratings = get_post_meta( $id, '_ratings', true );
		if ( ! is_array( $ratings ) ) {
			$ratings = array();
		}
	}

	$ratings_logged_in = array();
	if ( true == $_logged_in || "both" == $_logged_in ) {
		$ratings_logged_in = get_post_meta( $id, '_ratings_logged_in', true );
		if ( ! is_array( $ratings_logged_in ) ) {
			$ratings_logged_in = array();
		}
	}

	$ratings_count = array_merge( $ratings, $ratings_logged_in );
	$rating_counts = array_count_values( $ratings_count );

	// If no ratings given, then set to zero
	if ( ! isset( $rating_counts[0] ) ) {
		$rating_counts[0] = 0;
	}
	if ( ! isset( $rating_counts[1] ) ) {
		$rating_counts[1] = 0;
	}

	return $rating_counts[ $value ];
}
