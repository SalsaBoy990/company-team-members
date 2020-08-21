<?php
namespace AGCompanyTeam;
/**
 * Shortcode functionality class
 * 
 * Note: uses a global constant called 'AG_COMPANY_TEAM_PLUGIN_DIR',
 * but has no other dependencies
 */
class ShortCodes
{

  const DEBUG = 0;
  const LOGGING = 1;

  public function __construct()
  {
  }
  public function __destruct()
  {
  }


  /**
   * Get all members from the database
   * 
   * argument passed by reference
   * @param reference $formData
   * @return bool
   */
  public function get_all_members_from_db(&$formData)
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {
      // db abstraction layer
      global $wpdb;
      $valid = true;
  
      $sql = "SELECT * FROM " . $wpdb->prefix . "company_team";
  
      $formData = $wpdb->get_results($sql);
  
      // print_r($formData);
  
      if (!$formData) {
        $valid = false;
        throw new EmptyDBTableException('Warning: Data table does not contain any records yet.');
      }

    } catch (EmptyDBTableException $ex) {
      echo '<div class="notice notice-warning is-dismissible"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (\Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    } finally {
      // always executes
      return $valid;
    }

  }



  /**
   * List all members as shortcode
   * 
   * table and list views
   * extract shortcode arguments
   * - type: 'list' or 'table'
   * - default: 'list'
   * @param array $atts shortcode arguments as key-value pairs
   * @return void
   * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
   */
  public function company_team_user_form($atts, $content = null)
  {
    if (self::DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    
    global $post;

    $formData = null;
    $valid = $this->get_all_members_from_db($formData);

    /**
     * extract shortcode arguments
     * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
     * type: 'list' or 'table'
     * default: 'list'
     */
    extract(shortcode_atts(array(
      'type'              => 'list',
      'name'              => true,
      'first_name_first'  => false,
      'photo'             => true,
      'phone'             => true,
      'email'             => true,
      'position'          => true,
      'department'        => false,
      'works_since'       => false,
    ), $atts));

    ob_start();

    if ($type === 'table') {
      include __DIR__ . '/../pages/company_team_shortcode_table.php';
    } else if ($type === 'list') {
      include __DIR__ . '/../pages/company_team_shortcode_list.php';
    }

    $content = ob_get_clean();

    return $content;
  }
}
