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


class Mirasvit_Advd_Model_Dashboard extends Varien_Object
{
    protected $_dashboard = null;

    public function _construct()
    {
        return $this;
    }

    public function load($id)
    {
        $this->setId($id);

        $this->_dashboard = Mage::helper('advd')->getVariable($this->getVariableCode());

        if (!is_array($this->_dashboard)) {
            $this->_dashboard = array();
        }

        return $this;
    }

    public function getVariableCode()
    {
        return 'dashboard_'.$this->getId();
    }

    public function getDashboard()
    {
        return $this->_dashboard;
    }

    public function setDashboard($dashboard)
    {
        $this->_dashboard = $dashboard;

        return $this;
    }

    public function isEditable()
    {
        if ($this->getId() == 'global') {
            return Mage::getSingleton('admin/session')->isAllowed('dashboard/advd_dashboard_global/actions/manage');
        }

        return true;
    }

    public function save()
    {
        Mage::helper('advd')->setVariable($this->getVariableCode(), $this->_dashboard);

        return $this;
    }

    public function updateWidget($id, $params)
    {
        $widget = $this->getWidget($id);

        foreach ($params as $key => $value) {
            $widget[$key] = $value;
        }
        
        $this->_dashboard[$id] = $widget;

        return $this->save();
    }

    public function loadWidget($id)
    {
        $block = $this->_getWidgetBlock($id);
        
        return array(
            'title'    => $block->getWidgetTitle(),
            'content'  => $block->toHtml(),
            'settings' => true,
        );
    }

    public function loadWidgetSettings($id, $type = null)
    {
        if ($type) {
            $block = $this->_getWidgetBlockByType($type);
        } else {
            $block = $this->_getWidgetBlock($id);
        }

        return $block->getConfigurationForm();
    }

    protected function _getWidgetBlock($id)
    {
        $params = $this->getWidget($id);

        if (isset($params['widget'])) {
            return Mage::app()->getLayout()->createBlock($params['widget'])
                ->setParams($params);
        } else {
            return Mage::app()->getLayout()->createBlock('advd/adminhtml_widget_empty')
                ->setParams($params);
        }
    }

    public function getWidget($id)
    {
        if (isset($this->_dashboard[$id])) {
            return $this->_dashboard[$id];
        }

        return array();
    }

    public function isWidgetExists($id)
    {
        if (isset($this->_dashboard[$id])) {
            return true;
        }

        return false;
    }

    protected function _getWidgetBlockByType($type)
    {
        $params = array();

        return Mage::app()->getLayout()->createBlock($type)
            ->setParams($params);
    }
}