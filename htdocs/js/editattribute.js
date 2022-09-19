// function editattribute(tableRow, attribute, dn) {
function editattribute(tableRow, attribute) {

    var targetAttribute = tableRow.querySelector("td:nth-child(3)");// Get attribute cell
    var targetEditButton = tableRow.querySelector("td:nth-child(4)");// Get edit button cell
    
    /////////////////
    // Building attribute user text input field within "td:nth-child(3)"
    var td = document.createElement('td');// Create <td></td>
    td.style = "padding:4px;"

    var input = document.createElement('input');// Create text input field <input></input>
    input.name = "editField";
    input.className = "form-control";
    input.value = targetAttribute.innerText;
    input.placeholder = targetAttribute.innerText;
    input.style = "height:100%;"
    td.appendChild(input);// Wrap text input field in <td></td>
    tableRow.replaceChild(td, targetAttribute);// Replace DOM element with text input field

    /////////////////
    // Building edit submit/save button within "td:nth-child(4)"
    var button = document.createElement('button');// Create text input field <button></button>
    button.id = "edit-"+attribute;
    button.type = "submit";
    button.style = "border:none;background:none;color:green;font-weight:16px";
    button.className = "fa fad fa-save";

    var td2 = document.createElement('td');// Create <td></td>
    td2.appendChild(button);// Append button to td2
    tableRow.replaceChild(td2, targetEditButton);// Replace DOM element with text input field

    attributeField = document.createElement('input');// Create <input></input>
    attributeField.type = 'hidden';
    attributeField.name = "attribute";
    attributeField.value = attribute;
    tableRow.appendChild(attributeField);

    /////////////////
    // Enable form-action after initial edit-click
    form = document.getElementById("submitedits");
    form.action = "index.php?page=editattribute";

    /////////////////
    // Disable other edit buttons temporarily for better UX
    var otherButtons = form.getElementsByTagName("button");
    for (let i = 0; i < otherButtons.length; i++){
        if (otherButtons[i].id != "edit-"+attribute) {
            otherButtons[i].disabled = true;
        }
    }

}