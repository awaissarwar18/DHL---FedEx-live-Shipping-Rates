<?php
//Change these values below.

define('FEDEX_ACCOUNT_NUMBER', '510087640');
define('FEDEX_METER_NUMBER', '100508774');
define('FEDEX_KEY', 'lMxENbnB6PLkPtky');
define('FEDEX_PASSWORD', 'cBmlpEDukDXKA7Lzj2gsEmPpT');


if (!defined('FEDEX_ACCOUNT_NUMBER') || !defined('FEDEX_METER_NUMBER') || !defined('FEDEX_KEY') || !defined('FEDEX_PASSWORD')) {
    die("The constants 'FEDEX_ACCOUNT_NUMBER', 'FEDEX_METER_NUMBER', 'FEDEX_KEY', and 'FEDEX_PASSWORD' need to be defined in: " . realpath(__FILE__));
}
