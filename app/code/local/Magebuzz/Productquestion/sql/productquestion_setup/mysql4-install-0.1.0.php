<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com
 */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('productquestion')} (
  `productquestion_id` int(11) unsigned NOT NULL auto_increment,
  `question` text NOT NULL default '',
  `author_name` varchar(255) NOT NULL default '',
  `author_email` varchar(255) NOT NULL default '',
  `answer` text NOT NULL default '',
  `countup` INT(11) UNSIGNED NOT NULL default '0',
  `countdown` INT(11) UNSIGNED NOT NULL default '0',
  `replied` smallint(6) NOT NULL default '0',
  `visibility` smallint(6) NOT NULL default '0',
  `date` datetime NULL,
  PRIMARY KEY (`productquestion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();