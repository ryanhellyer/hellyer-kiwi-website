<?php
/**
 * The Legal Notice template file.
 * This file should not be translated.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
get_header();

echo '<article>';
$markdown = new Free_Advice_Berlin_Markdown();
$content = file_get_contents( dirname( __FILE__ ) . '/legal-notice.txt' );
echo $markdown->text( $content );
echo '</article>';

get_footer();
