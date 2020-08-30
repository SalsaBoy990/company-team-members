<?php

namespace AG\CompanyTeam\Shortcodes;

defined('\ABSPATH') or die();

use\AG\CompanyTeam\Crud\{EmptyDBTableException as EmptyDBTableException, DBQueryException as DBQueryException};


/**
 * Shortcode functionality class
 * Note: uses a global constant called 'AG_COMPANY_TEAM_PLUGIN_DIR',
 * but has no other dependencies
 */
class ShortCodes
{
    use \AG\CompanyTeam\Log\Logger;

    private const DEBUG = 0;
    private const LOGGING = 1;

    public function __construct()
    {
    }
    public function __destruct()
    {
    }


    /**
     * Get all members from the database argument passed by reference
     * @param reference $formData
     * @return bool
     */
    public function getAllMembersFromDB(&$formData): bool
    {
        $this->logger(self::DEBUG, self::LOGGING);

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
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (DBQueryException $ex) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } finally {
            // always executes
            return $valid;
        }
    }



    /**
     * List all members as shortcode
     * table and list views
     * extract shortcode arguments
     * - type: 'list' or 'table'
     * - default: 'list'
     * @param array $atts shortcode arguments as key-value pairs
     * @return string
     * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
     */
    public function companyTeamUserForm(array $atts, string $content = null): string
    {
        $this->logger(self::DEBUG, self::LOGGING);

        global $post;

        $formData = null;
        $valid = $this->getAllMembersFromDB($formData);

        /**
         * extract shortcode arguments
         * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
         * type: 'list' or 'table'
         * default: 'list'
         */
        extract(shortcode_atts(array(
            'type'              => 'list',
            'name'              => 1,
            'first_name_first'  => 0,
            'photo'             => 1,
            'phone'             => 1,
            'email'             => 1,
            'position'          => 1,
            'department'        => 0,
            'works_since'       => 0,
        ), $atts));

        ob_start();

        if ($type === 'table') {
            include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_shortcode_table.php';
        } elseif ($type === 'list') {
            include AG_COMPANY_TEAM_PLUGIN_DIR . '/pages/company_team_shortcode_list.php';
        }

        $content = ob_get_clean();

        return $content;
    }
}
