<?php

if ( isset( $_GET['stats'] ) ) {
        print_r( get_option( 'spam-destroyer-stats' ) );
        die;
}

