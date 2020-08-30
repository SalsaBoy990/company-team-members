<?php

namespace AG\CompanyTeam\Crud;

// require_once '../../autoload.php';

defined('ABSPATH') or die();

/**
 * CRUD functionality class
 * Note: uses a global constant called 'AG_COMPANY_TEAM_PLUGIN_DIR',
 * but has no other dependencies
 */
class Crud extends \AG\CompanyTeam\DB\WPDBHandle implements CrudInterface
{
    // use \AG\CompanyTeam\Log\Logger;
    // use \AG\CompanyTeam\Input\FormInput;

    private const DEBUG = 0;
    private const LOGGING = 1;
    private const TABLE_NAME = 'company_team';

    public function __construct()
    {
        parent::__construct();
    }
    public function __destruct()
    {
    }


    /**
     * Post actions switcher function
     */
    public function postAction(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        global $id;

        // if (isset($_POST) && !empty($_POST)) {
        if (($_POST ?? 0) && !empty($_POST)) {
            $listaction = $_POST['listaction'];

            if ($_POST['memberid'] ?? 0) {
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
                    $this->handleUpdate();
                    include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
                    break;

                    // handler function when deleting
                case 'handledelete':
                    $this->handleDelete();
                    include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
                    break;

                    // handler function when inserting new member
                case 'handleinsert':
                    $this->handleInsert();
                    include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
                    break;
                default:
                    include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
                    break;
            }
        } else {
            include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
        }
    }



    /**
     * Insert new record, add new team member
     * @return void
     */
    public function handleInsert(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        // !!! verify insert nonce !!!
        if (
            !isset($_POST['company_admin_insert_security'])
            || !wp_verify_nonce($_POST['company_admin_insert_security'], 'company_team_insert')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                // get sanitized form values from inputs
                $sanitizedData = $this->getFormInputValues();

                // prepare query, update table
                $res = $this->insert(self::TABLE_NAME, $sanitizedData);

                if ($res === false) {
                    throw new InsertRecordException('Database Error: Unable to insert new member into table.');
                } else {
                    echo <<<ADDMEMBER
                        <div class="notice notice-success is-dismissible">
                            <p>Team member successfully added.</p>
                        </div>
ADDMEMBER;
                }
            } catch (InsertRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            }
        }
    }



    /**
     * Update current member
     * @return void
     */
    public function handleUpdate(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        // !!! verify edit nonce !!!
        if (
            !isset($_POST['company_admin_edit_security']) ||
            !wp_verify_nonce($_POST['company_admin_edit_security'], 'company_team_edit')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                // get sanitized form values from inputs
                $sanitizedData = $this->getFormInputValues();

                // echo '<pre>';
                // print_r($sanitizedData['new_file_url']);
                // echo '</pre>';

                // if we do not want to update the profile image, e.g. url is an empty string,
                // do not update profile_photo field!
                if ($sanitizedData['new_file_url'] == null) {
                    $res = $this->update(self::TABLE_NAME, $sanitizedData, false);
                } else {
                    // we want to change profile_photo too!
                    // prepare query, update table
                    $res = $this->update(self::TABLE_NAME, $sanitizedData, true);
                }
                // echo $res . PHP_EOL;
                // echo $res === false;
                if ($res == null) {
                    echo <<<MEMBERNOCHANGE
                        <div class="notice notice-success is-dismissible">
                            <p>Team member data unchanged and saved.</p>
                        </div>
MEMBERNOCHANGE;
                } elseif ($res === false) {
                    throw new UpdateRecordException(
                        'Database Error: Unable to update team member data/record.'
                    );
                } else {
                    echo <<<MEMBERUPDATE
                        <div class="notice notice-success is-dismissible">
                            <p>Team member data successfully updated.</p>
                        </div>
MEMBERUPDATE;
                }
            } catch (UpdateRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            }
        }
    }


    /**
     * delete current member
     * @return void
     */
    public function handleDelete(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        // !!! verify edit nonce !!!
        if (
            !isset($_POST['company_admin_edit_security'])
            || !wp_verify_nonce($_POST['company_admin_edit_security'], 'company_team_edit')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                if ($_POST['memberid'] ?? 0) {
                    $id = $_POST['memberid'];

                    // DELETE item
                    $res = $this->delete($id);

                    if ($res === false) {
                        throw new DeleteRecordException('Database error: Unable to delete team member.');
                    } else {
                        echo <<<DELETEMEMBER
                            <div class="notice notice-success is-dismissible">
                                <p>Team member successfully deleted.</p>
                            </div>
DELETEMEMBER;
                    }
                }
            } catch (DeleteRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            }
        }
    }


    /**
     * Add new member
     * @return void
     */
    public function insertRecord(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        try {
            if (!current_user_can('manage_options')) {
                throw new PermissionsException('You do not have sufficent permissions to view this page.');
                wp_die('You do not have sufficent permissions to view this page.');
            }

            if (!empty($_POST)) {
                $this->postAction();
            } else {
                include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_insert.php';
            }
        } catch (PermissionsException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        }
    }




    /**
     * Get list of all members
     * @return void
     */
    public function listTable(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        try {
            global $wpdb;

            // note: current_user_can() always returns false if the user is not logged in
            if (!current_user_can('manage_options')) {
                throw new PermissionsException(
                    'You do not have sufficent permissions to view this page.'
                );
                // wp_die('You do not have sufficent permissions to view this page.');
            }

            $this->postAction();
            // include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_list.php';
        } catch (PermissionsException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        }
    }
}
