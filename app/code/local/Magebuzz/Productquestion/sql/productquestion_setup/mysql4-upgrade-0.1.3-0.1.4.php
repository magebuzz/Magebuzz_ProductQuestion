<?php
/**
 * @copyright Copyright (c) 2015 www.magebuzz.com
 */
$installer = $this;

$installer->startSetup();

$installer->run("
  ALTER TABLE {$this->getTable('productquestion_product')} 
  ADD INDEX(`productquestion_id`);

  ALTER TABLE {$this->getTable('productquestion_product')} 
  CHANGE `productquestion_id` `productquestion_id` INT(11) UNSIGNED NOT NULL;

  ALTER TABLE {$this->getTable('productquestion_product')}
  ADD CONSTRAINT `fk_questionid_productid`
  FOREIGN KEY (`productquestion_id`)
  REFERENCES {$this->getTable('productquestion')}(`productquestion_id`)
  ON DELETE CASCADE;

  ALTER TABLE {$this->getTable('productquestion_store')}
  ADD PRIMARY KEY(`productquestion_id`),
  CHANGE `productquestion_id` `productquestion_id` INT(11) UNSIGNED NOT NULL;

  ALTER TABLE {$this->getTable('productquestion_store')}
  ADD CONSTRAINT `fk_questionid_storeid`
  FOREIGN KEY (`productquestion_id`)
  REFERENCES {$this->getTable('productquestion')}(`productquestion_id`)
  ON DELETE CASCADE;
");

$installer->endSetup();