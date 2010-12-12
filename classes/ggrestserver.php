<?php
/**
 *
 * @author G. Giunta
 * @version $Id: ggjsonrpcserver.php 199 2010-12-04 00:06:40Z gg $
 * @copyright (C) G. Giunta 2010
 */

class ggRESTServer extends ggWebservicesServer
{

    function prepareResponse( $response )
    {
        $response->setContentType( $this->ResponseType );
        $response->setJsonpCallback( $this->JsonpCallback );
    }

    /**
      Processes the request and returns a request object (or false).
    */
    function parseRequest( $payload )
    {
        $request = new ggRESTRequest();
        if ( $request->decodeStream( $payload ) )
        {
            $this->ResponseType = $request->responseType();
            $this->JsonpCallback = $request->jsonpCallback();
            return $request;
        }
        else
        {
            $this->ResponseType = '';
            return false;
        }
    }

    protected $ResponseType = '';
    protected $ResponseClass = 'ggRESTResponse';
    protected $JsonpCallback = false;
}

?>