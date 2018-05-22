<?php

class InterSales_Overheat_Block_JsRenderer extends Mage_Core_Block_Template
{
    /**
     * @return InterSales_Overheat_Helper_Data
     */
    public function getOverheatHelper()
    {
        return Mage::helper('intersales_overheat');
    }

    /**
     * @return string
     */
    public function getJavascript()
    {
        Mage::log("######## Any Blocks below this can't be tracked! ########", Zend_Log::DEBUG, 'intersales_overheat.log');
        $js = $this->getOverheatHelper()->getStackJs();
        $this->getOverheatHelper()->clearStacks();
        return $js;
    }
}