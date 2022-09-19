function editAttribute(tableRow, attribute) {

    var targetAttribute = tableRow.querySelector("td:nth-child(3)");// Get attribute cell
    var targetEditButton = tableRow.querySelector("td:nth-child(4)");// Get edit button cell
    
    /////////////////
    // Save form selector
    form = document.submitedits;

    /////////////////
    // Building attribute user text input field within "td:nth-child(3)"
    var td = document.createElement('td');// Create <td></td>
    td.style = "padding:4px;"

    var input = document.createElement('input');// Create text input field <input></input>
    input.name = "editField";
    input.id = "edit-"+attribute;
    input.className = "form-control";
    input.value = targetAttribute.innerText;
    input.placeholder = targetAttribute.innerText;
    input.style = "height:100%;"
    td.appendChild(input);// Wrap text input field in <td></td>
    tableRow.replaceChild(td, targetAttribute);// Replace DOM element with text input field

    /////////////////
    // Building edit submit/save button within "td:nth-child(4)"
    var saveButton = document.createElement('button');// Create text input field <button></button>
    saveButton.id = "edit-"+attribute;
    saveButton.type = "submit";
    saveButton.style = "border:none;background:none;font-size:18px;";
    saveButton.className = "fa fad fa-save";
    saveButton.onclick = function(){
        form.action = "index.php?page=editattribute";
        form.method = "post";
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