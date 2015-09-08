<?php

class Mycloset_Membership_PickupController extends Mage_Core_Controller_Front_Action {


    public function indexAction() {
        if (!empty(Mage::app()->getRequest()->getPost())) {
            $userid = Mage::getSingleton('customer/session')->getId();
            $values = Mage::app()->getRequest()->getParams();

            $products = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToFilter('customer_name', $userid);
                    exit();
            if (Mage::getSingleton('customer/session')->isLoggedIn() && count($products) > 0) {

                $final = array();
                $i = 0;
                foreach ($values['categoryname'] as $cat_id) {

                    $final[$i]['cat_id'] = $cat_id;
                    $final[$i]['quantity'] = $values['quantity'][$cat_id];

                    $i++;
                }
                $info = serialize($final);
                $data = array(
                    'pickup_user_id' => $userid,
                    'pickup_info' => $info,
                    'pickup_comment' => $values['comment']
                );
                $model = Mage::getModel('membership/pickupdetails')->setData($data);
                $model->save()->getId();
               
//               unset(Mage::app()->getRequest()->getPost());
//                unset($_POST);
                 $this->_redirect('mycloset/pickup/success');
            } else {
                $this->_redirect('/membership/account/login/');
            }
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function successAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
