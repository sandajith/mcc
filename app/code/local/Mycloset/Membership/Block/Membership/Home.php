<?php

class Mycloset_Membership_Block_Membership_Home extends Mage_Core_Block_Template {

    public function getUsername() {
return $username = Mage::getSingleton('customer/session')->getCustomer()->getName();

    }

}
