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
        add_filter('the_content', array($this, 'if_wrap'));
	}

    function if_wrap($content) {
        if((is_main_query() AND
            is_single()) AND (
                get_option('wcp_wordcount', '1') OR
                get_option('wcp_charcount', '1') OR
                get_option('wcp_readtime', '1')
        )) {
            return $this->create_html($content);
        }
        return $content;
    }

    function create_html($content) {
        $html = '<h3>'.get_option('wcp_headline', 'Post Statistics').'</h3><p>';

        // get word count once

        if(get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1')) {
	        $word_count = str_word_count( strip_tags( $content ) );
        }

        if(get_option('wcp_wordcount', '1')) {
            $html .= 'This post has '. $word_count .' words. <br>';
        }

	    if(get_option('wcp_charcount', '1')) {
		    $html .= 'This post has '. strlen(strip_tags($content)) .' characters. <br>';
	    }

        if(get_option('wcp_readtime', '1')) {
            $html .= 'This post will take about '. round($word_count / 225) . ' minute(s) to read. <br>';
        }

        $html .= '</p>';

        if(get_option('wcp_location', '0') == '0') {
            return $html . $content;
        }
        return $content . $html;
    }

    function settings() {
        add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');

        // Location

	    add_settings_field('wcp_location', 'Display Location', array($this, 'location_html'), 'word-count-settings-page', 'wcp_first_section');
	    register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitize_location'), 'default' => '0'));

        // 0 - Display at the beginning; 1 - display at the end

        //Headline

	    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headline_html'), 'word-count-settings-page', 'wcp_first_section');
	    register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));

        // Checkboxes

	    add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkbox_html'), 'word-count-settings-page', 'wcp_first_section', array('the_name' => 'wcp_wordcount'));
	    register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

	    add_settings_field('wcp_charcount', 'Character Count', array($this, 'checkbox_html'), 'word-count-settings-page', 'wcp_first_section', array('the_name' => 'wcp_charcount'));
	    register_setting('wordcountplugin', 'wcp_charcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

	    add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkbox_html'), 'word-count-settings-page', 'wcp_first_section', array('the_name' => 'wcp_readtime'));
	    register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    }

    function sanitize_location($input) {
        if($input != 0 AND $input != 1) {
            add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end');
            return get_option('wcp_location');
        }
        return $input;
    }

    function checkbox_html($type) { ?>
        <input type="checkbox" name="<?php echo $type['the_name']?>" value="1" <?php echo checked(get_option($type['the_name'], '1')); ?>>
    <?php }

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