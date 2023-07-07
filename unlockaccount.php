<?php
/*
 * Unlock account in LDAP directory
 */

$result = "";
$dn = "";
$password = "";
$returnto = "display";

if (isset($_POST["dn"]) and $_POST["dn"]) {
    $dn = $_POST["dn"];
} else if (isset($_GET["dn"]) and $_GET["dn"]) {
    $dn = $_GET["dn"];
} else {
    $result = "dnrequired";
}

if (isset($_GET["returnto"]) and $_GET["returnto"]) {
    $returnto = $_GET["returnto"];
}

if ($result === "") {

    require_once(__DIR__ . "/conf/config.inc.php");
    require_once(__DIR__ . "/lib/ldap.inc.php");

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {
        $modification = ldap_mod_del($ldap, $dn, array("pwdAccountLockedTime" => array()));
        $errno = ldap_errno($ldap);
        if ( $errno ) {
            $result = "ldaperror";
        }
    }
}

header('Location: index.php?page='.$returnto.'&dn='.$dn.'&unlockaccountresult='.$result);
