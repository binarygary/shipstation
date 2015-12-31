<?php
/**
 * Copyright 2013 CPI Group, LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 *
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


$store['YourAmazonStore']['merchantId'] = 'A213L254EYOZFM';//Merchant ID for this store
$store['YourAmazonStore']['marketplaceId'] = 'ATVPDKIKX0DER'; //Marketplace ID for this store
$store['YourAmazonStore']['keyId'] = 'AKIAJYLPPN755GBDZNCQ'; //Access Key ID
$store['YourAmazonStore']['secretKey'] = '+e+2bNnkUaJaxtzBfjwNU5hSt4zRZGGVoPn2kEbD'; //Secret Access Key for this store

//Service URL Base
//Current setting is United States
$AMAZON_SERVICE_URL = 'https://mws.amazonservices.com/';

//Location of log file to use
$logpath = __DIR__.'/log.txt';

//Name of custom log function to use
$logfunction = '';

//Turn off normal logging
$muteLog = true;

?>