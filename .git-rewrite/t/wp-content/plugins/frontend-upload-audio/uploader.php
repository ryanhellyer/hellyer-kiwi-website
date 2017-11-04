<?php

$content .= '
<form enctype="multipart/form-data" action="http://arousingaudio.com/upload/" method="POST">

<!--
<form action="" method="post" enctype="multipart/form-data">
-->

	<p>
		<label>Upload a file</label>
		<input type="file" name="audio_upload[]" />
	</p>

	<p>
		<label>Describe your upload</label>
		<input name="audio-title" type="text" placeholder="Title" />
		<input name="audio-tagline" type="text" placeholder="Tagline: A short piece of text describing your upload" />
		<textarea name="audio-description" name="" placeholder="Add a description of your upload"></textarea>
	</p>

	<p>
		<fieldset>
			<legend>Genre</legend>';

// Loop through terms and add CSS to each
$terms = get_terms( array(
	'taxonomy'   => 'genre',
	'hide_empty' => true,
) );
$css = '';
foreach ( $terms as $key => $term ) {
	$content .='

			<label>' . esc_html( $term->name ) . '</label>
			<input name="audio-genre[]" value="' . esc_html( $term->slug ) . '" type="checkbox" />

			<br />';
}

$content .= '
		</fieldset>
	</p>

	<p>
		<input type="submit" value="Upload" />
	</p>

</form>
';
