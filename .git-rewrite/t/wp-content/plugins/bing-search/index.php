<?php
/*
Plugin Name: Bing Search
Plugin URI: http://geek.ryanhellyer.net/
Description: Bing Search

Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/

Copyright 2013 Metronet

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

define( 'BING_SEARCH_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'BING_SEARCH_URL', plugins_url( '/', __FILE__ ) ); // Plugin folder URL

require( 'inc/class-bing-search.php' );
require( 'inc/class-bing-search-admin.php' );
