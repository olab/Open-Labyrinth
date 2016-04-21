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
 * H5P Library Admin class
 *
 * @package H5P_Plugin_Admin
 * @author Joubel <contact@joubel.com>
 */
class H5PLibraryAdmin
{

    /**
     * @since 1.1.0
     */
    private $plugin_slug = null;

    /**
     * Keep track of the current library.
     *
     * @since 1.1.0
     */
    private $library = null;

    /**
     * Initialize library admin
     *
     * @since 1.1.0
     * @param string $plugin_slug
     */
    public function __construct($plugin_slug)
    {
        $this->plugin_slug = $plugin_slug;
    }

    /**
     * Load content and alter page title for certain pages.
     *
     * @since 1.1.0
     * @param string $page
     * @param string $admin_title
     * @param string $title
     * @return string
     */
    public function alter_title($page, $admin_title, $title)
    {
        $task = filter_input(INPUT_GET, 'task', FILTER_SANITIZE_STRING);

        // Find library title
        $show = ($task === 'show');
        $delete = ($task === 'delete');
        $upgrade = ($task === 'upgrade');
        if ($show || $delete || $upgrade) {
            $library = $this->get_library();
            if ($library) {
                if ($delete) {
                    $admin_title = str_replace($title, __('Delete', $this->plugin_slug), $admin_title);
                } else {
                    if ($upgrade) {
                        $admin_title = str_replace($title, __('Content Upgrade', $this->plugin_slug), $admin_title);
                        $plugin = H5P_Plugin::get_instance();
                        $plugin->get_h5p_instance('core'); // Load core
                    }
                }
                $admin_title = esc_html($library->title) . ($upgrade ? ' (' . H5PCore::libraryVersion($library) . ')' : '') . ' &lsaquo; ' . $admin_title;
            }
        }

        return $admin_title;
    }

    /**
     * Load library
     *
     * @since 1.1.0
     * @param int $id optional
     */
    public function get_library($id = null)
    {
        global $wpdb;

        if ($this->library !== null) {
            return $this->library; // Return the current loaded library.
        }

        if ($id === null) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        }

        // Try to find content with $id.
        $this->library = $wpdb->get_row($wpdb->prepare(
            "SELECT id, title, name, major_version, minor_version, patch_version, runnable, fullscreen
          FROM {$wpdb->prefix}h5p_libraries
          WHERE id = %d",
            $id
        )
        );
        if (!$this->library) {
            H5P_Plugin_Admin::set_error(sprintf(__('Cannot find library with id: %d.'), $id));
        }

        return $this->library;
    }

    /**
     * Handles upload of H5P libraries.
     *
     * @since 1.1.0
     */
    public function process_libraries()
    {
        $post = ($_SERVER['REQUEST_METHOD'] === 'POST');
        $task = filter_input(INPUT_GET, 'task');

        if ($post) {
            // A form as has been submitted

            if (isset($_FILES['h5p_file'])) {
                // If file upload, we're uploading libraries

                if ($_FILES['h5p_file']['error'] === 0) {
                    // No upload errors, try to install package
                    check_admin_referer('h5p_library', 'lets_upgrade_that'); // Verify form
                    $plugin_admin = H5P_Plugin_Admin::get_instance();
                    $plugin_admin->handle_upload(null, filter_input(INPUT_POST, 'h5p_upgrade_only') ? true : false);
                } else {
                    $phpFileUploadErrors = array(
                        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                        3 => 'The uploaded file was only partially uploaded',
                        4 => 'No file was uploaded',
                        6 => 'Missing a temporary folder',
                        7 => 'Failed to write file to disk.',
                        8 => 'A PHP extension stopped the file upload.',
                    );

                    $errorMessage = $phpFileUploadErrors[$_FILES['h5p_file']['error']];
                    H5P_Plugin_Admin::set_error(__($errorMessage, $this->plugin_slug));
                }

                return;
            } elseif ($task === null) {
                // No files, we must be trying to auto download & update

                check_admin_referer('h5p_update', 'download_update'); // Verify form
                if (!H5P_Plugin_Admin::download_h5p_libraries(true)) {
                    // Ignore update if it failed, user must manually update.
                    update_option('h5p_current_update', get_option('h5p_update_available', 0));
                    H5P_Plugin_Admin::set_error(
                        vsprintf(
                            wp_kses(
                                __('Unfortunately, we were unable to update your installed content types. You must manually download the update from <a href="%s" target="_blank">H5P.org</a>, and then upload it through the <em>Upload Libraries</em> section. If you need futher assistance, please file a <a href="%s" target="_blank">support request</a> or check out our <a href="%s" target="_blank">forum</a>.',
                                    $this->plugin_slug),
                                array('a' => array('href' => array(), 'target' => array()), 'em' => array())
                            ),
                            array(
                                esc_url('https://h5p.org/update-all-content-types'),
                                esc_url('https://wordpress.org/support/plugin/h5p'),
                                esc_url('https://h5p.org/forum')
                            )
                        ));
                }
            }
        }

        if ($task === 'delete') {
            $library = $this->get_library();
            if (!$library) {
                return;
            }

            $plugin = H5P_Plugin::get_instance();
            $interface = $plugin->get_h5p_instance('interface');

            // Check if this library can be deleted
            $usage = $interface->getLibraryUsage($library->id, $interface->getNumNotFiltered() ? true : false);
            if ($usage['content'] !== 0 || $usage['libraries'] !== 0) {
                H5P_Plugin_Admin::set_error(__('This Library is used by content or other libraries and can therefore not be deleted.',
                    $this->plugin_slug));

                return; // Nope
            }

            if ($post) {
                check_admin_referer('h5p_library', 'lets_delete_this'); // Verify delete form
                $interface->deleteLibrary($this->library);
                wp_safe_redirect(admin_url('admin.php?page=h5p_libraries'));
            }
        }
    }

    /**
     * Display a list of all h5p content libraries.
     *
     * @since 1.1.0
     */
    private function display_content_upgrades($library)
    {
        global $wpdb;

        $plugin = H5P_Plugin::get_instance();
        $core = $plugin->get_h5p_instance('core');
        $interface = $plugin->get_h5p_instance('interface');

        $versions = $wpdb->get_results($wpdb->prepare(
            "SELECT hl2.id, hl2.name, hl2.title, hl2.major_version, hl2.minor_version, hl2.patch_version
          FROM {$wpdb->prefix}h5p_libraries hl1
          JOIN {$wpdb->prefix}h5p_libraries hl2
            ON hl2.name = hl1.name
          WHERE hl1.id = %d
          ORDER BY hl2.title ASC, hl2.major_version ASC, hl2.minor_version ASC",
            $library->id
        ));

        foreach ($versions as $version) {
            if ($version->id === $library->id) {
                $upgrades = $core->getUpgrades($version, $versions);
                break;
            }
        }

        if (count($versions) < 2) {
            H5P_Plugin_Admin::set_error(__('There are no available upgrades for this library.', $this->plugin_slug));

            return null;
        }

        // Get num of contents that can be upgraded
        $contents = $interface->getNumContent($library->id);
        if (!$contents) {
            H5P_Plugin_Admin::set_error(__("There's no content instances to upgrade.", $this->plugin_slug));

            return null;
        }

        $contents_plural = sprintf(_n('1 content', '%d contents', $contents, $this->plugin_slug), $contents);

        // Add JavaScript settings
        $return = filter_input(INPUT_GET, 'destination');
        $settings = array(
            'containerSelector' => '#h5p-admin-container',
            'libraryInfo' => array(
                'message' => sprintf(__('You are about to upgrade %s. Please select upgrade version.',
                    $this->plugin_slug), $contents_plural),
                'inProgress' => __('Upgrading to %ver...', $this->plugin_slug),
                'error' => __('An error occurred while processing parameters:', $this->plugin_slug),
                'errorData' => __('Could not load data for library %lib.', $this->plugin_slug),
                'errorContent' => __('Could not upgrade content %id:', $this->plugin_slug),
                'errorScript' => __('Could not load upgrades script for %lib.', $this->plugin_slug),
                'errorParamsBroken' => __('Parameters are broken.', $this->plugin_slug),
                'done' => sprintf(__('You have successfully upgraded %s.', $this->plugin_slug),
                        $contents_plural) . ($return ? '<br/><a href="' . $return . '">' . __('Return',
                            $this->plugin_slug) . '</a>' : ''),
                'library' => array(
                    'name' => $library->name,
                    'version' => $library->major_version . '.' . $library->minor_version,
                ),
                'libraryBaseUrl' => admin_url('admin-ajax.php?action=h5p_content_upgrade_library&library='),
                'scriptBaseUrl' => plugins_url('h5p/h5p-php-library/js'),
                'buster' => '?ver=' . H5P_Plugin::VERSION,
                'versions' => $upgrades,
                'contents' => $contents,
                'buttonLabel' => __('Upgrade', $this->plugin_slug),
                'infoUrl' => admin_url('admin-ajax.php?action=h5p_content_upgrade_progress&id=' . $library->id),
                'total' => $contents,
                'token' => wp_create_nonce('h5p_content_upgrade')
            )
        );

        $this->add_admin_assets();
        H5P_Plugin_Admin::add_script('version', 'h5p-php-library/js/h5p-version.js');
        H5P_Plugin_Admin::add_script('content-upgrade', 'h5p-php-library/js/h5p-content-upgrade.js');

        return $settings;
    }

    /**
     * Helps rebuild all content caches.
     *
     * @since 1.1.0
     */
    public function ajax_rebuild_cache()
    {
        global $wpdb;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit; // POST is required
        }

        $plugin = H5P_Plugin::get_instance();
        $core = $plugin->get_h5p_instance('core');

        // Do as many as we can in five seconds.
        $start = microtime(true);

        $contents = $wpdb->get_results(
            "SELECT id
          FROM {$wpdb->prefix}h5p_contents
          WHERE filtered = ''"
        );

        $done = 0;
        foreach ($contents as $content) {
            $content = $core->loadContent($content->id);
            $core->filterParameters($content);
            $done++;

            if ((microtime(true) - $start) > 5) {
                break;
            }
        }

        print (count($contents) - $done);
        exit;
    }

    /**
     * Add generic admin interface assets.
     *
     * @since 1.1.0
     */
    private function add_admin_assets()
    {
        foreach (H5PCore::$adminScripts as $script) {
            H5P_Plugin_Admin::add_script('admin-' . $script, 'h5p-php-library/' . $script);
        }
        H5P_Plugin_Admin::add_style('h5p', 'h5p-php-library/styles/h5p.css');
        H5P_Plugin_Admin::add_style('admin', 'h5p-php-library/styles/h5p-admin.css');
    }

    /**
     * AJAX processing for content upgrade script.
     */
    public function ajax_upgrade_progress()
    {
        global $wpdb;
        header('Cache-Control: no-cache');

        if (!wp_verify_nonce(filter_input(INPUT_POST, 'token'), 'h5p_content_upgrade')) {
            print __('Error, invalid security token!', $this->plugin_slug);
            exit;
        }

        $library_id = filter_input(INPUT_GET, 'id');
        if (!$library_id) {
            print __('Error, missing library!', $this->plugin_slug);
            exit;
        }

        // Get the library we're upgrading to
        $to_library = $wpdb->get_row($wpdb->prepare(
            "SELECT id, name, major_version, minor_version
          FROM {$wpdb->prefix}h5p_libraries
          WHERE id = %d",
            filter_input(INPUT_POST, 'libraryId')
        ));
        if (!$to_library) {
            print __('Error, invalid library!', $this->plugin_slug);
            exit;
        }

        // Prepare response
        $out = new stdClass();
        $out->params = array();
        $out->token = wp_create_nonce('h5p_content_upgrade');

        // Get updated params
        $params = filter_input(INPUT_POST, 'params');
        if ($params !== null) {
            // Update params.
            $params = json_decode($params);
            foreach ($params as $id => $param) {
                $wpdb->update(
                    $wpdb->prefix . 'h5p_contents',
                    array(
                        'updated_at' => current_time('mysql', 1),
                        'parameters' => $param,
                        'library_id' => $to_library->id,
                        'filtered' => ''
                    ),
                    array(
                        'id' => $id
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d',
                        '%s'
                    ),
                    array(
                        '%d'
                    )
                );

                // Log content upgrade successful
                new H5P_Event('content', 'upgrade',
                    $id,
                    $wpdb->get_var($wpdb->prepare("SELECT title FROM {$wpdb->prefix}h5p_contents WHERE id = %d", $id)),
                    $to_library->name, $to_library->major_version . '.' . $to_library->minor_version);
            }
        }

        // Prepare our interface
        $plugin = H5P_Plugin::get_instance();
        $interface = $plugin->get_h5p_instance('interface');

        // Get number of contents for this library
        $out->left = $interface->getNumContent($library_id);

        if ($out->left) {
            // Find the 10 first contents using library and add to params
            $contents = $wpdb->get_results($wpdb->prepare(
                "SELECT id, parameters
            FROM {$wpdb->prefix}h5p_contents
            WHERE library_id = %d
            LIMIT 40",
                $library_id
            ));
            foreach ($contents as $content) {
                $out->params[$content->id] = $content->parameters;
            }
        }

        header('Content-type: application/json');
        print json_encode($out);
        exit;
    }

    /**
     * AJAX loading of libraries for content upgrade script.
     *
     * @since 1.1.0
     * @param string $name
     * @param int $major
     * @param int $minor
     */
    public function ajax_upgrade_library()
    {
        header('Cache-Control: no-cache');

        $library_string = filter_input(INPUT_GET, 'library');
        if (!$library_string) {
            print __('Error, missing library!', $this->plugin_slug);
            exit;
        }

        $library_parts = explode('/', $library_string);
        if (count($library_parts) !== 4) {
            print __('Error, invalid library!', $this->plugin_slug);
            exit;
        }

        $library = (object)array(
            'name' => $library_parts[1],
            'version' => (object)array(
                'major' => $library_parts[2],
                'minor' => $library_parts[3]
            )
        );

        $plugin = H5P_Plugin::get_instance();
        $core = $plugin->get_h5p_instance('core');

        $library->semantics = $core->loadLibrarySemantics($library->name, $library->version->major,
            $library->version->minor);
        if ($library->semantics === null) {
            print __('Error, could not library semantics!', $this->plugin_slug);
            exit;
        }

        // TODO: Library development mode
//    if ($core->development_mode & H5PDevelopment::MODE_LIBRARY) {
//      $dev_lib = $core->h5pD->getLibrary($library->name, $library->version->major, $library->version->minor);
//    }

        if (isset($dev_lib)) {
            $upgrades_script_path = $upgrades_script_url = $dev_lib['path'] . '/upgrades.js';
        } else {
            $suffix = '/libraries/' . $library->name . '-' . $library->version->major . '.' . $library->version->minor . '/upgrades.js';
            $upgrades_script_path = $plugin->get_h5p_path() . $suffix;
            $upgrades_script_url = $plugin->get_h5p_url() . $suffix;
        }

        if (file_exists($upgrades_script_path)) {
            $library->upgradesScript = $upgrades_script_url;
        }

        header('Content-type: application/json');
        print json_encode($library);
        exit;
    }

}
