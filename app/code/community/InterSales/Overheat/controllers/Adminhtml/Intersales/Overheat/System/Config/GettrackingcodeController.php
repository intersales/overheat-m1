<?php
/**
 * Get tracking code controller
 *
 * @category   InterSales
 * @package    InterSales_Overheat
 * @author     Daniel Rose <dr@intersales.de>
 */
class InterSales_Overheat_Adminhtml_Intersales_Overheat_System_Config_GettrackingcodeController extends Mage_Adminhtml_Controller_Action {
    /**
     * Retrieve tracking code
     *
     * @return void
     */
    public function getTrackingCodeAction() {
        $trackingCode = '';
        $errorMessage = '';
        $validateCode = $this->getRequest()->getParam('validate_code');

        if($validateCode != '') {
            $trackingCode = Mage::getSingleton('intersales_overheat/gateway')->getTrackingCode($validateCode);
        } else {
            $errorMessage = Mage::helper('intersales_overheat')->__('Validate code is required');
        }

        if($trackingCode == '') {
            $errorMessage = Mage::helper('intersales_overheat')->__('Validate code is not valid');
        }

        $body = Mage::helper('core')->jsonEncode(array(
            'errorMessage' => $errorMessage,
            'trackingCode' => $trackingCode
        ));

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($body);
    }
}
