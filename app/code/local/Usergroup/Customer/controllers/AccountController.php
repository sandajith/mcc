<?php
/**
 *Customer account controller
 *
 * @package     WACI_Customer
 */

require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';

class Usergroup_Customer_AccountController extends Mage_Customer_AccountController
{

    /**
    * Create customer account action
    */
    public function createPostAction()
    {
 $customer = Mage::getSingleton('customer/session')->getCustomer();
$customer->setGroupId(4);
// contents of createPostAction(), with some extra logic

            /**
             * Initialize customer group id
             */
//  $customer->setGroupId(4);
//            /* catch groupid at account creation */
//
//            if($this->getRequest()->getPost('group_id')){ 
//                $customer->setGroupId($this->getRequest()->getPost('group_id'));
//                $customer->setGroupId(4);
//            } else {
//               // $customer->getGroupId(); 
//                 $customer->setGroupId(4);
//            } 



 // rest of method

    }
//      protected function _getCustomer()
//    {
//        $customer = $this->_getFromRegistry('current_customer');
//        if (!$customer) {
//            $customer = $this->_getModel('customer/customer')->setId(null);
//        }
//        if ($this->getRequest()->getParam('is_subscribed', false)) {
//            $customer->setIsSubscribed(1);
//        }
//        /**
//         * Initialize customer group id
//         */
//        $customer->setGroupId(4);
//
//        return $customer;
//    }
protected function _getCustomer()
    {
        $customer = $this->_getFromRegistry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->setGroupId(4);

        return $customer;
    }
}