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

class H5PEditorOpenLabyrinthStorage implements H5peditorStorage
{

    /**
     * Empty contructor.
     */
    function __construct()
    {
    }

    public function getLanguage($name, $majorVersion, $minorVersion, $language)
    {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare(
            "SELECT hlt.translation
          FROM h5p_libraries_languages hlt
          JOIN h5p_libraries hl ON hl.id = hlt.library_id
          WHERE hl.name = %s
          AND hl.major_version = %d
          AND hl.minor_version = %d
          AND hlt.language_code = %s",
            $name, $majorVersion, $minorVersion, $language)
        );
    }

    public function addTmpFile($file)
    {
        // TODO: Keep track of tmp files.
    }

    public function keepFile($oldPath, $newPath)
    {
        // TODO: No longer a tmp file.
    }

    public function removeFile($path)
    {
        // TODO: Removed from file tracking.
    }

    public function getLibraries($libraries = null)
    {
        global $wpdb;
        $super_user = current_user_can('manage_h5p_libraries');

        if ($libraries !== null) {
            // Get details for the specified libraries only.
            $librariesWithDetails = array();
            foreach ($libraries as $library) {
                $details = $wpdb->get_row($wpdb->prepare(
                    "SELECT title, runnable, restricted, tutorial_url
              FROM {$wpdb->prefix}h5p_libraries
              WHERE name = %s
              AND major_version = %d
              AND minor_version = %d
              AND semantics IS NOT NULL",
                    $library->name, $library->majorVersion, $library->minorVersion
                ));
                if ($details) {
                    $library->tutorialUrl = $details->tutorial_url;
                    $library->title = $details->title;
                    $library->runnable = $details->runnable;
                    $library->restricted = $super_user ? false : ($details->restricted === '1' ? true : false);
                    $librariesWithDetails[] = $library;
                }
            }

            return $librariesWithDetails;
        }

        $libraries = array();

        $libraries_result = $wpdb->get_results(
            "SELECT name,
                title,
                major_version AS majorVersion,
                minor_version AS minorVersion,
                tutorial_url AS tutorialUrl,
                restricted
          FROM {$wpdb->prefix}h5p_libraries
          WHERE runnable = 1
          AND semantics IS NOT NULL
          ORDER BY title"
        );
        foreach ($libraries_result as $library) {
            // Make sure we only display the newest version of a library.
            foreach ($libraries as $key => $existingLibrary) {
                if ($library->name === $existingLibrary->name) {
                    // Mark old ones
                    // This is the newest
                    if (($library->majorVersion === $existingLibrary->majorVersion && $library->minorVersion > $existingLibrary->minorVersion) ||
                        ($library->majorVersion > $existingLibrary->majorVersion)
                    ) {
                        $existingLibrary->isOld = true;
                    } else {
                        $library->isOld = true;
                    }
                }
            }

            $library->restricted = $super_user ? false : ($library->restricted === '1' ? true : false);

            // Add new library
            $libraries[] = $library;
        }

        return $libraries;
    }

    /**
     * Implements alterLibrarySemantics
     *
     * Gives you a chance to alter all the library files.
     */
    public function alterLibraryFiles(&$files, $libraries)
    {
        $plugin = H5PPlugin::get_instance();
        $plugin->alter_assets($files, $libraries, 'editor');
    }
}
