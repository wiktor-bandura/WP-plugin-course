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
        add_action('admin_init', array($this, 'settings'));
	}

    function settings() {
        add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');

        // Location
	    add_settings_field('wcp_location', 'Display Location', array($this, 'location_html'), 'word-count-settings-page', 'wcp_first_section');
	    register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
        // 0 - Display at the beginning; 1 - display at the end

        //Headline
	    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headline_html'), 'word-count-settings-page', 'wcp_first_section');
	    register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));
    }

    function headline_html() { ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    <?php }

    function location_html() { ?>
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location'), '0'); ?>>Beginning of post</option>
            <option value="1" <?php selected(get_option('wcp_location'), '1'); ?>>End of Post</option>
        </select>
    <?php }

	function admin_page() {
		add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this, 'settings_page_template'));
	}

	function settings_page_template() { ?>
		<div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields('wordcountplugin');
                    do_settings_sections('word-count-settings-page');
                    submit_button();
                ?>
            </form>
        </div>
	<?php }
}

$word_count_and_time_plugin = new WordCountAndTimePlugin();