--
-- Table structure for table `lrs`
--

CREATE TABLE `lrs` (
  `id` int(10) unsigned NOT NULL,
  `is_enabled` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_version` tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `lrs`
--
ALTER TABLE `lrs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `lrs`
--
ALTER TABLE `lrs`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `lrs_statement`
--

CREATE TABLE `lrs_statement` (
  `id` int(10) unsigned NOT NULL,
  `lrs_id` int(10) unsigned NOT NULL,
  `statement_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `statements`
--

CREATE TABLE `statements` (
  `id` int(10) unsigned NOT NULL,
  `session_id` int(10) unsigned DEFAULT NULL,
  `statement` text NOT NULL,
  `timestamp` decimal(20,4) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lrs_statement`
--
ALTER TABLE `lrs_statement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lrs_id` (`lrs_id`),
  ADD KEY `statement_id` (`statement_id`);

--
-- Indexes for table `statements`
--
ALTER TABLE `statements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lrs_statement`
--
ALTER TABLE `lrs_statement`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `statements`
--
ALTER TABLE `statements`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `lrs_statement`
--
ALTER TABLE `lrs_statement`
  ADD CONSTRAINT `lrs_statement_ibfk_1` FOREIGN KEY (`lrs_id`) REFERENCES `lrs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lrs_statement_ibfk_2` FOREIGN KEY (`statement_id`) REFERENCES `statements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `statements`
--
ALTER TABLE `statements`
  ADD CONSTRAINT `statements_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `user_sessions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;




ALTER TABLE `user_sessiontraces` ADD `is_redirected` BOOLEAN NOT NULL DEFAULT FALSE AFTER `node_id`;

ALTER TABLE `statements` CHANGE `timestamp` `timestamp` DECIMAL(18,6) NOT NULL;

ALTER TABLE `maps` ADD `send_xapi_statements` BOOLEAN NOT NULL DEFAULT FALSE;