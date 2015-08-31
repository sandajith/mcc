<?php

class Mycloset_Membership_PickupController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        if(!empty(Mage::app()->getRequest()->getPost())){

            $userid = Mage::getSingleton('customer/session')->getId();


            $products = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToFilter('customer_name', 19);


            if (Mage::getSingleton('customer/session')->isLoggedIn() && count($products) > 0) {
                $data = array('pickup_user_id' => $userid,
                    'pickup_category_id' => Mage::app()->getRequest()->getParam('categoryname'),
                    'pickup_comment' => Mage::app()->getRequest()->getParam('comment'));
                $model = Mage::getModel('membership/pickupdetails')->setData($data);
                $model->save()->getId();
//                unset(Mage::app()->getRequest()->getPost());
                 unset($_POST);
            } else {
                $this->_redirect('/membership/account/login/');
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    
    }
    

}
