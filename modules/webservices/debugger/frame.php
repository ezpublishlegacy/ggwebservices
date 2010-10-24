<?php
/**
 * WS debugger: external frame
 *
 * @author G. Giunta
 * @version $Id: debugger.php 145 2010-06-19 20:25:32Z gg $
 * @copyright 2010
 */

//$query_string = '';

$wsINI = eZINI::instance( 'wsproviders.ini' );

// calculate params for local server, for consistency with what we do below
foreach ( array( 'jsonrpc' , 'xmlrpc' ) as  $protocol )
{
    $uri = "webservices/execute/$protocol";
    eZURI::transformURI( $uri , false, 'full' );
    if ( $wsINI->variable( 'GeneralSettings', 'Enable' . strtoupper( $protocol ) ) == 'true' )
    {
        $url = parse_url( $uri );
        $params = '?action=';
        $params .= '&host=' . $url['host'];
        $params .= '&port=' . ( isset( $url['port'] ) ? $url['port'] : '' );
        $params .= '&path=' . ( isset( $url['path'] ) ? $url['path'] : '/' );
        if ( $url['scheme'] == 'https' )
        {
            $params .= '&protocol=2';
        }
        if ( $protocol == 'jsonrpc' )
        {
            $params .= '&wstype=1';
        }
        $server_list[$protocol] = $params;
    }
    else
    {
        $server_list[$protocol] = '';
    }
}

// calculate list of target ws servers as it is hard to do that in tpl code
$target_list = array();
foreach ( $wsINI->groups() as $groupname => $groupdef )
{
    if ( $groupname != 'GeneralSettings' && $groupname != 'ExtensionSettings' )
    {
        if ( $wsINI->hasVariable( $groupname, 'providerType' ) )
        {
            $target_list[$groupname] = $groupdef;
            $url = parse_url( $groupdef['providerUri'] );
            if ( !isset( $url['scheme'] ) || !isset( $url['host'] ) )
            {
                $target_list[$groupname]['providerType'] = 'FAULT';
            }
            else
            {
                $params = '?action=';
                $params .= '&host=' . $url['host'];
                $params .= '&port=' . ( isset( $url['port'] ) ? $url['port'] : '' );
                $params .= '&path=' . ( isset( $url['path'] ) ? $url['path'] : '/' );
                if ( $url['scheme'] == 'htps' )
                {
                    $params .= '&protocol=2';
                }
                if ( isset( $target_list[$groupname]['providerUsername'] ) && $target_list[$groupname]['providerUsername'] != '' )
                {
                    $params .= '&username=' . $target_list[$groupname]['providerUsername'] . '&amp;password=' . $target_list[$groupname]['providerPassword'];
                }
                if ( isset( $target_list[$groupname]['timeout'] ) )
                {
                    $params .= '&timeout=' . $target_list[$groupname]['timeout'];
                }
                if ( $target_list[$groupname]['providerType'] == 'JSONRPC' )
                {
                    // nb: we leave REST, SOAP as wstype 1, which is wrong
                    // currently the left menu tpl does not show a link for other types than jsronrpc and xmlrcp anyway
                    $params .= '&wstype=1';
                }
                $target_list[$groupname]['urlparams'] = $params;
            }
        }
    }
}
// display the iframe_based template
require_once( "kernel/common/template.php" );
$tpl = templateInit();
//$tpl->setVariable( 'query_string', $query_string );
$tpl->setVariable( 'target_list', $target_list );
$tpl->setVariable( 'server_list', $server_list );
$Result = array();
$Result['content'] = $tpl->fetch( "design:webservices/debugger/frame.tpl" );
$Result['left_menu'] = 'design:parts/wsdebugger/menu.tpl';
$Result['path'] = array( array( 'url' => 'webservices/debugger',
                                'text' => ezi18n( 'extension/webservices', 'WS Debugger' ) ) );

?>