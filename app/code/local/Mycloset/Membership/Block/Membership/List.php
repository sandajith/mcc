<?php

class Mycloset_Membership_Block_Membership_List extends Mage_Core_Block_Template {
    
    const PATH_GATE_URL = 'membership/general/gatewayurl';
    const PATH_API_LOGIN = 'membership/general/apilogin';
    const PATH_MER_EMAIL = 'membership/general/merchantmail';
    const PATH_TRANS_KEY = 'membership/general/transkey';
   
    
   
    public function getMemberships() {

        $model = Mage::getModel('membership/types');
        $data = $model->getCollection()->getData();
        $mem = '<option value="">Select membership</option>';
        foreach ($data as $value) {
            $mem .= '<option value=' . $value['membership_id'] . '>' . $value['membership_type'] . "-" . $value['membership_price'] . '</option>';
        }

        return $mem;
    }

    public function getChosenplan() {
        $model = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getMemId());
        $memtypeid = $model->getMemType();
        $one = Mage::getModel('membership/types')->load($memtypeid);
        $id = $one->getMembershipId();
        $price = $one->getMembershipPrice();
        $plan = $one->getMembershipType();
        return $price.'-'.$plan.'-'.$id;
    }
    
    public function getSettings() {
     $setting['a'] = Mage::getStoreConfig(self::PATH_GATE_URL);
     $setting['b'] = Mage::getStoreConfig(self::PATH_API_LOGIN);
     $setting['c'] = Mage::getStoreConfig(self::PATH_MER_EMAIL);
     $setting['d'] = Mage::getStoreConfig(self::PATH_TRANS_KEY);
     //return Mage::getStoreConfig(self::PATH_TRANS_KEY);
     return $setting;
     
    }

}
//Mage::getSingleton('customer/session')->getMemId();