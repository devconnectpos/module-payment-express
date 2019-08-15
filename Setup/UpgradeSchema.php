<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\PaymentExpress\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '0.0.1', '<')) {
            $this->addPaymentExpressHistoryLogTable($setup);
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    protected function addPaymentExpressHistoryLogTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $setup->getConnection()->dropTable($setup->getTable('sm_payment_express_history'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('sm_payment_express_history')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
            'Entity ID'
        )->addColumn(
            'hit_username',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'HIT Username'
        )->addColumn(
            'hit_key',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'HIT Key'
        )->addColumn(
            'device_id',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Device ID'
        )->addColumn(
            'station_id',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Station ID'
        )->addColumn(
            'dl1',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'DL1 Message'
        )->addColumn(
            'dl2',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'DL2 Message'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Text Message'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'Type Transaction'
        )->addColumn(
            'txnref',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'unsigned' => true,],
            'TxnRef Transaction'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creation Time'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
