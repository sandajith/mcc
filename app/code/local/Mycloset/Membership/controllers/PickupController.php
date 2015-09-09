<?php
class Mycloset_Membership_PickupController extends Mage_Core_Controller_Front_Action {

    public function IndexAction() {
        $this->loadLayout();
        if (!empty(Mage::app()->getRequest()->getPost())) {
            $userid = Mage::getSingleton('customer/session')->getId();
            $values = Mage::app()->getRequest()->getParams();
            $products = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToFilter('customer_name', $userid);
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
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
                $this->_redirect('mycloset/pickup/success');
            } else {
                $this->_redirect('/membership/account/login/');
            }
        }
        $this->renderLayout();
    }

    public function successAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
