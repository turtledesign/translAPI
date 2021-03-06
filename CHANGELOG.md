﻿# Changes (going through).

    0.6.0.0       24/09/2018 Service interface added with end to end testing.
    0.5.6.0       24/09/2018 Added the guzzle http transport.
    0.5.5.0       24/09/2018 Added the request schema and tests.
    0.5.4.0       18/09/2018 Added the Netdespatch error checking (for both xml error and request rejection).
    0.5.3.0       18/09/2018 XML response parsing added.
    0.5.2.0       16/09/2018 Cancellation request added (with updates for complete request including credentials).
    0.5.1.0       15/09/2018 Added in XML serialisation via sabre.io/xml and XMLWriter with array format conversion.
    0.5.0.0       12/09/2018 Start of Netdespatch version.
    0.4.0.2       30/07/2018 https://github.com/turtledesign/royalmail-php/issues/12 - $helper in Interpreter() call should be [] not Helper/Data object.
    0.4.0.1       30/06/2018 https://github.com/turtledesign/royalmail-php/issues/11 - Parameter count mismatch on new Interpreter() call.
    0.4.0.0       16/03/2018 Added the international shipping options to the createShipment schema.
    0.3.1.6       14/03/2018 Fixed createLabel ->succeeded() response and improved Symfony YAML requirement specification.
    0.3.1.4       09/03/2018 Postal town shouldn't have been required on printLabel as the address is option in its entirity. 
    0.3.1.3       09/03/2018 Removed call to each() in validation module as it's deprecated in PHP 7.2 
    0.3.1.2       09/03/2018 Fixed errors in some test data files that YAML 4.* picks up where previous versions ignored. 
    0.3.1.1       09/03/2018 Added v4 of YAML package and latest Atoum for compatability with Laravel 5.6 and PHP 7.1 
    0.3.0.1       06/03/2018 Added some extra tests while hunting down why the weight value wasn't working.
    0.3.0.0       18/10/2016 Updated to the latest version (v2_0_9) of the API + new endpoint as per https://developer.royalmail.net/node/688#/
    0.2.0.0       18/10/2016 Fix for PHP 7 (it doesn't like references in soap client arrays).
    0.1.10.6      14/08/2016 Added basic JSON serialisation for the response/interpreter object.
    0.1.9.6       06/01/2016 Added (and removed) PHP 7 to Travis CI test - errors on Travis setup.
    0.1.9.5       06/01/2016 Modified the error and warning test sample responses to account for RM apparently removing xmlns files. [1]
    0.1.9.4       30/12/2015 Merged in changes from @minioak - fixed up tests. [1.5]
    0.1.9.0       16/10/2015 Added request1DRanges action. [0.6]
    0.1.8.0       16/10/2015 Added an exception catcher to provide debug info in the response object. [1.89]
    0.1.7.0       16/10/2015 Added printDocument action. [0.2]
    0.1.6.0       16/10/2015 Added createManifest action & re-organised response test YML files. [0.7]
    0.1.5.0       16/10/2015 createShipment response map. [1]
    0.1.4.0       14/10/2015 Couple of changes to get all the auth active and usable. [1.5] 
    0.1.3.0       12/10/2015 Linked up dev helper, added basic usage example. [0.6]
    0.1.2.0       11/10/2015 Extracted test schema loading to the (new) Development helper [0.8]
    0.1.1.0       11/10/2015 Added magic API request action methods. [0.5] 
    0.1.0.0       11/10/2015 First Alpha Release - Main entry dispatcher working end to end in development mode.
    0.0.25.1      11/10/2015 Work on dispatcher/entry point - Helper and Connector loading working, inc. static version for dev integration. [3]
    0.0.24.1      04/10/2015 Added printLabel request + testing and methods for binary files. [3]
    0.0.23.1      02/10/2015 serviceEnhancements working with subkey nesting of values. [0.5]
    0.0.22.1      02/10/2015 Items key on createShipment working, added Round filter and generation of county key for multi-properties [2.5]
    0.0.21.1      30/09/2015 Added loading for the errors and warnings in the integrationFooter, tested with single error [2.5 (+1c)]
    0.0.20.1      27/09/2015 Got the createManifest response parsing OK, including handling multiple/single shipment values [4]
    0.0.19.1      26/09/2015 Response interpreter working (with filters+meta) and tested with the provided cancelShipment response [3.9]
    0.0.18.1      23/09/2015 Factored out a load of the structure processing to implement it in Request and Response + dev on response interpreter. [1.8]
    0.0.17.1      23/09/2015 Got the WS security SOAP header functioning [1.7]
    0.0.16.1      21/09/2015 Added mock soap client and tests [6.8]
    0.0.15.1      20/09/2015 Basic multiple element addition [1.8]
    0.0.14.1      15/09/2015 Added Address block with filter and validator for UK postcodes [2.4]
    0.0.13.1      13/09/2015 Added conditional require: ThisRequiredWhenThat [2.2]
    0.0.12.1      13/09/2015 Start switch to Valitron validation, implemented + tested SkipThisIfThatEmpty filtering for conditional value inclusion. [3.5]
    0.0.11.1      11/09/2015 Skip filtering added + couple more string filters. Added help to schema. createShipment passing tests up to recipientContact. [3.5]
    0.0.10.1      11/09/2015 Added tests for the data files and cleaned the data a bit. [2]
    0.0.10.0      07/09/2015 Added date & boolean valdation + filters. [3]
    0.0.9.0       07/09/2015 Added value filtering and path creation. [3.5]
    0.0.7.0       05/09/2015 Working to the point where it can validate and structure the integrationHeader. [2]
    0.0.6.0       05/09/2015 Got basic validation functionality added via the Validator trait. [1.5]
    0.0.5.0       04/09/2015 Ditched multi-class request in favour of a single request builder utility (using the schema). [1.5]
    0.0.5.0       04/09/2015 Started on SOAP connector, but left till we get the auth details for testing. [1.9]
    0.0.3.0       03/09/2015 More work on the structure, started on connectors + switched to atoum and got first test running. [4.0]
    0.0.1.0       01/09/2015 Started stubbing out the basic structure. [1.1]
    0.0.0.0       31/08/2015 Created initial module structure. [1.1]
    
    
