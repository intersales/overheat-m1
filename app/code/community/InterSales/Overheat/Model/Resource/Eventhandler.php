<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */

class InterSales_Overheat_Model_Resource_Eventhandler extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('intersales_overheat/eventhandler', 'eventhandler_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId())
        {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
}