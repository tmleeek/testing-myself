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


class Mirasvit_Advr_Block_Adminhtml_Product_Bestseller
    extends Mirasvit_Advr_Block_Adminhtml_Product_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getChart()
            ->setXAxisType('category')
            ->setXAxisField('product_name');

        $this->getGrid()
            ->setDefaultSort('sum_row_total')
            ->setDefaultDir('desc')
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Bestsellers'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $categories = Mage::getSingleton('advr/system_config_source_category')->toOptionArray();

        $products = Mage::getResourceModel('advr/product_collection')
            ->setFilterData($filterData);

        if ($this->getFilterData()->getGroupByParent()) {
            $products->joinParentProduct(array('entity_id', 'sku'))
                ->joinOrderItem()
                ->joinOrder()
                ->joinProductName()
                ;

            $categories ? $products->joinCategoryId() : $products->joinCategoryName();
            $products->groupByParentProduct();
        } elseif ($this->getFilterData()->getGroupBySimple()) {
            $products->joinProduct(array('entity_id', 'sku'))
                ->joinOrderItem()
                ->joinOrder()
                ->joinProductName()
                ;

            $categories ? $products->joinCategoryId() : $products->joinCategoryName();
            $products->groupBySimpleProduct();
        } else {
            $products->joinProduct(array('entity_id', 'sku'))
                ->joinOrderItem()
                ->joinOrder()
                ->joinProductName()
                ;

            $categories ? $products->joinCategoryId() : $products->joinCategoryName();
            $products->groupByProduct();
        }

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($products)
            ->setColumnGroupBy('entity_id');
        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'sku' => array(
                'header'              => 'SKU',
                'type'                => 'text',
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'filter_index'        => 'main_table.sku',
                'sortable'            => true,
            ),

            'product_name' => array(
                'header'         => 'Product',
                'type'           => 'text',
                'filter_index'   => 'product_name.value',
                'totals_label'   => '',
                'sortable'       => true,
            ),
        );

        $categories = Mage::getSingleton('advr/system_config_source_category')->toOptionArray();
        if ($categories) {
            $columns['category'] = array(
                'header'          => 'Category',
                'type'            => 'options',
                'options'         => Mage::getSingleton('advr/system_config_source_category')->toOptionArray(),
                'filter_index'    => 'category',
                'totals_label'    => '',
                'sortable'        => true,
                'frame_callback'  => array(Mage::helper('advr/callback'), 'multiCategory'),
                'export_callback' => array(Mage::helper('advr/callback'), 'multiCategory'),
            );
        } else {            
            $columns['category'] = array(
                'header'         => 'Category',
                'type'           => 'text',
                'filter_index'   => 'category',
                'totals_label'   => '',
                'sortable'       => true,
            );
        }

        $columns['percent'] = array(
            'header'          => 'Total, %',
            'type'            => 'percent',
            'index'           => 'sum_row_total',
            'frame_callback'  => array(Mage::helper('advr/callback'), 'percent'),
            'filter'          => false,
            'export_callback' => array(Mage::helper('advr/callback'), '_percent'),
        );

        $columns += $this->getBaseColumns();

        $columns['gross_profit'] = array(
            'header'          => 'Gross profit',
            'type'            => 'currency',
            'index'           => 'gross_profit',
            'filter'          => false,
            'hidden'          => true,
            'sortable'        => true,
        );

        $columns['detail'] = array(
            'header'          => 'Actions',
            'type'            => 'text',
            'totals_label'    => '',
            'frame_callback'  => array($this, 'frameDetailUrlCallback'),
            'filter'          => false,
            'sortable'        => false,
        );

        return $columns;
    }

    public function _initToolbar()
    {
        parent::_initToolbar();

        $form = $this->_toolbar->getForm();

        $form->addField('group_by_parent', 'checkbox', array(
            'name'    => 'group_by_parent',
            'label'   => Mage::helper('advr')->__('Group by parent product'),
            'value'   => 1,
            'checked' => $this->getFilterData()->getGroupByParent(),
        ));

        $form->addField('group_by_simple', 'checkbox', array(
            'name'    => 'group_by_simple',
            'label'   => Mage::helper('advr')->__('Show only simple products'),
            'value'   => 1,
            'checked' => $this->getFilterData()->getGroupBySimple(),
        ));

        return $this;
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getEntityId()));
    }

    public function frameDetailUrlCallback($value, $row, $column)
    {
        $format = Mage::getSingleton('advr/config')->dateFormat();

        $from = new Zend_Date(strtotime($this->getFilterData()->getFrom()), null, Mage::app()->getLocale()->getLocaleCode());
        $to   = new Zend_Date(strtotime($this->getFilterData()->getTo()), null, Mage::app()->getLocale()->getLocaleCode());

        $filter = array(
            'from' => $from->toString($format),
            'to'   => $to->toString($format)
        );

        $filter = base64_encode(http_build_query($filter));

        $url = $this->getUrl('advradmin/adminhtml_product/productDetail', array(
                'id'     => $row->getEntityId(),
                'filter' => $filter
            )
        );

        return '<a href="'.$url.'">'.Mage::helper('advr')->__('Detail').'</a>';
    }
}