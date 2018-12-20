<?php
/**
 * WHMCS Sample Payment Gateway Module
 *
 * Payment Gateway modules allow you to integrate payment solutions with the
 * WHMCS platform.
 *
 * This sample file demonstrates how a payment gateway module for WHMCS should
 * be structured and all supported functionality it can contain.
 *
 * Within the module itself, all functions must be prefixed with the module
 * filename, followed by an underscore, and then the function name. For this
 * example file, the filename is "gatewaymodule" and therefore all functions
 * begin "gatewaymodule_".
 *
 * If your module or third party API does not support a given function, you
 * should not define that function within your module. Only the _config
 * function is required.
 *
 * For more information, please refer to the online documentation.
 *
 * @see https://developers.whmcs.com/payment-gateways/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function cardconnect_MetaData()
{
    return array(
        'DisplayName' => 'Card Connect Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function cardconnect_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Card Connect Payment Gateway Module',
        ),
        'user' => array(
            'FriendlyName' => 'Username',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your Card Connect username.',
        ),
        'password' => array(
            'FriendlyName' => 'Password',
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter your password.',
        ),
		'merchant_id' => array(
            'FriendlyName' => 'Merchant ID',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Enter your Card Connect Merchant ID.',
        ),
		'live_url' => array(
            'FriendlyName' => 'Card Connect Live URL',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'https://fts.cardconnect.com:8443/cardconnect/rest/',
            'Description' => 'Enter the live url provided by Card Connect for use with the API.',
        ),
        'test_url' => array(
            'FriendlyName' => 'Card Connect Test URL',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'https://fts.cardconnect.com:6443',
            'Description' => 'Enter the test (or "sandbox") url provided by Card Connect for use with the API.',
        ),
        'test_mode' => array(
            'FriendlyName' => 'Sandbox Test Mode',
            'Type' => 'yesno',
            'Description' => 'Check yes to enable test mode.',
        ),
    );
}

function cardconnect_capture($params) {
	if($params['test_mode'] == 1) {
		$url = $params['test_url'];
	}
	else {
		$url = $params['live_url'];
	}
	
	require 'cardconnect/CardConnectRestClient.php';
	
	//connect to Card Connect and process transaction
	$client = new CardConnectRestClient($url, $params['user'], $params['password']);

	$name = $params['clientdetails']['firstname'] . " " . $params['clientdetails']['lastname'];
	
	$request = array(
		'merchid'   => $params['merchant_id'],
		'accttyppe' => "",
		'account'   => $params['cardnum'],
		'expiry'    => $params['cardexp'],
		'cvv2'      => $params['cccvv'],
		'amount'    => $params['amount'],
		'currency'  => $params['currency'],
		'orderid'   => $params['invoiceid'],
		'name'      => $name,
		'street'    => $params['clientdetails']['address1'],
		'city'      => $params['clientdetails']['city'],
		'region'    => $params['clientdetails']['state'],
		'country'   => $params['clientdetails']['country'],
		'postal'    => $params['clientdetails']['postcode'],
		'tokenize'  => "Y",
		'capture'   => "Y",
	);

	$response = $client->authorizeTransaction($request);
	$payment_response = serialize($response);
	$payment_status = $response['respstat'];
	$payment_status = strtolower($payment_status);
	
	switch($payment_status) {
		case 'a': $status = "success"; break;
		case 'b': $status = "Something was wrong with your information. Please ensure the accurracy of your information and try again.<p>System Message: " . $response['resptext'] . "<br />Debug Code: " . $response['respcode'] . "</p>"; break;
		default: $status = "We were not able to process your card. Please contact your banking institution for assistance.<p>System Message: " . $response['resptext'] . "<br />Debug Code: " . $response['respcode'] . "</p>";
	}
	
	return array(
		'status' => $status,
		'rawdata' => $payment_response,
		'transid' => $response['retref'],
		'fees' => 0,
	);
}

/**
 * Refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/refunds/
 *
 * @return array Transaction response status
 */
function cardconnect_refund($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Transaction Parameters
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // perform API call to initiate refund and interpret result

    return array(
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
        // Unique Transaction ID for the refund transaction
        'transid' => $refundTransactionId,
        // Optional fee amount for the fee value refunded
        'fees' => $feeAmount,
    );
}

/**
 * Cancel subscription.
 *
 * If the payment gateway creates subscriptions and stores the subscription
 * ID in tblhosting.subscriptionid, this function is called upon cancellation
 * or request by an admin user.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/subscription-management/
 *
 * @return array Transaction response status
 */
function cardconnect_cancelSubscription($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Subscription Parameters
    $subscriptionIdToCancel = $params['subscriptionID'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // perform API call to cancel subscription and interpret result

    return array(
        // 'success' if successful, any other value for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
    );
}

function cardconnect_storeremote {
	return array(
		"status" => "success",
		"gatewayid" => $results["token"],
		"rawdata" => $results,
	);

	return array(
		"status" => "failed",
		"rawdata" => $results,
	);
}