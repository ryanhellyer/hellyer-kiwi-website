		<tr>
			<td class="prisna_social_counter_row_field_name">{{ title_message }}</td>
			<td>
				<div class="prisna_social_counter_field" id="{{ id }}">
					<div class="prisna_social_counter_toggle_container">
						<input type="radio" name="{{ name }}" value="{{ value_true }}"{{ value_true.checked.true:begin }} checked="checked"{{ value_true.checked.true:end }} id="{{ id }}_true" class="prisna_social_counter_radio_option" />
						<label for="{{ id }}_true">{{ option_true }}</label>
					</div>
					<div class="prisna_social_counter_toggle_container">
						<input type="radio" name="{{ name }}" value="{{ value_false }}"{{ value_false.checked.true:begin }} checked="checked"{{ value_false.checked.true:end }} id="{{ id }}_false" class="prisna_social_counter_radio_option" />
						<label for="{{ id }}_false">{{ option_false }}</label>
					</div>
				</div>
			</td>
			<td class="prisna_social_counter_row_field_tooltip"></td>
		</tr>