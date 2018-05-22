<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('intersales_overheat/eventhandler'))
    ->addColumn('eventhandler_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true
    ), 'Eventhandler ID')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array('nullable'  => false), 'Internal Identifier')
    ->addColumn('observer_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Observer Name')
    ->addColumn('block_selector', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(), 'Block Selector JSON')
    ->addColumn('css_selector', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(), 'CSS Selector JSON')
    ->addColumn('trigger_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Trigger Type')
    ->addColumn('custom_js', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(), 'Custom JS') // function which returns the parameters for the action
    ->addColumn('action_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Action Type') // function which returns the parameters for the action
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->setComment('Eventhandler');

$installer->getConnection()->createTable($table);

$installer->endSetup();