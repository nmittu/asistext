<?php
    /* Make a call using Twilio. You can run this file 3 different ways:
     *
     * 1. Save it as call.php and at the command line, run 
     *        php call.php
     *
     * 2. Upload it to a web host and load mywebhost.com/call.php 
     *    in a web browser.
     *
     * 3. Download a local server like WAMP, MAMP or XAMPP. Point the web root 
     *    directory to the folder containing this file, and load 
     *    localhost:8888/call.php in a web browser.
     */

    // Step 1: Get the Twilio-PHP library from twilio.com/docs/libraries/php, 
    // following the instructions to install it with Composer.
    
    require_once "/var/www/html/Twilio/autoload.php";
    use Twilio\Rest\Client;

    
    // Step 2: Set our AccountSid and AuthToken from https://twilio.com/console
    $AccountSid = "ACbbda6d0cfebada2bdd19d45e1972ad27";
    $AuthToken = "c364d62aad010796b1a8aeaab6ebd34d";

    // Step 3: Instantiate a new Twilio Rest Client
    $client = new Client($AccountSid, $AuthToken);

    try {
        // Initiate a new outbound call
        $call = $client->account->calls->create(
            // Step 4: Change the 'To' number below to whatever number you'd like 
            // to call.
            $argv[1],

            // Step 5: Change the 'From' number below to be a valid Twilio number 
            // that you've purchased or verified with Twilio.
            "+12028998765",

            // Step 6: Set the URL Twilio will request when the call is answered.
            array("url" => "https://asistext.com/requestedCall.xml")
        );
        echo "Started call: " . $call->sid;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

?>