<?php
/*
Plugin Name: Company Team Members
Plugin URI: https://example.com/
Description: Company Team Members plugin
Version: 0.1
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: company-team
Domain Path: /languages
*/


// require all requires once
require_once 'requires.php';


/**
 * Company Team Members plugin class
 */
class Company_Team
{
  // use shortcodes trait
  use ShortCodes;

  // use crud functionality trait
  use Crud;

  const br = '<br />';
  const TEXT_DOMAIN = 'company-team';

  // class instance
  private static $instance;



  /**
   * Get class instance, if not exists -> instantiate it
   * @return self $instance
   */
  public static function getInstance()
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    if (self::$instance == NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }



  /**
   * Constructor
   * - add hooks here
   * @return void
   */
  private function __construct()
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    add_action('plugins_loaded', array($this, 'load_textdomain'));

    // register shortcode to list all members
    add_shortcode('company_team', array($this, 'company_team_user_form'));

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

  public static function load_textdomain() {
    // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php
  
    $domain = self::TEXT_DOMAIN;
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    
    load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
    load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
  }



  /**
   * Register admin menu page and submenu page
   * @return void
   */
  function company_team_admin_menu()
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    add_menu_page(
      __('Company Team Members', 'company-team'), // page title
      __('Company Team', 'company-team'), // menu title
      'manage_options', // capability
      'company_team_list', // menu slug
      array($this, 'company_team_list'), // callback
      'dashicons-groups' // icon
    );

    add_submenu_page(
      'company_team_list', //parent slug
      __('Add new team member', 'company-team'), // page title
      __('Add new', 'company-team'),  // menu title
      'manage_options', // capability
      'company_team_insert', // menu slug
      array($this, 'company_team_insert') // callback
    );
  }



  /**
   * Add some styling to the plugin's admin and shortcode UI
   * @return void
   */
  function company_team_admin_css()
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
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
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
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
   * Sanitizes input values
   * strips tags, more sanitization needed!
   * @return string
   */
  function sanitize_input($input)
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    return wp_strip_all_tags(trim($input));
  }



  /**
   * Get form input, sanitize values
   * @return array associative
   */
  function get_form_input_values()
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    // store escaped user input field values
    $formValues = array();


    if (isset($_FILES['profilepicture']) && !empty($_FILES['profilepicture'])) {

      // echo '<pre>';
      // print_r($_FILES['profilepicture']);
      // echo '</pre>';

      try {
        // get error code from file input object
        $error_code = intval($_FILES['profilepicture']['error'], 10);
        $profilepicture = $_FILES['profilepicture'];
        // POST image error
        if ( $error_code > 0) {
          /**
           * Error code explanations
           * @see https://www.php.net/manual/en/features.file-upload.errors.php
           */
          switch ($error_code) {
            case 1:
              throw new ImageInputException('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
              break;
            case 2:
              throw new ImageInputException('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
              break;
            case 3:
              throw new ImageInputException('The uploaded file was only partially uploaded.');
              break;
            case 4:
              throw new NoImageUploadException('No profile image was uploaded. The existing image will be used, or if no image exists, a placeholder image will be used.');
              break;
            case 6:
              throw new ImageInputException('Missing a temporary folder.');
              break;
            case 7:
              throw new ImageInputException('Failed to write file to disk.');
              break;
            case 8:
              throw new ImageInputException('A PHP extension stopped the file upload.');
              break;
            default:
              throw new ImageInputException('An unspecified PHP error occured.');
              break;
          }
        }
        $new_file_url = $this->add_profile_photo($profilepicture);
        $formValues['new_file_url'] = $new_file_url;


      } catch (NoImageUploadException $ex) {
        echo '<div class="notice notice-warning is-dismissable"><p>' . $ex->getMessage() . '</p></div>';
        if (AG_COMPANY_TEAM_LOGGING) {
          global $log;
          $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
        }
        $formValues['new_file_url'] = '';

      } catch (ImageInputException $ex) {
        echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
        if (AG_COMPANY_TEAM_LOGGING) {
          global $log;
          $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
        }
        $formValues['new_file_url'] = '';

      } catch (Exception $ex) {
        echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
        if (AG_COMPANY_TEAM_LOGGING) {
          global $log;
          $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
        }
        $formValues['new_file_url'] = '';
      }
    }

    if (isset($_POST['memberid'])) {
      $id = $this->sanitize_input($_POST['memberid']);
      $id = intval($id, 10);
      $formValues['id'] = absint($id);
    }

    if (isset($_POST['last_name'])) {
      $last_name = $this->sanitize_input($_POST['last_name']);
      $formValues['last_name'] = $last_name;
    }

    if (isset($_POST['first_name'])) {
      $first_name = $this->sanitize_input($_POST['first_name']);
      $formValues['first_name'] = $first_name;
    }

    if (isset($_POST['phone'])) {
      $phone = $this->sanitize_input($_POST['phone']);
      $formValues['phone'] = $phone;
    }

    if (isset($_POST['email'])) {
      $email = $this->sanitize_input($_POST['email']);
      $formValues['email'] = $email;
    }

    if (isset($_POST['position'])) {
      $position = $this->sanitize_input($_POST['position']);
      $formValues['position'] = $position;
    }

    if (isset($_POST['department'])) {
      $department = $this->sanitize_input($_POST['department']);
      $formValues['department'] = $department;
    }

    if (isset($_POST['works_since'])) {
      $works_since = $this->sanitize_input($_POST['works_since']);
      $formValues['works_since'] = $works_since;
    }


    return $formValues;
  }
}

Company_Team::getInstance();


global $company_team_db_version;
$company_team_db_version = '1.0';


// need to happen in global namespace

// we don't need to do anything when deactivation
// register_deactivation_hook(__FILE__, function () {});

register_activation_hook(__FILE__, 'activate_plugin_create_table');


/**
 * Create a wp db table (if not exists) when plugin is activated
 */
function activate_plugin_create_table() {
  if (AG_COMPANY_TEAM_DEBUG) {
    $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
    echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
  }
  if (AG_COMPANY_TEAM_LOGGING) {
    global $log;
    $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
  }
  global $wpdb;

  $table_name = $wpdb->prefix . 'company_team';
	
	$charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile_photo` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL',
    `email` VARCHAR(100) NOT NULL',
    `position` VARCHAR(100) NOT NULL',
    `department` VARCHAR(100) NOT NULL',
    `works_since` DATE NOT NULL,
    PRIMARY KEY (`id`)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
  
  add_option( 'company_team_db_version', $company_team_db_version );

  return;
}

// when uninstalling the plugin, the uninstall.php will run (see it in the root folder of the plugin)
// it will drop the custom database table
// if you want to keep your data, save it
// click on Company Team menu,
// beneath the table there are the links to download table data in JSON and in CSV format

