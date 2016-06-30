<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com
 */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('productquestion_product')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `productquestion_id` int(11)  NOT NULL ,
  `product_id` int(11)  NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('productquestion_store')} (
  `productquestion_id` int(11)  NOT NULL ,
  `store_id` int(11)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();