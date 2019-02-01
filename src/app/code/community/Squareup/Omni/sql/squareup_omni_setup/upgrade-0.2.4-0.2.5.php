<?php
$installer = $this;
$installer->startSetup();

$installer->run(
    "ALTER TABLE {$installer->getTable('squareup_omni/location')}
    ADD currency VARCHAR( 255 ) NULL;"
);

$installer->endSetup();