<?php
/*
 * Create a new account in LDAP directory
 */



// Page load actions
$authenticated = $_SESSION["authenticated"];
$isadmin = $_SESSION['isadmin'];
if ($authenticated and $isadmin) {// Do basic authentication check before loading page PHP

    if (isset($_GET["createaccount"]) and $_GET["createaccount"]) {
        $createaccount = $_GET["createaccount"];
        $smarty->assign("createaccount", $createaccount);
    }
    if (isset($_GET["groupadd"]) and $_GET["groupadd"]) {
        $groupadd = $_GET["groupadd"];
        $smarty->assign("groupadd", $groupadd);
    }

    require_once(__DIR__ . "/conf/config.inc.php");
    require_once(__DIR__ . "/lib/ldap.inc.php");
    require_once("functions.php");

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
        $ou_tree = get_org_units($ldap, $ldap_base);
        $smarty->assign("org_tree", $ou_tree);

        /* Query #3: Get Groups */
        $groups = get_groups($ldap, $ldap_group_base);
        $smarty->assign("ldap_groups", $groups);


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
            if (!empty($value)) {
                $ldaprecord[$key] = $value;
            }
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

                /* STEP 2: Add the user to any groups */
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

                /* STEP 3: Set the user password */
                $encodedPass = array('unicodepwd' => encodePassword($pwdtxt));
                if(ldap_mod_replace ($ldap, $dn, $encodedPass)){
                    // echo "Successfully created new user.<br>";
                    $callback .= '&createaccount=success';
                } else {
                    // echo "LDAP - User creation error $errno  (".ldap_error($ldap).")<br>";
                    error_log("LDAP - User creation error on password change (".ldap_error($ldap).")");
                    $callback .= '&createaccount=pwderror';
                }

                $success_url .= $callback;// Append callback(s)
                header('Location: '.$success_url);//Do the final redirect with callbacks appended
                ldap_close($ldap); //Close connection

            }
        }// END if($ldap)


    }// END POST actions



} else {
    die("You are not allowed to use this resource.<br>Please access this page via index.php.");
}// END Authentication Check