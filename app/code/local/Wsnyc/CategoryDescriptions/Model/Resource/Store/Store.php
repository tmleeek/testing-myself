<?php

class Wsnyc_CategoryDescriptions_Model_Resource_Store_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    public function _construct() {        
        $this->_init('wsnyc_categorydescriptions/store');
    }
}