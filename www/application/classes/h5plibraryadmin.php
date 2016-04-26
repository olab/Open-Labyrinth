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
 * @package H5PPluginAdmin
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
    public $library = null;

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
                        $plugin = H5PPlugin::get_instance();
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
        $wpdb = getWPDB();

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
            H5PPluginAdmin::set_error(sprintf(__('Cannot find library with id: %d.'), $id));
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
                    $plugin_admin = H5PPluginAdmin::get_instance();
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
                    H5PPluginAdmin::set_error(__($errorMessage, $this->plugin_slug));
                }

                return;
            } elseif ($task === null) {
                // No files, we must be trying to auto download & update

                check_admin_referer('h5p_update', 'download_update'); // Verify form
                if (!H5PPluginAdmin::download_h5p_libraries(true)) {
                    // Ignore update if it failed, user must manually update.
                    update_option('h5p_current_update', get_option('h5p_update_available', 0));
                    H5PPluginAdmin::set_error(
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
        $plugin = H5PPlugin::get_instance();
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

        $plugin = H5PPlugin::get_instance();
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
