<style>
.form-table .inactive td fieldset label,
.form-table .inactive th {
	color: #ccc;
}
</style>

<table class="form-table">

<?php

// Grab options array and output a new row for each setting
$options = get_option( self::SETTINGS_OPTION );
foreach ( $this->fields() as $key => $field ) {

	$slug        = $field['slug'];
	$label       = $field['label'];
	$description = $field['description'];

	$type        = $field['type'];
	if ( 'editor' === $type ) {
		continue; // Ignore editor fields as these are handled on a seperate admin page
	}

	$default     = '';
	if ( isset( $field['default'] ) ) {
		$default = $field['default'];
	}

	$value = $default;
	if ( isset( $options[ $slug ] ) ) {
		$value = $options[ $slug ];
	}

	$class_name = 'regular-text';
	if ( 'number' === $type ) {
		$class_name = 'small-text';
	}

	$disabled       = '';
	$disabled_block = '';
	if ( true !== $field['active'] ) {
		$disabled = ' disabled="disabled"';
		$alert_text = __( 'Sorry, this feature is only available in Strattic Search Pro.', 'strattic-search' );
		$disabled_block = ' class="inactive" onclick="alert(\'' . esc_attr( $alert_text ) . '\')"';
	}

	if ( 'textarea' === $type ) {
		$tag = '<textarea' . $disabled . ' name="' . esc_attr( self::SETTINGS_OPTION . '[' . $slug . ']' ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" class="large-text code" rows="5" cols="50">' . $value /* No escaping due to use of HTML here */ . '</textarea>';
	} else if ( 'checkbox' === $type ) {
		$tag = '<input' . $disabled . '  name="' . esc_attr( self::SETTINGS_OPTION . '[' .$slug . ']' ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" ' . checked( 'on', $value, false ) . ' value="on" class="' . esc_attr( $class_name ) . '" />';
	} else if ( 'number' === $type ) {
		$tag = '<input' . $disabled . '  step="0.1" name="' . esc_attr( self::SETTINGS_OPTION . '[' .$slug . ']' ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '" class="' . esc_attr( $class_name ) . '" />';
	} else {
		$tag = '<input' . $disabled . '  name="' . esc_attr( self::SETTINGS_OPTION . '[' .$slug . ']' ) . '" id="' . esc_attr( $slug ) . '" type="' . esc_attr( $type ) . '" value="' . esc_attr( $value ) . '" class="' . esc_attr( $class_name ) . '" />';
	}

	echo '

	<tr' . $disabled_block . '>
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
