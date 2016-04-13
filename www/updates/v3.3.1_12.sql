SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_contents` (
  `id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `library_id` int(10) unsigned NOT NULL,
  `parameters` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `filtered` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(127) COLLATE utf8mb4_unicode_ci NOT NULL,
  `embed_type` varchar(127) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disable` int(10) unsigned NOT NULL DEFAULT '0',
  `content_type` varchar(127) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(127) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_contents_libraries` (
  `content_id` int(10) unsigned NOT NULL,
  `library_id` int(10) unsigned NOT NULL,
  `dependency_type` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` smallint(5) unsigned NOT NULL DEFAULT '0',
  `drop_css` tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_contents_tags` (
  `content_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_contents_user_data` (
  `content_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `sub_content_id` int(10) unsigned NOT NULL,
  `data_id` varchar(127) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `preload` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `invalidate` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_counters` (
  `type` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `library_name` varchar(127) COLLATE utf8mb4_unicode_ci NOT NULL,
  `library_version` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_events` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `type` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_type` varchar(63) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `content_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `library_name` varchar(127) COLLATE utf8mb4_unicode_ci NOT NULL,
  `library_version` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_libraries` (
  `id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `name` varchar(127) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `major_version` int(10) unsigned NOT NULL,
  `minor_version` int(10) unsigned NOT NULL,
  `patch_version` int(10) unsigned NOT NULL,
  `runnable` int(10) unsigned NOT NULL,
  `restricted` int(10) unsigned NOT NULL DEFAULT '0',
  `fullscreen` int(10) unsigned NOT NULL,
  `embed_types` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preloaded_js` text COLLATE utf8mb4_unicode_ci,
  `preloaded_css` text COLLATE utf8mb4_unicode_ci,
  `drop_library_css` text COLLATE utf8mb4_unicode_ci,
  `semantics` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tutorial_url` varchar(1023) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_libraries_cachedassets` (
  `library_id` int(10) unsigned NOT NULL,
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_libraries_languages` (
  `library_id` int(10) unsigned NOT NULL,
  `language_code` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translation` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_libraries_libraries` (
  `library_id` int(10) unsigned NOT NULL,
  `required_library_id` int(10) unsigned NOT NULL,
  `dependency_type` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_results` (
  `id` int(10) unsigned NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `score` int(10) unsigned NOT NULL,
  `max_score` int(10) unsigned NOT NULL,
  `opened` int(10) unsigned NOT NULL,
  `finished` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure
--

CREATE TABLE `h5p_tags` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(31) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `h5p_contents`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `h5p_contents_libraries`
  ADD PRIMARY KEY (`content_id`,`library_id`,`dependency_type`);

ALTER TABLE `h5p_contents_tags`
  ADD PRIMARY KEY (`content_id`,`tag_id`);

ALTER TABLE `h5p_contents_user_data`
  ADD PRIMARY KEY (`content_id`,`user_id`,`sub_content_id`,`data_id`);

ALTER TABLE `h5p_counters`
  ADD PRIMARY KEY (`type`,`library_name`,`library_version`);

ALTER TABLE `h5p_events`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `h5p_libraries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name_version` (`name`,`major_version`,`minor_version`,`patch_version`),
  ADD KEY `runnable` (`runnable`);

ALTER TABLE `h5p_libraries_cachedassets`
  ADD PRIMARY KEY (`library_id`,`hash`);

ALTER TABLE `h5p_libraries_languages`
  ADD PRIMARY KEY (`library_id`,`language_code`);

ALTER TABLE `h5p_libraries_libraries`
  ADD PRIMARY KEY (`library_id`,`required_library_id`);

ALTER TABLE `h5p_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_user` (`content_id`,`user_id`);

ALTER TABLE `h5p_tags`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `h5p_contents`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `h5p_events`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `h5p_libraries`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `h5p_results`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `h5p_tags`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;