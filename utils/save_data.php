<?php

class Company_Team_Save_Data
{

  private static $instance;

  /**
   * Get class instance, if not exists -> instantiate it
   * @return self $instance
   */
  public static function getInstance()
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br />";
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
   * Save table data to a .json file
   * 
   * @param string $filename
   * @param json $json_data
   * @return void
   */
  public static function saveDataToJSON($filename, $json_data)
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br />";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }

    // remove illegal characters from filename
    $filename = sanitize_file_name($filename);

    // add path and extension
    $download_link = plugin_dir_url(__FILE__) . 'download/' . $filename . '.json';
    $filename = plugin_dir_path(__FILE__) . 'download/' . $filename . '.json';

    // try writing to file
    try {
      $result = fopen($filename, 'w+');

      if ($result === false) {
        throw new FileOpenException('Cannot open the file because it is currently not accessible.');
      }

      fwrite($result, $json_data);

      if (!fclose($result)) {
        throw new FileCloseException('Writing data to file failed.');
      }

      $successMsg = '<a href="' . $download_link  . '">' . __('Download table in JSON', 'company-team') . '</a>';
      echo '<div><p>' .  $successMsg . '</p></div>';

    } catch (FileOpenException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (FileCloseException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (FileException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    }
  }



  /**
   * Save table data to a .csv file
   * 
   * @param string $filename
   * @param std_object $formData
   * @param string $delimiter 
   * @return void
   */
  public static function saveDataToCSV($filename, $formData, $delimiter = ';')
  {
    if (AG_COMPANY_TEAM_DEBUG) {
      $info_text = "Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__ . "<br />";
      echo '<div class="notice notice-info is-dismissible">' . $info_text . '</p></div>';
    }
    if (AG_COMPANY_TEAM_LOGGING) {
      global $log;
      $log->logInfo("Entering - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
    }


    // remove illegal characters from filename
    $filename = sanitize_file_name($filename);


    $download_link = plugin_dir_url(__FILE__) . 'download/' . $filename . '.csv';
    // add path and extension
    $filename = plugin_dir_path(__FILE__) . 'download/' . $filename . '.csv';
   

    // try writing to file
    try {
      // binary safe mode
      $result = fopen($filename, 'w+');

      if ($result === false) {
        throw new FileOpenException('Cannot open the file because it is currently not accessible.');
      }

      // all prop names as comma separated values
      $csv_header = $formData[0]->id . $delimiter .
        $formData[0]->profile_photo . $delimiter .
        $formData[0]->last_name . $delimiter .
        $formData[0]->first_name . $delimiter .
        $formData[0]->phone . $delimiter .
        $formData[0]->email . $delimiter .
        $formData[0]->position . $delimiter .
        $formData[0]->department . $delimiter .
        $formData[0]->works_since;


      // The ISO-8859-2 encoding is specific to Hungarian language, comment it out
      fwrite($result, iconv('utf-8', 'ISO-8859-2', "{$csv_header}\r\n"));
      // uncomment next line to use utf-8 encoding
      // fwrite($result, utf8_encode($csv_header) . "\r\n");

      $tmp_row = '';
      foreach ($formData as $row) {
        $tmp_row = $row->id . $delimiter .
          $row->profile_photo . $delimiter .
          $row->last_name . $delimiter .
          $row->first_name . $delimiter .
          $row->phone . $delimiter .
          $row->email . $delimiter .
          $row->position . $delimiter .
          $row->department . $delimiter .
          $row->works_since;
        fwrite($result, iconv('utf-8', 'ISO-8859-2', "{$tmp_row}\r\n"));
        // fwrite($result, utf8_encode($tmp_row) . "\r\n");
      }


      if (!fclose($result)) {
        throw new FileCloseException('Writing data to file failed.');
      }

      $successMsg = '<a href="' . $download_link  . '">' . __('Download table in CSV', 'company-team') . '</a>';
      echo '<div><p>' .  $successMsg . '</p></div>';

    } catch (FileOpenException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (FileCloseException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (FileException $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }

    } catch (Exception $ex) {
      echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '. </p></div>';
      if (AG_COMPANY_TEAM_LOGGING) {
        global $log;
        $log->logInfo($ex->getMessage() . " - " . __FILE__ . ":" . __FUNCTION__ . ":" . __LINE__);
      }
    
    }
  }


}

Company_Team_Save_Data::getInstance();
