<?php

class Wsnyc_Homepagebanner_Model_Mysql4_Banner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('wsnyc_homepagebanner/banner');
    }

}
