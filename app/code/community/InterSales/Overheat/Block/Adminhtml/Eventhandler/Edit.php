<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */

class InterSales_Overheat_Block_Adminhtml_Eventhandler_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'intersales_overheat';
        $this->_controller = 'adminhtml_eventhandler';
        $this->_mode = 'edit';
        $modelTitle = $this->_getModelTitle();
        $this->_updateButton('save', 'label', $this->_getHelper()->__("Save %s", $modelTitle));
        $this->_addButton('saveandcontinue', array('label' => $this->_getHelper()->__('Save and Continue Edit'), 'onclick' => 'saveAndContinueEdit()', 'class' => 'save',), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled())
        {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        }
    }

    /**
     * Retrieves the helper
     *
     * @return InterSales_Overheat_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('intersales_overheat');
    }

    /**
     * Retrieves the Model
     *
     * @return mixed
     */
    protected function _getModel()
    {
        return Mage::registry('current_eventhandler');
    }

    /**
     * Retrieves the model title
     *
     * @return string
     */
    protected function _getModelTitle()
    {
        return 'Eventhandler';
    }

    /**
     * Retrieves the header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = $this->_getModel();
        $modelTitle = $this->_getModelTitle();
        if ($model && $model->getId())
        {
            return $this->_getHelper()->__("Edit %s (ID: %d)", $modelTitle, $model->getId());
        }
        else
        {
            return $this->_getHelper()->__("New %s", $modelTitle);
        }
    }

    /**
     * Retrieves URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    /**
     * Retrieves URL for delete button
     *
     * @return string
     * @throws Exception
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    /**
     * Get form save URL
     *
     * @deprecated
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        $this->setData('form_action_url', 'save');
        return $this->getFormActionUrl();
    }
}