<?php
/*
 * Handle AJAX Requests
 * AJAX data must contain variable 'request'. See example below for best practices:
        $.ajax({
        type: 'GET',
        url: 'ajax.php',
        data: {request: 'org_units'},
        success: function(data){
            console.log(data);
        },
        error: function(xhr, status, error){
            console.error(xhr);
        }
        })
 */

require_once("../conf/config.inc.php");
require_once("../lib/ldap.inc.php");
require_once("functions.php");

 # Store GET request as variable to control which PHP is executed in this script.
if (isset($_GET["request"]) and $_GET["request"]) {

    $request = $_GET["request"];

}


# Handle AJAX requests to query for all Organizational Units
if ( strcmp('org_units',$request) == 0 ) {
    
    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {
    
        # Get All Available Org Units
        $ou_tree = array();
        $ou_tree = get_org_units($ldap, $ldap_base);

        # Create return variable in Select2 JSON data format (https://select2.org/data-sources/formats)
        $sub_options = array();
        for ($i=0; $i < sizeof($ou_tree); $i++) {// For each Organizational Unit
            $sub_options[$i]['id'] = $ou_tree[$i]['dn'];
            $sub_options[$i]['text'] = $ou_tree[$i]['option'];
        }
        $results = array("results" => array_values($sub_options));
        echo json_encode($results);// Pass the results to javascript
    
    }
}

?>