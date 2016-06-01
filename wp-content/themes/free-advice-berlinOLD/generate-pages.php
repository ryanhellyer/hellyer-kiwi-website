<?php
$pages = array(
'Apartment search',
'nmeldung information/new in Berlin?',
'Abmeldung',
'Welcome to Berlin',
'cheap seconhand bikes to buy',
'Biking in Berlin',
'Bike fixing',
'Laptop Friendly Cafes in Berlin',
'Cleaning Services / Putzfrau',
'Currency exchange: fair rate',
'Doctors',
'Door locks',
'Fixing/repairing/transporting',
'Flohmarkts/street markets',
'food:',
'Berliner Currywurst',
'Vegan',
'Kebab',
'Brownies',
'Shushi',
'Sunday brunch',
'German language: ',
'House rules',
'Immigration related help in Berlin',
'Jobs in Berlin?',
'Libraries and shops for books in English',
'Lawyers (job and Bluecard)',
'Leaving Germany for good!',
'Locked door!',
'Open supermarkets 365 days',
'Pubs in Berlin',
'places in Berlin',
'Recycling',
'Rental problems',
'Rundfunkbeitrag - TV/radio license',
'Skills Sharing',
'Spice shops (country oriented)',
'TAX! Steurberater',
'Transport: Cheap tickets for bus, train and plain tickets',
'Techno places',
'Volunteering for Refugee',
'Waste Management',
'Winter precautions/ Save energy ',
'Wine Shops',
);
if ( !is_admin() ){
foreach ( $pages as $page_name ) {
	$args = array(
		'post_title'    => $page_name,
		'post_content'  => 'This is dummy content.',
		'post_status'   => 'publish',
		'post_type'     => 'page',
		'post_author'   => 1,
	);
	wp_insert_post( $args );
}
die('done');
}

