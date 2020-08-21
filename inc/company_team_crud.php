<?php
namespace AGCompanyTeam;
/**
 * CRUD functionality class
 * 
 * Note: uses a global constant called 'AG_COMPANY_TEAM_PLUGIN_DIR',
 * but has no other dependencies
 */
class Crud
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
   * Post actions switcher function
   */
  public function company_team_post_action()
  {
    if (self::DEBUG) {
      $company_team_info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $company_team_info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    global $id;

    if (isset($_POST) && !empty($_POST)) {
      $listaction = $_POST['listaction'];

      if (isset($_POST['memberid'])) {
        $id = absint(intval($_POST['memberid'], 10));
      }

      switch ($listaction) {
          // add new member
        case 'insert':
          include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_insert.php';
          break;

          // edit member
        case 'edit':
          include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_edit.php';
          break;

          // list elements
        case 'list':
          include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
          break;

          // handler function when updating
        case 'handleupdate':
          $this->handle_update();
          include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
          break;

          // handler function when deleting
        case 'handledelete':
          $this->handle_delete();
          include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
          break;

          // handler function when inserting new member
        case 'handleinsert':
          $this->handle_insert();
          include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
          break;

        default:
          // ???
          echo '<h2>Nothing found.</h2>';
      }
    } else {
      include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
    }
  }



  /**
   * Insert new record, add new team member
   * @return void
   */
  function handle_insert()
  {
    if (self::DEBUG) {
      $company_team_info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $company_team_info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    global $wpdb;

    // get sanitized form values from inputs
    $sanitizedData = $this->get_form_input_values();

    try {
      // prepare query, update table
      $res = $wpdb->insert(
        $wpdb->prefix . 'company_team',
        array(
          'profile_photo' => $sanitizedData['new_file_url'],
          'last_name'     => $sanitizedData['last_name'],
          'first_name'    => $sanitizedData['first_name'],
          'phone'         => $sanitizedData['phone'],
          'email'         => $sanitizedData['email'],
          'position'      => $sanitizedData['position'],
          'department'    => $sanitizedData['department'],
          'works_since'   => $sanitizedData['works_since']
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s') // data format
      );

      if ($res === false) {
        throw new InsertRecordException('Database Error: Unable to insert new member into table.');
      } else {
        echo '<div class="notice notice-success is-dismissible"><p>Team member successfully added.' . '</p></div>';
      }
    } catch (InsertRecordException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (DBQueryException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
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
    }
  }



  /**
   * Update current member
   * @return void
   */
  function handle_update()
  {
    if (self::DEBUG) {
      $company_team_info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $company_team_info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {
      global $wpdb;

      // get sanitized form values from inputs
      $sanitizedData = $this->get_form_input_values();

      // if we do not want to update the profile image, e.g. url is an empty string,
      // do not update profile_photo field!
      if ($sanitizedData['new_file_url'] == '') {

        // prepare query, update table
        $res = $wpdb->update(
          $wpdb->prefix . 'company_team',
          array(
            'last_name'     => $sanitizedData['last_name'],
            'first_name'    => $sanitizedData['first_name'],
            'phone'         => $sanitizedData['phone'],
            'email'         => $sanitizedData['email'],
            'position'      => $sanitizedData['position'],
            'department'    => $sanitizedData['department'],
            'works_since'   => $sanitizedData['works_since']
          ),
          array('id'  => $sanitizedData['id']), // where clause
          array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'), // data format
          array('%d') // where format
        );
      } else { // we want to change profile_photo too!
        // prepare query, update table
        $res = $wpdb->update(
          $wpdb->prefix . 'company_team',
          array(
            'profile_photo' => $sanitizedData['new_file_url'],
            'last_name'     => $sanitizedData['last_name'],
            'first_name'    => $sanitizedData['first_name'],
            'phone'         => $sanitizedData['phone'],
            'email'         => $sanitizedData['email'],
            'position'      => $sanitizedData['position'],
            'department'    => $sanitizedData['department'],
            'works_since'   => $sanitizedData['works_since']
          ),
          array('id'  => $sanitizedData['id']), // where clause
          array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'), // data format
          array('%d') // where format
        );
      }



      if ($res === false) {
        throw new UpdateRecordException('Database Error: Unable to update team member data/record.');
      } else {
        echo '<div class="notice notice-success is-dismissible"><p>Team member data successfully updated.' . '</p></div>';
      }
    } catch (UpdateRecordException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (DBQueryException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
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

    }
  }



  /**
   * delete current member
   * @return void
   */
  function handle_delete()
  {
    if (self::DEBUG) {
      $company_team_info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $company_team_info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {

      global $wpdb;

      if (isset($_POST['memberid'])) {
        $id = $_POST['memberid'];

        // prepare get statement protect against SQL inject attacks!
        $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "company_team WHERE id = %d", $id);

        // perform query
        $res = $wpdb->query($sql);

        if ($res === false) {
          throw new DeleteRecordException('Database error: Unable to delete team member.');
        } else {
          echo '<div class="notice notice-success is-dismissible"><p>Team member successfully deleted.' . '</p></div>';
        }
      }
    } catch (DeleteRecordException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (DBQueryException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
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

    }
  }


  /**
   * Add new member
   * @return void
   */
  function company_team_insert()
  {
    if (self::DEBUG) {
      $company_team_info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $company_team_info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }
    
    try {
      global $wpdb;
      if (!current_user_can('manage_options')) {
        throw new PermissionsException('You do not have sufficent permissions to view this page.');
        wp_die('You do not have sufficent permissions to view this page.');
      }

      if (!empty($_POST)) {
        $this->company_team_post_action();
      } else {
        include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_insert.php';
      }
    } catch (PermissionsException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
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
    }
  }



  /**
   * Get list of all members
   * @return void
   */
  function company_team_list()
  {
    if (self::DEBUG) {
      $company_team_info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br>";
      echo '<div class="notice notice-info is-dismissible">' . $company_team_info_text . '</p></div>';
    }
    if (self::LOGGING) {
      global $company_team_log;
      $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    try {
      global $wpdb;

      // note: current_user_can() always returns false if the user is not logged in
      if (!current_user_can('manage_options')) {

        throw new PermissionsException('You do not have sufficent permissions to view this page.');
        // wp_die('You do not have sufficent permissions to view this page.');
      }

      $this->company_team_post_action();
      // include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';

    } catch (PermissionsException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
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
    }
  }
}
