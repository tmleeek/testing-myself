<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     370
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Block_Adminhtml_Order_Customer
    extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $this->getChart()
            ->setXAxisType('category')
            ->setXAxisField('customer_name');

        $this->getGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setPagerVisibility(true)
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Customer'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $orders = Mage::getResourceModel('advr/order_collection');

        if ($filterData->getFilterBySku()) {
            $orders = Mage::getResourceModel('advr/order_item_collection');
            $orders->addFilterBySku($filterData->getFilterBySku());
        }

        $orders
            ->setFilterData($filterData)
            ->joinCustomerGroup()
            ->joinBillingAddress()
            ->groupByCustomer()
            ;


        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($orders)
            ->setColumnGroupBy('customer_email');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'customer_email' => array(
                'header'                => 'Customer',
                'type'                  => 'text',
                'filter_index'          => 'customer_email',
                'totals_label'          => 'Total',
                'filter_totals_label'   => 'Subtotal',
                'frame_callback'        => array(Mage::helper('advr/callback'), 'linkToCustomer'),
                'chart'                 => true,
            ),

            'customer_name' => array(
                'header'                => 'Customer Name',
                'type'                  => 'text',
                'filter'                => false,
                'totals_label'          => '',
                'filter_totals_label'   => '',
            ),

            'customer_group_code' => array(
                'header'               => 'Customer Group',
                'type'                 => 'text',
                'index'                => 'customer_group_code',
                'totals_label'         => '',
                'filter_totals_label'  => '',
                'chart'                => false,
            ),

            'percent' => array(
                'header'          => 'Number Of Orders, %',
                'type'            => 'percent',
                'filter'          => false,
                'index'           => 'quantity',
                'frame_callback'  => array(Mage::helper('advr/callback'), 'percent'),
                'export_callback' => array(Mage::helper('advr/callback'), '_percent'),
            ),
        );

        $columns += $this->getBaseColumns();

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $row->getCustomerId()));
    }
}