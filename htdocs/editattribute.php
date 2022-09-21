<?php
/*
 * Edit attribute in LDAP directory
 */

 $dn = "";
 $result = "";

if ( isset($_POST["attribute"]) and isset($_POST["dn"]) and isset($_POST["editField"]) and $authenticated ) {
    
    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");

    $attribute = $_POST["attribute"];
    $dn = $_POST["dn"];
    $edits = $_POST["editField"];
    echo "Attribute: $attribute <br>";
    echo "Edits: $edits <br>";
    echo "DN: $dn <br>";

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);
    $ldap = $ldap_connection[0];

    # Do the modification
    if ($ldap) {
        $entry[$attribute] = $edits;
        $modification = ldap_mod_replace($ldap, $dn, $entry);
        $errno = ldap_errno($ldap);

        if ( $errno != 0 ) {// If there's an ldap error, stop here.
            $result = "Cannot modify attribute (".ldap_error($ldap).")";
        } else {
            $result = "successfuledit";
        }
    }

} else {
    $result = "POST attr was empty";
}
// die();

$location = 'index.php?page=display&dn='.$dn.'&editattributeresult='.$result;
header('Location: '.$location);