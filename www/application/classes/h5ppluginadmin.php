<?php
/**
 * H5P Plugin.
 *
 * @package   H5P
 * @author    Joubel <contact@joubel.com>
 * @license   MIT
 * @link      http://joubel.com
 * @copyright 2014 Joubel
 */

/**
 * Plugin admin class.
 *
 * TODO: Add development mode
 * TODO: Move results stuff to seperate class
 *
 * @package H5PPluginAdmin
 * @author Joubel <contact@joubel.com>
 */
class H5PPluginAdmin
{

    /**
     * Instance of this class.
     *
     * @since 1.0.0
     * @var \H5PPluginAdmin
     */
    protected static $instance = null;

    /**
     * @since 1.1.0
     */
    private $plugin_slug = null;

    /**
     * Keep track of the current content.
     *
     * @since 1.0.0
     */
    private $content = null;

    /**
     * Keep track of the current library.
     *
     * @since 1.1.0
     */
    private $library = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since 1.0.0
     */
    private function __construct()
    {
        $plugin = H5PPlugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Prepare admin pages / sections
        $this->content = new H5PContentAdmin($this->plugin_slug);
        $this->library = new H5PLibraryAdmin($this->plugin_slug);

        // Load admin style sheet and JavaScript.
        /*add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles_and_scripts'));

        // Add the options page and menu item.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

        // Allow altering of page titles for different page actions.
        add_filter('admin_title', array($this, 'alter_title'), 10, 2);

        // Add settings link to plugin page
        add_filter('plugin_action_links_h5p/h5p.php', array($this, 'add_settings_link'));

        // Custom media button for inserting H5Ps.
        add_action('media_buttons_context',
            array($this->content, 'add_insert_button')); // TODO: Deprecated. Use media_buttons instead!
        add_action('admin_footer', array($this->content, 'print_insert_content_scripts'));
        add_action('wp_ajax_h5p_insert_content', array($this->content, 'ajax_insert_content'));
        add_action('wp_ajax_h5p_inserted', array($this->content, 'ajax_inserted'));

        // Editor ajax
        add_action('wp_ajax_h5p_libraries', array($this->content, 'ajax_libraries'));
        add_action('wp_ajax_h5p_files', array($this->content, 'ajax_files'));

        // AJAX for rebuilding all content caches
        add_action('wp_ajax_h5p_rebuild_cache', array($this->library, 'ajax_rebuild_cache'));

        // AJAX for content upgrade
        add_action('wp_ajax_h5p_content_upgrade_library', array($this->library, 'ajax_upgrade_library'));
        add_action('wp_ajax_h5p_content_upgrade_progress', array($this->library, 'ajax_upgrade_progress'));

        // AJAX for handling content usage datas
        add_action('wp_ajax_h5p_contents_user_data', array($this, 'ajax_contents_user_data'));

        // AJAX for logging results
        add_action('wp_ajax_h5p_setFinished', array($this, 'ajax_results'));

        // AJAX for display content results
        add_action('wp_ajax_h5p_content_results', array($this->content, 'ajax_content_results'));

        // AJAX for display user results
        add_action('wp_ajax_h5p_my_results', array($this, 'ajax_my_results'));

        // AJAX for getting contents list
        add_action('wp_ajax_h5p_contents', array($this->content, 'ajax_contents'));

        // AJAX for restricting library access
        add_action('wp_ajax_h5p_restrict_library', array($this->library, 'ajax_restrict_access'));

        // Display admin notices
        add_action('admin_notices', array($this, 'admin_notices'));

        // Embed
        add_action('wp_ajax_h5p_embed', array($this, 'embed'));
        add_action('wp_ajax_nopriv_h5p_embed', array($this, 'embed'));

        // Remove user data and results
        add_action('deleted_user', array($this, 'deleted_user'));*/
    }

    /**
     * Add settings link to plugin overview page
     *
     * @since 1.6.0
     */
    function add_settings_link($links)
    {
        $links[] = '<a href="' . admin_url('options-general.php?page=h5p_settings') . '">Settings</a>';

        return $links;
    }

    /**
     * Return an instance of this class.
     *
     * @since 1.0.0
     * @return \H5PPluginAdmin A single instance of this class.
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Print messages to admin UI.
     *
     * @since 1.3.0
     */
    public function admin_notices()
    {
        $wpdb = getWPDB();

        // Gather all messages before printing
        $messages = array();

        // Some messages used multiple places
        $staying_msg = __('Thank you for staying up to date with H5P.', $this->plugin_slug);
        $updates_msg = sprintf(wp_kses(__('You should head over to the <a href="%s">Libraries</a> page and update your content types to the latest version.',
            $this->plugin_slug), array('a' => array('href' => array(), 'target' => array()))),
            admin_url('admin.php?page=h5p_libraries'));
        $fetching_msg = sprintf(wp_kses(__('By default, H5P is set up to automatically fetch information regarding Content Type updates from H5P.org. When doing so, H5P will also contribute anonymous usage data to aid the development of H5P. This behaviour can be altered through the <a href="%s">Settings</a> page.',
            $this->plugin_slug), array('a' => array('href' => array()))),
            admin_url('options-general.php?page=h5p_settings'));
        $help_msg = sprintf(wp_kses(__('If you need any help you can always file a <a href="%s" target="_blank">Support Request</a>, check out our <a href="%s" target="_blank">Forum</a> or join the conversation in the <a href="%s" target="_blank">H5P Community Chat</a>.',
            $this->plugin_slug), array('a' => array('href' => array(), 'target' => array()))),
            esc_url('https://wordpress.org/support/plugin/h5p'), esc_url('https://h5p.org/forum'),
            esc_url('https://gitter.im/h5p/CommunityChat'));

        // Handle library updates
        $update_available = get_option('h5p_update_available', 0);
        $current_update = get_option('h5p_current_update', 0);
        if ($update_available != 0 && $current_update == 0) {
            // A new update is available and no version of the H5P Content Types
            // is currently installed.
            $inspiration_msg = sprintf(wp_kses(__('Check out our <a href="%s" target="_blank">Examples and Downloads</a> page for inspiration.',
                $this->plugin_slug), array('a' => array('href' => array(), 'target' => array()))),
                esc_url('https://h5p.org/content-types-and-applications'));

            // Check to see if content types might be installed any way
            if ($wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}h5p_libraries") === '0') {
                // No content types, automatically download and install the latest release
                $messages[] = __('Thank you for choosing H5P.', $this->plugin_slug);
                if (!self::download_h5p_libraries()) {
                    // Prevent trying again automatically. The user will have to press
                    // the download & update button on the libraries page.
                    update_option('h5p_current_update', 1);
                    $messages[] = sprintf(wp_kses(__('Unfortunately, we were unable to automatically install the default content types. You must manually download the content types you wish to use from the <a href="%s" target="_blank">Examples and Downloads</a> page, and then upload them through the <a href="%s">Libraries</a> page.',
                        $this->plugin_slug), array('a' => array('href' => array(), 'target' => array()))),
                        esc_url('https://h5p.org/content-types-and-applications'),
                        admin_url('admin.php?page=h5p_libraries'));
                } else {
                    $messages[] = sprintf(wp_kses(__('We\'ve automatically installed the default content types for your convenience. You can now <a href="%s">start creating</a> your own interactive content!',
                        $this->plugin_slug), array('a' => array('href' => array(), 'target' => array()))),
                        admin_url('admin.php?page=h5p_new'));
                    $messages[] = $inspiration_msg;
                }
            } else {
                update_option('h5p_current_update', 1);

                $messages[] = $staying_msg;
                $messages[] = $updates_msg;
                $messages[] = $inspiration_msg;
            }
            $messages[] = $fetching_msg;
            $messages[] = $help_msg;
            update_option('h5p_last_info_print', H5PPlugin::VERSION);
        }

        // Always print a message after
        $last_print = get_option('h5p_last_info_print', 0);
        if (empty($messages) && $last_print !== H5PPlugin::VERSION) {
            // Looks like we've just updated, always thank the user for updating.
            $messages[] = $staying_msg;
            if ($update_available > $current_update) {
                // User should update content types
                $messages[] = $updates_msg;
            }
            if ($last_print == 0) {
                // Notify user about anonymous data tracking
                $messages[] = $fetching_msg;
            }
            // Always offer help
            $messages[] = $help_msg;
            update_option('h5p_last_info_print', H5PPlugin::VERSION);
        }

        if (!empty($messages)) {
            // Print all messages
            ?>
            <div class="updated"><?php
            foreach ($messages as $message) {
                ?><p><?php print $message; ?></p><?php
            }
            ?></div><?php

            // Print any other messages
            self::print_messages();
        }
    }

    /**
     * Download and install all the H5P content types.
     *
     * @since 1.5.5
     * @param boolean $update_only
     */
    public static function download_h5p_libraries($update_only = false)
    {
        $url = get_option('h5p_update_available_path', null);
        if (!$url) {
            return; // No path to available updates
        }

        // Get instances
        $plugin = H5PPlugin::get_instance();
        $interface = $plugin->get_h5p_instance('interface');
        $validator = $plugin->get_h5p_instance('validator');
        $storage = $plugin->get_h5p_instance('storage');

        // Will try to download in 28 seconds, we shouldn't hold the UI or the PHP
        // thread for any longer. Will have to manually update if server is to slow.
        $phptimeout = ini_get('max_execution_time');
        if ($phptimeout < 30) {
            @set_time_limit(30);
        }

        // Download file
        $_FILES['h5p_file'] = array('name' => 'install.h5p');
        $path = $interface->getUploadedH5pPath();
        $response = wp_safe_remote_get($url, array(
            'stream' => true,
            'filename' => $path,
            'timeout' => 28
        ));

        if (is_wp_error($response)) {
            // Print errors
            $interface->setErrorMessage(__('Unable to download H5P content types.', $plugin->get_plugin_slug()));

            $error_codes = $response->get_error_codes();
            foreach ($error_codes as $error_code) {
                $errors = $response->get_error_messages($error_code);
                $interface->setErrorMessage(implode('<br/>', $errors));
            }
        } elseif (wp_remote_retrieve_response_code($response) != 200) {
            // Print errors
            $interface->setErrorMessage(__('Unable to download H5P content types.', $plugin->get_plugin_slug()));
            $interface->setErrorMessage('HTTP ' . wp_remote_retrieve_response_code($response));
        } else {
            // Install
            if ($validator->isValidPackage(true, $update_only)) {
                $storage->savePackage(null, null, true);
                update_option('h5p_current_update', get_option('h5p_update_available', 0));

                return true;
            } else {
                @unlink($path);
            }
        }

        return false;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_styles_and_scripts()
    {
        $plugin = H5PPlugin::get_instance();
        $plugin->enqueue_styles_and_scripts();
        wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('styles/admin.css', __FILE__), array(),
            H5PPlugin::VERSION);
    }

    /**
     * Load content and add to title for certain pages.
     * Should we have used get_current_screen() ?
     *
     * @since 1.1.0
     * @param string $admin_title
     * @param string $title
     * @return string
     */
    public function alter_title($admin_title, $title)
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

        switch ($page) {
            case 'h5p':
            case 'h5p_new':
                return $this->content->alter_title($page, $admin_title, $title);

            case 'h5p_libraries':
                return $this->library->alter_title($page, $admin_title, $title);
        }

        return $admin_title;
    }

    /**
     * Handle upload of new H5P content file.
     *
     * @since 1.1.0
     * @param array $content
     * @return boolean
     */
    public function handle_upload($content = null, $only_upgrade = null)
    {
        $plugin = H5PPlugin::get_instance();
        $validator = $plugin->get_h5p_instance('validator');
        $interface = $plugin->get_h5p_instance('interface');

        if (current_user_can('disable_h5p_security')) {
            $core = $plugin->get_h5p_instance('core');

            // Make it possible to disable file extension check
            $core->disableFileCheck = (filter_input(INPUT_POST, 'h5p_disable_file_check',
                FILTER_VALIDATE_BOOLEAN) ? true : false);
        }

        // Move so core can validate the file extension.
        rename($_FILES['h5p_file']['tmp_name'], $interface->getUploadedH5pPath());

        $skipContent = ($content === null);
        if ($validator->isValidPackage($skipContent, $only_upgrade) && ($skipContent || $content['title'] !== null)) {
            if (isset($content['id'])) {
                $interface->deleteLibraryUsage($content['id']);
            }
            $storage = $plugin->get_h5p_instance('storage');
            $storage->savePackage($content, null, $skipContent);

            return $storage->contentId;
        }

        // The uploaded file was not a valid H5P package
        @unlink($interface->getUploadedH5pPath());

        return false;
    }

    /**
     * Set error message.
     *
     * @param string $message
     */
    public static function set_error($message)
    {
        $plugin = H5PPlugin::get_instance();
        $interface = $plugin->get_h5p_instance('interface');
        $interface->setErrorMessage($message);
    }

    /**
     * Print messages.
     *
     * @since 1.0.0
     */
    public static function print_messages()
    {
        $plugin = H5PPlugin::get_instance();
        $interface = $plugin->get_h5p_instance('interface');

        foreach (array('updated', 'error') as $type) {
            $messages = $interface->getMessages($type);
            if (!empty($messages)) {
                print '<div class="' . $type . '"><ul>';
                foreach ($messages as $message) {
                    print '<li>' . $message . '</li>';
                }
                print '</ul></div>';
            }
        }
    }

    /**
     * @return string
     */
    public static function getMessagesHTML()
    {
        ob_start();
        static::print_messages();

        return ob_get_clean();
    }

    /**
     * Small helper for simplifying script enqueuing.
     *
     * @since 1.1.0
     * @param string $handle
     * @param string $path
     */
    public static function add_script($handle, $path)
    {
        CustomAssetManager::addScript($handle, $path);

    }

    /**
     * Small helper for simplifying style enqueuing.
     *
     * @since 1.1.0
     * @param string $handle
     * @param string $path
     */
    public static function add_style($handle, $path)
    {
        CustomAssetManager::addStyle($handle, $path);
    }

    /**
     * Create the where part of the results queries.
     *
     * @since 1.2.0
     * @param array $query_args
     * @param int $content_id
     * @param int $user_id
     * @return array
     */
    private function get_results_query_where(&$query_args, $content_id = null, $user_id = null, $filters = array())
    {
        if ($content_id !== null) {
            $where = ' WHERE hr.content_id = %d';
            $query_args[] = $content_id;
        }
        if ($user_id !== null) {
            $where = (isset($where) ? $where . ' AND' : ' WHERE') . ' hr.user_id = %d';
            $query_args[] = $user_id;
        }
        if (isset($where) && isset($filters[0])) {
            $where .= ' AND ' . ($content_id === null ? 'hc.title' : 'u.nickname') . " LIKE '%%%s%%'";
            $query_args[] = $filters[0];
        }

        return (isset($where) ? $where : '');
    }

    /**
     * Find number of results.
     *
     * @since 1.2.0
     * @param int $content_id
     * @param int $user_id
     * @return int
     */
    public function get_results_num($content_id = null, $user_id = null, $filters = array())
    {
        $wpdb = getWPDB();

        $query_args = array();

        return (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM {$wpdb->prefix}h5p_results hr" .
            $this->get_results_query_where($query_args, $content_id, $user_id),
            $query_args
        ));
    }

    /**
     * Handle user results reported by the H5P content.
     *
     * @since 1.2.0
     * @param int $content_id
     * @param int $user_id
     * @return array
     */
    public function get_results(
        $content_id = null,
        $user_id = null,
        $offset = 0,
        $limit = 20,
        $sort_by = 0,
        $sort_dir = 0,
        $filters = array()
    ) {
        $wpdb = getWPDB();

        $extra_fields = '';
        $joins = '';
        $query_args = array();

        // Add extra fields and joins for the different result lists
        if ($content_id === null) {
            $extra_fields .= " hr.content_id, hc.title AS content_title,";
            $joins .= " LEFT JOIN {$wpdb->prefix}h5p_contents hc ON hr.content_id = hc.id";
        }
        if ($user_id === null) {
            $extra_fields .= " hr.user_id, u.nickname AS user_name,";
            $joins .= " LEFT JOIN {$wpdb->base_prefix}users u ON hr.user_id = u.id";
        }

        // Add filters
        $where = $this->get_results_query_where($query_args, $content_id, $user_id, $filters);

        // Order results by the select column and direction
        $order_by = $this->get_order_by($sort_by, $sort_dir, array(
            (object)array(
                'name' => ($content_id === null ? 'hc.title' : 'u.nickname'),
                'reverse' => true
            ),
            'hr.score',
            'hr.max_score',
            'hr.opened',
            'hr.finished'
        ));

        $query_args[] = $offset;
        $query_args[] = $limit;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT hr.id,
              {$extra_fields}
              hr.score,
              hr.max_score,
              hr.opened,
              hr.finished,
              hr.time
        FROM {$wpdb->prefix}h5p_results hr
        {$joins}
        {$where}
        {$order_by}
        LIMIT %d, %d",
            $query_args
        ));
    }

    /**
     * Generate order by part of SQLs.
     *
     * @since 1.2.0
     * @param int $field Index of field to order by
     * @param int $direction Direction to order in. 0=DESC,1=ASC
     * @param array $field Objects containing name and reverse sort option.
     * @return string Order by part of SQL
     */
    public function get_order_by($field, $direction, $fields)
    {
        // Make sure selected sortable field is valid
        if (!isset($fields[$field])) {
            $field = 0; // Fall back to default
        }

        // Find selected sortable field
        $field = $fields[$field];

        if (is_object($field)) {
            // Some fields are reverse sorted by default, e.g. text fields.
            if (!empty($field->reverse)) {
                $direction = !$direction;
            }

            $field = $field->name;
        }

        return 'ORDER BY ' . $field . ' ' . ($direction ? 'ASC' : 'DESC');
    }

    /**
     * Print settings, adds JavaScripts and stylesheets necessary for providing
     * a data view.
     *
     * @since 1.2.0
     * @param string $name of the data view
     * @param string $source URL for data
     * @param array $headers for the table
     */
    public function print_data_view_settings($name, $source, $headers, $filters, $empty, $order)
    {
        // Add JS settings
        $data_views = array();
        $data_views[$name] = array(
            'source' => $source,
            'headers' => $headers,
            'filters' => $filters,
            'order' => $order,
            'l10n' => array(
                'loading' => __('Loading data.'),
                'ajaxFailed' => __('Failed to load data.'),
                'noData' => __("There's no data available that matches your criteria."),
                'currentPage' => __('Page $current of $total'),
                'nextPage' => __('Next page'),
                'previousPage' => __('Previous page'),
                'search' => __('Search'),
                'remove' => __('Remove'),
                'empty' => $empty,
            )
        );
        $plugin = H5PPlugin::get_instance();
        $settings = array('dataViews' => $data_views);
        $plugin->print_settings($settings);

        // Add JS
        H5PPluginAdmin::add_script('jquery', 'scripts/h5p/jquery.js');
        H5PPluginAdmin::add_script('event-dispatcher', 'scripts/h5p/h5p-event-dispatcher.js');
        H5PPluginAdmin::add_script('utils', 'scripts/h5p/h5p-utils.js');
        H5PPluginAdmin::add_script('data-view', 'scripts/h5p/h5p-data-view.js');
        H5PPluginAdmin::add_script('data-views', 'scripts/h5p/h5p-data-views.js');
        H5PPluginAdmin::add_style('admin', 'css/h5p/h5p-admin.css');
    }

    public function get_data_view_settings($name, $source, $headers, $filters, $empty, $order)
    {
        ob_start();
        $this->print_data_view_settings($name, $source, $headers, $filters, $empty, $order);

        return ob_get_clean();
    }

    /**
     * Print results ajax data for either content or user, not both.
     *
     * @since 1.2.0
     * @param int $content_id
     * @param int $user_id
     */
    public function print_results($content_id = null, $user_id = null)
    {
        // Load input vars.
        list($offset, $limit, $sortBy, $sortDir, $filters) = $this->get_data_view_input();

        // Get results
        $results = $this->get_results($content_id, $user_id, $offset, $limit, $sortBy, $sortDir, $filters);

        $datetimeformat = get_option('date_format') . ' ' . get_option('time_format');
        $offset = get_option('gmt_offset') * 3600;

        // Make data more readable for humans
        $rows = array();
        foreach ($results as $result) {
            if ($result->time === '0') {
                $result->time = $result->finished - $result->opened;
            }
            $seconds = ($result->time % 60);
            $time = floor($result->time / 60) . ':' . ($seconds < 10 ? '0' : '') . $seconds;

            $rows[] = array(
                esc_html($content_id === null ? $result->content_title : $result->user_name),
                (int)$result->score,
                (int)$result->max_score,
                date($datetimeformat, $offset + $result->opened),
                date($datetimeformat, $offset + $result->finished),
                $time,
            );
        }

        // Print results
        header('Cache-Control: no-cache');
        header('Content-type: application/json');
        print json_encode(array(
            'num' => $this->get_results_num($content_id, $user_id, $filters),
            'rows' => $rows
        ));
        exit;
    }

    /**
     * Load input vars for data views.
     *
     * @since 1.2.0
     * @return array offset, limit, sort by, sort direction, filters
     */
    public function get_data_view_input()
    {
        $offset = filter_input(INPUT_GET, 'offset', FILTER_SANITIZE_NUMBER_INT);
        $limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
        $sortBy = filter_input(INPUT_GET, 'sortBy', FILTER_SANITIZE_NUMBER_INT);
        $sortDir = filter_input(INPUT_GET, 'sortDir', FILTER_SANITIZE_NUMBER_INT);
        $filters = filter_input(INPUT_GET, 'filters', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $facets = filter_input(INPUT_GET, 'facets', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $limit = (!$limit ? 20 : (int)$limit);
        if ($limit > 100) {
            $limit = 100; // Prevent wrong usage.
        }

        // Use default if not set or invalid
        return array(
            (!$offset ? 0 : (int)$offset),
            $limit,
            (!$sortBy ? 0 : (int)$sortBy),
            (!$sortDir ? 0 : (int)$sortDir),
            $filters,
            $facets
        );

    }

    /**
     * Remove user data and results when user is removed.
     *
     * @since 1.5.0
     */
    public function deleted_user($id)
    {
        $wpdb = getWPDB();

        // Remove user scores/results
        $wpdb->delete('h5p_results', array('user_id' => $id), array('%d'));

        // Remove contents user/usage data
        $wpdb->delete('h5p_contents_user_data', array('user_id' => $id), array('%d'));
    }
}
