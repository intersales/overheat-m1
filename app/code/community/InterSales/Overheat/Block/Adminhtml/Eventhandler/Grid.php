<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */
class InterSales_Overheat_Block_Adminhtml_Eventhandler_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('intersales_overheat_eventhandler_grid');
        $this->setDefaultSort('eventhandler_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('intersales_overheat/eventhandler_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('eventhandler_id', array(
                'header' => Mage::helper('intersales_overheat')->__('ID'),
                'width' => '40px',
                'index' => 'eventhandler_id')
        );

        $this->addColumn('identifier', array(
                'header' => Mage::helper('intersales_overheat')->__('Identifier'),
                'index' => 'identifier')
        );

        $this->addColumn('trigger_type', array(
                'header' => Mage::helper('intersales_overheat')->__('Trigger Type'),
                'index' => 'trigger_type')
        );

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('intersales_overheat')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('intersales_overheat')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $modelPk = Mage::getModel('intersales_overheat/eventhandler')->getResource()->getIdFieldName();
        $this->setMassactionIdField($modelPk);
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('intersales_overheat')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete')
        ));

        return $this;
    }
}