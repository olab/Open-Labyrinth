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
class H5POpenLabyrinth implements H5PFrameworkInterface
{

    /**
     * Kesps track of messages for the user.
     *
     * @since 1.0.0
     * @var array
     */
    protected $messages = array('error' => array(), 'updated' => array());

    /**
     * Implements setErrorMessage
     */
    public function setErrorMessage($message)
    {
        if (current_user_can('edit_h5p_contents')) {
            $this->messages['error'][] = $message;
        }
    }

    /**
     * Implements setInfoMessage
     */
    public function setInfoMessage($message)
    {
        if (current_user_can('edit_h5p_contents')) {
            $this->messages['updated'][] = $message;
        }
    }

    /**
     * Return the selected messages.
     *
     * @since 1.0.0
     * @param string $type
     * @return array
     */
    public function getMessages($type)
    {
        return isset($this->messages[$type]) ? $this->messages[$type] : null;
    }

    /**
     * Implements t
     */
    public function t($message, $replacements = array())
    {
        // Insert !var as is, escape @var and emphasis %var.
        foreach ($replacements as $key => $replacement) {
            if ($key[0] === '@') {
                $replacements[$key] = esc_html($replacement);
            } elseif ($key[0] === '%') {
                $replacements[$key] = '<em>' . esc_html($replacement) . '</em>';
            }
        }
        $message = preg_replace('/(!|@|%)[a-z0-9]+/i', '%s', $message);

        $plugin = H5PPlugin::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        // Assumes that replacement vars are in the correct order.
        return vsprintf(__($message), $replacements);
    }

    /**
     * Helper
     */
    private function getH5pPath()
    {
        $plugin = H5PPlugin::get_instance();

        return $plugin->get_h5p_path();
    }

    /**
     * Implements getUploadedH5PFolderPath
     */
    public function getUploadedH5pFolderPath()
    {
        static $dir;

        if (is_null($dir)) {
            $plugin = H5PPlugin::get_instance();
            $core = $plugin->get_h5p_instance('core');
            $dir = $core->fs->getTmpPath();
        }

        return $dir;
    }

    /**
     * Implements getUploadedH5PPath
     */
    public function getUploadedH5pPath()
    {
        static $path;

        if (is_null($path)) {
            $plugin = H5PPlugin::get_instance();
            $core = $plugin->get_h5p_instance('core');
            $path = $core->fs->getTmpPath() . '.h5p';
        }

        return $path;
    }

    /**
     * Implements getLibraryId
     */
    public function getLibraryId($name, $majorVersion = null, $minorVersion = null)
    {
        global $wpdb;

        // Look for specific library
        $sql_where = 'WHERE name = %s';
        $sql_args = array($name);

        if ($majorVersion !== null) {
            // Look for major version
            $sql_where .= ' AND major_version = %d';
            $sql_args[] = $majorVersion;
            if ($minorVersion !== null) {
                // Look for minor version
                $sql_where .= ' AND minor_version = %d';
                $sql_args[] = $minorVersion;
            }
        }

        // Get the lastest version which matches the input parameters
        $id = $wpdb->get_var($wpdb->prepare(
            "SELECT id
        FROM {$wpdb->prefix}h5p_libraries
        {$sql_where}
        ORDER BY major_version DESC,
                 minor_version DESC,
                 patch_version DESC
        LIMIT 1",
            $sql_args)
        );

        return $id === null ? false : $id;
    }

    /**
     * Implements isPatchedLibrary
     */
    public function isPatchedLibrary($library)
    {
        global $wpdb;

        if (defined('H5P_DEV') && H5P_DEV) {
            // Makes sure libraries are updated, patch version does not matter.
            return true;
        }

        $operator = $this->isInDevMode() ? '<=' : '<';

        return $wpdb->get_var($wpdb->prepare(
            "SELECT id
          FROM {$wpdb->prefix}h5p_libraries
          WHERE name = '%s'
          AND major_version = %d
          AND minor_version = %d
          AND patch_version {$operator} %d",
            $library['machineName'],
            $library['majorVersion'],
            $library['minorVersion'],
            $library['patchVersion']
        )) !== null;
    }

    /**
     * Implements isInDevMode
     */
    public function isInDevMode()
    {
        return false;
    }

    /**
     * Implements mayUpdateLibraries
     */
    public function mayUpdateLibraries()
    {
        return current_user_can('manage_h5p_libraries');
    }

    /**
     * Implements getLibraryUsage
     */
    public function getLibraryUsage($id, $skipContent = false)
    {
        $content = DB_SQL::select()
            ->column(DB_SQL::expr("COUNT(distinct c.id)"), 'counter')
            ->from('h5p_libraries', 'l')
            ->join('INNER', 'h5p_contents_libraries', 'cl')->on('l.id', '=', 'cl.library_id')
            ->join('INNER', 'h5p_contents', 'c')->on('cl.content_id', '=', 'c.id')
            ->where('l.id', '=', $id)
            ->query()
            ->fetch(0)['counter'];

        $libraries = DB_SQL::select()
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->from('h5p_libraries_libraries')
            ->where('required_library_id', '=', $id)
            ->query()
            ->fetch(0)['counter'];

        return array(
            'content' => $skipContent ? -1 : intval($content),
            'libraries' => intval($libraries)
        );
    }

    /**
     * Implements saveLibraryData
     */
    public function saveLibraryData(&$library, $new = true)
    {
        global $wpdb;

        $preloadedJs = $this->pathsToCsv($library, 'preloadedJs');
        $preloadedCss = $this->pathsToCsv($library, 'preloadedCss');
        $dropLibraryCss = '';

        if (isset($library['dropLibraryCss'])) {
            $libs = array();
            foreach ($library['dropLibraryCss'] as $lib) {
                $libs[] = $lib['machineName'];
            }
            $dropLibraryCss = implode(', ', $libs);
        }

        $embedTypes = '';
        if (isset($library['embedTypes'])) {
            $embedTypes = implode(', ', $library['embedTypes']);
        }
        if (!isset($library['semantics'])) {
            $library['semantics'] = '';
        }
        if (!isset($library['fullscreen'])) {
            $library['fullscreen'] = 0;
        }
        if ($new) {
            $wpdb->insert(
                $wpdb->prefix . 'h5p_libraries',
                array(
                    'name' => $library['machineName'],
                    'title' => $library['title'],
                    'major_version' => $library['majorVersion'],
                    'minor_version' => $library['minorVersion'],
                    'patch_version' => $library['patchVersion'],
                    'runnable' => $library['runnable'],
                    'fullscreen' => $library['fullscreen'],
                    'embed_types' => $embedTypes,
                    'preloaded_js' => $preloadedJs,
                    'preloaded_css' => $preloadedCss,
                    'drop_library_css' => $dropLibraryCss,
                    'semantics' => $library['semantics']
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%s'
                )
            );
            $library['libraryId'] = $wpdb->insert_id;
        } else {
            $wpdb->update(
                $wpdb->prefix . 'h5p_libraries',
                array(
                    'title' => $library['title'],
                    'patch_version' => $library['patchVersion'],
                    'runnable' => $library['runnable'],
                    'fullscreen' => $library['fullscreen'],
                    'embed_types' => $embedTypes,
                    'preloaded_js' => $preloadedJs,
                    'preloaded_css' => $preloadedCss,
                    'drop_library_css' => $dropLibraryCss,
                    'semantics' => $library['semantics']
                ),
                array('id' => $library['libraryId']),
                array(
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%s'
                ),
                array('%d')
            );
            $this->deleteLibraryDependencies($library['libraryId']);
        }

        // Log library successfully installed/upgraded
        //new H5P_Event('library', ($new ? 'create' : 'update'),
        //    null, null,
        //    $library['machineName'], $library['majorVersion'] . '.' . $library['minorVersion']);

        // Update languages
        $wpdb->delete(
            $wpdb->prefix . 'h5p_libraries_languages',
            array('library_id' => $library['libraryId']),
            array('%d')
        );

        if (isset($library['language'])) {
            foreach ($library['language'] as $languageCode => $translation) {
                $wpdb->insert(
                    $wpdb->prefix . 'h5p_libraries_languages',
                    array(
                        'library_id' => $library['libraryId'],
                        'language_code' => $languageCode,
                        'translation' => $translation
                    ),
                    array(
                        '%d',
                        '%s',
                        '%s'
                    )
                );
            }
        }
    }

    /**
     * Convert list of file paths to csv
     *
     * @param array $library
     *  Library data as found in library.json files
     * @param string $key
     *  Key that should be found in $libraryData
     * @return string
     *  file paths separated by ', '
     */
    private function pathsToCsv($library, $key)
    {
        if (isset($library[$key])) {
            $paths = array();
            foreach ($library[$key] as $file) {
                $paths[] = $file['path'];
            }

            return implode(', ', $paths);
        }

        return '';
    }

    /**
     * Implements deleteLibraryDependencies
     */
    public function deleteLibraryDependencies($id)
    {
        global $wpdb;

        $wpdb->delete(
            $wpdb->prefix . 'h5p_libraries_libraries',
            array('library_id' => $id),
            array('%d')
        );
    }

    /**
     * Implements deleteLibrary
     */
    public function deleteLibrary($library)
    {
        global $wpdb;

        // Delete library files
        H5PCore::deleteFileTree($this->getH5pPath() . '/libraries/' . $library->name . '-' . $library->major_version . '.' . $library->minor_version);

        // Remove library data from database
        $wpdb->delete($wpdb->prefix . 'h5p_libraries_libraries', array('library_id' => $library->id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'h5p_libraries_languages', array('library_id' => $library->id), array('%d'));
        $wpdb->delete($wpdb->prefix . 'h5p_libraries', array('id' => $library->id), array('%d'));
    }

    /**
     * Implements saveLibraryDependencies
     */
    public function saveLibraryDependencies($id, $dependencies, $dependencyType)
    {
        global $wpdb;

        foreach ($dependencies as $dependency) {
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$wpdb->prefix}h5p_libraries_libraries (library_id, required_library_id, dependency_type)
        SELECT %d, hl.id, %s
        FROM {$wpdb->prefix}h5p_libraries hl
        WHERE name = %s
        AND major_version = %d
        AND minor_version = %d
        ON DUPLICATE KEY UPDATE dependency_type = %s",
                $id, $dependencyType, $dependency['machineName'], $dependency['majorVersion'],
                $dependency['minorVersion'], $dependencyType)
            );
        }
    }

    /**
     * Implements updateContent
     */
    public function updateContent($content, $contentMainId = null)
    {
        $wpdb = getWPDB();

        $table = 'h5p_contents';
        $data = array(
            'updated_at' => date('Y-m-d H:i:s'),
            'title' => $content['title'],
            'parameters' => $content['params'],
            'embed_type' => 'div', // TODO: Determine from library?
            'library_id' => $content['library']['libraryId'],
            'filtered' => '',
            'disable' => $content['disable'],
        );
        $format = array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%d'
        );

        if (!isset($content['id'])) {
            // Insert new content
            $data['created_at'] = $data['updated_at'];
            $format[] = '%s';
            $data['user_id'] = Auth::instance()->get_user()->id;
            $format[] = '%d';
            $wpdb->insert($table, $data, $format);
            $content['id'] = $wpdb->insert_id;
            $event_type = 'create';
        } else {
            // Update existing content
            $wpdb->update($table, $data, array('id' => $content['id']), $format, array('%d'));
            $event_type = 'update';
        }

        // Log content create/update/upload
        if (!empty($content['uploaded'])) {
            $event_type .= ' upload';
        }
        //new H5P_Event('content', $event_type,
        //    $content['id'],
        //    $content['title'],
        //    $content['library']['machineName'],
        //    $content['library']['majorVersion'] . '.' . $content['library']['minorVersion']);

        return $content['id'];
    }

    /**
     * Implements insertContent
     */
    public function insertContent($content, $contentMainId = null)
    {
        return $this->updateContent($content);
    }

    /**
     * Implement getWhitelist
     */
    public function getWhitelist($isLibrary, $defaultContentWhitelist, $defaultLibraryWhitelist)
    {
        // TODO: Get this value from a settings page.
        $whitelist = $defaultContentWhitelist;
        if ($isLibrary) {
            $whitelist .= ' ' . $defaultLibraryWhitelist;
        }

        return $whitelist;
    }

    /**
     * Implements copyLibraryUsage
     */
    public function copyLibraryUsage($contentId, $copyFromId, $contentMainId = null)
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->prefix}h5p_contents_libraries (content_id, library_id, dependency_type, weight, drop_css)
        SELECT %d, hcl.library_id, hcl.dependency_type, hcl.weight, hcl.drop_css
          FROM {$wpdb->prefix}h5p_contents_libraries hcl
          WHERE hcl.content_id = %d",
            $contentId, $copyFromId)
        );
    }

    /**
     * Implements deleteContentData
     */
    public function deleteContentData($id)
    {
        global $wpdb;

        // Remove content data and library usage
        $wpdb->delete($wpdb->prefix . 'h5p_contents', array('id' => $id), array('%d'));
        $this->deleteLibraryUsage($id);

        // Remove user scores/results
        $wpdb->delete($wpdb->prefix . 'h5p_results', array('content_id' => $id), array('%d'));

        // Remove contents user/usage data
        $wpdb->delete($wpdb->prefix . 'h5p_contents_user_data', array('content_id' => $id), array('%d'));
    }

    /**
     * Implements deleteLibraryUsage
     */
    public function deleteLibraryUsage($contentId)
    {
        DB_SQL::delete()
            ->from('h5p_contents_libraries')
            ->where('content_id', '=', $contentId)
            ->execute();
    }

    /**
     * Implements saveLibraryUsage
     */
    public function saveLibraryUsage($contentId, $librariesInUse)
    {
        global $wpdb;

        $dropLibraryCssList = array();
        foreach ($librariesInUse as $dependency) {
            if (!empty($dependency['library']['dropLibraryCss'])) {
                $dropLibraryCssList = array_merge($dropLibraryCssList,
                    explode(', ', $dependency['library']['dropLibraryCss']));
            }
        }

        foreach ($librariesInUse as $dependency) {
            $dropCss = in_array($dependency['library']['machineName'], $dropLibraryCssList) ? 1 : 0;
            $wpdb->insert(
                $wpdb->prefix . 'h5p_contents_libraries',
                array(
                    'content_id' => $contentId,
                    'library_id' => $dependency['library']['libraryId'],
                    'dependency_type' => $dependency['type'],
                    'drop_css' => $dropCss,
                    'weight' => $dependency['weight']
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                )
            );
        }
    }

    /**
     * Implements loadLibrary
     */
    public function loadLibrary($name, $majorVersion, $minorVersion)
    {
        global $wpdb;

        $library = $wpdb->get_row($wpdb->prepare(
            "SELECT id as libraryId, name as machineName, title, major_version as majorVersion, minor_version as minorVersion, patch_version as patchVersion,
          embed_types as embedTypes, preloaded_js as preloadedJs, preloaded_css as preloadedCss, drop_library_css as dropLibraryCss, fullscreen, runnable, semantics
        FROM {$wpdb->prefix}h5p_libraries
        WHERE name = %s
        AND major_version = %d
        AND minor_version = %d",
            $name,
            $majorVersion,
            $minorVersion),
            ARRAY_A
        );

        $dependencies = $wpdb->get_results($wpdb->prepare(
            "SELECT hl.name as machineName, hl.major_version as majorVersion, hl.minor_version as minorVersion, hll.dependency_type as dependencyType
        FROM {$wpdb->prefix}h5p_libraries_libraries hll
        JOIN {$wpdb->prefix}h5p_libraries hl ON hll.required_library_id = hl.id
        WHERE hll.library_id = %d",
            $library['libraryId'])
        );
        foreach ($dependencies as $dependency) {
            $library[$dependency->dependencyType . 'Dependencies'][] = array(
                'machineName' => $dependency->machineName,
                'majorVersion' => $dependency->majorVersion,
                'minorVersion' => $dependency->minorVersion,
            );
        }
        if ($this->isInDevMode()) {
            $semantics = $this->getSemanticsFromFile($library['machineName'], $library['majorVersion'],
                $library['minorVersion']);
            if ($semantics) {
                $library['semantics'] = $semantics;
            }
        }

        return $library;
    }

    private function getSemanticsFromFile($name, $majorVersion, $minorVersion)
    {
        $semanticsPath = $this->getH5pPath() . '/libraries/' . $name . '-' . $majorVersion . '.' . $minorVersion . '/semantics.json';
        if (file_exists($semanticsPath)) {
            $semantics = file_get_contents($semanticsPath);
            if (!json_decode($semantics, true)) {
                $this->setErrorMessage($this->t('Invalid json in semantics for %library', array('%library' => $name)));
            }

            return $semantics;
        }

        return false;
    }

    /**
     * Implements loadLibrarySemantics
     */
    public function loadLibrarySemantics($name, $majorVersion, $minorVersion)
    {
        $wpdb = getWPDB();

        if ($this->isInDevMode()) {
            $semantics = $this->getSemanticsFromFile($name, $majorVersion, $minorVersion);
        } else {
            $semantics = $wpdb->get_var($wpdb->prepare(
                "SELECT semantics
          FROM h5p_libraries
          WHERE name = %s
          AND major_version = %d
          AND minor_version = %d",
                $name, $majorVersion, $minorVersion)
            );
        }

        return ($semantics === false ? null : $semantics);
    }

    /**
     * Implements alterLibrarySemantics
     */
    public function alterLibrarySemantics(&$semantics, $name, $majorVersion, $minorVersion)
    {
        /**
         * Allows you to alter the H5P library semantics, i.e. changing how the
         * editor looks and how content parameters are filtered.
         *
         * @since 1.5.3
         *
         * @param object &$semantics
         * @param string $libraryName
         * @param int $libraryMajorVersion
         * @param int $libraryMinorVersion
         */
        //do_action_ref_array('h5p_alter_library_semantics', array(&$semantics, $name, $majorVersion, $minorVersion));
    }

    /**
     * Implements loadContent
     */
    public function loadContent($id)
    {
        $wpdb = getWPDB();

        $content = $wpdb->get_row($wpdb->prepare(
            "SELECT hc.id
              , hc.title
              , hc.parameters AS params
              , hc.filtered
              , hc.slug AS slug
              , hc.user_id
              , hc.embed_type AS embedType
              , hc.disable
              , hl.id AS libraryId
              , hl.name AS libraryName
              , hl.major_version AS libraryMajorVersion
              , hl.minor_version AS libraryMinorVersion
              , hl.embed_types AS libraryEmbedTypes
              , hl.fullscreen AS libraryFullscreen
        FROM {$wpdb->prefix}h5p_contents hc
        JOIN {$wpdb->prefix}h5p_libraries hl ON hl.id = hc.library_id
        WHERE hc.id = %d",
            $id),
            ARRAY_A
        );

        return $content;
    }

    /**
     * Implements loadContentDependencies
     */
    public function loadContentDependencies($id, $type = null)
    {
        global $wpdb;

        $query =
            "SELECT hl.id
              , hl.name AS machineName
              , hl.major_version AS majorVersion
              , hl.minor_version AS minorVersion
              , hl.patch_version AS patchVersion
              , hl.preloaded_css AS preloadedCss
              , hl.preloaded_js AS preloadedJs
              , hcl.drop_css AS dropCss
              , hcl.dependency_type AS dependencyType
        FROM {$wpdb->prefix}h5p_contents_libraries hcl
        JOIN {$wpdb->prefix}h5p_libraries hl ON hcl.library_id = hl.id
        WHERE hcl.content_id = %d";
        $queryArgs = array($id);

        if ($type !== null) {
            $query .= " AND hcl.dependency_type = %s";
            $queryArgs[] = $type;
        }

        $query .= " ORDER BY hcl.weight";

        return $wpdb->get_results($wpdb->prepare($query, $queryArgs), ARRAY_A);
    }

    /**
     * Implements getOption().
     */
    public function getOption($name, $default = false)
    {
        if ($name === 'site_uuid') {
            $name = 'h5p_site_uuid'; // Make up for old core bug
        }

        return get_option('h5p_' . $name, $default);
    }


    /**
     * Implements setOption().
     */
    public function setOption($name, $value)
    {
        if ($name === 'site_uuid') {
            $name = 'h5p_site_uuid'; // Make up for old core bug
        }
        $var = $this->getOption($name);
        $name = 'h5p_' . $name; // Always prefix to avoid conflicts
        if ($var === false) {
            add_option($name, $value);
        } else {
            update_option($name, $value);
        }
    }

    /**
     * Convert variables to fit our DB.
     */
    private static function camelToString($input)
    {
        $input = preg_replace('/[a-z0-9]([A-Z])[a-z0-9]/', '_$1', $input);

        return strtolower($input);
    }

    /**
     * Implements setFilteredParameters().
     */
    public function updateContentFields($id, $fields)
    {
        global $wpdb;

        $processedFields = array();
        $format = array();
        foreach ($fields as $name => $value) {
            if (is_int($value)) {
                $format[] = '%d'; // Int
            } else {
                if (is_float($value)) {
                    $format[] = '%f'; // Float
                } else {
                    $format[] = '%s'; // String
                }
            }

            $processedFields[self::camelToString($name)] = $value;
        }

        $wpdb->update(
            $wpdb->prefix . 'h5p_contents',
            $processedFields,
            array('id' => $id),
            $format,
            array('%d'));
    }

    /**
     * Implements clearFilteredParameters().
     */
    public function clearFilteredParameters($library_id)
    {
        global $wpdb;

        $wpdb->update($wpdb->prefix . 'h5p_contents', array('filtered' => null), array('library_id' => $library_id),
            array('%s'), array('%d'));
    }

    /**
     * Implements getNumNotFiltered().
     */
    public function getNumNotFiltered()
    {

        return (int)DB_SQL::select()
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->from('h5p_contents')
            ->where('filtered', '=', '')
            ->query()
            ->fetch(0)['counter'];
    }

    /**
     * Implements getNumContent().
     */
    public function getNumContent($library_id)
    {
        return (int)DB_SQL::select()
            ->column(DB_SQL::expr("COUNT(*)"), 'counter')
            ->from('h5p_contents')
            ->where('library_id', '=', $library_id)
            ->query()
            ->fetch(0)['counter'];
    }


    /**
     * Implements loadLibraries.
     */
    public function loadLibraries()
    {

        $results = DB_SQL::select()
            ->column('id')
            ->column('name')
            ->column('title')
            ->column('major_version')
            ->column('minor_version')
            ->column('patch_version')
            ->column('runnable')
            ->column('restricted')
            ->from('h5p_libraries')
            ->order_by('title')
            ->order_by('major_version')
            ->order_by('minor_version')
            ->query();

        $libraries = array();
        foreach ($results as $library) {
            $library = (object)$library;
            $libraries[$library->name][] = $library;
        }

        return $libraries;
    }

    /**
     * Implements getAdminUrl.
     */
    public function getAdminUrl()
    {

    }

    /**
     * Implements getPlatformInfo
     */
    public function getPlatformInfo()
    {
        global $wp_version;

        return array(
            'name' => 'WordPress',
            'version' => $wp_version,
            'h5pVersion' => H5PPlugin::VERSION
        );
    }

    /**
     * Implements fetchExternalData
     */
    public function fetchExternalData($url, $data = null)
    {
        if ($data !== null) {
            // Post
            $response = wp_remote_post($url, array('body' => $data));
        } else {
            // Get
            $response = wp_remote_get($url);
        }

        if (is_wp_error($response)) {
            //$error_message = $response->get_error_message();
        } elseif ($response['response']['code'] === 200) {
            return $response['body'];
        } else {

        }

        return null;
    }

    /**
     * Implements setLibraryTutorialUrl
     */
    public function setLibraryTutorialUrl($library_name, $url)
    {
        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'h5p_libraries',
            array('tutorial_url' => $url),
            array('name' => $library_name),
            array('%s'),
            array('%s')
        );
    }

    /**
     * Implements resetContentUserData
     */
    public function resetContentUserData($contentId)
    {
        global $wpdb;

        // Reset user datas for this content
        $wpdb->update(
            $wpdb->prefix . 'h5p_contents_user_data',
            array(
                'updated_at' => date('Y-m-d H:i:s'),
                'data' => 'RESET'
            ),
            array(
                'content_id' => $contentId,
                'invalidate' => 1
            ),
            array('%s', '%s'),
            array('%d', '%d')
        );
    }

    /**
     * Implements isContentSlugAvailable
     */
    public function isContentSlugAvailable($slug)
    {
        global $wpdb;

        return !$wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}h5p_contents WHERE slug = '%s'", $slug));
    }

    /**
     * Implements getLibraryContentCount
     */
    public function getLibraryContentCount()
    {
        global $wpdb;
        $count = array();

        // Find number of content per library
        $results = $wpdb->get_results("
        SELECT l.name, l.major_version, l.minor_version, COUNT(*) AS count
          FROM {$wpdb->prefix}h5p_contents c, {$wpdb->prefix}h5p_libraries l
         WHERE c.library_id = l.id
      GROUP BY l.name, l.major_version, l.minor_version
        ");

        // Extract results
        foreach ($results as $library) {
            $count[$library->name . ' ' . $library->major_version . '.' . $library->minor_version] = $library->count;
        }

        return $count;
    }

    /**
     * Implements getLibraryStats
     */
    public function getLibraryStats($type)
    {
        global $wpdb;
        $count = array();

        $results = $wpdb->get_results($wpdb->prepare("
        SELECT library_name AS name,
               library_version AS version,
               num
          FROM {$wpdb->prefix}h5p_counters
         WHERE type = %s
        ", $type));

        // Extract results
        foreach ($results as $library) {
            $count[$library->name . ' ' . $library->version] = $library->num;
        }

        return $count;
    }

    /**
     * Implements getNumAuthors
     */
    public function getNumAuthors()
    {
        global $wpdb;

        return $wpdb->get_var("
        SELECT COUNT(DISTINCT user_id)
          FROM {$wpdb->prefix}h5p_contents
    ");
    }

    // Magic stuff not used, we do not support library development mode.
    public function lockDependencyStorage()
    {
    }

    public function unlockDependencyStorage()
    {
    }

    /**
     * Implements saveCachedAssets
     */
    public function saveCachedAssets($key, $libraries)
    {
        $wpdb = getWPDB();

        foreach ($libraries as $library) {

            $id = isset($library['id']) ? $library['id'] : $library['libraryId'];

            $already_exists = DB_SQL::select()
                ->from('h5p_libraries_cachedassets')
                ->where('library_id', '=', $id)
                ->where('hash', '=', $key)
                ->query()
                ->fetch(0);

            if (!$already_exists) {
                $wpdb->insert(
                    "h5p_libraries_cachedassets",
                    array(
                        'library_id' => $id,
                        'hash' => $key
                    ),
                    array(
                        '%d',
                        '%s'
                    ));
            }
        }
    }

    /**
     * Implements deleteCachedAssets
     */
    public function deleteCachedAssets($library_id)
    {
        global $wpdb;

        // Get all the keys so we can remove the files
        $results = $wpdb->get_results($wpdb->prepare("
        SELECT hash
          FROM h5p_libraries_cachedassets
         WHERE library_id = %d
        ", $library_id));

        // Remove all invalid keys
        $hashes = array();
        foreach ($results as $key) {
            $hashes[] = $key->hash;

            $wpdb->delete(
                "h5p_libraries_cachedassets",
                array('hash' => $key->hash),
                array('%s'));
        }

        return $hashes;
    }

    /**
     * Implements afterExportCreated
     */
    public function afterExportCreated()
    {

    }
}
