<?php

// always use namespaces to avoid
// class/function/const name collisions
namespace AG\CompanyTeam;

// require_once '../../autoload.php';

defined('ABSPATH') or die();

/**
 * Company Team Members plugin class
 */
class CompanyTeam
{
    private const TEXT_DOMAIN = 'company-team';

    private const TABLE_NAME = 'company_team';

    private const DB_VERSION = '1.0';

    // class instance
    private static $instance;

    private static $crud;

    private static $shortcodes;


    /**
     * Get class instance, if not exists -> instantiate it
     * @return self $instance
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self(
                new \AG\CompanyTeam\Crud\Crud(),
                new \AG\CompanyTeam\ShortCodes\ShortCodes()
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
        \AG\CompanyTeam\Crud\Crud $crud,
        \AG\CompanyTeam\ShortCodes\ShortCodes $shortcodes
    ) {
        self::$crud = $crud;
        self::$shortcodes = $shortcodes;


        add_action('plugins_loaded', array($this, 'loadTextdomain'));

        // register shortcode to list all members
        add_shortcode('company_team', array(self::$shortcodes, 'companyTeamUserForm'));

        // add admin menu and page
        add_action('admin_menu', array($this, 'companyTeamAdminMenu'));

        // put the css into head (olnly admin page)
        // add_action('admin_head', array($this, 'companyTeamAdminCSS'));
        // add script on the backend
        add_action('admin_enqueue_scripts', array($this, 'adminLoadScripts'));


        // put the css before end of </body>
        add_action('wp_enqueue_scripts', array($this, 'companyTeamAdminCSS'));

        // add ajax script
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('ag-company-team-js', plugin_dir_url(dirname(__FILE__)) . 'js/companyTeam.js', array('jquery'));

            // enable ajax on frontend
            wp_localize_script('ag-company-team-js', 'AGCompanyTeamAJAX', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('ag-comteam-45zjslkja')
            ));
        });

        // connect AJAX request with PHP hooks
        add_action('wp_ajax_ag_company_team_action', array($this, 'company_team_ajax_handler'));
        add_action('wp_ajax_nopriv_ag_company_team_action', array($this, 'company_team_ajax_handler'));
    }




    // destructor
    private function ____destruct()
    {
    }



    // METHODS
    public static function loadTextdomain(): void
    {
        // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php

        $domain = self::TEXT_DOMAIN;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(\WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, basename(dirname(__FILE__, 2)) . '/languages/');
    }



    /**
     * Register admin menu page and submenu page
     * @return void
     */
    public function companyTeamAdminMenu(): void
    {
        add_menu_page(
            __('Company Team Members', 'company-team'), // page title
            __('Company Team', 'company-team'), // menu title
            'manage_options', // capability
            'company_team_list', // menu slug
            array(self::$crud, 'listTable'), // callback
            'dashicons-groups' // icon
        );

        add_submenu_page(
            'company_team_list', //parent slug
            __('Add new team member', 'company-team'), // page title
            __('Add new', 'company-team'),  // menu title
            'manage_options', // capability
            'company_team_insert', // menu slug
            array(self::$crud, 'insertRecord') // callback
        );
    }



    /**
     * Add some styling to the plugin's admin and shortcode UI
     * @return void
     */
    public function companyTeamAdminCSS(): void
    {
        wp_enqueue_style(
            'company_team_css',
            plugin_dir_url(dirname(__FILE__)) . 'css/company-team.css'
        );
    }

    public function adminLoadScripts($hook): void
    {
        if (
            $hook != 'toplevel_page_company_team_list'
            && $hook != 'ceges-csapat_page_company_team_insert'
        ) {
            return;
        }

        wp_enqueue_style(
            'ag_company_team_admin_css',
            plugin_dir_url(dirname(__FILE__)) . 'css/company-team.css'
        );
        // wp_enqueue_script('custom-js', plugins_url('js/custom.js', dirname(__FILE__, 2)));
    }


    public function company_team_ajax_handler()
    {
        if (check_ajax_referer('ag-comteam-45zjslkja', 'security')) {
            $args = $_REQUEST;
            $args = $_REQUEST['args'];
            $content = self::$shortcodes->companyTeamUserForm($args);
            wp_send_json_success($content);
        } else {
            wp_send_json_error();
        }
        wp_die();
    }


    /**
     * Create a wp db table (if not exists) when plugin is activated
     */
    public static function activatePlugin(): void
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

        require_once(\ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);


        $company_team_options = 'ag_' . self::TABLE_NAME . '_db_version';
        // check if option exists, then delete
        if (!get_option($company_team_options)) {
            add_option($company_team_options, self::DB_VERSION);
        }
    }

    // when uninstalling the plugin, the uninstall.php will run (see it in the root folder of the plugin)
    // it will drop the custom database table
    // if you want to keep your data, save it
    // click on Company Team menu,
    // beneath the table there are the links to download table data in JSON and in CSV format
}
