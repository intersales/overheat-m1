<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */

class InterSales_Overheat_Adminhtml_Intersales_Overheat_EventhandlerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('intersales_overheat/adminhtml_eventhandler'));
        $this->renderLayout();
    }

    /**
     * Mass Delete Action
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');

        if (!is_array($ids))
        {
            $this->_getSession()->addError($this->__('Please select Eventhandler(s).'));
        }
        else
        {
            try
            {
                foreach ($ids as $id)
                {
                    $model = Mage::getModel('intersales_overheat/eventhandler')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($ids)));
            }
            catch (Mage_Core_Exception $e)
            {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e)
            {
                $this->_getSession()->addError(Mage::helper('intersales_overheat')->__('An error occurred while mass deleting items. Please review log and try again.'));
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Edit Action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('intersales_overheat/eventhandler');

        Mage::log("Test: " . get_class($model));

        if ($id)
        {
            $model->load($id);
            if (!$model->getId())
            {
                $this->_getSession()->addError(Mage::helper('intersales_overheat')->__('This Eventhandler no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data))
        {
            $model->setData($data);
        }

        Mage::register('current_eventhandler', $model);

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('intersales_overheat/adminhtml_eventhandler_edit'));
        $this->renderLayout();
    }

    /**
     * New Action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost())
        {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('intersales_overheat/eventhandler');
            if ($id)
            {
                $model->load($id);
                if (!$model->getId())
                {
                    $this->_getSession()->addError(Mage::helper('intersales_overheat')->__('This Eventandler no longer exists.'));
                    $this->_redirect('*/*/index');
                    return;
                }
            }

            // save model
            try
            {
                $model->addData($data);
                $model->save();
                $this->_getSession()->setFormData($data);
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(Mage::helper('intersales_overheat')->__('The Eventhandler has been saved.'));
            }
            catch (Mage_Core_Exception $e)
            {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
            catch (Exception $e)
            {
                $this->_getSession()->addError(Mage::helper('intersales_overheat')->__('Unable to save the Eventhandler.'));
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack)
            {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/index');
    }
}