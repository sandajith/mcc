<?php

class Mycloset_Membership_Block_Adminhtml_Customer_Tab2 extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Set the template for the block
     *
     */
    public function _construct() {
        parent::_construct();
        $this->setTemplate('membership/customer/tab2.phtml');
    }

    public function getCustomerpayments() {

        $customer = Mage::registry('current_customer');
        // $model = Mage::getModel('membership/payment')->load($customer['entity_id']);
        $model = Mage::getModel('membership/payment')->load($customer['entity_id'], 'customer_id');
        $customer_pro_id = $model->getCustomerProfileId();
        $customer_payment_id = $model->getPaymentProfileId();
        $customer_address_id = $model->getShippingAddressId();
        $membership_id = $model->getMembershipId();
        // $timestamp = $model->getTimestamp();
        //$acttime = strtotime($timestamp);

        $model2 = Mage::getModel('membership/membership')->load($membership_id, 'membership_id');
        $getamount = $model2->getMembershipPrice();
        $getmembership_type = $model2->getMembershipType();
        if ($customer_payment_id == NULL) {
            return 'The customer has not made any payments';
        }
        return $customer_pro_id . '-' . $customer_payment_id . '-' . $customer_address_id . '-' . $membership_id . '-' . $getmembership_type . '-' . $getamount;
    }

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->__('Membership settings');
    }

    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->__('Cutomer settings section');
    }

    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden() {
        return false;
    }

}
