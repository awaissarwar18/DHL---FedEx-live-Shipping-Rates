<?php
/**
 * This test will send the same test data as in FedEx's documentation:
 * /php/RateAvailableServices/RateAvailableServices.php5
 */

//remember to copy example.credentials.php as credentials.php replace 'FEDEX_KEY', 'FEDEX_PASSWORD', 'FEDEX_ACCOUNT_NUMBER', and 'FEDEX_METER_NUMBER'
require_once 'credentials.php';
require_once 'bootstrap.php';

use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;


$rateRequest = new ComplexType\RateRequest();

//authentication & client details
$rateRequest->WebAuthenticationDetail->UserCredential->Key = FEDEX_KEY;
$rateRequest->WebAuthenticationDetail->UserCredential->Password = FEDEX_PASSWORD;
$rateRequest->ClientDetail->AccountNumber = FEDEX_ACCOUNT_NUMBER;
$rateRequest->ClientDetail->MeterNumber = FEDEX_METER_NUMBER;

$rateRequest->TransactionDetail->CustomerTransactionId = 'testing rate service request';

//version
$rateRequest->Version->ServiceId = 'crs';
$rateRequest->Version->Major = 28;
$rateRequest->Version->Minor = 0;
$rateRequest->Version->Intermediate = 0;

$rateRequest->ReturnTransitAndCommit = true;

//shipper
$rateRequest->RequestedShipment->PreferredCurrency = 'USD';
// $rateRequest->RequestedShipment->Shipper->Address->StreetLines = ['10 Fed Ex Pkwy'];
$rateRequest->RequestedShipment->Shipper->Address->City = $data['s_city'];
// $rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = 'TN';
$rateRequest->RequestedShipment->Shipper->Address->PostalCode = $data['s_code'];
$rateRequest->RequestedShipment->Shipper->Address->CountryCode = $data['s_iso2'];

//recipient
// $rateRequest->RequestedShipment->Recipient->Address->StreetLines = ['13450 Farmcrest Ct'];
$rateRequest->RequestedShipment->Recipient->Address->City = $data['d_city'];
// $rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = 'VA';
$rateRequest->RequestedShipment->Recipient->Address->PostalCode = $data['d_code'];
$rateRequest->RequestedShipment->Recipient->Address->CountryCode = $data['d_iso2'];

//shipping charges payment
$rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;

//rate request types
$rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED, SimpleType\RateRequestType::_LIST];

$rateRequest->RequestedShipment->PackageCount = 1;

//create package line items
$rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem()];

//package 1
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = $data["weight"];
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_LB;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = $data["length"];
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = $data["width"];
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = $data["height"];
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;


$rateServiceRequest = new Request();
//$rateServiceRequest->getSoapClient()->__setLocation(Request::PRODUCTION_URL); //use production URL

$rateReply = $rateServiceRequest->getGetRatesReply($rateRequest); // send true as the 2nd argument to return the SoapClient's stdClass response.

