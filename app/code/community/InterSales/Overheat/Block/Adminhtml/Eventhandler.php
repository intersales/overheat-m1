<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */
class InterSales_Overheat_Block_Adminhtml_Eventhandler extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup      = 'intersales_overheat';
        $this->_controller      = 'adminhtml_eventhandler';

        $this->_headerText      = $this->__('Eventhandler');
        $this->_addButtonLabel  = $this->__('Add Eventhandler');

        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }
}