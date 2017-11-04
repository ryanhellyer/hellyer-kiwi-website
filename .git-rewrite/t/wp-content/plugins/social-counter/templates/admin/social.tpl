<div class="prisna_social_counter_{{ type }}_{{ kind }} prisna_social_counter_section prisna_social_counter_{{ type }}{{ dependence.show.false:begin }} prisna_social_counter_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_social_counter_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">
{{ description.true:begin }}	
	<div class="prisna_social_counter_tooltip"></div>
	<div class="prisna_social_counter_description prisna_social_counter_no_display">{{ description_message }}</div>
{{ description.true:end }}
{{ title.true:begin }}
	<div class="prisna_social_counter_title_container prisna_social_counter_icon prisna_social_counter_icon_paint"><h3 class="prisna_social_counter_title">{{ title_message }}</h3></div>
{{ title.true:end }}
	<div class="prisna_social_counter_setting{{ title.false:begin }} prisna_social_counter_setting_no_title{{ title.false:end }}">

		<input type="hidden" name="{{ id }}_order" id="{{ id }}_order" value="{{ group }}" class="prisna_social_counter_social_order" />

		<table border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="0">
							<tbody>
								{{ enabled_formatted }}
								{{ name_formatted }}
								{{ background_color_formatted }}
								{{ icon_color_formatted }}
								{{ text_color_formatted }}
							</tbody>
						</table>
					</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0">
							<tbody>
								{{ current_formatted }}
								{{ format_formatted }}
								{{ unit_formatted }}
							</tbody>
						</table>
					</td>
					<td class="prisna_social_counter_preview" id="prisna_social_counter_preview_{{ kind }}">
						<table border="0" cellpadding="0" cellspacing="0" class="prisna_social_counter_no_display">
							<tbody>
								<tr><td><div class="prisna_social_counter_preview_title">Preview</div></td></tr>
								<tr>
									<td>
										<div class="prisna_social_counter_preview_container">
											<ul>
												<li class="prisna_social_counter_network" id="prisna_social_counter_network_{{ kind }}">
													<a href="javascript:;" onclick="PrisnaSocialCounterAdmin.previewLink('{{ kind }}');" target="_blank">
														<i class="prisna_social_counter_network_icon"></i>
														<span class="prisna_social_counter_value">2M</span>
														<span class="prisna_social_counter_unit">Likes</span>
													</a>
												</li>
											</ul>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="prisna_social_counter_shortcode_container">{{ shortcode_formatted }}<br/><div id="{{ id }}_shortcode" class="prisna_social_counter_shortcode"></div></td>
				</tr>
			</tbody>
		</table>

		{{ has_dependence.true:begin }}
		<input type="hidden" name="{{ id }}_dependence" id="{{ id }}_dependence" value="{{ formatted_dependence }}" />
		<input type="hidden" name="{{ id }}_dependence_show_value" id="{{ id }}_dependence_show_value" value="{{ formatted_dependence_show_value }}" />
		{{ has_dependence.true:end }}	

		{{ nonce }}

		<div class="prisna_social_counter_clear"></div>
	
	</div>
</div>