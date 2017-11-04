<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after.
 *
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */
?>

</div>

<footer id="footer">

	<!-- Time stamp -->
	<div id="time-stamp">
		<span></span>
		<div id="time-elapsed-line"></div>
	</div>

	<!-- Track information -->
	<div id="track-information">

		<!-- Track description -->
		<a href="#" id="track-description">
			<h2></h2>
			<p></p>
		</a>

	</div>

		<!-- Ratings -->
		<div id="ratings">

			<div id="thumbs-up" class="icon-button">
				<span>Thumbs up</span>
				<span id="thumbs-up-value" class="value"></span>
			</div>

			<div id="thumbs-down" class="icon-button">
				<span>Thumbs down</span>
				<span id="thumbs-down-value" class="value"></span>
			</div>

		</div>


	<!-- Volume controls -->
	<div id="volume-wrapper">

		<!-- Mute button -->
		<div id="mute" class="icon-button"></div>

		<!-- Volume control -->
		<div id="volume-control">
			<span></span>
		</div>

	</div>

	<!-- Wrapper for play button controls -->
	<div id="play-wrapper">

		<!-- Repeat button -->
		<div id="repeat-button" class="icon-button">
			<span>Repeat</span>
		</div>

		<!-- Previous track button -->
		<div id="previous" class="icon-button">
			<span>Previous</span>
		</div>

		<!-- Play button -->
		<div id="play" class="icon-button">
			<span>Play</span>
		</div>

		<!-- Next track button -->
		<div id="next" class="icon-button">
			<span>Next</span>
		</div>

		<!-- Shuff button -->
		<div id="shuffle-button" class="icon-button">
			<span><?php _e( 'Shuffle', 'arousingaudio' ); ?></span>
		</div>

	</div>




<div style="display:none;">
	<div id="volume-value-wrapper"><span id="volume-value">0</span>%</div>
	<div id="current-time" style="width:5%;height:30px;border:1px solid red;">Current time</div>
	<div id="duration-time" style="width:5%;height:30px;border:1px solid lime;">Duration time</div>
</div>




</footer>

<audio id="audio-player">
	<source type="audio/mp3">
	Your browser does not support the audio tag.
</audio>

<?php wp_footer(); ?>

</body>
</html>