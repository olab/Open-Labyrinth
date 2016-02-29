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