<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Upgrade_Migration_ToVersion611_Processing
{
    /** @var Ess_M2ePro_Model_Upgrade_MySqlSetup */
    protected $_installer = null;

    //########################################

    /**
     * @return Ess_M2ePro_Model_Upgrade_MySqlSetup
     */
    public function getInstaller()
    {
        return $this->_installer;
    }

    /**
     * @param Ess_M2ePro_Model_Upgrade_MySqlSetup $installer
     */
    public function setInstaller(Ess_M2ePro_Model_Upgrade_MySqlSetup $installer)
    {
        $this->_installer = $installer;
    }

    //########################################

    /**
        ALTER TABLE `m2epro_lock_item`
        CHANGE COLUMN `data` `data` TEXT DEFAULT NULL,
        ADD COLUMN `parent_id` INT(11) UNSIGNED DEFAULT NULL AFTER `nick`,
        ADD INDEX `parent_id` (`parent_id`);

        RENAME TABLE `m2epro_synchronization_run` TO `m2epro_operation_history`;

        ALTER TABLE `m2epro_operation_history`
        ADD COLUMN `nick` VARCHAR(255) NOT NULL AFTER `id`,
        ADD COLUMN `parent_id` INT(11) UNSIGNED DEFAULT NULL AFTER `nick`,
        ADD COLUMN `data` TEXT DEFAULT NULL AFTER `end_date`,
        DROP COLUMN `kill_now`,
        CHANGE COLUMN `initiator` `initiator` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
        ADD INDEX `start_date` (`start_date`),
        ADD INDEX `end_date` (`end_date`),
        ADD INDEX `nick` (`nick`),
        ADD INDEX `parent_id` (`parent_id`);

        UPDATE `m2epro_operation_history` SET `nick` = 'synchronization';
        UPDATE `m2epro_operation_history` SET `initiator` = `initiator` + 5 WHERE (`initiator` IN (1,2));
        UPDATE `m2epro_operation_history` SET `initiator` = 1 WHERE (`initiator` = 7);
        UPDATE `m2epro_operation_history` SET `initiator` = 2 WHERE (`initiator` = 6);

        UPDATE `m2epro_processing_request` SET `responser_model` = REPLACE(`responser_model`, '_Tasks', '');
    */

    public function process()
    {
        if ($this->isNeedToSkip()) {
            return;
        }

        $this->processLockItemTable();
        $this->processOperationHistoryTable();
        $this->processProcessingRequestTable();
    }

    //########################################

    protected function isNeedToSkip()
    {
        $connection = $this->_installer->getConnection();

        $tempTable = $this->_installer->getTable('m2epro_lock_item');
        if ($connection->tableColumnExists($tempTable, 'parent_id') !== false) {
            return true;
        }

        return false;
    }

    //########################################

    protected function processLockItemTable()
    {
        $connection = $this->_installer->getConnection();

        $tempTable = $this->_installer->getTable('m2epro_lock_item');
        $tempTableIndexList = $connection->getIndexList($tempTable);

        if ($connection->tableColumnExists($tempTable, 'data') !== false) {
            $connection->changeColumn(
                $tempTable, 'data', 'data',
                'TEXT DEFAULT NULL'
            );
        }

        if ($connection->tableColumnExists($tempTable, 'parent_id') === false) {
            $connection->addColumn(
                $tempTable, 'parent_id',
                'INT(11) UNSIGNED DEFAULT NULL AFTER `nick`'
            );
        }

        if (!isset($tempTableIndexList[strtoupper('parent_id')])) {
            $connection->addKey($tempTable, 'parent_id', 'parent_id');
        }

        foreach ($connection->select()->from($tempTable, '*')->query() as $row) {
            $nick = preg_replace('/(listing|listing_other)_(ebay|amazon|buy|play)/', '$2_$1', $row['nick']);
            $connection->update($tempTable, array('nick' => $nick), "id={$row['id']}");
        }
    }

    protected function processOperationHistoryTable()
    {
        $connection = $this->_installer->getConnection();

        $oldTable = $this->_installer->getTable('m2epro_synchronization_run');
        $newTable = $this->_installer->getTable('m2epro_operation_history');

        if ($this->_installer->tableExists($oldTable) && !$this->_installer->tableExists($newTable)) {
            $connection->query("RENAME TABLE `{$oldTable}` TO `{$newTable}`");
        }

        if ($connection->tableColumnExists($newTable, 'nick') === false) {
            $connection->addColumn(
                $newTable, 'nick',
                'VARCHAR(255) NOT NULL AFTER `id`'
            );
        }

        if ($connection->tableColumnExists($newTable, 'parent_id') === false) {
            $connection->addColumn(
                $newTable, 'parent_id',
                'INT(11) UNSIGNED DEFAULT NULL AFTER `nick`'
            );
        }

        if ($connection->tableColumnExists($newTable, 'data') === false) {
            $connection->addColumn(
                $newTable, 'data',
                'TEXT DEFAULT NULL AFTER `end_date`'
            );
        }

        if ($connection->tableColumnExists($newTable, 'kill_now') !== false) {
            $connection->dropColumn($newTable, 'kill_now');
        }

        if ($connection->tableColumnExists($newTable, 'initiator') !== false) {
            $connection->changeColumn(
                $newTable, 'initiator', 'initiator',
                'TINYINT(2) UNSIGNED NOT NULL DEFAULT 0'
            );
        }

        $indexList = $connection->getIndexList($newTable);

        if (!isset($indexList[strtoupper('start_date')])) {
            $connection->addKey($newTable, 'start_date', 'start_date');
        }

        if (!isset($indexList[strtoupper('end_date')])) {
            $connection->addKey($newTable, 'end_date', 'end_date');
        }

        if (!isset($indexList[strtoupper('nick')])) {
            $connection->addKey($newTable, 'nick', 'nick');
        }

        if (!isset($indexList[strtoupper('parent_id')])) {
            $connection->addKey($newTable, 'parent_id', 'parent_id');
        }

        $offset = 5;

        $connection->update($newTable, array('nick' => 'synchronization'));
        $connection->update(
            $newTable, array('initiator' => new Zend_Db_Expr('`initiator` + '.$offset)), '`initiator` IN (1,2)'
        );

        $connection->update($newTable, array('initiator' => 1), '`initiator` = '.(2 + $offset));
        $connection->update($newTable, array('initiator' => 2), '`initiator` = '.(1 + $offset));
    }

    protected function processProcessingRequestTable()
    {
        $connection = $this->_installer->getConnection();
        $tempTable = $this->_installer->getTable('m2epro_processing_request');

        $connection->update(
            $tempTable, array('responser_model' => new Zend_Db_Expr("REPLACE(`responser_model`,'_Tasks','')"))
        );

        $connection->update(
            $tempTable,
            array('responser_model' => 'M2ePro/Amazon_Synchronization_Orders_Receive_Responser'),
            array("responser_model = 'M2ePro/Amazon_Synchronization_Orders_Responser'")
        );
    }

    //########################################
}
