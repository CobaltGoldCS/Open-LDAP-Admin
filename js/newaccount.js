/*
 * Javascript controlling input for attribute editing.
 * Referenced in: newaccount.tpl
 *  
 */

/////////////////
// Select2 JQuery OnLoad Classes for selection boxes.
$(document).ready(function() {

    // Org Unit selection
    $('.select2-org_unit').select2({
        theme: "bootstrap4"
    });

    // Groups selection
    $('.select2-groups').select2({
        // theme: "bootstrap4"
    });


    /////////////////
    // Save selectors for all attribute input fields
    form = document.getElementById("newaccount");// Get form selector with ID "newaccount"
    attributes = form.elements;// Get all form elements on page load from account creation


    /////////////////
    // Displayname auto-fill
    //   Take each js autofill_attributes config item and pass to AutoFill() function
    //   Here we use window[] to interpret string as variable so the selector is passed to AutoFill()
    js_config_obj.autofill_attributes.forEach(function (d) {
        AutoFill(window[d.source],window[d.target]);
    });


    /////////////////
    // Check for uniqueness on LDAP attributes upon user input
    //   This checks user input for uniqueness in real-time by quering LDAP to see if object exists.
    //   This is to avoid LDAP exists error upon new account creation.
    js_config_obj.check_unique.forEach(function (attr) {// For each attribute specified in config.inc.php
        let selector = window[attr];// Save input selector for given attribute
        if ( selector ) {// If field exists
            var message = alertMsg("message-"+attr);// create HTML message element
            selector.addEventListener('input', delay(function (e) {// Add delayed input listener
                query = query_ldap(this.value,attr);// Query LDAP
                if (query.count === 1){
                    selector.style.border = "2px solid red";
                    message.innerHTML = ucFirstLetter(attr)+" already exists.";
                    selector.insertAdjacentElement('afterend',message);
                } else {
                    selector.style.border = "2px solid green";
                    message.remove();
                }
            }, 200));//End delay(), End addEventListener()
        }
    });

});// End $(document).ready()

/////////////////
// Javascript function for controlling creation of new accounts.
function newaccount() {

    // Get form selector
    form = document.getElementById("newaccount");

    // Input validation array for all attributes in form
    var validation_array = [];
    var password_check = false;
    var org_check = false;

    attributes = document.getElementById("newaccount").elements;// Get form elements
    for (var i = 0, iLen = attributes.length; i < iLen; i++) {

        var attribute = attributes[i].name;
        selector = document.getElementById(attribute);
        // console.log(attribute);

        /////////////////
        // Building input validation error message below text input fields
        var message = alertMsg("message-"+attribute);

        /////////////////
        // Input validation
        if (selector != null && selector.id != 'newpassword' && selector.id != 'confirmpassword') {
            validation = EditAttributeValidate(attribute,selector);// Do the validation
            validation_array.push(validation.validated);// Append validation state to array
            err_msg = document.getElementById("message-"+attribute);// Get any previous error messages

            if (validation.validated) {
                selector.style.border = "1px solid #ccc";// Reset border to normal
                if(err_msg) { err_msg.remove(); }// If previous error message exists, remove it
            } else if (selector.style.border != "2px solid red") {// If not already checked
                selector.style.border = "2px solid red";
                message.innerHTML = validation.message;
                selector.insertAdjacentElement('afterend',message);
            } 

        }

    }

    /////////////////
    // Password input validation
    passwd_err_msg = document.getElementById("password-verif");// Get any previous password error messages
    if(passwd_err_msg) { passwd_err_msg.remove(); }// If previous password error message exists, remove it

    if ( attributes.newpassword.value ===  attributes.confirmpassword.value && !isEmpty(attributes.newpassword.value) ) {
        password_check = true;
    } 
    else if ( isEmpty(attributes.newpassword.value) ) {
        message = alertMsg("password-verif","Password cannot be blank.");
        attributes.newpassword.style.border = "2px solid red";
        attributes.newpassword.parentNode.insertAdjacentElement('afterend',message);
    }
    else if (attributes.confirmpassword.style.border != "2px solid red") {
        message = alertMsg("password-verif","The passwords do not match.");
        attributes.confirmpassword.style.border = "2px solid red";
        attributes.confirmpassword.parentNode.insertAdjacentElement('afterend',message);
    } 

    /////////////////
    // Org Unit validation
    org_panel = document.getElementById("org-unit-selection")

    if ( !isEmpty(attributes.org_unit.value) ) {
        org_panel.style.border = "none";
    }
    else if (org_panel.style.border != "2px solid red") {
        org_panel.style.border = "2px solid red";
    } 

    /////////////////
    // Final form validation action
    if (validation_array.every(Boolean) && password_check) {// If all validation conditions are met, do the POST
        form.action = "index.php?page=newaccount";
        form.method = "post";
        return true;
    } else {// Otherwise prevent default
        return false;
    }
    
}
