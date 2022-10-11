<?php
/*
 * Create a new account in LDAP directory
 */



// Page load actions
$authenticated = $_SESSION["authenticated"];
$isadmin = $_SESSION['isadmin'];
if ($authenticated and $isadmin) {// Do basic authentication check before loading page PHP

    if (isset($_GET["createaccountresult"]) and $_GET["createaccountresult"]) {
        $createaccountresult = $_GET["createaccountresult"];
        $smarty->assign("createaccountresult", $createaccountresult);
    }

    require_once("../conf/config.inc.php");
    require_once("../lib/ldap.inc.php");

    $required_attributes = array('firstname','lastname');// Attributes required for user creation

    # Connect to LDAP
    $ldap_connection = wp_ldap_connect($ldap_url, $ldap_starttls, $ldap_binddn, $ldap_bindpw);

    $ldap = $ldap_connection[0];
    $result = $ldap_connection[1];

    if ($ldap) {

        /* Query #1: Get available attributes */
        
        # Search attributes
        $attributes = array();
        $creation_attributes = isset($user_creation_attributes) ? array_merge($required_attributes,$user_creation_attributes) : $required_attributes;
        foreach( $creation_attributes as $item ) {
            $attributes[] = $attributes_map[$item]['attribute'];
        }
        // echo "Search items: $attributes <br>";
        // print_r($attributes);

        # Search entry
        $search = ldap_read($ldap, $ldap_binddn, $ldap_user_filter, $attributes);
        $errno = ldap_errno($ldap);

        if ( $errno ) {
            $result = "ldaperror";
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        } else {
            $entry = ldap_get_entries($ldap, $search);
        }
        ldap_free_result($search);// End Query #1

        
        /* Query #2: Get Organizational Units */

        $filter="(objectClass=organizationalunit)";
        $justthese = array("dn", "ou"); 
        $search = ldap_search($ldap, $ldap_base, $filter, $justthese);
        $errno = ldap_errno($ldap);
        // $orgUnits = ldap_get_entries($ldap, $search);
        // print_r($orgUnits);
        
        if ( $errno ) {
            $result = "ldaperror";
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        } else {

            $orgUnits = ldap_get_entries($ldap, $search);// Query LDAP for full list of organizational units

            /* Build LDAP Org Tree in the following example format:
                Array (3):
                0 => Array (3)
                tree => Array (1)
                    Alumni => Array (2)
                    level => 2
                    parent => "example"
                dn => "OU=Alumni,DC=example,DC=org"
                option => "Alumni"
            */
            $exploded_ous = array(); $ou_tree = array();
            for ($i=0; $i < $orgUnits['count']; $i++) {// For each Organizational Unit
                // echo $orgUnits[$i]["dn"]."<br>";
                $exploded_ous = array_reverse(ldap_explode_dn($orgUnits[$i]['dn'],2));// Explode OU's ommitting "DN=" and "OU="
                // print_r($exploded_ous);
                for ($j=2; $j < $exploded_ous['count']; $j++) {// Create sub-key for each OU in tree, ignoring top-level ($j=2)
                    $key = !isset($ou_tree[$i]['tree'][$exploded_ous[$j]]) ? $exploded_ous[$j] : $key."\0";// Handle situation in which key already exists by appending invisible character
                    $ou_tree[$i]['tree'][$key]['level'] = $j;// Capture directory tree-level
                    $ou_tree[$i]['tree'][$key]['parent'] = isset($exploded_ous[$j-1])?$exploded_ous[$j-1]:'';// Capture parent
                }
                $ou_tree[$i]['dn'] = $orgUnits[$i]['dn'];// Create key: 'dn' --Save the DN of the Organizational Unit
                $ou_tree[$i]['option'] = implode(" / ",array_keys($ou_tree[$i]['tree']));// Create key: 'tree' --Create human-readable text fields to show in drop-down
                // print_r($ou_tree[$i]);
                // echo "<br>";
            }
            
            // Sort Org Structure for better human readibility
            usort($ou_tree, 'sortByOption');

            $smarty->assign("org_tree", $ou_tree);
            
        }
        ldap_free_result($search);// End Query #2

        
        /* Query #3: Get Groups Units */

        $filter="(objectClass=group)";
        $justthese = array("dn"); 
        $search = ldap_search($ldap, $ldap_group_base, $filter, $justthese);
        $errno = ldap_errno($ldap);
        // $orgUnits = ldap_get_entries($ldap, $search);
        // print_r($orgUnits);
        
        if ( $errno ) {
            $result = "ldaperror";
            error_log("LDAP - Search error $errno  (".ldap_error($ldap).")");
        } else {

            $groups = ldap_get_entries($ldap, $search);// Query LDAP for full list of groups

            for ($i=0; $i < $groups['count']; $i++) {// For each group
                // echo $groups[$i]["dn"]."<br>";
                $groups[$i]['option'] = ldap_explode_dn($groups[$i]['dn'],2)[0];
                // print_r($exploded_groups);
                // echo "<br>";
            }
            usort($groups, 'sortByOption');
            $smarty->assign("ldap_groups", $groups);
            
        }
        ldap_free_result($search);// End Query #3


        # Sort attributes values
        foreach ($entry[0] as $attr => $values) {
            if ( is_array($values) && $values['count'] > 1 ) {
                asort($values);
            }
            if ( isset($values['count']) ) {
                unset($values['count']);
            }
            $entry[0][$attr] = $values;
        }

        $smarty->assign("entry", $entry[0]);
        $smarty->assign("card_items", $creation_attributes);

    }// END if($ldap)



    /*
    *   POST Actions
    */
    if (isset($_POST["givenname"]) and isset($_POST["sn"]) and isset($_POST["ldap_groups"]) and isset($_POST["org_unit"]) and isset($_POST["newpassword"])) {

        // POST values to LDAP attribute conversion
        $ou = $_POST["org_unit"];// Organizational Unit
        $dn = "CN=".$_POST['givenname']." ".$_POST['sn'].",".$ou;// Distinguished Name
        $name = $_POST['givenname']." ".$_POST['sn'];// First Name
        $groups = $_POST["ldap_groups"];// Array of group DN's
        $newpassword = $_POST["newpassword"];// Password (placeholder)
    
        $ldaprecord = array();// Preallocate

        // Use the rest of the POST values as definied by the $creation_attribute array
        $remaining_attributes = array_slice($_POST, 0, sizeof($creation_attributes));// Trim off 'org_unit', 'ldap_groups', 'newpassword', 'confirmpassword' and 'pwdreset' which are non-LDAP attributes
        foreach($remaining_attributes as $key => $value) {
            $ldaprecord[$key] = $value;
        }

        // Build the rest of the LDAP record with minimum required attributes unless specified by user config
        $ldaprecord['cn'] = isset($_POST['cn']) ? $_POST['cn'] : $_POST['givenname']." ".$_POST['sn'];
        $ldaprecord['displayname'] = isset($_POST['displayname']) ? $_POST['displayname'] : $name;
        $ldaprecord['name'] = isset($_POST['name']) ? $_POST['name'] : $name;
        // $ldaprecord['userPassword'] =  '{MD5}' . base64_encode(pack('H*',md5($pwdtxt)));
        
        $ldaprecord['objectclass'] = array("top","person","organizationalPerson","user");
        $ldaprecord["useraccountcontrol"] = "544";//544 - Account enabled, require password change
    
        if ($ldap) {
    
            $result = ldap_add($ldap, $dn, $ldaprecord);
            $errno = ldap_errno($ldap);
            $success_url = 'index.php?page=display&dn='.$dn;//Redirect page to display new account on success
    
            /* STEP 1: Create the account */
            if ( $errno ) {// If there is an error, stop here
                // echo "LDAP - User creation error $errno  (".ldap_error($ldap).")<br>";
                header('Location: index.php?page=newaccount&createaccount='.ldap_error($ldap));
                error_log("LDAP - User creation error $errno  (".ldap_error($ldap).")");
            } else {// Else continue to steps 2 & 3
    
                /* STEP 2: Set the user password */
                $encodedPass = array('unicodepwd' => encodePassword($pwdtxt));
                if(ldap_mod_replace ($ldap, $dn, $encodedPass)){ 
                    // echo "Successfully created new user.<br>";
                    $callback .= '&createaccount=success';
                } else {
                    // echo "LDAP - User creation error $errno  (".ldap_error($ldap).")<br>";
                    error_log("LDAP - User creation error on password change (".ldap_error($ldap).")");
                    $callback .= '&createaccount=pwderror';
                }

                /* STEP 3: Add the user to any groups */
                foreach( $groups as $group ) {
                    $member['member'] = $dn;// User's DN is added to group's 'member' array
                    if(ldap_mod_add($ldap, $group, $member)) {
                        $callback .= '&groupadd=success';
                    } else {
                        // echo "Failed to add user to group (".ldap_error($ldap).")<br>";
                        error_log("LDAP - Failed to add user to group (".ldap_error($ldap).")");
                        $callback .= '&groupadd=adderror';
                    }
                }
                $success_url .= $callback;// Append callback(s)
                header('Location: '.$success_url);//Do the final redirect with callbacks appended
                ldap_close($ldap); //Close connection
    
            }
        }
 

    }


    
} else {
    die("You are not allowed to use this resource.<br>Please access this page via index.php.");
}// END Authentication Check



// Sort options custom method
function sortByOption($a, $b) {
    return strcmp($a['option'], $b['option']);
}



// Create unicode password
function encodePassword($password) {
    $password="\"".$password."\"";
    $encoded="";
    for ($i=0; $i <strlen($password); $i++){ $encoded.="{$password{$i}}\000";}
    return $encoded;
}