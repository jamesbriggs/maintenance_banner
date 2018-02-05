CREATE TABLE `intercom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` varchar(7) NOT NULL DEFAULT 'info',
  `dt_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dt_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lang` char(2) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`id`),
  KEY `idx_dt_lang` (`dt_start`,`dt_end`,`lang`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

