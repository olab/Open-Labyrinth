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
defined('SYSPATH') or die('No direct script access.');

class Controller_H5P extends Controller_Base
{

    public static function loadH5PClasses()
    {
        require_once MODPATH . 'h5p-php-library/h5p-file-storage.interface.php';
        require_once MODPATH . 'h5p-php-library/h5p.classes.php';
        require_once MODPATH . 'h5p-php-library/h5p-default-storage.class.php';
        require_once MODPATH . 'h5p-php-library/h5p-development.class.php';
        require_once MODPATH . 'h5p-php-library/h5p-event-base.class.php';
        require_once 'application/classes/class-h5p-content-admin.php';
        require_once 'application/classes/class-h5p-content-query.php';
        require_once 'application/classes/class-h5p-library-admin.php';
        require_once 'application/classes/class-h5p-plugin.php';
        require_once 'application/classes/class-h5p-plugin-admin.php';
        require_once 'application/classes/class-h5p-wordpress.php';
        require_once 'application/classes/wp-db.php';
    }

    public static function loadH5PEditorClasses()
    {
        require_once MODPATH . 'h5p-editor-php-library/h5peditor-storage.interface.php';
        require_once MODPATH . 'h5p-editor-php-library/h5peditor.class.php';
        require_once MODPATH . 'h5p-editor-php-library/h5peditor-file.class.php';
        require_once 'application/classes/class-h5p-editor-wordpress-storage.php';
    }

    public function before()
    {
        parent::before();
        static::loadH5PClasses();
        Breadcrumbs::add(Breadcrumb::factory()->set_title(__('H5P manager'))->set_url(URL::base() . 'h5p/index'));
    }

    public function action_addContent()
    {
        static::loadH5PEditorClasses();
        $this->templateData['scripts_stack'][] = '/scripts/h5p/jquery.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-event-dispatcher.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-utils.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-data-view.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-data-views.js';

        $this->templateData['styles_stack'][] = '/css/h5p/h5p.css';
        $this->templateData['styles_stack'][] = '/css/h5p/h5p-admin.css';

        $id = $this->request->param('id');

        $content_admin = new H5PContentAdmin('H5P');
        //$content_admin->load_content($id);
        $content = $content_admin->content;

        $contentExists = ($content !== null);

        $plugin = H5P_Plugin::get_instance();
        $core = $plugin->get_h5p_instance('core');

        // Prepare form
        $title = $contentExists ? $content['title'] : '';
        $library = $contentExists ? H5PCore::libraryToString($content['library']) : 0;
        $parameters = $contentExists ? $core->filterParameters($content) : '{}';

        // Determine upload or create
        if (!$contentExists && !$content_admin->has_libraries()) {
            $upload = true;
        } else {
            //$upload = (filter_input(INPUT_POST, 'action') === 'upload');
            $upload = false;
        }

        // Filter/escape parameters, double escape that is...
        $parameters = esc_html($parameters, ENT_QUOTES, false, true);

        //include_once('views/new-content.php');
        $settings = $content_admin->get_editor_assets($contentExists ? $content['id'] : null);
        H5P_Plugin_Admin::add_script('jquery', '/scripts/h5p/jquery.js');
        H5P_Plugin_Admin::add_script('disable', '/scripts/h5p/disable.js');
        H5P_Plugin_Admin::add_script('toggle', '/scripts/h5p/editor/scripts/h5p-toggle.js');

        $this->loadAssets();

        $this->templateData['center'] = View::factory('h5p/contentAdd')
            ->set('templateData', $this->templateData)
            ->set('content', $content)
            ->set('settings', $settings)
            ->set('title', $title)
            ->set('library', $library)
            ->set('upload', $upload)
            ->set('parameters', $parameters);
        $this->template->set('templateData', $this->templateData);
    }

    /**
     * ajax request
     */
    public function action_addContentSubmit()
    {
        static::loadH5PEditorClasses();
        $plugin = H5P_Plugin::get_instance();

        // Check if we have any content or errors loading content
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $content_admin = new H5PContentAdmin('H5P');
        if ($id) {
            $content_admin->load_content($id);
            if (is_string($content_admin->content)) {
                //H5P_Plugin_Admin::set_error($content_admin->content);
                Session::instance()->set('error_message', $content_admin->content);
                $content_admin->content = null;
                Request::initial()->redirect(URL::base() . 'h5p/addContent');
            }
        }

        //if ($content_admin->content !== null) {
        //    // We have existing content
//
        //    if (/*!$this->current_user_can_edit($this->content)*/
        //    false
        //    ) {
        //        // The user isn't allowed to edit this content
        //        H5P_Plugin_Admin::set_error(__('You are not allowed to edit this content.'));
//
        //        return;
        //    }
//
        //    // Check if we're deleting content
        //    $delete = filter_input(INPUT_GET, 'delete');
        //    if ($delete) {
        //        $this->deleteContent($plugin, $content_admin);
        //        //TODO: redirect
        //    }
        //}

        // Check if we're uploading or creating content
        $action = filter_input(INPUT_POST, 'action', FILTER_VALIDATE_REGEXP,
            array('options' => array('regexp' => '/^(upload|create)$/')));
        if ($action) {
            $core = $plugin->get_h5p_instance('core'); // Make sure core is loaded

            $result = false;
            if ($action === 'create') {
                // Handle creation of new content.
                $result = $content_admin->handle_content_creation($content_admin->content);
            } elseif (isset($_FILES['h5p_file']) && $_FILES['h5p_file']['error'] === 0) {
                // Create new content if none exists
                $content = ($content_admin->content === null ? array('disable' => H5PCore::DISABLE_NONE) : $content_admin->content);
                $content['title'] = $content_admin->get_input_title();
                $content['uploaded'] = true;
                $content_admin->get_disabled_content_features($core, $content);

                // Handle file upload
                $plugin_admin = H5P_Plugin_Admin::get_instance();
                $result = $plugin_admin->handle_upload($content);
            }

            if ($result) {
                $content['id'] = $result;
                $content_admin->set_content_tags($content['id'], filter_input(INPUT_POST, 'tags'));

                Session::instance()->set('success_message', 'Saved.');
            } else {
                Session::instance()->set('error_message', implode(', ', $core->h5pF->getMessages('error')));
            }
        }

        Request::initial()->redirect(URL::base() . 'h5p/addContent');
    }

    private function deleteContent(H5P_Plugin $plugin, H5PContentAdmin $content_admin)
    {
        $content_admin->set_content_tags($content_admin->content['id']);
        $storage = $plugin->get_h5p_instance('storage');
        $storage->deletePackage($content_admin->content);
    }

    /**
     * ajax request
     */
    public function action_contents()
    {
        $this->ajax_contents();
    }

    public function action_index()
    {
        $this->templateData['scripts_stack'][] = '/scripts/h5p/jquery.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-event-dispatcher.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-utils.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-data-view.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-data-views.js';

        $this->templateData['styles_stack'][] = '/css/h5p/h5p.css';
        $this->templateData['styles_stack'][] = '/css/h5p/h5p-admin.css';


        $headers = array(
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
                'text' => __('Author'),
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
                'text' => __('ID'),
                'sortable' => true
            )
        );
        if (get_option('h5p_track_user', true)) {
            $headers[] = (object)array(
                'class' => 'h5p-results-link'
            );
        }
        $headers[] = (object)array(
            'class' => 'h5p-edit-link'
        );

        $plugin_admin = H5P_Plugin_Admin::get_instance();
        $data_view = $plugin_admin->get_data_view_settings(
            'h5p-contents',
            admin_url('/h5p/contents'),
            $headers,
            array(true),
            __("No H5P content available. You must upload or create new content."),
            (object)array(
                'by' => 4,
                'dir' => 0
            )
        );

        $this->templateData['center'] = View::factory('h5p/contentList')
            ->set('templateData', $this->templateData)
            ->set('data_view', $data_view);
        $this->template->set('templateData', $this->templateData);
    }

    public function action_ajax_libraries()
    {
        static::loadH5PEditorClasses();
        $content_admin = new H5PContentAdmin('H5P');
        $content_admin->ajax_libraries();
    }

    public function action_libraries()
    {
        $this->add_admin_assets();
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-library-list.js';
        $this->templateData['scripts_stack'][] = '/scripts/h5p/h5p-event-dispatcher.js';

        $plugin = H5P_Plugin::get_instance();
        $core = $plugin->get_h5p_instance('core');
        $interface = $plugin->get_h5p_instance('interface');

        $not_cached = $interface->getNumNotFiltered();
        $libraries = $interface->loadLibraries();

        $settings = array(
            'containerSelector' => '#h5p-admin-container',
            'extraTableClasses' => 'wp-list-table widefat fixed',
            'l10n' => array(
                'NA' => __('N/A'),
                'viewLibrary' => __('View library details'),
                'deleteLibrary' => __('Delete library'),
                'upgradeLibrary' => __('Upgrade library content')
            )
        );

        // Find out which version of libraries that should be upgraded
        $minVersions = $core->getMinimumVersionsSupported(MODPATH . 'h5p-php-library/library-support.json');
        $needsUpgrade = '';

        // Add settings for each library
        $i = 0;
        foreach ($libraries as $versions) {
            foreach ($versions as $library) {
                $usage = $interface->getLibraryUsage($library->id, $not_cached ? true : false);
                if ($library->runnable) {
                    $upgrades = $core->getUpgrades($library, $versions);
                    $upgradeUrl = empty($upgrades) ? false : admin_url('h5p/libraries/upgrade/' . $library->id . '?destination=' . admin_url('h5p/libraries'));

                    $restricted = ($library->restricted ? true : false);
                    $restricted_url = admin_url('admin-ajax.php?action=h5p_restrict_library' .
                        '&id=' . $library->id .
                        //'&token=' . wp_create_nonce('h5p_library_' . $i) .
                        //'&token_id=' . $i .
                        '&restrict=' . ($library->restricted === '1' ? 0 : 1));
                } else {
                    $upgradeUrl = null;
                    $restricted = null;
                    $restricted_url = null;
                }

                // Check if this should be upgraded.
                if ($minVersions !== null && isset($minVersions[$library->name])) {
                    $min = $minVersions[$library->name];
                    if (!$core->isLibraryVersionSupported($library, $min->versions)) {
                        $needsUpgrade .= '<li><a href="' . $min->downloadUrl . '">' . $library->name . '</a> (' . H5PCore::libraryVersion($library) . ')</li>';
                    }
                }

                $contents_count = $interface->getNumContent($library->id);
                $settings['libraryList']['listData'][] = array(
                    'title' => $library->title . ' (' . H5PCore::libraryVersion($library) . ')',
                    'restricted' => $restricted,
                    'restrictedUrl' => $restricted_url,
                    'numContent' => $contents_count === 0 ? '' : $contents_count,
                    'numContentDependencies' => $usage['content'] < 1 ? '' : $usage['content'],
                    'numLibraryDependencies' => $usage['libraries'] === 0 ? '' : $usage['libraries'],
                    'upgradeUrl' => $upgradeUrl,
                    'detailsUrl' => admin_url('h5p/libraries/view/' . $library->id),
                    'deleteUrl' => admin_url('h5p/libraries/delete/' . $library->id)
                );

                $i++;
            }
        }

        // Translations
        $settings['libraryList']['listHeaders'] = array(
            __('Title'),
            __('Restricted'),
            array(
                'text' => __('Contents'),
                'class' => 'h5p-admin-center'
            ),
            array(
                'text' => __('Contents using it'),
                'class' => 'h5p-admin-center'
            ),
            array(
                'text' => __('Libraries using it'),
                'class' => 'h5p-admin-center'
            ),
            __('Actions')
        );

        // Make it possible to rebuild all caches.
        if ($not_cached) {
            $settings['libraryList']['notCached'] = $this->get_not_cached_settings($not_cached);
        }

        if ($needsUpgrade !== '') {
            // Set update message
            $interface->setErrorMessage('
          <p>' . __('The following libraries are outdated and should be upgraded:') . '</p>
          <ul id="h5p-outdated">' . $needsUpgrade . '</ul>
          <p>' . __('To upgrade all the installed libraries, do the following:') . '</p>
          <ol>
            <li>' . sprintf(__('Download the H5P file from the %s page.', 'H5P'),
                    '<a href="https://h5p.org/update-all-content-types">Upgrade All Content Types</a>') . '</li>
            <li>' . sprintf(__('Select the downloaded <em> %s</em> file in the form below.', 'H5P'), 'upgrades.h5p') . '</li>
            <li>' . __('Check off "Only update existing libraries" and click the <em>Upload</em> button.') . '</li>
          </ol> </p>'
            );
        }

        // Updates
        $update_available = get_option('h5p_update_available', 0);
        $current_update = get_option('h5p_current_update', 0);
        $updates_available = ($update_available !== 0 && $current_update !== 0 && $current_update < $update_available ? 1 : 0);
        $H5PAdminIntegration = $plugin->getSettingsHTML($settings, 'H5PAdminIntegration');

        $this->templateData['center'] = View::factory('h5p/librariesList')
            ->set('templateData', $this->templateData)
            ->set('H5PAdminIntegration', $H5PAdminIntegration)
            ->set('update_available', $update_available)
            ->set('updates_available', $updates_available)
            ->set('current_update', $current_update)
            ->set('messages', H5P_Plugin_Admin::getMessagesHTML());
        $this->template->set('templateData', $this->templateData);
    }

    /**
     * JavaScript settings needed to rebuild content caches.
     *
     * @since 1.1.0
     */
    private function get_not_cached_settings($num)
    {
        return array(
            'num' => $num,
            'url' => admin_url('admin-ajax.php?action=h5p_rebuild_cache'),
            'message' => __('Not all content has gotten their cache rebuilt. This is required to be able to delete libraries, and to display how many contents that uses the library.'),
            'progress' => sprintf(__('1 content need to get its cache rebuilt.',
                '%d contents needs to get their cache rebuilt.'), $num),
            'button' => __('Rebuild cache')
        );
    }

    private function add_admin_assets()
    {
        foreach (H5PCore::$adminScripts as $script) {
            $this->templateData['scripts_stack'][] = '/scripts/h5p/' . str_replace(array('styles/', 'js/'), '',
                    $script);
        }

        $this->templateData['styles_stack'][] = '/css/h5p/h5p.css';
        $this->templateData['styles_stack'][] = '/css/h5p/h5p-admin.css';
    }

    private function ajax_contents($insert = false)
    {

        // Load input vars.
        $admin = H5P_Plugin_Admin::get_instance();
        list($offset, $limit, $sort_by, $sort_dir, $filters, $facets) = $admin->get_data_view_input();

        // Different fields for insert
        if ($insert) {
            $fields = array('title', 'content_type', 'tags', 'updated_at', 'id', 'content_type_id', 'slug');
        } else {
            $fields = array(
                'title',
                'content_type',
                'user_name',
                'tags',
                'updated_at',
                'id',
                'user_id',
                'content_type_id'
            );
        }

        // Add filters to data query
        $conditions = array();
        if (isset($filters[0])) {
            $conditions[] = array('title', $filters[0], 'LIKE');
        }

        if ($facets !== null) {
            $facetmap = array(
                'content_type' => 'content_type_id',
                'user_name' => 'user_id',
                'tags' => 'tags'
            );
            foreach ($facets as $field => $value) {
                if (isset($facetmap[$fields[$field]])) {
                    $conditions[] = array($facetmap[$fields[$field]], $value, '=');
                }
            }
        }

        // Create new content query
        $content_query = new H5PContentQuery($fields, $offset, $limit, $fields[$sort_by], $sort_dir, $conditions);
        $results = $content_query->get_rows();

        // Make data more readable for humans
        $rows = array();
        foreach ($results as $result) {
            $rows[] = ($insert ? $this->get_contents_insert_row($result) : $this->get_contents_row($result));
        }

        // Print results
        header('Cache-Control: no-cache');
        header('Content-type: application/json');
        print json_encode(array(
            'num' => $content_query->get_total(),
            'rows' => $rows
        ));
        exit;
    }


    /**
     * Get row for insert table with all values escaped and ready for view.
     *
     * @since 1.2.0
     * @param stdClass $result Database result for row
     * @return array
     */
    private function get_contents_insert_row($result)
    {
        return array(
            esc_html($result->title),
            array(
                'id' => $result->content_type_id,
                'title' => esc_html($result->content_type)
            ),
            $this->format_tags($result->tags),
            $this->format_time($result->updated_at),
            '<button class="button h5p-insert" data-id="' . $result->id . '" data-slug="' . $result->slug . '">' . __('Insert') . '</button>'
        );
    }

    /**
     * Get row for contents table with all values escaped and ready for view.
     *
     * @since 1.2.0
     * @param stdClass $result Database result for row
     * @return array
     */
    private function get_contents_row($result)
    {
        $row = array(
            '<a href="' . admin_url('admin.php?page=h5p&task=show&id=' . $result->id) . '">' . $result->title . '</a>',
            array(
                'id' => $result->content_type_id,
                'title' => esc_html($result->content_type)
            ),
            array(
                'id' => $result->user_id,
                'title' => esc_html($result->user_name)
            ),
            $this->format_tags($result->tags),
            $this->format_time($result->updated_at),
            $result->id
        );

        $content = array('user_id' => $result->user_id);

        // Add user results link
        if (get_option('h5p_track_user', true)) {
            $row[] = '<a href="' . admin_url('admin.php?page=h5p&task=results&id=' . $result->id) . '">' . __('Results') . '</a>';
        }

        // Add edit link
        $row[] = '<a href="' . admin_url('admin.php?page=h5p_new&id=' . $result->id) . '">' . __('Edit') . '</a>';

        return $row;
    }

    /**
     * Format tags for use in content lists.
     *
     * @since 1.6.0
     * @param string $tags
     * @return array With tag objects
     */
    private function format_tags($tags)
    {
        // Tags come in CSV format, create Array instead
        $result = array();
        $csvtags = explode(';', $tags);
        foreach ($csvtags as $csvtag) {
            if ($csvtag !== '') {
                $tag = explode(',', $csvtag);
                $result[] = array(
                    'id' => $tag[0],
                    'title' => esc_html($tag[1])
                );
            }
        }

        return $result;
    }

    /**
     * Format time for use in content lists.
     *
     * @since 1.6.0
     * @param int $timestamp
     * @return string
     */
    private function format_time($timestamp)
    {
        // Get timezone offset
        $offset = get_option('gmt_offset') * 3600;

        // Format time
        $time = strtotime($timestamp);
        $current_time = time();
        //$human_time = human_time_diff($time + $offset, $current_time) . ' ' . __('ago');
        $human_time = ' ' . __('ago');
        $formatted_time = $human_time;

        //if ($current_time > $time + DAY_IN_SECONDS) {
        //    // Over a day old, swap human time for formatted time
        //    $formatted_time = $human_time;
        //    $human_time = date('Y/m/d', $time + $offset);
        //} else {
        //    $formatted_time = date(get_option('time_format'), $time + $offset);
        //}

        $iso_time = date('c', $time);

        return "<time datetime=\"{$iso_time}\" title=\"{$formatted_time}\">{$human_time}</time>";
    }

    private function loadAssets()
    {
        foreach (CustomAssetManager::getScripts() as $script) {
            $this->templateData['scripts_stack'][] = $script;
        }

        foreach (CustomAssetManager::getStyles() as $script) {
            $this->templateData['styles_stack'][] = $script;
        }
    }
}