function editAttribute(tableRow, attribute) {

    var targetAttribute = tableRow.querySelector("td:nth-child(3)");// Get attribute cell
    var targetEditButton = tableRow.querySelector("td:nth-child(4)");// Get edit button cell
    
    /////////////////
    // Save form selector
    form = document.submitedits;

    /////////////////
    // Hide any messages from previous edits
    message = document.getElementById('editattributeresult');
    if (message) { message.style.display = "none"; }

    /////////////////
    // Building attribute user text input field within "td:nth-child(3)"
    var td = document.createElement('td');// Create <td></td>
    td.style = "padding:4px;"

    var input = document.createElement('input');// Create text input field <input></input>
    input.name = "editField";
    input.id = "edit-"+attribute;
    input.className = "form-control";
    input.value = ( targetAttribute.innerText.includes('Not defined') ) ? "" : targetAttribute.innerText;
    input.placeholder = ( targetAttribute.innerText.includes('Not defined') ) ? "" : targetAttribute.innerText;
    input.style = "height:100%;"
    td.appendChild(input);// Wrap text input field in <td></td>
    tableRow.replaceChild(td, targetAttribute);// Replace DOM element with text input field

    /////////////////
    // Building input validation error message within "td:nth-child(3)"
    var message = document.createElement('div');
    message.style = "font-size:10px;display:table-footer-group;";
    message.className = "alert alert-warning";
    message.role = "alert";

    /////////////////
    // Building edit submit/save button within "td:nth-child(4)"
    var saveButton = document.createElement('button');// Create text input field <button></button>
    saveButton.id = "edit-"+attribute;
    saveButton.type = "submit";
    saveButton.style = "border:none;background:none;font-size:18px;";
    saveButton.className = "fa fad fa-save";
    saveButton.onclick = function(event){
        
    /////////////////
    // Input validation
     validation = validateinput(attribute);
        if (validation.validated) {
            form.action = "index.php?page=editattribute";
            form.method = "post";
        } else {
            event.preventDefault();// Disable default form submit action
            input.style = "border:2px solid red;";
            message.innerHTML = validation.message;
            input.insertAdjacentElement('afterend',message);
        }

    };

    /////////////////
    // Building edit cancel button within "td:nth-child(4)"
    var cancelButton = document.createElement('button');// Create text input field <button></button>
    cancelButton.id = "edit-"+attribute;
    cancelButton.style = "border:none;background:none;font-size:18px;";
    cancelButton.className = "fa fad fa-remove";
    cancelButton.onclick = function(){
        console.log("Exiting");
        // form.preventDefault();
    };

    /////////////////
    // Adding save/cancel buttons "td:nth-child(4)"
    var td2 = document.createElement('td');// Create <td></td>
    td2.style = "width:5%;white-space:nowrap"
    td2.appendChild(saveButton);// Append saveButton to td2
    td2.appendChild(cancelButton);// Append cancelButton to td2
    tableRow.replaceChild(td2, targetEditButton);// Replace DOM element with text input field

    /////////////////
    // Building hidden input for "attribute"
    attributeField = document.createElement('input');// Create <input></input>
    attributeField.type = 'hidden';
    attributeField.name = "attribute";
    attributeField.value = attribute;
    tableRow.appendChild(attributeField);

    /////////////////
    // Disable other edit buttons temporarily for better UX
    var otherButtons = document.submitedits.getElementsByTagName("button");
    for (let i = 0; i < otherButtons.length; i++){
        if (otherButtons[i].id != "edit-"+attribute) {
            otherButtons[i].disabled = true;
            otherButtons[i].style.display = 'none';
        }
    }

}

/////////////////
// Custom input validation function
function validateinput(attribute){          

    selector = document.getElementById("edit-"+attribute);
    
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