<div class="prisna_social_counter_section prisna_social_counter_{{ type }}{{ dependence.show.false:begin }} prisna_social_counter_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_social_counter_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">
{{ description.true:begin }}	
	<div class="prisna_social_counter_tooltip"></div>
	<div class="prisna_social_counter_description prisna_social_counter_no_display">{{ description_message }}</div>
{{ description.true:end }}
{{ title.true:begin }}
	<div class="prisna_social_counter_title_container prisna_social_counter_icon prisna_social_counter_icon_paint"><h3 class="prisna_social_counter_title">{{ title_message }}</h3></div>
{{ title.true:end }}
	<div class="prisna_social_counter_setting{{ title.false:begin }} prisna_social_counter_setting_no_title{{ title.false:end }}">

{{ collection_formatted }}
		
		<div class="prisna_social_counter_clear"></div>
	
	</div>
</div>