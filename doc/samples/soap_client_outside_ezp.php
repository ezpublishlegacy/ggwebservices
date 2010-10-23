<?php
/**
 * Sample soap client that uses the ggws classes outside of an eZ Publish context
 * The server endpoint in use is the public one from mssoapinterop.org
 *
 * @version $Id$
 * @author Gaetano Giunta
 * @copyright (c) 2010 G. Giunta
 * @license code licensed under the GNU GPL. See LICENSE file
 */

// include client classes (this is done by autload when within an eZP context)
include_once( "ggwebservices/classes/ggwebservicesclient.php" );
include_once( "ggwebservices/classes/ggphpsoapclient.php" );
include_once( "ggwebservices/classes/ggwebservicesrequest.php" );
include_once( "ggwebservices/classes/ggphpsoaprequest.php" );
include_once( "ggwebservices/classes/ggwebservicesresponse.php" );
include_once( "ggwebservices/classes/ggphpsoapresponse.php" );

// create a new client
$client = new ggPhpSOAPClient( "mssoapinterop.org", "/asmx/simple.asmx", 80, null, 'http://mssoapinterop.org/asmx/simple.asmx?WSDL' );
// NB: this also works:
//$client = new ggPhpSOAPClient(null, null, null , null, 'http://mssoapinterop.org/asmx/simple.asmx?WSDL');

// define the request
$request = new ggPhpSOAPRequest( "echoInteger", array( 123 ) );

// send the request to the server and fetch the response
$response = $client->send( $request );

// check if the server returned a fault, if not print out the result
if ( $response->isFault() )
{
    print( "<pre>Fault: " . $response->faultCode(). " - \"" . $response->faultString() . "\"" );
}
else
{
    print( "<pre>Returned value was: \"" . $response->value() . "\"" );
}

?>
