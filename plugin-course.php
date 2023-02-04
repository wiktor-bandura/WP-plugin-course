<?php
/*
	Plugin Name: First WordPress Plugin
	Description: Simple WordPress plugin.
	Version: 1.0
	Author: Wiktor & Brad
*/

class WordCountAndTimePlugin {
	function __construct() {
		add_action('admin_menu', array($this, 'admin_page'));
	}
	function admin_page() {
		add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this, 'settings_page_template'));
	}
	function settings_page_template() { ?>
		Hello world from my plugin
	<?php }
}

$word_count_and_time_plugin = new WordCountAndTimePlugin();