<?php

namespace AGCompanyTeam;

defined('ABSPATH') or die();
if (!class_exists('Company_Team_Save_Data')) :
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

      // !!! verify insert nonce !!!
      if (
        !isset($_POST['company_admin_insert_security'])
        || !wp_verify_nonce($_POST['company_admin_insert_security'], 'company_team_insert')
      ) {
        print 'Sorry, your nonce did not verify.';
        exit;
      } else {

        try {

          global $wpdb;

          // get sanitized form values from inputs
          $sanitizedData = $this->get_form_input_values();


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

      // echo  $_POST['company_admin_edit_security'];

      // !!! verify edit nonce !!!
      if (
        !isset($_POST['company_admin_edit_security'])
        || !wp_verify_nonce($_POST['company_admin_edit_security'], 'company_team_edit')
      ) {
        print 'Sorry, your nonce did not verify.';
        exit;
      } else {
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

      // !!! verify edit nonce !!!
      if (
        !isset($_POST['company_admin_edit_security'])
        || !wp_verify_nonce($_POST['company_admin_edit_security'], 'company_team_edit')
      ) {
        print 'Sorry, your nonce did not verify.';
        exit;
      } else {

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


    /**
     * Get form input, sanitize values
     * @return array associative
     */
    function get_form_input_values()
    {
      if (self::DEBUG) {
        $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
        echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
      }
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
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
          if ($error_code > 0) {
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
          if (self::LOGGING) {
            global $company_team_log;
            $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
          }
          $formValues['new_file_url'] = '';
        } catch (ImageInputException $ex) {
          echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
          if (self::LOGGING) {
            global $company_team_log;
            $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
          }
          $formValues['new_file_url'] = '';
        } catch (\Exception $ex) {
          echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
          if (self::LOGGING) {
            global $company_team_log;
            $company_team_log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
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


    /**
     * Sanitizes input values
     * strips tags, more sanitization needed!
     * @return string
     */
    function sanitize_input($input)
    {
      if (self::DEBUG) {
        $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . Company_Team::br;
        echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
      }
      if (self::LOGGING) {
        global $company_team_log;
        $company_team_log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
      return wp_strip_all_tags(trim($input));
    }
  }

endif;
