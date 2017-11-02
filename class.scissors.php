<?php

	 /**
	  * Scissors is a HTML management system to maintain better
	  * organisation between UI and serverside code. It also
	  * serves as an easy way to scale and modify HTML across
	  * large enterprise level websites
	  *
	  * @author Tristan Mastrodicasa
	  */

	/**
	 * Html and web resource snippet tool
	 */
	class Scissors {

		// The main HTML directory and HTML output //
		private $html_dir = "/";
		public $html = '';

		/**
		 * Scissors constructor
		 * @param string $html_dir Directory containing all of the HTML resources
		 */
		public function __construct ($html_dir) {

			// Set the directory containing all of the HTML files //
			if (isset($html_dir) && is_string($html_dir) && is_dir($html_dir)) {

				// Append a slash if the directory definition doen't have one //
				if (substr($html_dir, -1) != '/') $html_dir .= '/';
				$this->html_dir = $html_dir;

			} else if (isset($html_dir)) throw new Exception("Main html file directory could not be found");

		}

		/**
		 * function takes a string or file path and set's it as the
		 * main canvas which all other html code will be cut and pasted to
		 * @param string $html HTML directory or html string
		 */
		public function set_canvas ($html) {

			// Check if the canvas has already been set //
			if (strlen($this->html) > 0) throw new Exception("Canvas has already been set");

			// Take the canvas HTML from a file if path exists //
			if (is_file($this->html_dir . $html)) $this->html .= file_get_contents($this->html_dir . $html);

			// Otherwise the passed param is treated as a string //
			else $this->html .= $html;
		}

		/**
		 * This function takes a string or file path or an array of both
		 * ($html) and pastes it to (replaces) the identifier string
		 * ($identifier) where ever found within the canvas.
		 * @param  mixed   $html        HTML file path, string or an array of both
		 * @param  string  $identifier  String to be replaced with $html
		 * @param  boolean $is_text     If true than passed "html" will be processed with htmlspecialchars
		 * @return string               Updates the object's HTML
		 */
		public function paste ($html, $identifier, $is_text = false) {

			// Plugs any html sources into an array //
			if (!is_array($html)) $html = array($html);

			// Append all html from all sources into a single string //
			$html_final = '';

			foreach ($html as $source) {

				// Compile the final HTML output (to be pasted) //
				if (is_file($this->html_dir . $source)) $html_final .= file_get_contents($this->html_dir . $source);
				else $html_final .= $source;

			}

			// Clean text if set //
			if ($is_text) $html_final = htmlspecialchars($html_final, ENT_SUBSTITUTE);

			// Escape all occurences of '-' //
			$identifier = str_replace("-", '\-', $identifier);

			// Replace the html //
			$this->html = preg_replace("/{{{\ $identifier\ }}}/", $html_final, $this->html);
		}

		/**
		 * This function replaces all of the identifiers in the html with the
		 * prefix 'a:' (ex. {{{ a:home }}} ) with url's defined inside a JSON
		 * file within the current HTML build
		 * @param  string  $json_path   A path for the JSON file with the URL's
		 * @param  mixed   $json_key    A key to restrict which variables to loop through
		 */
		public function update_urls ($json_path, $json_key = false) {

			// Check if the JSON file exists //
			if (is_file($this->html_dir . $json_path)) $json_raw = file_get_contents($this->html_dir . $json_path);
			else throw new Exception("JSON file not found");

			// Parse JSON data from JSON file //
			$json = json_decode($json_raw, true);
			if ($json == null) throw new Exception("Error parsing JSON file");

			// Choose a set of key => values to loop through //
			if (!$json_key) $parent_key = 'globals';
			else $parent_key = $json_key;

			foreach(array_keys($json[$parent_key]) as $key) {

				// Check if the URL is complete //
				if (!filter_var($json[$parent_key][$key], FILTER_VALIDATE_URL)) {

					// If the URL is incomplete than append the SERVER URL //
					$json[$parent_key][$key] =  ((array_key_exists("HTTPS", $_SERVER) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://") . $_SERVER['SERVER_NAME'] . '/' . $json[$parent_key][$key];
				}

				// Escape all occurences of '-' //
				$identifier = str_replace("-", '\-', $key);

				// Replace the links //
				$this->html = preg_replace("/{{{\ a:$identifier\ }}}/", $json[$parent_key][$key], $this->html);
			}

		}

	}

?>
