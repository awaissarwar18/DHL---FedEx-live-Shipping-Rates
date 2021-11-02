<?php
/**
 * @package shippingCalculator
 */
/*
Plugin Name: DHL & Fedex Shipping Calculator
Description: Shipping Calculator will calculate shipping costs with respect to particular products according to their attributes ( e.g. source, weight, dimensions etc ).
Version: 1.0.0
Author: Bitsclan IT Solutions Private Limited
Author uri: https://bitsclan.com/
*/

if (!defined("WPINC")) {
    die;
}

if (!defined("WPSHIP_PLUGIN_DIR")) {
    define("WPSHIP_PLUGIN_DIR", plugin_dir_url(__FILE__));
}


add_action('wp_ajax_my_action', 'calculating_shipping');
add_action('wp_ajax_nopriv_my_action', 'calculating_shipping');


function dhl_reponse($data)
{
   
   
    $siteid = esc_attr(get_option('dhl_api'));
    $password = esc_attr(get_option('dhl_password'));


    $dhl_items = '<Piece><PieceID>1</PieceID><Height>' . $data["height"] . '</Height><Depth>' . $data["length"] . '</Depth><Width>' . $data["width"] . '</Width>   <Weight>' . $data["weight"] . '</Weight></Piece>';

    $to_city = $data['d_city'];
    $to_postal_code = $data['d_code'];
    $to_country = $data['d_iso2'];


    $from_city = $data['s_city'];
    $from_zip = $data['s_code'];
    $from_country = $data['s_iso2'];


    $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
            <GetQuote>
                <Request>
                    <ServiceHeader>
    
                        <MessageTime>' . date('c') . '</MessageTime>
                        <MessageReference>1234567890123456789012345678901</MessageReference>
                        <SiteID>' . $siteid . '</SiteID>
                        <Password>' . $password . '</Password>
                    </ServiceHeader>
                </Request>
                <From>
                    <CountryCode>' . $from_country . '</CountryCode>
                    <Postalcode>' . $from_zip . '</Postalcode>
                    <City>' . $from_city . '</City>
                </From>
                <BkgDetails>
                    <PaymentCountryCode>US</PaymentCountryCode>
                    <Date>' . date('Y-m-d') . '</Date>
                    <ReadyTime>PT10H21M</ReadyTime>
                    <ReadyTimeGMTOffset>+00:00</ReadyTimeGMTOffset>
                    <DimensionUnit>IN</DimensionUnit>
                    <WeightUnit>LB</WeightUnit>
                    <Pieces>' . $dhl_items . '</Pieces>
                    <PaymentAccountNumber>849190478</PaymentAccountNumber>
                    <IsDutiable>N</IsDutiable>
                    <NetworkTypeCode>AL</NetworkTypeCode>
                </BkgDetails>
                <To>
                    <CountryCode>' . $to_country . '</CountryCode>
                    <Postalcode>' . $to_postal_code . '</Postalcode>
                    <City>' . $to_city . '</City>
                </To>     
            </GetQuote>
        </p:DCTRequest>';


    $xml_req = serialize($xml);

    $tuCurl = curl_init();
    curl_setopt($tuCurl, CURLOPT_URL, "https://xmlpitest-ea.dhl.com/XMLShippingServlet");
    curl_setopt($tuCurl, CURLOPT_PORT, 443);
    curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
    curl_setopt($tuCurl, CURLOPT_HEADER, 0);
    curl_setopt($tuCurl, CURLOPT_POST, 1);
    curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "SOAPAction: \"/soap/action/query\"", "Content-length: " . strlen($xml)));

    $tuData = curl_exec($tuCurl);

    if (curl_errno($tuCurl)) {
        echo 'Request Error:' . curl_error($tuCurl);
    }

    curl_close($tuCurl);
    $xml = simplexml_load_string($tuData);

    $json = json_encode($xml);
    $xml = json_decode($json, TRUE);

    return $xml;


 // this is required to terminate immediately and return a proper response
}

function fedex_response($data){
   include_once plugin_dir_path(__FILE__) . 'fedex/examples/rate-request.php';
   return $rateReply->getValue();
}





function calculating_shipping()
{
    $data = array(
        's_country' => $_REQUEST['source_country'],
        's_city' => $_REQUEST['source_city'],
        's_iso2' => $_REQUEST['source_iso2'],
        's_code' => $_REQUEST['source_postalcode'],
        'd_country' => $_REQUEST['destination_country'],
        'd_city' => $_REQUEST['destination_city'],
        'd_iso2' => $_REQUEST['destination_iso2'],
        'd_code' => $_REQUEST['destination_postalcode'],
        'length' => $_REQUEST['length'],
        'width' => $_REQUEST['width'],
        'height' => $_REQUEST['height'],
        'weight' => $_REQUEST['weight']


    );
    $dhl_response = dhl_reponse($data);
    
   $fedex_response = fedex_response($data);
       $error = false;

    if (!isset($dhl_response['GetQuoteResponse']['BkgDetails']) && !isset($fedex_response->RateReplyDetails)) {
        
        $error = true;
        $error_msg = 'Shipping rate cannot be calculated. Please check the information you entered is valid or not and try again.';
    }

    if ($error) {
        $template = '<div class="reponseDiv"><div class="container customContainer"><div class="alert alert-danger"><strong>Error!</strong> ' . $error_msg . '.</div></div></div>';
        echo json_encode($template);
        exit();
    }

    $dhl = "";
    $fedex = "";

    if (true) {
        $dhl_count = rand();
        if (isset($dhl_response['GetQuoteResponse']['BkgDetails'])) {
            if (isset($dhl_response['GetQuoteResponse']['BkgDetails']['QtdShp'][0])) {
                foreach ($dhl_response['GetQuoteResponse']['BkgDetails']['QtdShp'] as $key => $value) {

                    $dhl_count++;
                    $dhl_price = $value['QtdSInAdCur'][0]['TotalAmount'];
                    $transmitdays = $value['TotalTransitDays'];
                    $dhl_name = $value['ProductShortName'];
                    $weight = $value['DimensionalWeight'];
                    $weightcharges = $value['WeightCharge'];
                    $percentage = get_option('dhl_commission');
                    $price = 0;
                    if ($percentage > 0) {
                        $price = ($percentage / 100) * $dhl_price;
                    }

                    $final_dhl_price = round($dhl_price + $price);

                    $dhl .= '  <div class="panel panel-default">
                        <div class="panel-heading">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $dhl_count . '" class="row">
                        <div class="col-lg-2 col-sm-4 js-logo text-center"><img src="//s3.amazonaws.com/ship7/images/carriers/dhl-500x158.png" style="width: 80px;"></div>
                        <div class="col-lg-5 col-xs-12 js-name text-left">DHL ' . $dhl_name . '</div>
                        <div class="col-lg-2 col-xs-12 col-sm-4 js-price text-right">' . $final_dhl_price . ' USD</div>
                        <div class="col-lg-2 col-xs-12 col-sm-4 js-tt text-center"><span class="badge badge-orange">' . $transmitdays . " - " . ($transmitdays + 1) . ' days</span></div>
                        <div class="col-sm-1 col-xs-12 js-arrow"><i class="fa fa-caret-down" style="font-size: 20px;"></i></div>
                        <div class="clearfix"></div>
                        </a>
                        </div>
                        <div id="collapse' . $dhl_count . '" class="panel-collapse collapse">
                        <div class="panel-body">
                        <div class="clearfix form-item">
                        <div class="col-sm-12"><i class="fa fa-info-circle"></i>Express delivery to your door by DHL. DHL is a worldwide carrier with presence in 220 countries, usually offering the best rates to most of the destinations in the world.</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-clock-o"></i>Estimated delivery time</div>
                        <div class="col-sm-8">
                        ' . $transmitdays . " - " . ($transmitdays + 1) . ' days
                        <div class="sub">† Delivery time may increase due to destination country`s customs processing or remote area delivery terms, please see <a href="http://www.dhl-usa.com/en/express/shipping/shipping_advice/terms_conditions.html" target="_blank">DHL shipping service terms</a></div>
                        </div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-shopping-basket"></i>DHL Product Name</div>
                        <div class="col-sm-8">' . $dhl_name . '</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-navicon"></i>Total Weight</div>
                        <div class="col-sm-8">No upper limit for multiple packages</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-arrows"></i>Dimensional weight</div>
                        <div class="col-sm-8">
                        Applies
                        <div class="sub">' . $weight . " LB" . '</div>
                        </div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-shopping-basket"></i>Weight Charges</div>
                        <div class="col-sm-8">' . $weightcharges . ' USD</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-binoculars"></i>Tracking</div>
                        <div class="col-sm-8"><i class="fa fa-check" aria-hidden="true"></i></div>
                        </div>
                        </div>
                        </div>
                        </div>';
                }
            } else {
                $value = $dhl_response['GetQuoteResponse']['BkgDetails']['QtdShp'];
                $dhl_price = $value['QtdSInAdCur'][0]['TotalAmount'];
                $transmitdays = $value['TotalTransitDays'];
                $dhl_name = $value['ProductShortName'];
                $weight = $value['DimensionalWeight'];
                $weightcharges = $value['WeightCharge'];
                $percentage = get_option('dhl_commission');
                $price = 0;
                if ($percentage > 0) {
                    $price = ($percentage / 100) * $dhl_price;
                }
                $final_dhl_price = round($dhl_price + $price);
                $dhl .= '  <div class="panel panel-default">
                        <div class="panel-heading">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $dhl_count . '" class="row">
                        <div class="col-lg-2 col-sm-4 js-logo text-center"><img src="//s3.amazonaws.com/ship7/images/carriers/dhl-500x158.png" style="width: 80px;"></div>
                        <div class="col-lg-5 col-xs-12 js-name text-left">DHL ' . $dhl_name . '</div>
                        <div class="col-lg-2 col-xs-12 col-sm-4 js-price text-right">' . $final_dhl_price . ' USD</div>
                        <div class="col-lg-2 col-xs-12 col-sm-4 js-tt text-center"><span class="badge badge-orange">' . $transmitdays . " - " . ($transmitdays + 1) . ' days</span></div>
                        <div class="col-sm-1 col-xs-12 js-arrow"><i class="fa fa-caret-down" style="font-size: 20px;"></i></div>
                        <div class="clearfix"></div>
                        </a>
                        </div>
                        <div id="collapse' . $dhl_count . '" class="panel-collapse collapse">
                        <div class="panel-body">
                        <div class="clearfix form-item">
                        <div class="col-sm-12"><i class="fa fa-info-circle"></i>Express delivery to your door by DHL. DHL is a worldwide carrier with presence in 220 countries, usually offering the best rates to most of the destinations in the world.</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-clock-o"></i>Estimated delivery time</div>
                        <div class="col-sm-8">
                        ' . $transmitdays . " - " . ($transmitdays + 1) . ' days
                        <div class="sub">† Delivery time may increase due to destination country`s customs processing or remote area delivery terms, please see <a href="http://www.dhl-usa.com/en/express/shipping/shipping_advice/terms_conditions.html" target="_blank">DHL shipping service terms</a></div>
                        </div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-shopping-basket"></i>DHL Product Name</div>
                        <div class="col-sm-8">' . $dhl_name . '</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-navicon"></i>Total Weight</div>
                        <div class="col-sm-8">No upper limit for multiple packages</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-arrows"></i>Dimensional weight</div>
                        <div class="col-sm-8">
                        Applies
                        <div class="sub">' . $weight . " LB" . '</div>
                        </div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-shopping-basket"></i>Weight Charges</div>
                        <div class="col-sm-8">' . $weightcharges . ' USD</div>
                        </div>
                        <div class="clearfix form-item">
                        <div class="col-sm-4"><i class="fa fa-binoculars"></i>Tracking</div>
                        <div class="col-sm-8"><i class="fa fa-check" aria-hidden="true"></i></div>
                        </div>
                        </div>
                        </div>
                        </div>';
            }
        }
    }

    
     if (get_option('fedex_active')) {

        $fedex_count = rand();

        if (isset($fedex_response->RateReplyDetails)) {
            foreach ($fedex_response->RateReplyDetails as $key => $value) {
                $fedex_count++;
                $service_type = str_replace('_', ' ', $value->ServiceType);

                $amount_index0 = 0;
                $amount_index1 = 0;

                if ($value->RatedShipmentDetails && is_array($value->RatedShipmentDetails)) {
                    $amount_index0 = $value->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
                }
                if ($value->RatedShipmentDetails && !is_array($value->RatedShipmentDetails)) {
                    $amount_index0 = $value->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount;
                }
                if (isset($value->RatedShipmentDetails[1])) {
                    $amount_index1 = $value->RatedShipmentDetails[1]->ShipmentRateDetail->TotalNetCharge->Amount;
                }

                if ($amount_index0 > $amount_index1) {
                    $amount = $amount_index0;
                } else {
                    $amount = $amount_index1;
                }

                if (isset($value->CommitDetails[0]->CommitTimestamp)) {
                    $date_committed = explode("T", $value->CommitDetails[0]->CommitTimestamp);
                    $now = time();
                    $your_date = strtotime($date_committed[0]);
                    $datediff = $your_date - $now;
                    $transitTime = round($datediff / (60 * 60 * 24));
                    $transitTime = ($transitTime - 1) . " - " . $transitTime . " days";
                } else {
                    $transitTime = "N/A";
                }


                $percentage = get_option('fedex_commission');
                $price = 0;

                if ($percentage > 0) {
                    $price = ($percentage / 100) * $amount;
                }

                $amount = round($amount + $price);

                $fedex .= '<div class="panel panel-default">
               <div class="panel-heading">
               <a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $fedex_count . '" class="row">
               <div class="col-lg-2 col-xs-12  col-sm-4 js-logo text-center"><img src="//s3.amazonaws.com/ship7/images/carriers/fedex-500x158.png" style="width: 80px;"></div>
               <div class="col-lg-5 col-xs-12  js-name text-left">FedEx ' . $service_type . '</div>
               <div class="col-lg-2 col-xs-12  col-sm-4 js-price text-right">' . $amount . ' USD</div>
               <div class="col-lg-2 col-xs-12  col-sm-4 js-tt text-center"><span class="badge badge-orange">' . $transitTime . '</span></div>
               <div class="col-sm-1 col-xs-12  js-arrow"><i class="fa fa-caret-down" style="font-size: 20px;"></i></div>
               <div class="clearfix"></div>
               </a>
               </div>
               <div id="collapse' . $fedex_count . '" class="panel-collapse collapse">
               <div class="panel-body">
               <div class="clearfix form-item">
               <div class="col-sm-12"><i class="fa fa-info-circle"></i>Express delivery to your door by FedEx. FedEx is a worldwide courier service provider best known for their time-definite delivery typically in 10-15 business days.</div>
               </div>
               <div class="clearfix form-item">
               <div class="col-sm-4"><i class="fa fa-clock-o"></i>Estimated delivery time</div>
               <div class="col-sm-8">
               <?php echo $transitTime; ?>
               <div class="sub">Delivery time may increase due to destination country`s customs processing or remote area delivery terms, please see <a href="http://www.fedex.com/cg/shippingguide/terms/" target="_blank">FedEx shipping service terms</a></div>
               </div>
               </div>
               <div class="clearfix form-item">
               <div class="col-sm-4"><i class="fa fa-shopping-basket"></i>Maximum Weight</div>
               <div class="col-sm-8">68 kg (150 lb) for single item</div>
               </div>
               <div class="clearfix form-item">
               <div class="col-sm-4"><i class="fa fa-navicon"></i>Total Weight</div>
               <div class="col-sm-8">No upper limit for multiple packages</div>
               </div>
               <div class="clearfix form-item">
               <div class="col-sm-4"><i class="fa fa-arrows"></i>Dimensional weight</div>
               <div class="col-sm-8">
               Applies
               <div class="sub">L x W x H / 139 (if inches) - L x W x H / 5000 (if cm)</div>
               </div>
               </div>
               <div class="clearfix form-item">
               <div class="col-sm-4"><i class="fa fa-binoculars"></i>Tracking</div>
               <div class="col-sm-8"><i class="fa fa-check" aria-hidden="true"></i></div>
               </div>
               <div class="clearfix form-item">
               <div class="col-sm-4"><i class="fa fa-umbrella"></i>Insurance</div>
               <div class="col-sm-8">First $100 are free<br>Optional additional insurance: $3 for every $100 value</div>
               </div>
               </div>
               </div>
               </div>';
            }
         }
      }
    


    $template = '<div class="reponseDiv">
        <div class="container customContainer">
        <div class="container-2-left col-sm-4 col-xs-12 margin-bottom-30">
        <div class="form-item row">
        <div class="col-sm-5 form-caption">ORIGIN</div>
        <div class="col-sm-7 form-value js-origin">' . $data['s_city'] . '</div>
        </div>
        <div class="form-item row">
        <div class="col-sm-5 form-caption">DESTINATION</div>
        <div class="col-sm-7 form-value js-destination">' . $data['d_city'] . '</div>
        </div>
        <div class="form-item row">
        <div class="col-sm-5 form-caption">WEIGHT</div>
        <div class="col-sm-7 form-value js-weight">' . $data['weight'] . ' lbs</div>
        </div>
        <div class="form-item row">
        <div class="col-sm-5 form-caption">DIMENSIONS</div>
        <div class="col-sm-7 form-value js-dimensions">' . $data['length'] . 'x' . $data['width'] . 'x' . $data['height'] . ' inches</div>
        </div>
        <div class="clearfix">&nbsp;</div>
        <div class="text-center"><button id="Modify" type="button" class="btn btn-success modifySelection">Modify Selections</button><br><br></div>
        </div>
        <div class="col-sm-8 col-xs-12">
        <div class="well">
        <div class="panel-group" id="accordion">
        ' . $dhl . '
        </div>
        <div class="panel-group" id="accordion">
        ' . $fedex . '
        </div>
        </div>
        </div>
        <div class="clearfix"></div>
        </div>
        </div>';

    echo json_encode($template);
    exit();
}

add_filter( 'plugin_action_links_shipping/shippingCalculator.php', 'shipping_settings_link' );
function shipping_settings_link( $links ) {
   $url = esc_url( add_query_arg(
      'page',
      'shipping/includes/settings.php',
      get_admin_url() . 'admin.php'
   ) );

   $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';

   array_push(
      $links,
      $settings_link
   );
   return $links;
}


