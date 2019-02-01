<?php
$installer = $this;
$installer->startSetup();

$installer->run(
    "ALTER TABLE {$installer->getTable('squareup_omni/transaction')}
    ADD tender_id VARCHAR( 255 ) NULL;"
);

$installer->endSetup();