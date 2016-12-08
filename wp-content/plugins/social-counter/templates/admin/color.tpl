<div class="prisna_social_counter_section prisna_social_counter_{{ type }}{{ dependence.show.false:begin }} prisna_social_counter_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_social_counter_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">

	<div class="prisna_social_counter_tooltip"></div>
	<div class="prisna_social_counter_description prisna_social_counter_no_display">{{ description_message }}</div>

	<div class="prisna_social_counter_title_container prisna_social_counter_icon prisna_social_counter_icon_paint"><h3 class="prisna_social_counter_title">{{ title_message }}</h3></div>
	<div class="prisna_social_counter_setting">
		<div class="prisna_social_counter_field">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><input class="prisna_social_counter_color_picker_view" name="{{ id }}_view" id="{{ id }}_view" type="text" value="" readonly="readonly" style="background-color: {{ value }};" /></td>
					<td><input class="prisna_social_counter_color_picker" name="{{ id }}" id="{{ id }}" type="text" value="{{ value }}" spellcheck="false" /></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="prisna_social_counter_clear"></div>

</div>
