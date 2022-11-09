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

if ( isset($_POST["saveGroups"]) and isset($_POST["dn"]) and $authenticated ) {
    
    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");
    require_once("functions.php");

    $dn = $_POST["dn"];
    $groups = $_POST["ldap_groups"];// Array of group DN's

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);
    $ldap = $ldap_connection[0];

    # Do the modification
    if ($ldap) {

        $member['member'] = $dn;// User's DN is added to group's 'member' array

        /* STEP 1: Remove user from all group memberships */
        $group_membership = get_group_memberships($ldap, $dn, $ldap_user_filter);
        if (sizeof($group_membership) > 0) {
            foreach( $group_membership as $membership ) {
                if(ldap_mod_del($ldap, $membership['dn'], $member)) {
                    $result = "successfuledit";
                } else {
                    // echo "Failed to add user to group (".ldap_error($ldap).")<br>";
                    error_log("LDAP - Failed to add user to group (".ldap_error($ldap).")");
                    $result = "Remove group error: (".ldap_error($ldap).")";
                }
            }
        }

        /* STEP 2: Add the user back to any groups */
        if (sizeof($groups) > 0) {
            foreach( $groups as $group ) {
                if(ldap_mod_add($ldap, $group, $member)) {
                    $result = "successfuledit";
                } else {
                    // echo "Failed to add user to group (".ldap_error($ldap).")<br>";
                    error_log("LDAP - Failed to add user to group (".ldap_error($ldap).")");
                    $result = "Add group error: (".ldap_error($ldap).")";
                }
            }
        }

    }
    $location = 'index.php?page=display&dn='.$dn.'&editattributeresult='.$result;

}

if ( isset($_POST["deleteGroup"]) and isset($_POST["dn"]) and $authenticated ) {
    
    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");

    $dn = $_POST["dn"];
    $groupDN = $_POST["deleteGroup"];

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);
    $ldap = $ldap_connection[0];

    # Do the modification
    if ($ldap) {

        $member['member'] = $dn;// User's DN is added to group's 'member' array

        /* Remove user from specified group */
        if(ldap_mod_del($ldap, $groupDN, $member)) {
            $result = "successfuledit";
        } else {
            // echo "Failed to add user to group (".ldap_error($ldap).")<br>";
            error_log("LDAP - Failed to add user to group (".ldap_error($ldap).")");
            $result = "Remove group error: (".ldap_error($ldap).")";
        }

    }
    $location = 'index.php?page=display&dn='.$dn.'&editattributeresult='.$result;

}

else {
    $result = "POST attr was empty";
}
// die();

header('Location: '.$location);