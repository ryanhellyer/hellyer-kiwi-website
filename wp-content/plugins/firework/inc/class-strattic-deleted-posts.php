<?php

/**
 * Class to save post URLs before permanent deletion.  These URLs are then
 * added to the scraper list during publish so that they are correctly deleted
 * from the stattic site.
 */
class Strattic_Deleted_Posts extends Strattic_Core {
    const OPTION_KEY = "strattic-deleted-posts";

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct() {
        add_action( 'before_delete_post', array( $this, 'cache_post_url' ) );
    }


    /**
     * Store the URL of a given post
     *
     * @param  mixed $postid
     *
     * @return void
     */
    public function cache_post_url( $postid ) {
        $post = get_post( $postid );

        $url = Strattic_URLs::get_published_url( get_post_status( $post ), get_the_permalink( $post ));

        foreach( $this->get_distribution_info() as $info) {
            $key = self::get_option_key( $info[ 'type' ] );
            $deleted_posts = get_option( $key );
            if( ! is_array( $deleted_posts ) ) {
                $deleted_posts = array( $deleted_posts );
            }
            $deleted_posts[] = $url;
            update_option( $key, $deleted_posts );
        }
    }

    
    /**
     * Get correct key for wp_options table given distribution type.
     *
     * @param  mixed $distribution_type
     *
     * @return void
     */
    public static function get_option_key( $distribution_type ) {
        return self::OPTION_KEY . "-" . $distribution_type;
    }
}