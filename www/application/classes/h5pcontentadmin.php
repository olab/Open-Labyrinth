<?php

/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
class H5PContentAdmin
{

    const PATH_SCRIPTS = '/scripts/h5p/editor/';
    const PATH_STYLES = '/css/h5p/editor/';
    /**
     * @since 1.1.0
     */
    private $plugin_slug = null;

    /**
     * Editor instance
     *
     * @since 1.1.0
     * @var \H5peditor
     */
    protected static $h5peditor = null;

    /**
     * Keep track of the current content.
     *
     * @since 1.1.0
     */
    public $content = null;

    /**
     * Are we inserting H5P content on this page?
     *
     * @since 1.2.0
     */
    private $insertButton = false;

    /**
     * Initialize content admin and editor
     *
     * @since 1.1.0
     * @param string $plugin_slug
     */
    public function __construct($plugin_slug)
    {
        getWPDB();

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
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Find content title
        $show = ($page === 'h5p' && ($task === 'show' || $task === 'results'));
        $edit = ($page === 'h5p_new');
        if (($show || $edit) && $id !== null) {
            if ($this->content === null) {
                $this->load_content($id);
            }

            if (!is_string($this->content)) {
                if ($edit) {
                    $admin_title = str_replace($title, 'Edit', $admin_title);
                }
                $admin_title = esc_html($this->content['title']) . ' &lsaquo; ' . $admin_title;
            }
        }

        return $admin_title;
    }

    /**
     * Will load and set the content variable.
     * Also loads tags related to content.
     *
     * @since 1.6.0
     * @param int $id
     */
    public function load_content($id)
    {
        $wpdb = getWPDB();
        $plugin = H5PPlugin::get_instance();

        $this->content = $plugin->get_content($id);
        if (!is_string($this->content)) {
            $tags = $wpdb->get_results($wpdb->prepare(
                "SELECT t.name
             FROM h5p_contents_tags ct
             JOIN h5p_tags t ON ct.tag_id = t.id
            WHERE ct.content_id = %d",
                $id
            ));
            $this->content['tags'] = '';
            foreach ($tags as $tag) {
                $this->content['tags'] .= ($this->content['tags'] !== '' ? ', ' : '') . $tag->name;
            }
        }
    }

    /**
     * Save tags for given content.
     * Removes unused tags.
     *
     * @param int $content_id
     * @param string $tags
     */
    public function set_content_tags($content_id, $tags = '')
    {
        global $wpdb;
        $tag_ids = array();

        // Create array and trim input
        $tags = explode(',', $tags);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag === '') {
                continue;
            }

            // Find out if tag exists and is linked to content
            $exists = $wpdb->get_row($wpdb->prepare(
                "SELECT t.id, ct.content_id
             FROM {$wpdb->prefix}h5p_tags t
        LEFT JOIN {$wpdb->prefix}h5p_contents_tags ct ON ct.content_id = %d AND ct.tag_id = t.id
            WHERE t.name = %s",
                $content_id, $tag
            ));

            if (empty($exists)) {
                // Create tag
                $exists = array('name' => $tag);
                $wpdb->insert("{$wpdb->prefix}h5p_tags", $exists, array('%s'));
                $exists = (object)$exists;
                $exists->id = $wpdb->insert_id;
            }
            $tag_ids[] = $exists->id;

            if (empty($exists->content_id)) {
                // Connect to content
                $wpdb->insert("{$wpdb->prefix}h5p_contents_tags",
                    array('content_id' => $content_id, 'tag_id' => $exists->id), array('%d', '%d'));
            }
        }

        // Remove tags that are not connected to content (old tags)
        $and_where = empty($tag_ids) ? '' : " AND tag_id NOT IN (" . implode(',', $tag_ids) . ")";
        $wpdb->query("DELETE FROM {$wpdb->prefix}h5p_contents_tags WHERE content_id = {$content_id}{$and_where}");

        // Maintain tags table by remove unused tags
        $wpdb->query("DELETE t.* FROM {$wpdb->prefix}h5p_tags t LEFT JOIN {$wpdb->prefix}h5p_contents_tags ct ON t.id = ct.tag_id WHERE ct.content_id IS NULL");
    }

    /**
     * Check to see if the installation has any libraries.
     *
     * @return bool
     */
    public function has_libraries()
    {
        $query = DB_SQL::select()
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->from('h5p_libraries')
            ->where('runnable', '=', 1);

        return ((int)$query->query()->fetch(0)['counter'] > 0);
    }

    /**
     * Create new content.
     *
     * @since 1.1.0
     * @param array $content
     * @return mixed
     */
    public function handle_content_creation($content)
    {
        $plugin = H5PPlugin::get_instance();
        $core = $plugin->get_h5p_instance('core');

        // Keep track of the old library and params
        $oldLibrary = null;
        $oldParams = null;
        if ($content !== null) {
            $oldLibrary = $content['library'];
            $oldParams = json_decode($content['params']);
        } else {
            $content = array(
                'disable' => H5PCore::DISABLE_NONE
            );
        }

        // Get library
        $content['library'] = $core->libraryFromString($this->get_input('library'));
        if (!$content['library']) {
            $core->h5pF->setErrorMessage(__('Invalid library.'));

            return false;
        }

        // Check if library exists.
        $content['library']['libraryId'] = $core->h5pF->getLibraryId($content['library']['machineName'],
            $content['library']['majorVersion'], $content['library']['minorVersion']);
        if (!$content['library']['libraryId']) {
            $core->h5pF->setErrorMessage(__('No such library.'));

            return false;
        }

        // Get title
        $content['title'] = $this->get_input_title();
        if ($content['title'] === null) {
            return false;
        }

        // Check parameters
        $content['params'] = $this->get_input('parameters');
        if ($content['params'] === null) {
            return false;
        }
        $params = json_decode($content['params']);
        if ($params === null) {
            $core->h5pF->setErrorMessage(__('Invalid parameters.'));

            return false;
        }

        // Set disabled features
        $this->get_disabled_content_features($core, $content);

        // Save new content
        $content['id'] = $core->saveContent($content);

        // Create content directory
        $editor = $this->get_h5peditor_instance();
        if (!$editor->createDirectories($content['id'])) {
            $core->h5pF->setErrorMessage(__('Unable to create content directory.'));
            // Remove content.
            $core->h5pF->deleteContentData($content['id']);

            return false;
        }

        // Move images and find all content dependencies
        $editor->processParameters($content['id'], $content['library'], $params, $oldLibrary, $oldParams);

        return $content['id'];
    }

    /**
     * Extract disabled content features from input post.
     *
     * @since 1.2.0
     * @param H5PCore $core
     * @param int $current
     * @return int
     */
    public function get_disabled_content_features($core, &$content)
    {
        $set = array(
            'frame' => filter_input(INPUT_POST, 'frame', FILTER_VALIDATE_BOOLEAN),
            'download' => filter_input(INPUT_POST, 'download', FILTER_VALIDATE_BOOLEAN),
            'embed' => filter_input(INPUT_POST, 'embed', FILTER_VALIDATE_BOOLEAN),
            'copyright' => filter_input(INPUT_POST, 'copyright', FILTER_VALIDATE_BOOLEAN),
        );
        $content['disable'] = $core->getDisable($set, $content['disable']);
    }

    /**
     * Get input post data field.
     *
     * @since 1.1.0
     * @param string $field The field to get data for.
     * @param string $default Optional default return.
     * @return string
     */
    private function get_input($field, $default = null)
    {
        // Get field
        $value = filter_input(INPUT_POST, $field);
        if ($value === null) {
            if ($default === null) {
                // No default, set error message.
                H5PPluginAdmin::set_error(sprintf(__('Missing %s.'), $field));
            }

            return $default;
        }

        return $value;
    }

    /**
     * Get input post data field title. Validates.
     *
     * @since 1.1.0
     * @return string
     */
    public function get_input_title()
    {
        $title = $this->get_input('title');
        if ($title === null) {
            return null;
        }

        // Trim title and check length
        $trimmed_title = trim($title);
        if ($trimmed_title === '') {
            H5PPluginAdmin::set_error(sprintf(__('Missing title.')));

            return null;
        }

        if (strlen($trimmed_title) > 255) {
            H5PPluginAdmin::set_error(__('Title is too long. Must be 256 letters or shorter.'));

            return null;
        }

        return $trimmed_title;
    }

    /**
     * Add custom media button for selecting H5P content.
     *
     * @since 1.1.0
     * @return string
     */
    public function add_insert_button()
    {
        $this->insertButton = true;

        $insert_method = get_option('h5p_insert_method', 'id');
        $button_content =
            '<a href="#" id="add-h5p" class="button" title="' . __('Insert H5P Content') . '" data-method="' . $insert_method . '">' .
            __('Add H5P') .
            '</a>';

        return $button_content;
    }

    /**
     * Adds scripts and settings for allowing selection of H5P contents when
     * inserting into pages, posts etc.
     *
     * @since 1.2.0
     */
    public function print_insert_content_scripts()
    {
        if (!$this->insertButton) {
            return;
        }

        $plugin_admin = H5PPluginAdmin::get_instance();
        $plugin_admin->print_data_view_settings(
            'h5p-insert-content',
            admin_url('admin-ajax.php?action=h5p_insert_content'),
            array(
                (object)array(
                    'text' => __('Title'),
                    'sortable' => true
                ),
                (object)array(
                    'text' => __('Content type'),
                    'sortable' => true,
                    'facet' => true
                ),
                (object)array(
                    'text' => __('Tags'),
                    'sortable' => false,
                    'facet' => true
                ),
                (object)array(
                    'text' => __('Last modified'),
                    'sortable' => true
                ),
                (object)array(
                    'class' => 'h5p-insert-link'
                )
            ),
            array(true),
            __("No H5P content available. You must upload or create new content."),
            (object)array(
                'by' => 3,
                'dir' => 0
            )
        );
    }

    /**
     * Log when content is inserted
     *
     * @since 1.6.0
     */
    public function ajax_inserted()
    {
        global $wpdb;

        $content_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$content_id) {
            return;
        }

        // Get content info for log
        $content = $wpdb->get_row($wpdb->prepare("
        SELECT c.title, l.name, l.major_version, l.minor_version
          FROM {$wpdb->prefix}h5p_contents c
          JOIN {$wpdb->prefix}h5p_libraries l ON l.id = c.library_id
         WHERE c.id = %d
        ", $content_id));

        // Log view
        new H5P_Event('content', 'shortcode insert',
            $content_id, $content->title,
            $content->name, $content->major_version . '.' . $content->minor_version);
    }

    /**
     * Returns the instance of the h5p editor library.
     *
     * @since 1.1.0
     * @return \H5peditor
     */
    public function get_h5peditor_instance()
    {
        if (self::$h5peditor === null) {
            $upload_dir = wp_upload_dir();
            $plugin = H5PPlugin::get_instance();
            self::$h5peditor = new H5peditor(
                $plugin->get_h5p_instance('core'),
                new H5PEditorOpenLabyrinthStorage(),
                '',
                $plugin->get_h5p_path()
            );
        }

        return self::$h5peditor;
    }

    /**
     * Add assets and JavaScript settings for the editor.
     *
     * @since 1.1.0
     * @param int $id optional content identifier
     */
    public function add_editor_assets($id = null)
    {
        $plugin = H5PPlugin::get_instance();
        $plugin->add_core_assets();

        // Make sure the h5p classes are loaded
        $plugin->get_h5p_instance('core');
        $this->get_h5peditor_instance();

        // Add JavaScript settings
        $settings = $plugin->get_settings();
        $cache_buster = '?ver=' . H5PPlugin::VERSION;

        // Use jQuery and styles from core.
        $assets = array(
            'css' => $settings['core']['styles'],
            'js' => $settings['core']['scripts']
        );

        // Add editor styles
        foreach (H5peditor::$styles as $style) {
            $assets['css'][] = self::PATH_STYLES . str_replace('styles/', '', $style) . $cache_buster;
        }

        // Add editor JavaScript
        foreach (H5peditor::$scripts as $script) {
            // We do not want the creator of the iframe inside the iframe
            if ($script !== 'scripts/h5peditor-editor.js') {
                $assets['js'][] = self::PATH_SCRIPTS . $script . $cache_buster;
            }
        }

        // Add JavaScript with library framework integration (editor part)
        H5PPluginAdmin::add_script('editor-editor', self::PATH_SCRIPTS . 'scripts/h5peditor-editor.js');
        H5PPluginAdmin::add_script('editor', self::PATH_SCRIPTS . 'scripts/h5p-editor.js');

        // Add translation
        //$language = $plugin->get_language();
        $language_script = self::PATH_SCRIPTS . 'language/en.js';
        //if (!file_exists(plugin_dir_path(__FILE__) . '../' . $language_script)) {
        //    $language_script = 'h5p-editor-php-library/language/en.js';
        //}
        H5PPluginAdmin::add_script('language', $language_script);

        // Add JavaScript settings
        $content_validator = $plugin->get_h5p_instance('contentvalidator');
        $settings['editor'] = array(
            'filesPath' => $plugin->get_h5p_url() . '/editor',
            'fileIcon' => array(
                'path' => '/images/h5p/binary-file.png',
                'width' => 50,
                'height' => 50,
            ),
            'ajaxPath' => admin_url('/h5p/ajax_'),
            // admin_url('admin-ajax.php?token=' . wp_create_nonce('h5p_editor_ajax') . '&action=h5p_')
            //'libraryUrl' => 'h5p/h5p-editor-php-library/h5peditor.class.php',
            'libraryUrl' => '/scripts/h5p/editor',
            'copyrightSemantics' => $content_validator->getCopyrightSemantics(),
            'assets' => $assets,
            'deleteMessage' => __('Are you sure you wish to delete this content?')
        );

        if ($id !== null) {
            $settings['editor']['nodeVersionId'] = $id;
        }

        $plugin->print_settings($settings);
    }

    public function get_editor_assets($id = null)
    {
        ob_start();
        $this->add_editor_assets($id);

        return ob_get_clean();
    }

    /**
     * Get library details through AJAX.
     *
     * @since 1.0.0
     */
    public function ajax_libraries()
    {
        $editor = $this->get_h5peditor_instance();

        $name = filter_input(INPUT_GET, 'machineName', FILTER_SANITIZE_STRING);
        $major_version = filter_input(INPUT_GET, 'majorVersion', FILTER_SANITIZE_NUMBER_INT);
        $minor_version = filter_input(INPUT_GET, 'minorVersion', FILTER_SANITIZE_NUMBER_INT);

        header('Cache-Control: no-cache');
        header('Content-type: application/json');

        if ($name) {
            $plugin = H5PPlugin::get_instance();
            print $editor->getLibraryData($name, $major_version, $minor_version, $plugin->get_language(),
                $plugin->get_h5p_path());

            // Log library load
            /*new H5P_Event('library', null,
                null, null,
                $name, $major_version . '.' . $minor_version);*/
        } else {
            print $editor->getLibraries();
        }

        exit;
    }
}
