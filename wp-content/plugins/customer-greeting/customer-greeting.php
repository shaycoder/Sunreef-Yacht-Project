<?php

/*
  Plugin Name: Customer Greeting Plugin
  Description: A truly amazing plugin.
  Version: 1.0
  Author: Nitin Rajan
*/

class CustomerGreeting {
  function __construct() {
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_shortcode( 'greetingMessage', array($this, 'CheckTime'));
  }

  function CheckTime() {
    date_default_timezone_set('Asia/Dubai');
    $time = date("H");
    if (
        (($time < "12") AND (get_option('wcp_morning', '1'))) OR 
        (($time >= "12" AND $time < "17") AND (get_option('wcp_afternoon', '1'))) OR
        (($time >= "17" AND $time < "19") AND (get_option('wcp_evening', '1')))
      ) {
        if(get_option('wcp_location') == 0) {
          return $this->createTopHTML();
        } else {
          return $this->createBottomHTML();
        }
        return;
    } 
  }

  function createTopHTML() {
    $html = '<h2 class="headline headline--medium">' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h2><p>';

    $html .= '</p>';

    return $html;
  }

  function createBottomHTML() {
    $html = '<div class="page-banner container t-center c-white">
                <h2 class="headline headline--medium">' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h2><p>';

    $html .= '</p></div>';

    return $html;
  }

  function settings() {
    add_settings_section('wcp_first_section', null, null, 'greeting-settings-page');

    add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'greeting-settings-page', 'wcp_first_section');
    register_setting('greetingplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));

    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'greeting-settings-page', 'wcp_first_section');
    register_setting('greetingplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));

    add_settings_field('wcp_morning', 'Show in Morning', array($this, 'checkboxHTML'), 'greeting-settings-page', 'wcp_first_section', array('theName' => 'wcp_morning'));
    register_setting('greetingplugin', 'wcp_morning', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    add_settings_field('wcp_afternoon', 'Show in Afternoon', array($this, 'checkboxHTML'), 'greeting-settings-page', 'wcp_first_section', array('theName' => 'wcp_afternoon'));
    register_setting('greetingplugin', 'wcp_afternoon', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    add_settings_field('wcp_evening', 'Show in Evening', array($this, 'checkboxHTML'), 'greeting-settings-page', 'wcp_first_section', array('theName' => 'wcp_evening'));
    register_setting('greetingplugin', 'wcp_evening', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
  }

  function sanitizeLocation($input) {
    if ($input != '0' AND $input != '1') {
      add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either top or bottom.');
      return get_option('wcp_location');
    }
    return $input;
  }

  // reusable checkbox function
  function checkboxHTML($args) { ?>
    <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
  <?php }

  function headlineHTML() { ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
  <?php }

  function locationHTML() { ?>
    <select name="wcp_location">
      <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Top of the page</option>
      <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>Bottom of the Page</option>
    </select>
  <?php }

  function adminPage() {
    add_options_page('Greeting Settings', 'Greeting', 'manage_options', 'greeting-settings-page', array($this, 'ourHTML'));
  }

  function ourHTML() { ?>
    <div class="wrap">
      <h1>Greeting Settings</h1>
      <form action="options.php" method="POST">
      <?php
        settings_fields('greetingplugin');
        do_settings_sections('greeting-settings-page');
        submit_button();
      ?>
      </form>
    </div>
  <?php }
}

$customerGreeting = new CustomerGreeting();