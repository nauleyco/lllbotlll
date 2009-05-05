CREATE TABLE `irc_bot_table` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `pattern` varchar(255) collate utf8_unicode_ci NOT NULL,
  `phpcode` text collate utf8_unicode_ci NOT NULL,
  `help` text collate utf8_unicode_ci NOT NULL,
  `example` text collate utf8_unicode_ci NOT NULL,
  `timerflg` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci