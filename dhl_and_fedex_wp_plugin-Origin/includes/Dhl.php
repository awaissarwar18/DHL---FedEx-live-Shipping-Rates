<?php

class Dhl
{
    /*
     * --------------------------------------------------------------------
     * QuoteCalculation DHL
     * --------------------------------------------------------------------
     */
    public function quoteCalculation($data = "")
    {
        $data = array(
            'warehouse' => 3,
            'country' => 1,
            'length' => 12,
            'width' => 12,
            'height' => 12,
            'weight' => 12,
            'source' => 'Shipmoe Wordpress',

        );
        $warehouse = $data['warehouse'];

        $siteid = 'v62_qlgl3szURE';
        $password = 'Iz89SeXrxu';

        $dhl_items = '<Piece><PieceID>1</PieceID><Height>' . $data["height"] . '</Height><Depth>' . $data["length"] . '</Depth><Width>' . $data["width"] . '</Width>   <Weight>' . $data["weight"] . '</Weight></Piece>';
        $to_country = $data['country'];

        $to_city = 'Pakistan';

        $to_country = 'PK';


        $from_zip = '121221';

        $from_country = "india";

        $from_country = 'IN';


        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
            <GetQuote>
                <Request>
                    <ServiceHeader>
                        <MessageTime>' . date('c') . '</MessageTime>
                        <MessageReference>1234567890123456789012345678901</MessageReference>
                        <SiteID>v62_qlgl3szURE</SiteID>
                        <Password>Iz89SeXrxu</Password>
                    </ServiceHeader>
                </Request>
                <From>
                    <CountryCode>' . $from_country . '</CountryCode>
                    <Postalcode>' . $from_zip . '</Postalcode>
                </From>
                <BkgDetails>
                    <PaymentCountryCode>US</PaymentCountryCode>
                    <Date>' . date('Y-m-d') . '</Date>
                    <ReadyTime>PT10H21M</ReadyTime>
                    <ReadyTimeGMTOffset>+01:00</ReadyTimeGMTOffset>
                    <DimensionUnit>IN</DimensionUnit>
                    <WeightUnit>LB</WeightUnit>
                    <Pieces>
                        ' . $dhl_items . '
                    </Pieces>
                    <IsDutiable>N</IsDutiable>
                    <NetworkTypeCode>AL</NetworkTypeCode>
                </BkgDetails>
                <To>
                    <CountryCode>' . $to_country . '</CountryCode>
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
            echo "------------------------------------------------------------<pre>";
            print_r($tuData);
            echo "</pre>------------------------------------------------------------<br>";
        $json = json_encode($xml);
        $xml = json_decode($json, TRUE);

        $item_array = array();
        $item_array['warehouse'] = $data['warehouse'];
        $item_array['country'] = $data['country'];
        $item_array['length'] = $data['length'];
        $item_array['width'] = $data['width'];
        $item_array['height'] = $data['height'];
        $item_array['weight'] = $data['weight'];
        $insert_data['source'] = $data['source'];
        $insert_data['parameters'] = serialize($item_array);
        $insert_data['response'] = serialize($xml);
        $insert_data['xml'] = $xml_req;
        $insert_data['created_date'] = date('Y-m-d H:i:s');
        print_r($xml);
        return $xml;

    }
}

$obj = new Dhl();
$obj->quoteCalculation();