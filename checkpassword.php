<?php
/*
 * Check password in LDAP directory
 */

$result = "";
$dn = "";
$password = "";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else {
    $result = "dnrequired";
}

if (isset($_POST["currentpassword"]) and $_POST["currentpassword"]) {
    $password = $_POST["currentpassword"];
} else {
    $result = "passwordrequired";
}



if ($result === "") {

    require_once(__DIR__ . "/conf/config.inc.php");
    require_once(__DIR__ . "/lib/ldap.inc.php");

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $dn, $password);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if( !$result ) {
        $result = "passwordok";
    }
}

header('Location: index.php?page=display&dn='.$dn.'&checkpasswordresult='.$result);
