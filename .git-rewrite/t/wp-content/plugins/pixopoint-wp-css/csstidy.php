<?php

function safecss_class() {
	// Wrapped so we don't need the parent class just to load the plugin
	require('csstidy/class.csstidy.php');
	class safecss extends csstidy_optimise {
		var $tales = array();
		var $props_w_urls = array('background', 'background-image', 'list-style', 'list-style-image');
		var $allowed_protocols = array('http');

		function safecss(&$css) {
			return $this->csstidy_optimise($css);
		}

		function postparse() {
			if ( !empty($this->parser->import) ) {
				$this->tattle("Import attempt:\n".print_r($this->parser->import,1));
				$this->parser->import = array();
			}
			if ( !empty($this->parser->charset) ) {
				$this->tattle("Charset attempt:\n".print_r($this->parser->charset,1));
				$this->parser->charset = array();
			}
			return parent::postparse();
		}

		function subvalue() {
			$this->sub_value = trim($this->sub_value);

			// Send any urls through our filter
			if ( preg_match('!^\\s*url\\s*(?:\\(|\\\\0028)(.*)(?:\\)|\\\\0029).*$!Dis', $this->sub_value, $matches) )
				$this->sub_value = $this->clean_url($matches[1]);

			// Strip any expressions
			if ( preg_match('!^\\s*expression!Dis', $this->sub_value) ) {
				$this->sub_value = '';
			}

			return parent::subvalue();
		}

		function clean_url($url) {
			// Clean up the string
			$url = trim($url, "' \" \r \n");

			// Check against whitelist for properties allowed to have URL values
			if ( ! in_array($this->property, $this->props_w_urls) ) {
				$this->tattle('URL in illegal property ' . $this->property . ":\n$url");
				return '';
			}

			$url = wp_kses_bad_protocol_once($url, $this->allowed_protocols);

			if ( empty($url) ) {
				$this->tattle('URL empty');
				return '';
			}

			return "url('$url')";
		}
	}
}
