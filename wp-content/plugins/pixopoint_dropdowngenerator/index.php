<?php
/*
Plugin Name: PixoPoint Web Development CSS generator widget
Plugin URI: http://pixopoint.com/
Description: CSS generator widget
Version: 1.0
Author: PixoPoint Web Development
Author URI: http://pixopoint.com/
License: Copyright PixoPoint Web Development. This software may not be used without permission of PixoPoint Web Development.
*/

$pixo_array['pixo_logged'] = 'premium';

define( 'DROPDOWNGEN_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'DROPDOWNGEN_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL

function pixo_fix_debug_probs() {
	$pixo_vars = array(
		'pixo_logged',
		'borderwidth',
		'suckerfish_background_colour',
		 'suckerfish_background_image',
		 'suckerfish_lineheight',
		 'suckerfish_hover_backgroundcolour',
		 'suckerfish_colour',
		 'suckerfish_hover_color',
		 'suckerfish_fontsize',
		 'suckerfish_fontfamily',
		 'pixo_logged',
		 'suckerfish_dropcolour',
		 'suckerfish_drophover_backgroundcolor',
		 'suckerfish_drop_hovercolor',
		 'suckerfish_drop_colour',
		 'suckerfish_drop_fontsize',
		 'suckerfish_drop_fontfamily',
		 'suckerfish_dropdown_opacity',
		 'pixo_logged',
		 'pixo_logged',
		 'suckerfish_dropdown_ulborder', 
		 'suckerfish_dropdown_width',
		 'suckerfish_flyoutdirection',
		 'suckerfish_flyoutwidth',
		'suckerfish_dropdownbordercolour',
		'suckerfish_wordpressoptomised',
		'suckerfish_buttonized',
		'suckerfish_buttonized',
		 'suckerfish_current_page_item_backgroundcolour',
		 'suckerfish_current_page_parent_backgroundcolour',
		 'suckerfish_current_page_item_indicatorcolour',
		'suckerfish_current_page_item_indicatorsymbol',
		 'suckerfish_current_page_parent_indicatorcolour',
		 'suckerfish_current_page_parent_indicatorsymbol',
		 'suckerfish_design',
		'suckerfish_type',
		'suckerfish_current_page_item_backgroundcolour',
		'suckerfish_current_page_parent_backgroundcolour',
		'suckerfish_dropdownbordercolour',
		'suckerfish_flyoutrepeatimage',
		'suckerfish_fontfamily',
		'suckerfish_background_colour',
		'suckerfish_background_image',
		'suckerfish_hover_backgroundcolour',
		'suckerfish_colour',
		'suckerfish_hover_color',
		'suckerfish_fontsize',
		'suckerfish_drop_fontfamily',
		'suckerfish_fontweight',
		'suckerfish_lineheight',
		'suckerfish_padding_horisontal',
		'suckerfish_border_width',
		'suckerfish_border_colour',
		'suckerfish_dropcolour',
		'suckerfish_drophover_backgroundcolor',
		'suckerfish_drop_colour',
		'suckerfish_drop_hovercolor',
		'suckerfish_drop_fontsize',
		'suckerfish_drop_fontweight',
		'suckerfish_dropdown_opacity',
		'suckerfish_dropdown_verticalpadding',
		'suckerfish_dropdown_border',
		'suckerfish_dropdown_width',
		'suckerfish_flyoutdirection',
		'suckerfish_type',
		'suckerfish_current_page_item_backgroundcolour',
		'suckerfish_current_page_parent_backgroundcolour',
		'suckerfish_dropdownbordercolour',
		'suckerfish_flyoutrepeatimage',
		'suckerfish_fontfamily',
		'suckerfish_background_colour',
		'suckerfish_background_image',
		'suckerfish_hover_backgroundcolour',
		'suckerfish_colour',
		'suckerfish_hover_color',
		'suckerfish_fontsize',
		'suckerfish_drop_fontfamily',
		'suckerfish_fontweight',
		'suckerfish_lineheight',
		'suckerfish_padding_horisontal',
		'suckerfish_border_width',
		'suckerfish_border_colour',
		'suckerfish_dropcolour',
		'suckerfish_drophover_backgroundcolor',
		'suckerfish_drop_colour',
		'suckerfish_drop_hovercolor',
		'suckerfish_drop_fontsize',
		'suckerfish_drop_fontweight',
		'suckerfish_dropdown_opacity',
		'suckerfish_dropdown_verticalpadding',
		'suckerfish_dropdown_border',
		'suckerfish_dropdown_width',
		'suckerfish_flyoutdirection',
		'suckerfish_type',
		'suckerfish_current_page_item_backgroundcolour',
		'suckerfish_current_page_parent_backgroundcolour',
		'suckerfish_dropdownbordercolour',
		'suckerfish_flyoutrepeatimage',
		'suckerfish_fontfamily',
		'suckerfish_background_colour',
		'suckerfish_background_image',
		'suckerfish_hover_backgroundcolour',
		'suckerfish_colour',
		'suckerfish_hover_color',
		'suckerfish_fontsize',
		'suckerfish_drop_fontfamily',
		'suckerfish_fontweight',
		'suckerfish_lineheight',
		'suckerfish_padding_horisontal',
		'suckerfish_border_width',
		'suckerfish_border_colour',
		'suckerfish_dropcolour',
		'suckerfish_drophover_backgroundcolor',
		'suckerfish_drop_colour',
		'suckerfish_drop_hovercolor',
		'suckerfish_drop_fontsize',
		'suckerfish_drop_fontweight',
		'suckerfish_dropdown_opacity',
		'suckerfish_dropdown_verticalpadding',
		'suckerfish_dropdown_border',
		'suckerfish_dropdown_width',
		'suckerfish_flyoutdirection',
		'suckerfish_type',
		'suckerfish_current_page_item_backgroundcolour',
		'suckerfish_current_page_parent_backgroundcolour',
		'suckerfish_dropdownbordercolour', 
		'suckerfish_flyoutrepeatimage',
		'suckerfish_fontfamily',
		'suckerfish_background_colour',
		'suckerfish_background_image',
		'suckerfish_hover_backgroundcolour',
		'suckerfish_colour',
		'suckerfish_hover_color',
		'suckerfish_fontsize',
		'suckerfish_drop_fontfamily',
		'suckerfish_fontweight',
		'suckerfish_lineheight',
		'suckerfish_padding_horisontal',
		'suckerfish_border_width',
		'suckerfish_border_colour',
		'suckerfish_dropcolour',
		'suckerfish_drophover_backgroundcolor',
		'suckerfish_drop_colour',
		'suckerfish_drop_hovercolor',
		'suckerfish_drop_fontsize',
		'suckerfish_drop_fontweight',
		'suckerfish_dropdown_opacity',
		'suckerfish_dropdown_verticalpadding',
		'suckerfish_dropdown_border',
		'suckerfish_dropdown_width',
		'suckerfish_flyoutdirection',
		'suckerfish_type', 
		'suckerfish_current_page_item_backgroundcolour',
		'suckerfish_design',
	);
	foreach( $pixo_vars as $key ) {
		$pixo_array[$key] = '';
	}
	return $pixo_array;
}
	
	
	


add_action('wp_print_scripts', 'pixopoint_dropdowngenhead');
function pixopoint_dropdowngenhead() {
	if ( ! is_page( DROPDOWNGEN_PAGEID ) )
		return;



	echo '
<script type="text/javascript" src="' . DROPDOWNGEN_URL . '/animatedcollapse.js"></script>
<script type="text/javascript" src="' . DROPDOWNGEN_URL . '/picker.js.php?plugin_url=' . DROPDOWNGEN_URL . '"></script>
<zscript type="text/javascript" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/picker.js"></script>
<style type="text/css">
	label {font-family:sans-serif;color:#666;font-size:1em;font-weight:bold}
	input {font-family:sans-serif;color:#666;float:right}
	select {font-family:sans-serif;color:#666;float:right}
	option {font-family:sans-serif;color:#666}
	p span {font-size:0.8em}

	.column {float:left;width:305px;margin-right:10px;font-size:0.8em;background:#eee}
	.column h3 {font-size:1em}

	.message {clear:right;font-family:sans-serif;color:#666;padding:0 5px 5px 5px;font-size:0.8em}
	.gap {border-top:2px solid #fff;padding:5px 5px 0 5px}
	.gap a {float:right}

	#maincontent_holder p {text-align:left;font-size:0.8em}
	p span {text-align:left;line-height:1em;font-size:0.8em;padding:0 0 10px 0;display:block;border-bottom:1px solid #dddddd}
	.generatorblock {float:left;width:44%;background-color:#FFFFFF;border:1px solid #aaaaaa;border-bottom:0;margin:3px;padding:3px}
	.generatorblock div {font-family:sans-serif;font-size:1em;color:#aaa}
	.generatorblock div a {color:#E17933;text-decoration:none}
	.generatorblock div a:hover {text-decoration:underline}
	.topgeneratorblock {float:left;width:40%}
	#maincontent_holder p a.temp1 {float:right}
	input {float:right;width:60px}
	select {float:right}
	#generatorcodebox {border:1px solid #aaaaaa;max-width:600px;background:#ffffff;display:block;overflow:scroll;height:300px}
	#generatorcodeboxtitle {clear:left}
	.premiumonly, #secondlevel, #all-levels, #toplevel {background:#eeeeee}
	.premiumonly {padding-top:10px}
	</style>';
}






function pixopoint_cssgeneratormenu() {
	?>
	<ul class="sf-menu" id="suckerfishnav">
		<li><a href="<?php echo home_url(); ?>/">Home</a></li>
		<li class="haschildren"><a href="#" >Products</a>
			<ul>
				<li><a href="<?php echo home_url(); ?>/products/pixopoint-menu/" >PixoPoint Menu Plugin</a></li>
				<li><a href="<?php echo home_url(); ?>/products/generator/" >Theme Generator</a></li>
				<li><a href="<?php echo home_url(); ?>/products/multi-level-navigation/" >Multi-level Navigation Plugin for WordPress</a></li>
				<li><a href="<?php echo home_url(); ?>/products/simplecms/" >Simple CMS plugin for WordPress</a></li>
				<li><a href="<?php echo home_url(); ?>/products/bbpress-forum-theme-generator/" >Beta: bbPress Forum Theme Generator</a></li>
				<li><a href="<?php echo home_url(); ?>/products/simple-cms-theme-update/" >Simple CMS theme</a></li>
			</ul>
		</li>
	
		<li class="current_page_ancestor"><a href="#">Stuff</a>
			<ul>
				<li class="current_page_ancestor"><a href="#">Another Level</a>
					<ul>
						<li class="current_page_ancestor"><a href="#">Next Level</a>
							<ul>
								<li><a href="#">Test</a></li>
								<li><a href="#">Test</a></li>
								<li><a href="#">Test</a></li>
								<li><a href="#">Test</a></li>
							</ul>
						</li>
						<li><a href="#">Test</a></li>
						<li><a href="#">Test</a></li>
						<li class="current_page_ancestor"><a href="#">Last Level</a>
							<ul>
								<li class="current_page_item"><a href="#">Current page item</a></li>
								<li><a href="#">Test</a></li>
								<li><a href="#">Test</a></li>
								<li><a href="#">Test</a></li>
								<li><a href="#">Test</a></li>
							</ul>
						</li>
						<li><a href="#">Test</a></li>
					</ul>
				</li>
				<li><a href="#">Test</a></li>
				<li><a href="#">Test</a></li>
				<li><a href="#">Test</a></li>
			</ul>
		</li>
		<li><a href="<?php echo home_url(); ?>/premium-support/" >Services</a></li>
		<li><a href="<?php echo home_url(); ?>/about/" >About</a></li>
		<li><a href="<?php echo home_url(); ?>/contact/" >Contact</a></li>
		<li><a href="<?php echo home_url(); ?>/forum/">Forum</a>
			<ul>
				<li><a href="<?php echo home_url(); ?>/forum/index.php?board=1.0" title="Themes board">Themes</a></li>
				<li><a href="<?php echo home_url(); ?>/forum/index.php?board=4.0" title="Dropdown menu board">Dropdown menus</a></li>
				<li><a href="<?php echo home_url(); ?>/forum/index.php?board=3.0" title="Simple CMS Plugin board">Simple CMS Plugin</a></li>
			</ul>
		</li>
	</ul><?php
} // End check if correct page



function pixopoint_dropdowngen($atts,$content = null) {
	ob_start();
	?>

	<div style="height:20px;width:100%"></div>
	

	
	
	<?php
		$pixo_array = pixo_fix_debug_probs();
		foreach ($_POST as $key=>$value) {
			$pixo_array[$key] = esc_html( $value );
		}
	
	if(isset($pixo_array['views']))
		$pixo_array['views'] = $pixo_array['views']+ 1;
	else
		$pixo_array['views'] = 1;
	
	
	
		
	if	($pixo_array['suckerfish_background_colour'] == '') {$pixo_array['suckerfish_background_colour'] = '9C1F1B';}
	if	($pixo_array['suckerfish_background_image'] == '') {$pixo_array['suckerfish_background_image'] = 'Red';}
	if	($pixo_array['suckerfish_lineheight'] == '') {$pixo_array['suckerfish_lineheight'] = '40';}
	if	($pixo_array['suckerfish_hover_backgroundcolour'] == '') {$pixo_array['suckerfish_hover_backgroundcolour'] = 'DA0909';}
	if	($pixo_array['suckerfish_colour'] == '') {$pixo_array['suckerfish_colour'] = 'dddddd';}
	if	($pixo_array['suckerfish_hover_color'] == '') {$pixo_array['suckerfish_hover_color'] = 'dddddd';}
	if	($pixo_array['suckerfish_fontsize'] == '') {$pixo_array['suckerfish_fontsize'] = '18';}
	if	($pixo_array['suckerfish_fontfamily'] == '') {$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_fontweight'] == '') {$pixo_array['suckerfish_fontweight'] = 'bold';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_padding_horisontal'] == '') {$pixo_array['suckerfish_padding_horisontal'] = '10';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_border_width'] == '') {$pixo_array['suckerfish_border_width'] = '1';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_border_colour'] == '') {$pixo_array['suckerfish_border_colour'] = 'aaa';}
	
	if	($pixo_array['suckerfish_dropcolour'] == '') {$pixo_array['suckerfish_dropcolour'] = '444444';}
	if	($pixo_array['suckerfish_drophover_backgroundcolor'] == '') {$pixo_array['suckerfish_drophover_backgroundcolor'] = '9C1F1B';}
	if	($pixo_array['suckerfish_drop_hovercolor'] == '') {$pixo_array['suckerfish_drop_hovercolor'] = 'dddddd';}
	if	($pixo_array['suckerfish_drop_colour'] == '') {$pixo_array['suckerfish_drop_colour'] = 'dddddd';}
	if	($pixo_array['suckerfish_drop_fontsize'] == '') {$pixo_array['suckerfish_drop_fontsize'] = '12';}
	if	($pixo_array['suckerfish_drop_fontfamily'] == '') {$pixo_array['suckerfish_drop_fontfamily'] = 'verdana,sans-serif';}
	
	if ($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_drop_fontweight'] == '') {$pixo_array['suckerfish_drop_fontweight'] = 'bold';}
	
	if	($pixo_array['suckerfish_dropdown_opacity'] == '') {$pixo_array['suckerfish_dropdown_opacity'] = '0.85';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_dropdown_verticalpadding'] == '') {$pixo_array['suckerfish_dropdown_verticalpadding'] = '4';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_dropdown_border'] == '') {$pixo_array['suckerfish_dropdown_border'] = '1';}
	if	($pixo_array['suckerfish_dropdown_ulborder'] == '') {$pixo_array['suckerfish_dropdown_ulborder'] = '1';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_dropdown_width'] == '') {$pixo_array['suckerfish_dropdown_width'] = '100';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_flyoutdirection'] == '') {$pixo_array['suckerfish_flyoutdirection'] = 'right';}
	if	($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_flyoutwidth'] == '') {$pixo_array['suckerfish_flyoutwidth'] = '150';}
	if ($pixo_array['pixo_logged'] != 'premium' || $pixo_array['suckerfish_dropdownbordercolour'] == '') {$pixo_array['suckerfish_dropdownbordercolour'] = '666666';}
	if ($pixo_array['pixo_logged'] != 'premium') {$pixo_array['suckerfish_wordpressoptomised'] = 'no';}
	if ($pixo_array['pixo_logged'] != 'premium') {$pixo_array['suckerfish_buttonized'] = 'no';}
	if ($pixo_array['suckerfish_background_image'] == '') {$pixo_array['suckerfish_buttonized'] = 'no';}
	
	if	($pixo_array['suckerfish_current_page_item_backgroundcolour'] == '') {$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '150';}
	if	($pixo_array['suckerfish_current_page_parent_backgroundcolour'] == '') {$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'ff0000';}
	if	($pixo_array['suckerfish_current_page_item_indicatorcolour'] == '') {$pixo_array['suckerfish_current_page_item_indicatorcolour'] = '000000';}
	if	($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '') {$pixo_array['suckerfish_current_page_item_indicatorsymbol'] = 'none';}
	if	($pixo_array['suckerfish_current_page_parent_indicatorcolour'] == '') {$pixo_array['suckerfish_current_page_parent_indicatorcolour'] = '000000';}
	if	($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '') {$pixo_array['suckerfish_current_page_parent_indicatorsymbol'] = 'none';}

	// PREDETERMINED STYLES
	if	($pixo_array['suckerfish_design'] == '') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = 'D31510';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'B13733';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour'] = '9C1F1B';$pixo_array['suckerfish_background_image'] = 'Red';$pixo_array['suckerfish_hover_backgroundcolour'] = 'DA0909';$pixo_array['suckerfish_colour'] = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '18';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '444444';$pixo_array['suckerfish_drophover_backgroundcolor']  = '9C1F1B';$pixo_array['suckerfish_drop_colour']  = 'dddddd';$pixo_array['suckerfish_drop_hovercolor']  = 'dddddd';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.85';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '150';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design'] == 'Red Dazzle') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = 'D31510';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'B13733';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour'] = '9C1F1B';$pixo_array['suckerfish_background_image'] = 'Red';$pixo_array['suckerfish_hover_backgroundcolour'] = 'DA0909';$pixo_array['suckerfish_colour'] = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '18';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '444444';$pixo_array['suckerfish_drophover_backgroundcolor']  = '9C1F1B';$pixo_array['suckerfish_drop_colour']  = 'dddddd';$pixo_array['suckerfish_drop_hovercolor']  = 'dddddd';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.85';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '150';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design'] == 'Blue Dazzle') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '122C83';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = '3D57A8';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour'] = '1F3E9F';$pixo_array['suckerfish_background_image'] = 'Blue';$pixo_array['suckerfish_hover_backgroundcolour'] = '5E7AD3';$pixo_array['suckerfish_colour'] = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '18';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '444444';$pixo_array['suckerfish_drophover_backgroundcolor']  = '1F3E9F';$pixo_array['suckerfish_drop_colour']  = 'dddddd';$pixo_array['suckerfish_drop_hovercolor']  = 'dddddd';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.85';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '150';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design'] == 'Green Dazzle') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '478A10';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = '97C842';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour'] = '6E9F1F';$pixo_array['suckerfish_background_image'] = 'Green';$pixo_array['suckerfish_hover_backgroundcolour'] = '9DDD35';$pixo_array['suckerfish_colour'] = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '18';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '444444';$pixo_array['suckerfish_drophover_backgroundcolor']  = '6E9F1F';$pixo_array['suckerfish_drop_colour']  = 'dddddd';$pixo_array['suckerfish_drop_hovercolor']  = 'dddddd';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.85';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '150';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design'] == 'Pink Dazzle') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '81125E';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'E84AB6';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour'] = '9F1F76';$pixo_array['suckerfish_background_image'] = 'Pink';$pixo_array['suckerfish_hover_backgroundcolour'] = 'E84AB6';$pixo_array['suckerfish_colour'] = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '18';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '444444';$pixo_array['suckerfish_drophover_backgroundcolor']  = '9F1F76';$pixo_array['suckerfish_drop_colour']  = 'dddddd';$pixo_array['suckerfish_drop_hovercolor']  = 'dddddd';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.85';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '150';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design'] == 'Yellow Dazzle') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '122C83';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = '727615';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily'] = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour'] = '9B9F1F';$pixo_array['suckerfish_background_image'] = 'Yellow';$pixo_array['suckerfish_hover_backgroundcolour'] = 'D9DB44';$pixo_array['suckerfish_colour'] = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '18';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '444444';$pixo_array['suckerfish_drophover_backgroundcolor']  = '9B9F1F';$pixo_array['suckerfish_drop_colour']  = 'dddddd';$pixo_array['suckerfish_drop_hovercolor']  = 'dddddd';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.85';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '150';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design']  == 'Nature') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '5FF75F';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = '39C339';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily']  = 'times,serif';$pixo_array['suckerfish_background_colour']  = '171';$pixo_array['suckerfish_background_image']  = '';$pixo_array['suckerfish_hover_backgroundcolour'] = '1a1';$pixo_array['suckerfish_colour']  = 'dddddd';$pixo_array['suckerfish_hover_color']  = 'dddddd';$pixo_array['suckerfish_fontsize'] = '15';$pixo_array['suckerfish_drop_fontfamily']  = 'times,serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '30';$pixo_array['suckerfish_padding_horisontal'] = '8';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = 'eee';$pixo_array['suckerfish_drophover_backgroundcolor']  = 'ccc';$pixo_array['suckerfish_drop_colour']  = '171';$pixo_array['suckerfish_drop_hovercolor']  = '171';$pixo_array['suckerfish_drop_fontsize']  = '11';$pixo_array['suckerfish_drop_fontweight']  = 'normal';$pixo_array['suckerfish_dropdown_opacity']  = '1';$pixo_array['suckerfish_dropdown_verticalpadding']  = '3';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '120';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design']  == 'Orange Clown') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = 'cccccc';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'dddddd';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily']  = 'zapf-chancery,cursive';$pixo_array['suckerfish_background_colour']  = 'FF6600';$pixo_array['suckerfish_background_image']  = '';$pixo_array['suckerfish_hover_backgroundcolour'] = 'FFFF00';$pixo_array['suckerfish_colour']  = 'ffffff';$pixo_array['suckerfish_hover_color']  = '444444';$pixo_array['suckerfish_fontsize'] = '16';$pixo_array['suckerfish_drop_fontfamily']  = 'zapf-chancery,cursive';$pixo_array['suckerfish_fontweight'] = 'normal';$pixo_array['suckerfish_lineheight']  = '30';$pixo_array['suckerfish_padding_horisontal'] = '10';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = 'eee';$pixo_array['suckerfish_drophover_backgroundcolor']  = 'FFFF00';$pixo_array['suckerfish_drop_colour']  = '444444';$pixo_array['suckerfish_drop_hovercolor']  = '444444';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'normal';$pixo_array['suckerfish_dropdown_opacity']  = '1';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '120';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design']  == 'Berrylicious') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '501350';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'bbbbbb';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily']  = 'helvetica,sans-serif';$pixo_array['suckerfish_background_colour']  = '990099';$pixo_array['suckerfish_background_image']  = '';$pixo_array['suckerfish_hover_backgroundcolour'] = 'ffffff';$pixo_array['suckerfish_colour']  = 'ffffff';$pixo_array['suckerfish_hover_color']  = '444444';$pixo_array['suckerfish_fontsize'] = '12';$pixo_array['suckerfish_drop_fontfamily']  = 'helvetica,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '30';$pixo_array['suckerfish_padding_horisontal'] = '5';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = 'fffeff';$pixo_array['suckerfish_drophover_backgroundcolor']  = '990099';$pixo_array['suckerfish_drop_colour']  = '444444';$pixo_array['suckerfish_drop_hovercolor']  = 'ffffff';$pixo_array['suckerfish_drop_fontsize']  = '10';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '0.95';$pixo_array['suckerfish_dropdown_verticalpadding']  = '4';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '130';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design']  == 'Death') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '501350';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'bbbbbb';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour']  = '333';$pixo_array['suckerfish_background_image']  = '';$pixo_array['suckerfish_hover_backgroundcolour'] = 'ffffff';$pixo_array['suckerfish_colour']  = 'ffffff';$pixo_array['suckerfish_hover_color']  = '333';$pixo_array['suckerfish_fontsize'] = '14';$pixo_array['suckerfish_drop_fontfamily']  = 'helvetica,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '40';$pixo_array['suckerfish_padding_horisontal'] = '15';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'aaa';$pixo_array['suckerfish_dropcolour']  = '333';$pixo_array['suckerfish_drophover_backgroundcolor']  = 'fff';$pixo_array['suckerfish_drop_colour']  = 'fff';$pixo_array['suckerfish_drop_hovercolor']  = '333';$pixo_array['suckerfish_drop_fontsize']  = '12';$pixo_array['suckerfish_drop_fontweight']  = 'bold';$pixo_array['suckerfish_dropdown_opacity']  = '1.0';$pixo_array['suckerfish_dropdown_verticalpadding']  = '10';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '120';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design']  == 'Bubble Gum') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = 'D568D5';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'E677E6';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily']  = 'zapf-chancery,cursive';$pixo_array['suckerfish_background_colour']  = 'FF99FF';$pixo_array['suckerfish_background_image']  = '';$pixo_array['suckerfish_hover_backgroundcolour'] = 'FF00FF';$pixo_array['suckerfish_colour']  = '9900FF';$pixo_array['suckerfish_hover_color']  = 'fff';$pixo_array['suckerfish_fontsize'] = '24';$pixo_array['suckerfish_drop_fontfamily']  = 'zapf-chancery,cursive';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '50';$pixo_array['suckerfish_padding_horisontal'] = '30';$pixo_array['suckerfish_border_width']  = '4';$pixo_array['suckerfish_border_colour']  = '9900FF';$pixo_array['suckerfish_dropcolour']  = 'ffeeff';$pixo_array['suckerfish_drophover_backgroundcolor']  = 'ff00ff';$pixo_array['suckerfish_drop_colour']  = '9900FF';$pixo_array['suckerfish_drop_hovercolor']  = 'fff';$pixo_array['suckerfish_drop_fontsize']  = '16';$pixo_array['suckerfish_drop_fontweight']  = 'normal';$pixo_array['suckerfish_dropdown_opacity']  = '1';$pixo_array['suckerfish_dropdown_verticalpadding']  = '7';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '190';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	if	($pixo_array['suckerfish_design']  == 'Default WordPress') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';$pixo_array['suckerfish_current_page_item_backgroundcolour'] = '8ABCEE';$pixo_array['suckerfish_current_page_parent_backgroundcolour'] = 'ACCFF1';$pixo_array['suckerfish_dropdownbordercolour'] = '666666';$pixo_array['suckerfish_flyoutrepeatimage'] = 'no';$pixo_array['suckerfish_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_background_colour']  = 'bbb';$pixo_array['suckerfish_background_image']  = '';$pixo_array['suckerfish_hover_backgroundcolour'] = '99CCFF';$pixo_array['suckerfish_colour']  = 'fff';$pixo_array['suckerfish_hover_color']  = 'fff';$pixo_array['suckerfish_fontsize'] = '13';$pixo_array['suckerfish_drop_fontfamily']  = 'verdana,sans-serif';$pixo_array['suckerfish_fontweight'] = 'bold';$pixo_array['suckerfish_lineheight']  = '20';$pixo_array['suckerfish_padding_horisontal'] = '25';$pixo_array['suckerfish_border_width']  = '1';$pixo_array['suckerfish_border_colour']  = 'bbb';$pixo_array['suckerfish_dropcolour']  = 'bbb';$pixo_array['suckerfish_drophover_backgroundcolor']  = '99CCFF';$pixo_array['suckerfish_drop_colour']  = 'fff';$pixo_array['suckerfish_drop_hovercolor']  = 'fff';$pixo_array['suckerfish_drop_fontsize']  = '13';$pixo_array['suckerfish_drop_fontweight']  = 'normal';$pixo_array['suckerfish_dropdown_opacity']  = '1';$pixo_array['suckerfish_dropdown_verticalpadding']  = '3';$pixo_array['suckerfish_dropdown_border']  = '1';$pixo_array['suckerfish_dropdown_width']  = '130';$pixo_array['suckerfish_flyoutdirection']  = 'right';}
	
	// CALCULATIONS

		$hexcolourvalue = 'enter a valid HEX colour value';
		$enterfontsize = 'enter a font size in PXs';
		$borderwidth = 'enter a border width in PXs';
		$enterheight = 'enter measurement in PXs';
		$enteropacity = 'enter an opacity between 0 and 1';
		$fontfamily = 'choose a font family';
		$enterfontweight = 'choose a font weight';
	?>
	
	<form method="post" action="" name="tcp_test">
	
	
		<input type="hidden" name="action" value="update" />
	
	<div class="generatorblock" style="width:100%">
	<div class="topgeneratorblock">
	<p>
		<select name="suckerfish_design">
			<option>Current Design</option>
			<option>Red Dazzle</option>
			<option>Green Dazzle</option>
			<option>Pink Dazzle</option>
			<option>Blue Dazzle</option>
			<option>Yellow Dazzle</option>
			<option>Nature</option>
			<option>Orange Clown</option>
			<option>Berrylicious</option>
			<option>Death</option>
			<option>Bubble Gum</option>
			<option>Default WordPress</option>
		</select>
		<label>Premade Designs: </label>
	</p>
	
	<div class="premiumonly">
	<p>
		<select name="suckerfish_type">
		<?php
			if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><option>Vertical Flyout</option><option>Horizontal Dropdown</option><option>Horizontal Slider</option><?php }
			elseif	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?><option>Horizontal Slider</option><option>Vertical Flyout</option><option>Horizontal Dropdown</option><?php }
			else { ?><option>Horizontal Dropdown</option><option>Vertical Flyout</option><option>Horizontal Slider</option><?php }
		?>
		</select>
		<label>Navigational type: </label>
	</p>
	</div>
	
	  <div><a class="temp1" href="javascript:collapse1.slideit()">(show extra options &raquo;)</a></div>
	<div id="all-levels" style="border-top:0">
	
	<p>
		<select name="suckerfish_wordpressoptomised">
		<?php
			if	($pixo_array['suckerfish_wordpressoptomised'] == 'yes') { ?><option>yes</option><option>no</option><?php }
			else { ?><option>no</option><option>yes</option><?php }
		?>
		</select>
		<label>WordPress Optomised: </label>
	</p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_current_page_item_backgroundcolour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_current_page_item_backgroundcolour" value="<?php echo $pixo_array['suckerfish_current_page_item_backgroundcolour']; ?>" />
		<label>Current Page Item Background Colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_current_page_paren_backgroundcolourt'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_current_page_parent_backgroundcolour" value="<?php echo $pixo_array['suckerfish_current_page_parent_backgroundcolour']; ?>" />
		<label>Current Page Parent/Ancestor Background Colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_current_page_parent_indicatorcolour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_current_page_parent_indicatorcolour" value="<?php echo $pixo_array['suckerfish_current_page_parent_indicatorcolour']; ?>" />
		<label>Current Page Item Indicator Colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<select name="suckerfish_current_page_item_indicatorsymbol">
			<?php
				if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '>') {echo '<option>></option><option><</option><option>^</option><option>none</option>';}
				elseif ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '<') {echo '<option><</option><option>></option><option>^</option><option>none</option>';}
				elseif ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '^') {echo '<option>^</option><option>></option><option><</option><option>none</option>';}
				else {echo '<option>none</option><option>></option><option><</option><option>^</option>';}
			?>
		</select>
		<label>Current Page Item Indicator Symbol</label>
	</p>
	<p><span>&nbsp;</span></p>
	
	<p>
		<select name="suckerfish_current_page_parent_indicatorsymbol">
			<?php
				if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '>') {echo '<option>></option><option><</option><option>^</option><option>none</option>';}
				elseif ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '<') {echo '<option><</option><option>></option><option>^</option><option>none</option>';}
				elseif ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '^') {echo '<option>^</option><option>></option><option><</option><option>none</option>';}
				else {echo '<option>none</option><option>></option><option><</option><option>^</option>';}
			?>
		</select>
		<label>Current Page Parent Indicator Symbol</label>
	</p>
	<p><span>&nbsp;</span></p>
	</div>
		<script type="text/javascript">
		//Syntax: var uniquevar=new animatedcollapse("DIV_id", animatetime_milisec, enablepersist(true/fase), [initialstate] )
		var collapse1=new animatedcollapse("all-levels", 800, true)
		</script>
	
	
	
	</div>
	<div class="topgeneratorblock">
		<input style="float:right;font-size:1.2em;width:9em;margin:60px 0 0 0"  type="submit" name="Submit" value="Submit Design" />
	</div>
	</div>
	
	
	
	
	<div class="generatorblock">
	  <h2>Top Level Menu items</h2>
	  <p>These options modify the top level menu items.</p>
	
	
	
	<p><span>&nbsp;</span></p>
	
	
	<p>
		<select name="suckerfish_background_image">
			<?php
				if	($pixo_array['suckerfish_background_image'] == 'Red') {echo '<option>Red</option><option>Pink</option><option>Blue</option><option>Green</option><option>Yellow</option><option>No image</option>';}
				elseif	($pixo_array['suckerfish_background_image'] == 'Green') {echo '<option>Green</option><option>Pink</option><option>Blue</option><option>Red</option><option>Yellow</option><option>No image</option>';}
				elseif	($pixo_array['suckerfish_background_image'] == 'Pink') {echo '<option>Pink</option><option>Green</option><option>Blue</option><option>Red</option><option>Yellow</option><option>No image</option>';}
				elseif	($pixo_array['suckerfish_background_image'] == 'Blue') {echo '<option>Blue</option><option>Pink</option><option>Green</option><option>Red</option><option>Yellow</option><option>No image</option>';}
				elseif	($pixo_array['suckerfish_background_image'] == 'Yellow') {echo '<option>Yellow</option><option>Pink</option><option>Blue</option><option>Red</option><option>Green</option><option>No image</option>';}
				else	{echo '<option>No image</option><option>Yellow</option><option>Pink</option><option>Blue</option><option>Red</option><option>Green</option>';}
			?>
		</select>
		<label>Background image: </label>
	</p>
	<p><span>If no image is specified, only the background colour will be displayed</span></p>
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_background_colour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_background_colour" value="<?php echo $pixo_array['suckerfish_background_colour']; ?>" />
		<label>Background colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_hover_backgroundcolour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_hover_backgroundcolour" value="<?php echo $pixo_array['suckerfish_hover_backgroundcolour']; ?>" />
		<label>Background colour on hover: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_colour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_colour" value="<?php echo $pixo_array['suckerfish_colour']; ?>" />
		<label>Text colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_hover_color'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_hover_color" value="<?php echo $pixo_array['suckerfish_hover_color']; ?>" />
		<label>Text colour on hover: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	
	<p>
		<input type="text" name="suckerfish_fontsize" value="<?php echo $pixo_array['suckerfish_fontsize']; ?>" />
		<label>Font size: </label>
	</p>
	<p><span><?php echo $enterfontsize; ?></span></p>
	
	<p>
		<select name="suckerfish_fontfamily">
			<?php
				if	($pixo_array['suckerfish_fontfamily'] == 'verdana,sans-serif') {echo '<option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_fontfamily'] == 'zapf-chancery,cursive') {echo '<option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_fontfamily'] == 'western,fantasy') {echo '<option style="font-family:western,cursive">western,fantasy</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_fontfamily'] == 'courier,monospace') {echo '<option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_fontfamily'] == 'helvetica,sans-serif') {echo '<option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_fontfamily'] == 'times,serif') {echo '<option style="font-family:times,serif">times,serif</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option>';}
			?>
		</select>
		<label>Font family:</label>
	</p>
	<p><span><?php echo $fontfamily; ?></span></p>
	
	
	
	
	  <div><a class="temp1"  href="javascript:collapse2.slideit()">(show extra options &raquo;)</a></div>
	
	
	<div id="toplevel" style="border-top:0">
	<p>
		<select name="suckerfish_fontweight">
		<?php
			if	($pixo_array['suckerfish_fontweight'] == 'bold') {echo '<option>bold</option><option>normal</option>';}
			if	($pixo_array['suckerfish_fontweight'] == 'normal') {echo '<option>normal</option><option>bold</option>';}
		?>
		</select>
		<label>Font weight: </label>
	</p>
	<p><span><?php echo $enterfontweight; ?></span></p>
	
	<p>
		<input type="text" name="suckerfish_lineheight" value="<?php echo $pixo_array['suckerfish_lineheight']; ?>" />
		<label>Height: </label>
	</p>
	<p><span><?php echo $enterheight; ?></span></p>
	
	<p>
		<input type="text" name="suckerfish_padding_horisontal" value="<?php echo $pixo_array['suckerfish_padding_horisontal']; ?>" />
		<label>Horizontal padding: </label>
	</p>
	<p><span><?php echo $enterheight; ?></span></p>
	<p>
		<input type="text" name="suckerfish_border_width" value="<?php echo $pixo_array['suckerfish_border_width']; ?>" />
		<label>Border width: </label>
	</p>
	<p><span><?php echo $pixo_array['borderwidth']; ?></span></p>
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_border_colour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_border_colour" value="<?php echo $pixo_array['suckerfish_border_colour']; ?>" />
		<label>Border colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	<p>
		<input type="text" name="suckerfish_flyoutwidth" value="<?php echo $pixo_array['suckerfish_flyoutwidth']; ?>" />
		<label>Flyout Width: </label>
	</p>
	<p><span>Only applies to Vertical Flyout menus</span></p>
	<p>
		<select name="suckerfish_flyoutrepeatimage">
			<?php
				if ($pixo_array['suckerfish_flyoutrepeatimage'] == 'no') {echo '<option>no</option><option>yes</option>';}
				else {echo '<option>yes</option><option>no</option>';}
			?>
		</select>
		<label>Repeat background image: </label>
	</p>
	<p><span>Only applies to Vertical Flyout menus</span></p>
	<p>
		<select name="suckerfish_buttonized">
			<?php
				if ($pixo_array['suckerfish_buttonized'] == 'yes') {echo '<option>yes</option><option>no</option>';}
				else {echo '<option>no</option><option>yes</option>';}
			?>
		</select>
		<label>Buttonized: (developmental) </label>
	</p>
	<p><span>Creates graphical buttons instead of basic text links. Note: You must have a background image colour specified above for this to work</span></p>
	
	
	</div>
	</div>
	
		<script type="text/javascript">
		//Syntax: var uniquevar=new animatedcollapse("DIV_id", animatetime_milisec, enablepersist(true/fase), [initialstate] )
		var collapse2=new animatedcollapse("toplevel", 800, true)
		</script>
	
	
	
	
	
	
	
	
	
	
	<div class="generatorblock">
	  <h2>Lower Level Menu items</h2>
	
	  <p>These options modify the second level menu items.</p>
	
	
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_dropcolour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_dropcolour" value="<?php echo $pixo_array['suckerfish_dropcolour']; ?>" />
		<label>Background colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_drophover_backgroundcolor'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_drophover_backgroundcolor" value="<?php echo $pixo_array['suckerfish_drophover_backgroundcolor']; ?>" />
		<label>Background colour on hover: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_drop_colour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_drop_colour" value="<?php echo $pixo_array['suckerfish_drop_colour']; ?>" />
		<label>Text colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_drop_hovercolor'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_drop_hovercolor" value="<?php echo $pixo_array['suckerfish_drop_hovercolor']; ?>" />
		<label>Text colour on hover: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<input type="text" name="suckerfish_drop_fontsize" value="<?php echo $pixo_array['suckerfish_drop_fontsize']; ?>" />
		<label>Font size: </label>
	</p>
	<p><span><?php echo $enterfontsize; ?></span></p>
	
	<p>
		<select name="suckerfish_drop_fontfamily">
			<?php
				if	($pixo_array['suckerfish_drop_fontfamily'] == 'verdana,sans-serif') {echo '<option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_drop_fontfamily'] == 'zapf-chancery,cursive') {echo '<option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_drop_fontfamily'] == 'western,fantasy') {echo '<option style="font-family:western,cursive">western,fantasy</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_drop_fontfamily'] == 'courier,monospace') {echo '<option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_drop_fontfamily'] == 'helvetica,sans-serif') {echo '<option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:times,serif">times,serif</option>';}
				if	($pixo_array['suckerfish_drop_fontfamily'] == 'times,serif') {echo '<option style="font-family:times,serif">times,serif</option><option style="font-family:verdana,sans-serif">verdana,sans-serif</option><option style="font-family:zapf-chancery,cursive">zapf-chancery,cursive</option><option style="font-family:western,cursive">western,fantasy</option><option style="font-family:courier,monospace">courier,monospace</option><option style="font-family:helvetica,sans-serif">helvetica,sans-serif</option>';}
			?>
		</select>
		<label>Font family:</label>
	</p>
	<p><span><?php echo $fontfamily; ?></span></p>
	
	
	
	
	
	
	
	  <div><a class="temp1"  href="javascript:collapse3.slideit()">(show extra options &raquo;)</a></div>
	<div id="secondlevel" style="border-top:0">
	<p><span>&nbsp;</span></p>
	
	<p>
		<select name="suckerfish_drop_fontweight">
			<?php
				if	($pixo_array['suckerfish_drop_fontweight'] == 'bold') {echo '<option>bold</option><option>normal</option>';}
				if	($pixo_array['suckerfish_drop_fontweight'] == 'normal') {echo '<option>normal</option><option>bold</option>';}
			?>
		</select>
		<label>Font weight: </label>
	</p>
	<p><span><?php echo $enterfontweight; ?></span></p>
	
	<p>
		<a class="temp1"  href="javascript:TCP.popup(document.forms['tcp_test'].elements['suckerfish_dropdownbordercolour'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="http://pixopoint.com/wp-content/plugins/pixopoint_dropdowngenerator/img/sel.gif" /></a>
		<input type="text" name="suckerfish_dropdownbordercolour" value="<?php echo $pixo_array['suckerfish_dropdownbordercolour']; ?>" />
		<label>Border colour: </label>
	</p>
	<p><span><?php echo $hexcolourvalue; ?></span></p>
	
	<p>
		<input type="text" name="suckerfish_dropdown_opacity" value="<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>" />
		<label>Opacity: </label>
	</p>
	<p><span><?php echo $enteropacity;  ?></span></p>
	
	<p>
		<input type="text" name="suckerfish_dropdown_verticalpadding" value="<?php echo $pixo_array['suckerfish_dropdown_verticalpadding']; ?>" />
		<label>Vertical padding: </label>
	</p>
	<p><span>enter vertical padding in PXs</span></p>
	<p>
		<input type="text" name="suckerfish_dropdown_border" value="<?php echo $pixo_array['suckerfish_dropdown_border']; ?>" />
		<label>Border width: </label>
	</p>
	<p><span>enter a border width in PXs</span></p>
	
	<p>
		<input type="text" name="suckerfish_dropdown_width" value="<?php echo $pixo_array['suckerfish_dropdown_width']; ?>" />
		<label>Width: </label>
	</p>
	<p><span>enter a width in PXs</span></p>
	
	<p>
		<select name="suckerfish_flyoutdirection">
			<?php
				if	($pixo_array['suckerfish_flyoutdirection'] == 'right') {echo '<option>right</option><option>left</option>';}
				if	($pixo_array['suckerfish_flyoutdirection'] == 'left') {echo '<option>left</option><option>right</option>';}
			?>
		</select>
		<label>Flyout direction:</label>
	</p>
	<p><span>&nbsp;</span></p>
	</div>
	</div>
	
		<script type="text/javascript">
		//Syntax: var uniquevar=new animatedcollapse("DIV_id", animatetime_milisec, enablepersist(true/fase), [initialstate] )
		var collapse3=new animatedcollapse("secondlevel", 800, true)
		</script>
	
	</form>
	
	
	<?php if ($pixo_array['pixo_logged'] != 'premium') {$pixo_array['suckerfish_type'] = 'Horizontal Dropdown';} ?>
	<?php $linebreak = ''; $tabgap = ''; ?>
		<style type="text/css">
#nav_wrapper {
	height: <?php echo $pixo_array['suckerfish_lineheight']; ?>px;
	min-height: <?php echo $pixo_array['suckerfish_lineheight']; ?>px;
}
	#suckerfishnav {<?php echo $linebreak; ?><?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_background_colour']; ?> <?php
			if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_green.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_pink.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_blue.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_red.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_yellow.png")';}
	
			if ($pixo_array['suckerfish_background_image'] != "") {
				if ($pixo_array['suckerfish_flyoutrepeatimage'] == "no") {echo ' repeat-x';}
				else {echo 'repeat';}
				} ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-size:<?php echo $pixo_array['suckerfish_fontsize']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-family:<?php echo $pixo_array['suckerfish_fontfamily']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-weight:<?php echo $pixo_array['suckerfish_fontweight']; ?>;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider' || $pixo_array['suckerfish_type'] == 'Horizontal Dropdown') { ?><?php echo $tabgap; ?>width:100%;<?php echo $linebreak; ?><?php	} ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav, #suckerfishnav ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>float:left;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>list-style:none;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>line-height:<?php echo $pixo_array['suckerfish_lineheight']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>padding:0;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border:<?php echo $pixo_array['suckerfish_border_width']; ?>px solid #<?php echo $pixo_array['suckerfish_border_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:0;<?php echo $linebreak; ?>
		<?php
			if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>width:<?php echo $pixo_array['suckerfish_flyoutwidth']; ?>px;<?php echo $linebreak; ?><?php	}
			elseif ($pixo_array['suckerfish_type'] == 'Horizontal Dropdown') { ?><?php echo $tabgap; ?>width:100%;<?php echo $linebreak; ?><?php }
			else {}
		?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>display:block;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>text-decoration:none;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>padding:0px <?php echo $pixo_array['suckerfish_padding_horisontal']; ?>px;<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?>
		<?php echo $tabgap; ?>background:<?php
			if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_green_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_pink_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_blue_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_red_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_yellow_right.png") no-repeat top right;';}
		?>
	<?php } ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>z-index:999;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>position: relative;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>float:left;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>padding:0;<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?>
		<?php echo $tabgap; ?>background:<?php
			if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_green_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_pink_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_blue_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_red_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("' .  DROPDOWNGEN_URL . '/images/suckerfish_yellow_left.png") no-repeat top left;';}
		?>
	<?php } ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?>
	#suckerfishnav li a {<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>width:<?php echo $pixo_array['suckerfish_flyoutwidth']; ?>px;<?php echo $linebreak; ?><?php	} ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_flyoutwidth'] - ($pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']))?>px;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>overflow:hidden<?php } ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?><?php } ?>
	#suckerfishnav ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>position:absolute;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>left:-999em;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>height:auto;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] != 'Horizontal Slider') { ?><?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_dropdown_border']) ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>font-weight:normal;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:0;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>line-height:1;<?php echo $linebreak; ?>
		<?php if ($pixo_array['pixo_logged'] == 'premium') {if	($pixo_array['suckerfish_dropdown_opacity'] < '1') { ?>
	<?php echo $tabgap; ?>-moz-opacity:<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>opacity:<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>khtml-opacity:<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>;<?php echo $linebreak; ?>
		<?php } } ?>
	<?php echo $tabgap; ?>border:0;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border-top:<?php echo $pixo_array['suckerfish_dropdown_border'] ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
		<?php if ($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>margin:-<?php echo $pixo_array['suckerfish_lineheight'] ?>px 0 0 	<?php if ($pixo_array['suckerfish_flyoutdirection'] == "left") {echo '-';} ?><?php echo $pixo_array['suckerfish_flyoutwidth'] ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li li {<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] != 'Horizontal Slider') { ?><?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] - $pixo_array['suckerfish_dropdown_border']) ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>border-bottom:<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border-left:<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border-right:<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-weight:<?php echo $pixo_array['suckerfish_drop_fontweight']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-family:<?php echo $pixo_array['suckerfish_drop_fontfamily']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li li a {<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?><?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_dropcolour']; ?>;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>padding:<?php echo $pixo_array['suckerfish_dropdown_verticalpadding']; ?>px 10px;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] != 'Horizontal Slider') { ?><?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] - $pixo_array['suckerfish_padding_horisontal'] - $pixo_array['suckerfish_padding_horisontal']); ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>font-size:<?php echo $pixo_array['suckerfish_drop_fontsize']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?>
	#suckerfishnav li ul ul li {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php } ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?>
	#suckerfishnav li ul ul a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?><?php } ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?>
	#suckerfishnav li ul ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:0 0 0 -<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li ul ul ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:-<?php echo ($pixo_array['suckerfish_drop_fontsize'] + $pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_verticalpadding'] + $pixo_array['suckerfish_dropdown_verticalpadding']) ?>px 0 0 <?php echo ($pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php }
				else { ?>
	#suckerfishnav li ul ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:-<?php echo ($pixo_array['suckerfish_drop_fontsize'] + $pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_verticalpadding'] + $pixo_array['suckerfish_dropdown_verticalpadding']) ?>px 0 0 <?php if ($pixo_array['suckerfish_flyoutdirection'] == "left") {echo '-';} ?><?php echo ($pixo_array['suckerfish_dropdown_width'])?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php } ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?>#suckerfishnav li li a:hover {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_drophover_backgroundcolor']; ?>;
	<?php echo $tabgap; ?>}<?php } ?>
	#suckerfishnav li li:hover {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_drophover_backgroundcolor']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li ul li:hover a, #suckerfishnav li ul li li:hover a, #suckerfishnav li ul li li li:hover a, #suckerfishnav li ul li li li:hover a  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_hovercolor']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover a, #suckerfishnav li.sfhover a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_hover_color']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover li a, #suckerfishnav li li:hover li a, #suckerfishnav li li li:hover li a, #suckerfishnav li li li li:hover li a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover ul ul, #suckerfishnav li:hover ul ul ul, #suckerfishnav li:hover ul ul ul ul, #suckerfishnav li.sfhover ul ul, #suckerfishnav li.sfhover ul ul ul, #suckerfishnav li.sfhover ul ul ul ul  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>left:-999em;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover ul, #suckerfishnav li li:hover ul, #suckerfishnav li li li:hover ul, #suckerfishnav li li li li:hover ul, #suckerfishnav li.sfhover ul, #suckerfishnav li li.sfhover ul, #suckerfishnav li li li.sfhover ul, #suckerfishnav li li li li.sfhover ul  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>left:auto;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_dropcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover, #suckerfishnav li.sfhover {<?php echo $linebreak; ?>
	<?php
		if ($pixo_array['suckerfish_buttonized'] == 'yes') {
			echo $tabgap; ?>background:<?php
				if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_green_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_pink_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_blue_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_red_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_yellow_left_hover.png") no-repeat top left;'; echo $linebreak;}
			echo $tabgap; ?>visibility:visible; /* FIX FOR IE7 */<?php echo $linebreak;
			}
		else {
			echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_hover_backgroundcolour']; ?>;<?php echo $linebreak;
			echo $tabgap; ?>}<?php echo $linebreak;
		}
	?>
	<?php if ($pixo_array['pixo_logged'] == 'premium') { ?>
	#suckerfishnav .current_page_parent<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>, #suckerfishnav .current_page_ancestor, #suckerfishnav .current-cat-parent<?php } ?> {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_current_page_parent_backgroundcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current-cat, <?php } ?>#suckerfishnav .current_page_item {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_current_page_item_backgroundcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if (!$pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '') { ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current_page_ancestor a:after, #suckerfishnav .current_page_ancestor li a:after, #suckerfishnav .current_page_ancestor li li a:after, #suckerfishnav .current_page_ancestor li li li a:after, #suckerfishnav .current_page_ancestor li li li li a:after, #suckerfishnav .current-cat-parent a:after, #suckerfishnav .current-cat-parent li a:after, #suckerfishnav .current-cat-parent li li a:after, #suckerfishnav .current-cat-parent li li li a:after, #suckerfishnav .current-cat-parent li li li li a:after, <?php } ?>#suckerfishnav .current_page_parent a:after, #suckerfishnav .current_page_parent li a:after, #suckerfishnav .current_page_parent li li a:after, #suckerfishnav .current_page_parent li li li a:after, #suckerfishnav .current_page_parent li li li li a:after {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>content:"";<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current_page_ancestor a:after, #suckerfishnav li .current_page_ancestor a:after, #suckerfishnav li li .current_page_ancestor a:after, #suckerfishnav li li li .current_page_ancestor a:after, #suckerfishnav .current-cat-parent a:after, #suckerfishnav li .current-cat-parent a:after, #suckerfishnav li li .current-cat-parent a:after, #suckerfishnav li li li .current-cat-parent a:after, <?php }
	?>#suckerfishnav .current_page_parent a:after, #suckerfishnav li .current_page_parent a:after, #suckerfishnav li li .current_page_parent a:after, #suckerfishnav li li li .current_page_parent a:after {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_current_page_item_indicatorcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>content:"<?php
		/* HAD DIFFICULTIES GETTING TO WORK
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '»') { ?> \00BB \0020<?php }
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '&raquo;') { ?> \00AB<?php } */
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '<') { ?> \003C<?php }
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '>') { ?> \003E<?php }
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '^') { ?> \005E<?php }
			?>";<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current-cat a:after, #suckerfishnav li .current-cat a:after, #suckerfishnav li li .current-cat a:after, #suckerfishnav li li li .current-cat a:after, #suckerfishnav li li li li .current-cat a:after, <?php }
	?>#suckerfishnav .current_page_item a:after, #suckerfishnav li .current_page_item a:after, #suckerfishnav li li .current_page_item a:after, #suckerfishnav li li li .current_page_item a:after, #suckerfishnav li li li li .current_page_item a:after  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_current_page_item_indicatorcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>content:"<?php
		/* HAD DIFFICULTIES GETTING TO WORK
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '»') { ?> \00BB \0020<?php }
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '&raquo;') { ?> \00AB<?php } */
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '<') { ?> \003C<?php }
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '>') { ?> \003E<?php }
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '^') { ?> \005E<?php }
			?>";<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?><?php } ?>
	
	<?php } /* END OF PREMIUM STUFF */ ?>
	
	</style>
	
	<h2 id="generatorcodeboxtitle">CSS Code</h2>
	<p>Paste the following code into your CSS file. This code can be used with any unordered list which has an ID of #suckerfishnav.</p>
	
	<?php $linebreak = '<br />'; $tabgap = '&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
	<code id="generatorcodebox">
	#suckerfishnav {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_background_colour']; ?> <?php
			if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_green.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_pink.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_blue.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_red.png")';}
			if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_yellow.png")';}
	
			if ($pixo_array['suckerfish_background_image'] != "") {
				if ($pixo_array['suckerfish_flyoutrepeatimage'] == "no") {echo ' repeat-x';}
				else {echo 'repeat';}
				} ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-size:<?php echo $pixo_array['suckerfish_fontsize']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-family:<?php echo $pixo_array['suckerfish_fontfamily']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-weight:<?php echo $pixo_array['suckerfish_fontweight']; ?>;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider' || $pixo_array['suckerfish_type'] == 'Horizontal Dropdown') { ?><?php echo $tabgap; ?>width:100%;<?php echo $linebreak; ?><?php	} ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav, #suckerfishnav ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>float:left;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>list-style:none;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>line-height:<?php echo $pixo_array['suckerfish_lineheight']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>padding:0;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border:<?php echo $pixo_array['suckerfish_border_width']; ?>px solid #<?php echo $pixo_array['suckerfish_border_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:0;<?php echo $linebreak; ?>
		<?php
			if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>width:<?php echo $pixo_array['suckerfish_flyoutwidth']; ?>px;<?php echo $linebreak; ?><?php	}
			elseif ($pixo_array['suckerfish_type'] == 'Horizontal Dropdown') { ?><?php echo $tabgap; ?>width:100%;<?php echo $linebreak; ?><?php }
			else {}
		?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>display:block;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>text-decoration:none;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>padding:0px <?php echo $pixo_array['suckerfish_padding_horisontal']; ?>px;<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?>
		<?php echo $tabgap; ?>background:<?php
			if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_green_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_pink_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_blue_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_red_right.png") no-repeat top right;';}
			if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_yellow_right.png") no-repeat top right;';}
		?>
	<?php } ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>z-index:999;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>position: relative;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>float:left;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>padding:0;<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?>
		<?php echo $tabgap; ?>background:<?php
			if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_green_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_pink_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_blue_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_red_left.png") no-repeat top left;';}
			if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_yellow_left.png") no-repeat top left;';}
		?>
	<?php } ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?>
	#suckerfishnav li a {<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>width:<?php echo $pixo_array['suckerfish_flyoutwidth']; ?>px;<?php echo $linebreak; ?><?php	} ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_flyoutwidth'] - ($pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']))?>px;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>overflow:hidden<?php } ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?><?php } ?>
	#suckerfishnav ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>position:absolute;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>left:-999em;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>height:auto;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] != 'Horizontal Slider') { ?><?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_dropdown_border']) ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>font-weight:normal;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:0;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>line-height:1;<?php echo $linebreak; ?>
		<?php if ($pixo_array['pixo_logged'] == 'premium') {if	($pixo_array['suckerfish_dropdown_opacity'] < '1') { ?>
	<?php echo $tabgap; ?>-moz-opacity:<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>opacity:<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>khtml-opacity:<?php echo $pixo_array['suckerfish_dropdown_opacity']; ?>;<?php echo $linebreak; ?>
		<?php } } ?>
	<?php echo $tabgap; ?>border:0;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border-top:<?php echo $pixo_array['suckerfish_dropdown_border'] ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
		<?php if ($pixo_array['suckerfish_type'] == 'Vertical Flyout') { ?><?php echo $tabgap; ?>margin:-<?php echo $pixo_array['suckerfish_lineheight'] ?>px 0 0 	<?php if ($pixo_array['suckerfish_flyoutdirection'] == "left") {echo '-';} ?><?php echo $pixo_array['suckerfish_flyoutwidth'] ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li li {<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] != 'Horizontal Slider') { ?><?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] - $pixo_array['suckerfish_dropdown_border']) ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>border-bottom:<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border-left:<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>border-right:<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px solid #<?php echo ($pixo_array['suckerfish_dropdownbordercolour']) ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-weight:<?php echo $pixo_array['suckerfish_drop_fontweight']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>font-family:<?php echo $pixo_array['suckerfish_drop_fontfamily']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li li a {<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?><?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_dropcolour']; ?>;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>padding:<?php echo $pixo_array['suckerfish_dropdown_verticalpadding']; ?>px 10px;<?php echo $linebreak; ?>
		<?php if	($pixo_array['suckerfish_type'] != 'Horizontal Slider') { ?><?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] - $pixo_array['suckerfish_padding_horisontal'] - $pixo_array['suckerfish_padding_horisontal']); ?>px;<?php echo $linebreak; ?><?php } ?>
	<?php echo $tabgap; ?>font-size:<?php echo $pixo_array['suckerfish_drop_fontsize']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?>
	#suckerfishnav li ul ul li {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php } ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?>
	#suckerfishnav li ul ul a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?><?php } ?>
	<?php if	($pixo_array['suckerfish_type'] == 'Horizontal Slider') { ?>
	#suckerfishnav li ul ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>width:<?php echo ($pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:0 0 0 -<?php echo $pixo_array['suckerfish_dropdown_border']; ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li ul ul ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:-<?php echo ($pixo_array['suckerfish_drop_fontsize'] + $pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_verticalpadding'] + $pixo_array['suckerfish_dropdown_verticalpadding']) ?>px 0 0 <?php echo ($pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_width'] + $pixo_array['suckerfish_padding_horisontal'] + $pixo_array['suckerfish_padding_horisontal']) ?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php }
				else { ?>
	#suckerfishnav li ul ul {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>margin:-<?php echo ($pixo_array['suckerfish_drop_fontsize'] + $pixo_array['suckerfish_dropdown_border'] + $pixo_array['suckerfish_dropdown_verticalpadding'] + $pixo_array['suckerfish_dropdown_verticalpadding']) ?>px 0 0 <?php if ($pixo_array['suckerfish_flyoutdirection'] == "left") {echo '-';} ?><?php echo ($pixo_array['suckerfish_dropdown_width'])?>px;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php } ?>
	<?php if ($pixo_array['suckerfish_buttonized'] == 'yes') { ?>#suckerfishnav li li a:hover {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_drophover_backgroundcolor']; ?>;
	<?php echo $tabgap; ?>}<?php } ?>
	#suckerfishnav li li:hover {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_drophover_backgroundcolor']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li ul li:hover a, #suckerfishnav li ul li li:hover a, #suckerfishnav li ul li li li:hover a, #suckerfishnav li ul li li li:hover a  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_hovercolor']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover a, #suckerfishnav li.sfhover a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_hover_color']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover li a, #suckerfishnav li li:hover li a, #suckerfishnav li li li:hover li a, #suckerfishnav li li li li:hover li a {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_drop_colour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover ul ul, #suckerfishnav li:hover ul ul ul, #suckerfishnav li:hover ul ul ul ul, #suckerfishnav li.sfhover ul ul, #suckerfishnav li.sfhover ul ul ul, #suckerfishnav li.sfhover ul ul ul ul  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>left:-999em;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover ul, #suckerfishnav li li:hover ul, #suckerfishnav li li li:hover ul, #suckerfishnav li li li li:hover ul, #suckerfishnav li.sfhover ul, #suckerfishnav li li.sfhover ul, #suckerfishnav li li li.sfhover ul, #suckerfishnav li li li li.sfhover ul  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>left:auto;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_dropcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	#suckerfishnav li:hover, #suckerfishnav li.sfhover {<?php echo $linebreak; ?>
	<?php
		if ($pixo_array['suckerfish_buttonized'] == 'yes') {
			echo $tabgap; ?>background:<?php
				if ($pixo_array['suckerfish_background_image'] == "Green") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_green_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Pink") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_pink_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Blue") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_blue_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Red") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_red_left_hover.png") no-repeat top left;'; echo $linebreak;}
				if ($pixo_array['suckerfish_background_image'] == "Yellow") {echo 'url("../multi-level-navigation-plugin/images/suckerfish_yellow_left_hover.png") no-repeat top left;'; echo $linebreak;}
			echo $tabgap; ?>visibility:visible; /* FIX FOR IE7 */<?php echo $linebreak;
			}
		else {
			echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_hover_backgroundcolour']; ?>;<?php echo $linebreak;
			echo $tabgap; ?>}<?php echo $linebreak;
		}
	?>
	<?php if ($pixo_array['pixo_logged'] == 'premium') { ?>
	#suckerfishnav .current_page_parent<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>, #suckerfishnav .current_page_ancestor, #suckerfishnav .current-cat-parent<?php } ?> {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_current_page_parent_backgroundcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current-cat, <?php } ?>#suckerfishnav .current_page_item {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>background:#<?php echo $pixo_array['suckerfish_current_page_item_backgroundcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if (!$pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '') { ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current_page_ancestor a:after, #suckerfishnav .current_page_ancestor li a:after, #suckerfishnav .current_page_ancestor li li a:after, #suckerfishnav .current_page_ancestor li li li a:after, #suckerfishnav .current_page_ancestor li li li li a:after, #suckerfishnav .current-cat-parent a:after, #suckerfishnav .current-cat-parent li a:after, #suckerfishnav .current-cat-parent li li a:after, #suckerfishnav .current-cat-parent li li li a:after, #suckerfishnav .current-cat-parent li li li li a:after, <?php } ?>#suckerfishnav .current_page_parent a:after, #suckerfishnav .current_page_parent li a:after, #suckerfishnav .current_page_parent li li a:after, #suckerfishnav .current_page_parent li li li a:after, #suckerfishnav .current_page_parent li li li li a:after {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>content:"";<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current_page_ancestor a:after, #suckerfishnav li .current_page_ancestor a:after, #suckerfishnav li li .current_page_ancestor a:after, #suckerfishnav li li li .current_page_ancestor a:after, #suckerfishnav .current-cat-parent a:after, #suckerfishnav li .current-cat-parent a:after, #suckerfishnav li li .current-cat-parent a:after, #suckerfishnav li li li .current-cat-parent a:after, <?php }
	?>#suckerfishnav .current_page_parent a:after, #suckerfishnav li .current_page_parent a:after, #suckerfishnav li li .current_page_parent a:after, #suckerfishnav li li li .current_page_parent a:after {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_current_page_item_indicatorcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>content:"<?php
		/* HAD DIFFICULTIES GETTING TO WORK
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '»') { ?> \00BB \0020<?php }
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '&raquo;') { ?> \00AB<?php } */
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '<') { ?> \003C<?php }
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '>') { ?> \003E<?php }
			if ($pixo_array['suckerfish_current_page_parent_indicatorsymbol'] == '^') { ?> \005E<?php }
			?>";<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?>
	<?php if ($pixo_array['suckerfish_wordpressoptomised']  == 'yes') { ?>#suckerfishnav .current-cat a:after, #suckerfishnav li .current-cat a:after, #suckerfishnav li li .current-cat a:after, #suckerfishnav li li li .current-cat a:after, #suckerfishnav li li li li .current-cat a:after, <?php }
	?>#suckerfishnav .current_page_item a:after, #suckerfishnav li .current_page_item a:after, #suckerfishnav li li .current_page_item a:after, #suckerfishnav li li li .current_page_item a:after, #suckerfishnav li li li li .current_page_item a:after  {<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>color:#<?php echo $pixo_array['suckerfish_current_page_item_indicatorcolour']; ?>;<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>content:"<?php
		/* HAD DIFFICULTIES GETTING TO WORK
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '»') { ?> \00BB \0020<?php }
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '&raquo;') { ?> \00AB<?php } */
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '<') { ?> \003C<?php }
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '>') { ?> \003E<?php }
			if ($pixo_array['suckerfish_current_page_item_indicatorsymbol'] == '^') { ?> \005E<?php }
			?>";<?php echo $linebreak; ?>
	<?php echo $tabgap; ?>}<?php echo $linebreak; ?><?php } ?>
	
	<?php } /* END OF PREMIUM STUFF */ ?>
	</code><?php
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}

add_shortcode('dropdowns', 'pixopoint_dropdowngen');

