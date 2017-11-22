<?php

/**
 * The following function was adapted from code found at http://www.phpbuilder.com/snippet/detail.php?type=snippet&id=1348
 * This function is for PHP scripts to clean up HTML code before outputting it. The function applies correct indentation to HTML/XHTML 1.0 and JavaScript and makes the output much more readable. You can specify the wanted indentation through the variable $indent
 * License: GNU General Public License
 * Additions by Ryan Hellyer include the ability to modify the indentation type and the initial indentation.
 */

 
/*
 * Indents HTML.
 *
 * @param  string  $fixthistext  The HTML to be fixed
 * @param  string  $indent       The indent code (defaults to a tab character)
 * @return string  The fixed HTML
 */
function varnish_indent_html( $uncleanhtml, $indent = "\t", $initial_indent = '' ) {

	// Seperate tags
	$fixthistext_array = explode( "\n", $uncleanhtml );
	foreach ( $fixthistext_array as $unfixedtextkey => $unfixedtextvalue ) {
		//Makes sure empty lines are ignores
		if ( ! preg_match( "/^(\s)*$/", $unfixedtextvalue ) ) {
			$fixedtextvalue = preg_replace( "/>(\s|\t)*</U", ">\n<", $unfixedtextvalue );
			$fixedtext_array[$unfixedtextkey] = $fixedtextvalue;
		}
	}
	$fixed_uncleanhtml = implode( "\n", $fixedtext_array );


	$uncleanhtml_array = explode( "\n", $fixed_uncleanhtml );

	//Sets no indentation
	$indentlevel = 0;

	//Sets wanted indentation
	foreach ( $uncleanhtml_array as $uncleanhtml_key => $currentuncleanhtml ) {
		//Removes all indentation
		$currentuncleanhtml = preg_replace( "/\t+/", "", $currentuncleanhtml );
		$currentuncleanhtml = preg_replace( "/^\s+/", "", $currentuncleanhtml );
		 
		$replaceindent = "";
		 
		//Sets the indentation from current indentlevel
		for ( $o = 0; $o < $indentlevel; $o++ ) {
			$replaceindent .= $indent;
		}

		//If self-closing tag, simply apply indent
		if ( preg_match( "/<(.+)\/>/", $currentuncleanhtml ) ) { 
			$cleanhtml_array[$uncleanhtml_key] = $replaceindent.$currentuncleanhtml;
		}
		//If doctype declaration, simply apply indent
		else if ( preg_match( "/<!(.*)>/", $currentuncleanhtml ) ) { 
			$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
		}
		//If opening AND closing tag on same line, simply apply indent
		else if ( preg_match( "/<[^\/](.*)>/", $currentuncleanhtml) && preg_match( "/<\/(.*)>/", $currentuncleanhtml ) ) { 
			$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
		}
		//If closing HTML tag or closing JavaScript clams, decrease indentation and then apply the new level
		else if (preg_match( "/<\/(.*)>/", $currentuncleanhtml ) || preg_match( "/^(\s|\t)*\}{1}(\s|\t)*$/", $currentuncleanhtml ) ) {
			$indentlevel--;
			$replaceindent = "";
			for ( $o = 0; $o < $indentlevel; $o++ ) {
				$replaceindent .= $indent;
			}

			$cleanhtml_array[$uncleanhtml_key] = $replaceindent . $currentuncleanhtml;
		}
		//If opening HTML tag AND not a stand-alone tag, or opening JavaScript clams, increase indentation and then apply new level
		else if ( ( preg_match( "/<[^\/](.*)>/", $currentuncleanhtml ) && ! preg_match( "/<(link|meta|base|br|img|hr)(.*)>/", $currentuncleanhtml ) ) || preg_match( "/^(\s|\t)*\{{1}(\s|\t)*$/", $currentuncleanhtml ) ) {
			$cleanhtml_array[$uncleanhtml_key] = $replaceindent.$currentuncleanhtml;

			$indentlevel++;
			$replaceindent = "";
			for ( $o = 0; $o < $indentlevel; $o++ ) {
				$replaceindent .= $indent;
			}
		} else {
			$cleanhtml_array[$uncleanhtml_key] = $replaceindent.$currentuncleanhtml;
		}

		// Add initial indentation (code added by Ryan Hellyer)
		$cleanhtml_array[$uncleanhtml_key] = $initial_indent . $cleanhtml_array[$uncleanhtml_key];

	}

	//Return single string seperated by newline
	return implode( "\n", $cleanhtml_array );
}
