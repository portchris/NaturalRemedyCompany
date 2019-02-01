<?php
$installer = $this;
$installer->startSetup();

$installer->run(
    "ALTER TABLE {$installer->getTable('sales/quote_payment')}
    ADD squareup_nonce VARCHAR( 255 ) NULL;
      
    ALTER TABLE {$installer->getTable('sales/order_payment')}
    ADD squareup_nonce VARCHAR( 255 ) NULL;"
);

$installer->endSetup();