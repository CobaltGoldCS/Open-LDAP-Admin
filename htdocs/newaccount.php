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

    }



    // Do the user creation on POST
    if (isset($_POST["givenname"]) and isset($_POST["sn"]) and isset($_POST["displayname"]) and isset($_POST["samaccountname"])) {
        // foreach($_POST as $key => $value) {
        //     echo "POST parameter '$key' has '$value' <br>";
        // }
        $groups = $_POST["ldap_groups"];
        $ou_dn = $_POST["org_unit"];
        $newpassword = $_POST["newpassword"];

        if ($groups){
            // foreach ($groups as $g){echo $g.'<br />';}
        }
        
        ldapAddUser($ldap, $ou_dn, $_POST["givenname"], $_POST["sn"], $_POST["samaccountname"], $newpassword, $_POST["displayname"], $_POST["mail"]);

    }


    
} else {
    die("You are not allowed to use this resource.<br>Please access this page via index.php.");
}// END Authentication Check



// LDAP new account creation method
function ldapAddUser($ldap, $ou_dn, $firstName, $lastName, $username, $pwdtxt, $displayname, $email){
    
    $dn = "CN=$firstName $lastName,".$ou_dn;// Distinguished Name

    $ldaprecord['cn'] = $firstName." ".$lastName;
    $ldaprecord['displayName'] = isset($displayname) ? $displayname : $firstName." ".$lastName;
    $ldaprecord['name'] = $firstName." ".$lastName;
    $ldaprecord['givenName'] = $firstName;
    $ldaprecord['sn'] = $lastName;
    $ldaprecord['mail'] = $email;
    $ldaprecord['sAMAccountName'] = $username;
    // $ldaprecord['userPassword'] =  '{MD5}' . base64_encode(pack('H*',md5($pwdtxt)));
    // foreach($ldaprecord as $key => $value) {
    //     echo "LDAP attr '$key' has '$value' <br>";
    // }

    // LDAP default properties
    $ldaprecord['objectclass'] = array("top","person","organizationalPerson","user");
    $ldaprecord["UserAccountControl"] = "544";//544 - Account enabled, require password change
    // print_r($ldaprecord);

    if ($ldap) {

        $result = ldap_add($ldap, $dn, $ldaprecord);
        $errno = ldap_errno($ldap);

        if ( $errno ) {// If there is an error, stop here
            // header('Location: index.php?page=newaccount&createaccountresult='.ldap_error($ldap));
            // error_log("LDAP - User creation error $errno  (".ldap_error($ldap).")");
            echo "LDAP - User creation error $errno  (".ldap_error($ldap).")<br>";
        } else {

            $encodedPass = array('unicodepwd' => encodePassword($pwdtxt));
            if(ldap_mod_replace ($ldap, $dn, $encodedPass)){ 
                echo "Successfully created new user.<br>";
                // header('Location: index.php?page=display&dn='.$dn.'&createaccountresult=success');            
            }else{
                echo "LDAP - User creation error $errno  (".ldap_error($ldap).")<br>";
                error_log("LDAP - User creation error on password change (".ldap_error($ldap).")");
            }
            ldap_close($ldap); //Close connection

        }
    }

}

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