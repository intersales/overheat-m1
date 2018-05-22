<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */

class InterSales_Overheat_Block_Adminhtml_Eventhandler_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Retrieve model
     *
     * @return mixed
     */
    protected function _getModel()
    {
        return Mage::registry('current_eventhandler');
    }

    /**
     * Retrieve helper
     *
     * @return InterSales_GratulationPage_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('intersales_overheat');
    }

    /**
     * Retrieve model title
     *
     * @return string
     */
    protected function _getModelTitle()
    {
        return 'Eventhandler';
    }

    /**
     * Prepare form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = $this->_getModel();
        $modelTitle = $this->_getModelTitle();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->_getHelper()->__("%s Information", $modelTitle),
            'class' => 'fieldset-wide'
        ));

        if ($model && $model->getId())
        {
            $modelPk = $model->getResource()->getIdFieldName();
            $fieldset->addField($modelPk, 'hidden', array('name' => $modelPk,));
        }

        //        $fieldset->addField('name', 'text' /* select | multiselect | hidden | password | ...  */, array(
        //            'name'      => 'name',
        //            'label'     => $this->_getHelper()->__('Label here'),
        //            'title'     => $this->_getHelper()->__('Tooltip text here'),
        //            'required'  => true,
        //            'options'   => array( OPTION_VALUE => OPTION_TEXT, ),                 // used when type = "select"
        //            'values'    => array(array('label' => LABEL, 'value' => VALUE), ),    // used when type = "multiselect"
        //            'style'     => 'css rules',
        //            'class'     => 'css classes',
        //        ));
        //          // custom renderer (optional)
        //          $renderer = $this->getLayout()->createBlock('Block implementing Varien_Data_Form_Element_Renderer_Interface');
        //          $field->setRenderer($renderer);

        //      // New Form type element (extends Varien_Data_Form_Element_Abstract)
        //        $fieldset->addType('custom_element','MyCompany_MyModule_Block_Form_Element_Custom');  // you can use "custom_element" as the type now in ::addField([name], [HERE], ...)

        if ($model->getBlockId()) {
            $fieldset->addField('eventhandler_id', 'hidden', array(
                'name' => 'eventhandler_id',
            ));
        }

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => $this->_getHelper()->__('Identifier'),
            'title'     => $this->_getHelper()->__('Identifier'),
            'required'  => true,
        ));

        $fieldset->addField('observer_name', 'text', array(
            'name'      => 'observer_name',
            'label'     => $this->_getHelper()->__('Observer Handler Name'),
            'title'     => $this->_getHelper()->__('Observer Handler Name'),
            'required'  => false,
        ));

        $fieldset->addField('block_selector', 'textarea', array(
            'name'      => 'block_selector',
            'label'     => $this->_getHelper()->__('Block Selector'),
            'title'     => $this->_getHelper()->__('Block Selector'),
            'required'  => false,
        ));

        $fieldset->addField('css_selector', 'textarea', array(
            'name'      => 'css_selector',
            'label'     => $this->_getHelper()->__('CSS Selector'),
            'title'     => $this->_getHelper()->__('CSS Selector'),
            'required'  => false,
        ));

        $fieldset->addField('trigger_type', 'select', array(
            'name'      => 'trigger_type',
            'label'     => $this->_getHelper()->__('Trigger Type'),
            'title'     => $this->_getHelper()->__('Trigger Type'),
            'values'    => $this->_getHelper()->getTriggerTypeOptions(),
            'required'  => true,
        ));

        $fieldset->addField('custom_js', 'textarea', array(
            'name'      => 'custom_js',
            'label'     => $this->_getHelper()->__('Custom Js'),
            'title'     => $this->_getHelper()->__('Custom Js'),
            'required'  => false,
        ));

        $fieldset->addField('action_type', 'select', array(
            'name'      => 'action_type',
            'label'     => $this->_getHelper()->__('Action Type'),
            'title'     => $this->_getHelper()->__('Action Type'),
            'values'    => $this->_getHelper()->getActionOptions(),
            'required'  => true,
        ));

        if ($model)
        {
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}