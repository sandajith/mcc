<?php

class Mycloset_Membership_PaymentController extends Mage_Core_Controller_Front_Action {

    const PATH_GATE_THRESHOLD = 'membership/general/threshold';
    const PATH_API_LOGIN = 'membership/general/apilogin';
    const PATH_TRANS_KEY = 'membership/general/transkey';
    const PATH_GATE_URL = 'membership/general/gatewayurl';

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
        $g_loginname = Mage::getStoreConfig(self::PATH_API_LOGIN); // Keep this secure.
        $g_transactionkey = Mage::getStoreConfig(self::PATH_TRANS_KEY); // Keep this secure.
        $g_apihost = Mage::getStoreConfig(self::PATH_GATE_URL);
        $g_apipath = "/xml/v1/request.api";
        require_once (Mage::getBaseDir('code') . '/local/Mycloset/Membership/Api/util.php');

        // Create new customer profile
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                "<profile>" .
                "<merchantCustomerId>" . time() . rand(1, 100) . "</merchantCustomerId>" . // Your own identifier for the customer.
                "<description>" . $this->getRequest()->getPost('mem_type') . " of " . Mage::getSingleton('customer/session')->getMemFname() . "</description>" .
                "<email>" . Mage::getSingleton('customer/session')->getMemEmail() . "</email>" .
                "</profile>" .
                "</createCustomerProfileRequest>";


        $response = send_xml_request($g_apihost, $g_apipath, $content);

        $parsedresponse = parse_api_response($response);
        $parsed_customer_id = $parsedresponse->customerProfileId;


        // Add payment profile

        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<createCustomerPaymentProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                "<customerProfileId>" . $parsed_customer_id . "</customerProfileId>" .
                "<paymentProfile>" .
                "<billTo>" .
                "<firstName>" . Mage::getSingleton('customer/session')->getMemFname() . "</firstName>" .
                "<lastName>" . Mage::getSingleton('customer/session')->getMemLname() . "</lastName>" .
                "<phoneNumber>" . Mage::getSingleton('customer/session')->getMemCustele() . "</phoneNumber>" .
                "</billTo>" .
                "<payment>" .
                "<creditCard>" .
                "<cardNumber>" . $this->getRequest()->getPost('x_card_num') . "</cardNumber>" .
                "<expirationDate>" . $this->getRequest()->getPost('card_exp_year') . '-' . $this->getRequest()->getPost('card_exp_month') . "</expirationDate>" . // required format for API is YYYY-MM
                "</creditCard>" .
                "</payment>" .
                "</paymentProfile>" .
                "<validationMode>none</validationMode>" . // or testMode
                "</createCustomerPaymentProfileRequest>";


        $response = send_xml_request($g_apihost, $g_apipath, $content);

        $parsedresponse = parse_api_response($response);
        $parsed_paymentprofile_id = $parsedresponse->customerPaymentProfileId;


//Add Shipping address profile
        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<createCustomerShippingAddressRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                "<customerProfileId>" . $parsed_customer_id . "</customerProfileId>" .
                "<address>" .
                "<firstName>" . Mage::getSingleton('customer/session')->getMemFname() . "</firstName>" .
                "<lastName>" . Mage::getSingleton('customer/session')->getMemLname() . "</lastName>" .
                "<company>" . Mage::getSingleton('customer/session')->getMemCompany() . "</company>" .
                "<phoneNumber>" . Mage::getSingleton('customer/session')->getMemCustele() . "</phoneNumber>" .
                "</address>" .
                "</createCustomerShippingAddressRequest>";


        $response = send_xml_request($g_apihost, $g_apipath, $content);

        $parsedresponse = parse_api_response($response);
        $parsed_address_id = $parsedresponse->customerAddressId;



        //Making a payment for the customerprofileid

        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                "<transaction>" .
                "<profileTransAuthOnly>" .
                "<amount>" . $this->getRequest()->getPost('amt') . "</amount>" . // should include tax, shipping, and everything.
                "<shipping>" .
                "<amount>0.00</amount>" .
                "<name>Free Shipping</name>" .
                "<description>Mycloset membership</description>" .
                "</shipping>" .
                "<lineItems>" .
                "<itemId>" . time() . "</itemId>" .
                "<name>" . $this->getRequest()->getPost('mem_type') . "</name>" .
                "<description>" . $this->getRequest()->getPost('mem_type') . " of " . Mage::getSingleton('customer/session')->getMemFname() . "</description>" .
                "<quantity>1</quantity>" .
                "<unitPrice>" . $this->getRequest()->getPost('amt') . "</unitPrice>" .
                "<taxable>false</taxable>" .
                "</lineItems>" .
                "<customerProfileId>" . $parsed_customer_id . "</customerProfileId>" .
                "<customerPaymentProfileId>" . $parsed_paymentprofile_id . "</customerPaymentProfileId>" .
                "<customerShippingAddressId>" . $parsed_address_id . "</customerShippingAddressId>" .
                "<order>" .
                "<invoiceNumber>" . "MCC" . $parsed_customer_id . "</invoiceNumber>" .
                "</order>" .
                "</profileTransAuthOnly>" .
                "</transaction>" .
                "</createCustomerProfileTransactionRequest>";

        //echo "Raw request: " . htmlspecialchars($content) . "<br><br>";
        $response = send_xml_request($g_apihost, $g_apipath, $content);
        //  echo "Raw response: " . htmlspecialchars($response) . "<br><br>";
        $parsedresponse = parse_api_response($response);
//        if ("Ok" == $parsedresponse_payment->messages->resultCode) {
//            echo "A transaction was successfully created for customerProfileId <b>"
//            . htmlspecialchars($_POST["customerProfileId"])
//            . "</b>.<br><br>";
//        }
        if (isset($parsedresponse->directResponse)) {
//            echo "direct response: <br>"
//            . htmlspecialchars($parsedresponse_payment->directResponse)
//            . "<br><br>";

            $directResponseFields = explode(",", $parsedresponse->directResponse);
            $responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
            $responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
            $responseReasonText = $directResponseFields[3];
            $approvalCode = $directResponseFields[4]; // Authorization code
            $transId = $directResponseFields[6];
            //Variables to send e-mail
            $z_firstname = Mage::getSingleton('customer/session')->getMemFname();
            $z_lastname = Mage::getSingleton('customer/session')->getMemLname();
            $z_email = Mage::getSingleton('customer/session')->getMemEmail();
            $z_memtype = $this->getRequest()->getPost('mem_type');
            $z_amount = $this->getRequest()->getPost('amt');

            if ("1" == $responseCode) {
                //Email sending to the customer upon successful payment
                $templateId = 'Success Mycloset Registration';
                $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
                $vars = array('first_name' => $z_firstname, 'last_name' => $z_lastname, 'email' => $z_email, 'mem_type' => $z_memtype, 'mem_amt' => $z_amount);
                $emailTemplate->getProcessedTemplate($vars);
                $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
               // $email = array($admin_email,$z_email);
                $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
                $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
                $emailTemplate->send($z_email, 'Mycloset mail', $vars);
                $emailTemplate->send($admin_email, 'Mycloset mail', $vars);
                //
                $date = date("Y-m-d H:i:s ", time());
                $data = array('customer_id' => Mage::getSingleton('customer/session')->getMemID(), 'customer_profile_id' => $parsed_customer_id, 'payment_profile_id' => $parsed_paymentprofile_id, 'shipping_address_id' => $parsed_address_id,'membership_id' => $this->getRequest()->getPost('mem_table_id'), 'created_at' => $date);
                $model = Mage::getModel('membership/payment')->setData($data);
                $insertId = $model->save()->getId();
                //logging-in the customer after successful payment
                $session = $this->_getSession();
                $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getMemID());
                //changing customer group
                $customerid = Mage::getSingleton('customer/session')->getMemID();
                $update = array(
                    'entity_id' => $customerid,
                    'group_id' => '1'
                );
                $model = Mage::getModel('customer/customer')->load($customerid)->addData($update);
                try {
                    $model->setId($customerid)->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                //changing customer group end
                $session->setCustomerAsLoggedIn($customer);
                $this->_redirect('mycloset/payment/confirmpayment');

                //echo "The transaction was successful.<br>";
            } else if ("2" == $responseCode) {
                $log_path = Mage::getBaseDir('code') . '/local/Mycloset/Membership/Api/authorizenet.txt';
                $fp = fopen($log_path, 'a+');
                fwrite($fp, print_r($parsedresponse, true));
                fclose($fp);
                $message = "The transaction was declined.<br>";
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('mycloset/payment');
            } else { 
                $message = "Payment failed !" . htmlspecialchars($responseReasonText) . "<br>";
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('mycloset/payment');
                //echo "The transaction resulted in an error.<br>";
                //echo "responseReasonCode = " . htmlspecialchars($responseReasonCode) . "<br>";
                //echo "responseReasonText = " . htmlspecialchars($responseReasonText) . "<br>";
                //echo "approvalCode = " . htmlspecialchars($approvalCode) . "<br>";
                //echo "transId = " . htmlspecialchars($transId) . "<br>";
            }
        }
    }

    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    public function paymeAction() {
        $g_loginname = Mage::getStoreConfig(self::PATH_API_LOGIN); // Keep this secure.
        $g_transactionkey = Mage::getStoreConfig(self::PATH_TRANS_KEY); // Keep this secure.
        $g_apihost = Mage::getStoreConfig(self::PATH_GATE_URL);
        $g_apipath = "/xml/v1/request.api";
        require_once (Mage::getBaseDir('code') . '\local\Mycloset\Membership\Api\util.php');

        $content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
                MerchantAuthenticationBlock($g_loginname, $g_transactionkey) .
                "<transaction>" .
                "<profileTransAuthOnly>" .
                "<amount>" . $this->getRequest()->getPost('amount') . "</amount>" . // should include tax, shipping, and everything.
                "<shipping>" .
                "<amount>0.00</amount>" .
                "<name>Free Shipping</name>" .
                "<description>Mycloset membership</description>" .
                "</shipping>" .
                "<lineItems>" .
                "<itemId>" . time() . "</itemId>" .
                "<name>" . $this->getRequest()->getPost('mem_type_name') . "</name>" .
                "<description>Membership Renewal/Upgrade</description>" .
                "<quantity>1</quantity>" .
                "<unitPrice>" . $this->getRequest()->getPost('amount') . "</unitPrice>" .
                "<taxable>false</taxable>" .
                "</lineItems>" .
                "<customerProfileId>" . $this->getRequest()->getPost('customer_pro_id') . "</customerProfileId>" .
                "<customerPaymentProfileId>" . $this->getRequest()->getPost('customer_payment_id') . "</customerPaymentProfileId>" .
                "<customerShippingAddressId>" . $this->getRequest()->getPost('customer_address_id') . "</customerShippingAddressId>" .
                "<order>" .
                "<invoiceNumber>" . "MCC" . $this->getRequest()->getPost('customer_address_id') . "</invoiceNumber>" .
                "</order>" .
                "</profileTransAuthOnly>" .
                "</transaction>" .
                "</createCustomerProfileTransactionRequest>";

        //echo "Raw request: " . htmlspecialchars($content) . "<br><br>";
        $response = send_xml_request($g_apihost, $g_apipath, $content);
        //  echo "Raw response: " . htmlspecialchars($response) . "<br><br>";
        $parsedresponse = parse_api_response($response);
//        if ("Ok" == $parsedresponse_payment->messages->resultCode) {
//            echo "A transaction was successfully created for customerProfileId <b>"
//            . htmlspecialchars($_POST["customerProfileId"])
//            . "</b>.<br><br>";
//        }
        if (isset($parsedresponse->directResponse)) {
//            echo "direct response: <br>"
//            . htmlspecialchars($parsedresponse_payment->directResponse)
//            . "<br><br>";

            $directResponseFields = explode(",", $parsedresponse->directResponse);
            $responseCode = $directResponseFields[0]; // 1 = Approved 2 = Declined 3 = Error
            $responseReasonCode = $directResponseFields[2]; // See http://www.authorize.net/support/AIM_guide.pdf
            $responseReasonText = $directResponseFields[3];
            $approvalCode = $directResponseFields[4]; // Authorization code
            $transId = $directResponseFields[6];

            if ("1" == $responseCode) {
                $data = array('customer_id' => $this->getRequest()->getPost('customer_entity_id'), 'transaction_id' => $transId, 'transaction_amount' => $this->getRequest()->getPost('amount'));
                $model = Mage::getModel('membership/paymenthistory')->setData($data);
                $insertId = $model->save()->getId();
                $customerid = Mage::getModel('membership/payment')->load($this->getRequest()->getPost('customer_entity_id'), 'customer_id');
                $payment_id = $customerid->getPaymentId();
                $path = $this->getRequest()->getPost('return_url') . '?q=success' . '&tranid=' . $transId;
                $this->_redirectUrl($path);
            } else if ("2" == $responseCode) {
                $path = $this->getRequest()->getPost('return_url') . '?q=error';
                $this->_redirectUrl($path);
            } else {
                $path = $this->getRequest()->getPost('return_url') . '?q=error';
                $this->_redirectUrl($path);
                //echo "The transaction resulted in an error.<br>";
                //echo "responseReasonCode = " . htmlspecialchars($responseReasonCode) . "<br>";
                //echo "responseReasonText = " . htmlspecialchars($responseReasonText) . "<br>";
                //echo "approvalCode = " . htmlspecialchars($approvalCode) . "<br>";
                //echo "transId = " . htmlspecialchars($transId) . "<br>";
            }
        }
    }

    public function settingsAction() {
        $customerid = $this->getRequest()->getPost('customer_entity_id');
        $customer_pay_id = Mage::getModel('membership/payment')->load($this->getRequest()->getPost('customer_entity_id'), 'customer_id');
        $payment_id = $customer_pay_id->getPaymentId();

        if ($this->getRequest()->getPost('mem_plan') == 1000) {

            $update = array(
                'entity_id' => $customerid,
                'group_id' => '6'
            );
            $model = Mage::getModel('customer/customer')->load($customerid)->addData($update);
            try {
                $model->setId($customerid)->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            $path = $this->getRequest()->getPost('return_url') . '?q=deact';
            $this->_redirectUrl($path);
        } elseif ($this->getRequest()->getPost('mem_plan') == 1111) {

            $update = array(
                'entity_id' => $customerid,
                'group_id' => '1'
            );
            $model = Mage::getModel('customer/customer')->load($customerid)->addData($update);
            try {
                $model->setId($customerid)->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            $path = $this->getRequest()->getPost('return_url') . '?q=act';
            $this->_redirectUrl($path);
        } elseif (($this->getRequest()->getPost('mem_plan') == 1) || ($this->getRequest()->getPost('mem_plan') == 2)||($this->getRequest()->getPost('mem_plan') == 3)) {
            $memtype = Mage::getModel('membership/membership')->load($this->getRequest()->getPost('mem_plan'), 'membership_id');
            $membershiptype = $memtype->getMembershipType();
            $membershipprice = $memtype->getMembershipPrice();


            $update = array(
                'membership_type' => $membershiptype,
                'amount' => $membershipprice
            );
            $model = Mage::getModel('membership/payment')->load($payment_id)->addData($update);
            try {
                $model->setId($payment_id)->save();
                //echo "Data updated successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            $path = $this->getRequest()->getPost('return_url') . '?q=saved';
            $this->_redirectUrl($path);
        } elseif ($this->getRequest()->getPost('mem_plan') == 4) {
            $membership = 'Membership Closed';
            //changing customer group
            $update = array(
                'entity_id' => $customerid,
                'group_id' => '5'
            );
            $model = Mage::getModel('customer/customer')->load($customerid)->addData($update);
            try {
                $model->setId($customerid)->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            //changing customer group end
            $path = $this->getRequest()->getPost('return_url') . '?q=closed';
            $this->_redirectUrl($path);
        }
    }

    public function testAction() {
        $templateId = 'Success Mycloset Registration';
        $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
        $vars = array('first_name' => $z_firstname, 'last_name' => $z_lastname, 'email' => $z_email, 'mem_type' => $z_memtype, 'mem_amt' => $z_amount);
        $emailTemplate->getProcessedTemplate($vars);
        $admin_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $email = array('sreedarsh.bridge@gmail.com','sreedarsh88@gmail.com');
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
        $emailTemplate->send('sreedarsh88@gmail.com', 'Mycloset mail', $vars);
        $emailTemplate->send('sreedarsh.bridge@gmail.com', 'Mycloset mail', $vars);
    }

    public function neeAction() {
        echo Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
//        $templateId = 'Pickup mail to user';
//        $emailTemplate = Mage::getModel('core/email_template')->loadByCode($templateId);
//        $vars = array('user_name' => 'sreedarsh');
//        $emailTemplate->getProcessedTemplate($vars);
//        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email', $storeId));
//        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
//        $emailTemplate->send('sreedarsh88@gmail.com', 'Neenu', $vars);
    }

}
