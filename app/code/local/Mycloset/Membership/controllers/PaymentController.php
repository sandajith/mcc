<?php

class Mycloset_Membership_PaymentController extends Mage_Core_Controller_Front_Action {

    const PATH_GATE_THRESHOLD = 'membership/general/threshold';
    const PATH_API_LOGIN = 'membership/general/apilogin';
    const PATH_TRANS_KEY = 'membership/general/transkey';

    public function IndexAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock("head")->setTitle($this->__("Choose your Payment"));
        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Mycloset"),
            "title" => $this->__("My closet"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("payment", array(
            "label" => $this->__("Payment"),
            "title" => $this->__("payment")
        ));

        $this->renderLayout();
    }

    public function GetpriceAction() {
        $value = Mage::app()->getRequest()->getParam('memid');
        $model = Mage::getModel('membership/types')->load($value);
        $mode = $model->getMembershipPrice();
        echo $mode;
    }

    public function confirmpaymentAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function authorizepaymentAction() {
       // echo Mage::getStoreConfig(self::PATH_TRANS_KEY);
        require 'authorise_payment/autoload.php';
        define("AUTHORIZENET_API_LOGIN_ID", Mage::getStoreConfig(self::PATH_API_LOGIN));
        define("AUTHORIZENET_TRANSACTION_KEY", Mage::getStoreConfig(self::PATH_TRANS_KEY));

        // Create new customer profile
        $request = new AuthorizeNetCIM;
        $customerProfile = new AuthorizeNetCustomer;
        $customerProfile->description = $this->getRequest()->getPost('mem_type') . " of " . Mage::getSingleton('customer/session')->getMemFname();
        $customerProfile->merchantCustomerId = time() . rand(1, 100);
        $customerProfile->email = Mage::getSingleton('customer/session')->getMemEmail();

        $response = $request->createCustomerProfile($customerProfile);
        $customerProfileId = $response->getCustomerProfileId();
        echo $customerProfileId;

        // Add payment profile.
        $paymentProfile = new AuthorizeNetPaymentProfile;
        $paymentProfile->customerType = "individual";
        $paymentProfile->payment->creditCard->cardNumber = $this->getRequest()->getPost('x_card_num');
        $paymentProfile->payment->creditCard->expirationDate = $this->getRequest()->getPost('card_exp_year') . '-' . $this->getRequest()->getPost('card_exp_month');
        $response = $request->createCustomerPaymentProfile($customerProfileId, $paymentProfile);
        //$this->assertTrue($response->isOk());
        $paymentProfileId = $response->getPaymentProfileId();

        //Add Shipping address profile
        $address = new AuthorizeNetAddress;
        $address->firstName = Mage::getSingleton('customer/session')->getMemFname();
        $address->lastName = Mage::getSingleton('customer/session')->getMemLname();
        $address->company = Mage::getSingleton('customer/session')->getMemCompany();
        $address->address = Mage::getSingleton('customer/session')->getMemStreet1();
        $address->city = Mage::getSingleton('customer/session')->getMemCity();
        $address->state = "";
        $address->zip = Mage::getSingleton('customer/session')->getMemZip();
        $address->country = "";
        $address->phoneNumber = Mage::getSingleton('customer/session')->getMemCustele();
        $address->faxNumber = "";
        $customerProfile->shipToList[] = $address;
        $response = $request->createCustomerShippingAddress($customerProfileId, $address);
        $customerAddressId = $response->getCustomerAddressId();

        //Payment
        $transaction = new AuthorizeNetTransaction;
        $transaction->amount = $this->getRequest()->getPost('amt');
        $transaction->customerProfileId = $customerProfileId;
        $transaction->customerPaymentProfileId = $paymentProfileId;
        $transaction->customerShippingAddressId = $customerAddressId;

        $response = $request->createCustomerProfileTransaction("AuthCapture", $transaction);
        //$this->assertTrue($response->isOk());
        $transactionResponse = $response->getTransactionResponse();
        // $this->assertTrue($transactionResponse->approved);
        $transactionId = $transactionResponse->transaction_id;
        //echo '<br>' . $transactionId;
        // Check the transaction response

        if ($response->isOk()) {

            //Saving transaction response in database table
            $data = array('customer_id' => Mage::getSingleton('customer/session')->getMemID(), 'customer_profile_id' => $customerProfileId, 'payment_profile_id' => $paymentProfileId, 'shipping_address_id' => $customerAddressId, 'membership_type' => $this->getRequest()->getPost('mem_type'), 'amount' => $transaction->amount);
            $model = Mage::getModel('membership/payment')->setData($data);
            $insertId = $model->save()->getId();
            //logging-in the customer after successful payment
            $session = $this->_getSession();
            $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getMemID());
            $session->setCustomerAsLoggedIn($customer);
            $this->_redirect('mycloset/payment/confirmpayment');
        } else {
            echo "FAILED";
            echo $response->xml->resultCode;
        }
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function paymeAction() {
        require 'authorise_payment/autoload.php';
        define("AUTHORIZENET_API_LOGIN_ID", Mage::getStoreConfig(self::PATH_API_LOGIN));
        define("AUTHORIZENET_TRANSACTION_KEY", Mage::getStoreConfig(self::PATH_TRANS_KEY));

        // Check customer profile
        $request = new AuthorizeNetCIM;
        $customerProfile = new AuthorizeNetCustomer;
        $customerProfileId = $this->getRequest()->getPost('customer_pro_id');


        // Get payment profile.
        $paymentProfile = new AuthorizeNetPaymentProfile;

        $paymentProfileId = $this->getRequest()->getPost('customer_payment_id');

        //Get Shipping address profile
        $address = new AuthorizeNetAddress;
        $customerAddressId = $this->getRequest()->getPost('customer_address_id');
        //Authorise and make payment payment
        $transaction = new AuthorizeNetTransaction;
        $transaction->amount = $this->getRequest()->getPost('amount');
        $transaction->customerProfileId = $customerProfileId;
        $transaction->customerPaymentProfileId = $paymentProfileId;
        $transaction->customerShippingAddressId = $customerAddressId;
        $response = $request->createCustomerProfileTransaction("AuthCapture", $transaction);
        $transactionResponse = $response->getTransactionResponse();
        $transactionId = $transactionResponse->transaction_id;


        // Checking the transaction response and redirecting back to the admin panel

        if ($response->isOk()) {

            $data = array('customer_id' => $this->getRequest()->getPost('customer_entity_id'), 'transaction_id' => $transactionId, 'transaction_amount' => $transaction->amount);
            $model = Mage::getModel('membership/paymenthistory')->setData($data);
            $insertId = $model->save()->getId();
            $path = $this->getRequest()->getPost('return_url') . '?q=success' . '&tranid=' . $transactionId;
            $this->_redirectUrl($path);
        } else {
            $path = $this->getRequest()->getPost('return_url') . '?q=error';
            $this->_redirectUrl($path);
        }
    }

}
