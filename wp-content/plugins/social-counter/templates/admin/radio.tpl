<div class="prisna_social_counter_section prisna_social_counter_{{ type }}{{ dependence.show.false:begin }} prisna_social_counter_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_social_counter_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">
	
	<div class="prisna_social_counter_tooltip"></div>
	<div class="prisna_social_counter_description prisna_social_counter_no_display">{{ description_message }}</div>
		
	<div class="prisna_social_counter_title_container prisna_social_counter_icon prisna_social_counter_icon_grid2"><h3 class="prisna_social_counter_title">{{ title_message }}</h3></div>
	<div class="prisna_social_counter_setting">
		<div class="prisna_social_counter_field" id="{{ id }}">
			<table border="0" cellspacing="0" cellpadding="0" id="{{ id }}">
				<tr>
{{ collection_formatted }}
				</tr>
			</table>
		</div>
		
		<div class="prisna_social_counter_clear"></div>
	
	</div>
</div>
