<?php
/*
Plugin Name: Company Team Members
Plugin URI: https://github.com/SalsaBoy990/company-team-members
Description: Company Team Members plugin
Version: 1.0.1
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: company-team
Domain Path: /languages
*/

// always use namespaces to avoid
// class/function/const name collisions
namespace AGCompanyTeam;

defined( 'ABSPATH' ) or die('hhrdshds');




// require all requires once
require_once 'requires.php';

if ( ! class_exists( 'Company_Team')):

/**
 * Company Team Members plugin class
 */
class Company_Team
{
  // use shortcodes trait
  // use \AGCompanyTeam\ShortCodes;
  // // use crud functionality trait
  // use \AGCompanyTeam\Crud;

  const br = '<br />';
  const TEXT_DOMAIN = 'company-team';

  // debug and logging constants
  const DEBUG = 0;
  const LOGGING = 1;

  const TABLE_NAME = 'company_team';
  const DB_VERSION = '1.0';

  // class instance
  private static $instance;

  public static $crud;
  public static $shortcodes;




  /**
   * Get class instance, if not exists -> instantiate it
   * @return self $instance
   */
  public static function getInstance()
  {
    if (self::$instance == NULL) {
      self::$instance = new self(
        new \AGCompanyTeam\Crud(),
        new \AGCompanyTeam\ShortCodes()
      );
    }
    return self::$instance;
  }



  /**
   * Constructor
   * - add hooks here
   * @return void
   */
  private function __construct(
    \AGCompanyTeam\Crud $crud,
    \AGCompanyTeam\ShortCodes $shortcodes
  ) {

    self::$crud = $crud;
    self::$shortcodes = $shortcodes;

    // need to happen in global namespace
    // we don't need to do anything when deactivation
    // register_deactivation_hook(__FILE__, function () {});
    register_activation_hook(__FILE__, array($this, 'activate_plugin_create_table'));


    add_action('plugins_loaded', array($this, 'load_textdomain'));

    // register shortcode to list all members
    add_shortcode('company_team', array(self::$shortcodes, 'company_team_user_form'));


    // add admin menu and page
    add_action('admin_menu', array($this, 'company_team_admin_menu'));

    // put the css into head (olnly admin page)
    add_action('admin_head', array($this, 'company_team_admin_css'));

    // put the css before end of </body>
    add_action('wp_enqueue_scripts', array($this, 'company_team_admin_css'));
  }



  // destructor
  private function ____destruct()
  {
  }



  // METHODS

  public static function load_textdomain()
  {
    // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php

    $domain = self::TEXT_DOMAIN;
    $locale = apply_filters('plugin_locale', get_locale(), $domain);

    load_textdomain($domain, trailingslashit(\WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
    load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages/');
  }



  /**
   * Register admin menu page and submenu page
   * @return void
   */
  function company_team_admin_menu()
  {
    add_menu_page(
      __('Company Team Members', 'company-team'), // page title
      __('Company Team', 'company-team'), // menu title
      'manage_options', // capability
      'company_team_list', // menu slug
      array(self::$crud, 'company_team_list'), // callback
      'dashicons-groups' // icon
    );

    add_submenu_page(
      'company_team_list', //parent slug
      __('Add new team member', 'company-team'), // page title
      __('Add new', 'company-team'),  // menu title
      'manage_options', // capability
      'company_team_insert', // menu slug
      array(self::$crud, 'company_team_insert') // callback
    );
  }



  /**
   * Add some styling to the plugin's admin and shortcode UI
   * @return void
   */
  function company_team_admin_css()
  {
    wp_enqueue_style(
      'company_team_css',
      plugins_url() . '/company-team/css/company-team.css'
    );
  }



  /**
   * Add profile photo, save file in media folder
   * @return string the image url
   */
  function add_profile_photo(&$profilepicture)
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }


    // upload profile image
    // @see https://rudrastyh.com/wordpress/how-to-add-images-to-media-library-from-uploaded-files-programmatically.html
    // wp media folder
    $wordpress_upload_dir = wp_upload_dir();
    // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
    // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
    $i = 1; // number of tries when the file with the same name is already exists


    // store file object
    // $profilepicture = $_FILES['profilepicture'];

    // new path for the image in media folder
    $new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'];
    $new_file_url = $wordpress_upload_dir['url'] . '/' . $profilepicture['name'];

    if (empty($profilepicture))
      wp_die('File is not selected.');

    if ($profilepicture['error'])
      wp_die($profilepicture['error']);

    if ($profilepicture['size'] > wp_max_upload_size())
      wp_die('It is too large than expected.');


    // get mime type
    $new_file_mime = mime_content_type($profilepicture['tmp_name']);

    if (!in_array($new_file_mime, get_allowed_mime_types()))
      wp_die('WordPress doesn\'t allow this type of uploads.');

    // if file exits with that name
    while (file_exists($new_file_path)) {
      $i++;
      $new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'] . '_' . $i;
      $new_file_url = $wordpress_upload_dir['url'] . '/' . $profilepicture['name'] . '_' . $i;
    }

    // looks like everything is OK, move file from temp to media folder, use path
    if (move_uploaded_file($profilepicture['tmp_name'], $new_file_path)) {

      // Insert an attachment
      $upload_id = wp_insert_attachment(array(
        'guid'           => $new_file_url, // use the url!
        'post_mime_type' => $new_file_mime,
        'post_title'     => preg_replace('/\.[^.]+$/', '', $profilepicture['name']),
        'post_content'   => '',
        'post_status'    => 'inherit'
      ), $new_file_url);
    }

    // wp_generate_attachment_metadata() won't work if you do not include this file
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Generate and save the attachment metas into the database
    wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));

    // Show the uploaded file in browser, not needed
    // wp_redirect($wordpress_upload_dir['url'] . '/' . basename($new_file_path));

    // return image url to store it in db table
    return $new_file_url;
  }




  /**
   * Create a wp db table (if not exists) when plugin is activated
   */
  public static function activate_plugin_create_table()
  {

    global $wpdb;

    $table_name = $wpdb->prefix . self::TABLE_NAME;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile_photo` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `position` VARCHAR(100) NOT NULL,
    `department` VARCHAR(100) NOT NULL,
    `works_since` DATE NOT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);


    $company_team_options = self::TABLE_NAME . '_db_version';
    // check if option exists, then delete
    if (!get_option($company_team_options) === false) {
      add_option($company_team_options, self::DB_VERSION);
    }

    return;
  }

  // when uninstalling the plugin, the uninstall.php will run (see it in the root folder of the plugin)
  // it will drop the custom database table
  // if you want to keep your data, save it
  // click on Company Team menu,
  // beneath the table there are the links to download table data in JSON and in CSV format



}


// pass dependency class instantiations as params
Company_Team::getInstance(
  new \AGCompanyTeam\Crud(),
  new \AGCompanyTeam\ShortCodes()
);

endif;