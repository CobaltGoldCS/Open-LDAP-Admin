<?php
/*
 * Edit attribute in LDAP directory
 */

 $dn = "";
 $result = "";

if ( isset($_POST["attribute"]) and isset($_POST["dn"]) and isset($_POST["editField"]) and $authenticated ) {
    
    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");

    // $user_editable_attributes = array_map(function($attributes_map) {return $attributes_map['usereditable'];}, $attributes_map);
    // $admin_editable_attributes = array_map(function($attributes_map) {return $attributes_map['admineditable'];}, $attributes_map);
    // print_r($user_editable_attributes);
    // print_r($admin_editable_attributes);
    
    $attribute = $_POST["attribute"];
    $dn = $_POST["dn"];
    $edits = $_POST["editField"];
    // echo "Attribute: $attribute <br>";
    // echo "Edits: $edits <br>";
    // echo "DN: $dn <br>";

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
    $location = 'index.php?page=display&dn='.$dn.'&editattributeresult='.$result;

} 

if ( isset($_POST["org_unit"]) and isset($_POST["dn"]) and $authenticated ) {
    
    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");

    $dn = $_POST["dn"];
    $newOU = $_POST["org_unit"];

    $tmp = ldap_explode_dn($dn,0);
    $new_rdn = $tmp[0];

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);
    $ldap = $ldap_connection[0];

    # Do the modification
    if ($ldap) {

        $modification = ldap_rename_ext($ldap, $dn, $new_rdn, $newOU, true);
        $errno = ldap_errno($ldap);

        if ( $errno != 0 ) {// If there's an ldap error, stop here.
            $result = "Could not move user: (".ldap_error($ldap).")";
        } else {
            $result = "successfuledit";
        }

    }
    $location = 'index.php?page=display&dn='.$new_rdn.",".$newOU.'&editattributeresult='.$result;

}

else {
    $result = "POST attr was empty";
}
// die();

header('Location: '.$location);