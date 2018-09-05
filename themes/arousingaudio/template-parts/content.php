
		<div id="content-wrapper">
			<div id="content-wrapper-inner">

				<h1 id="title"><?php echo esc_html( $data[ 'title' ] ); ?></h1>

				<div id="content"><?php echo $data[ 'content' ]; ?></div>

				<!-- Audio visualiser -->
				<canvas id="canvas" height="350"></canvas>

			</div>
		</div>

		<!-- Wrapper for comments -->
		<div id="comments"><?php echo $data[ 'comments' ]; ?></div>
