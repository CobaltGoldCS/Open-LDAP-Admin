/*
 * Custom Javascript Functions
 */

/////////////////
// Custom input validation function
// Expects an LDAP 'attribute' as a string and a DOM selector.
function EditAttributeValidate(attribute,selector){          

    switch (attribute) {
        case "mobile":
            var regex = /^\d{10}$/;
            validated = selector.value.match(regex) ? true : false;
            message = "Input must be a 10-digit phone number without spaces, dashes, or parenthesis.";
            break;
        case "mail":
            var regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            validated = selector.value.match(regex) ? true : false;
            message = "You have not entered a valid email address.";
            break;
        case "physicaldeliveryofficename":
            var regex = /^\d{7}$/;
            validated = selector.value.match(regex) ? true : false;
            message = "Input must be a 7-digit ID.";
            break;
        default:
            validated = true;
            message = "";
    }

    return { validated, message };

};