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
?>