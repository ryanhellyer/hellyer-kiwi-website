
<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<table class="form-table">

		<?php

		// Grab options array and output a new row for each setting
		foreach ( $this->fields as $key => $field ) {

			$slug = $field[ 'slug' ];
			$label = $field[ 'label' ];
			$type = $field[ 'type' ];
			$description = $field[ 'description' ];
			$default = '';
			if ( isset( $field[ 'default' ] ) ) {
				$default = $field[ 'default' ];
			}

			$value = $default;
			$option = get_option( self::SETTINGS_OPTION . '-' . $slug);
			if ( isset( $option ) ) {
				$value = $option;
			}

			$class_name = 'regular-text';
			if ( 'number' === $type ) {
				$class_name = 'small-text';
			}

			if ( 'textarea' === $type ) {
				$tag = '<textarea name="' . esc_attr( self::SETTINGS_OPTION . '-' . $slug ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" class="large-text code" rows="5" cols="50">' . esc_textarea( $value ) . '</textarea>';
			} else if ( 'checkbox' === $type ) {
				$tag = '<input name="' . esc_attr( self::SETTINGS_OPTION . '-' . $slug ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" ' . checked( 'on', $value, false ) . ' value="on" class="' . esc_attr( $class_name ) . '" />';
			} else {
				$tag = '<input name="' . esc_attr( self::SETTINGS_OPTION . '-' . $slug ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '" class="' . esc_attr( $class_name ) . '" />';
			}

			echo '

			<tr>
				<th scope="row">' . esc_html( $label ) . '</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>' . esc_html( $label ) . '</span></legend>
						<label for="' . esc_attr( $slug ) . '">
							' . $tag . '
							' . wp_kses_post( $description ) . '
						</label>
						<p class="description"></p>
					</fieldset>
				</td>
			</tr>
			';
		}

		?>
		</table>

		<?php settings_fields( 'strattic-settings' ); ?>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save', 'strattic' ); ?>" />
		</p>

	</form>

</div><?php
