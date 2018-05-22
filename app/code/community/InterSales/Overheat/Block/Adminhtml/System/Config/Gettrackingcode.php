<?php
/**
 * Adminhtml get tracking code block
 *
 * @category   InterSales
 * @package    InterSales_Overheat
 * @author     Daniel Rose <dr@intersales.de>
 */
class InterSales_Overheat_Block_Adminhtml_System_Config_Gettrackingcode extends Mage_Adminhtml_Block_System_Config_Form_Field {
    /**
     * Set template to itself
     *
     * @return InterSales_Overheat_Block_Adminhtml_System_Config_Gettrackingcode
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();

        if (!$this->getTemplate()) {
            $this->setTemplate('intersales/overheat/system/config/gettrackingcode.phtml');
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $originalData = $element->getOriginalData();

        $this->addData(array(
            'button_label'  => Mage::helper('intersales_overheat')->__($originalData['button_label']),
            'html_id'       => $element->getHtmlId(),
            'ajax_url'      => Mage::getSingleton('adminhtml/url')->getUrl('*/intersales_overheat_system_config_gettrackingcode/gettrackingcode')
        ));

        return $this->_toHtml();
    }
}