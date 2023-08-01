/*
 * Javascript controlling input for attribute editing.
 * Referenced in: display.tpl
 *  
 */

/////////////////
// Javascript function for controlling editing of attributes.
function editAttribute(tableRow, attribute) {

    hidePreviousMessages()// Hide any previous messages

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
    input.value = ( targetAttribute.innerText.includes('Not defined') ) ? "" : targetAttribute.innerText;
    input.placeholder = ( targetAttribute.innerText.includes('Not defined') ) ? "" : targetAttribute.innerText;
    input.style = "height:100%;";
    td.appendChild(input);// Wrap text input field in <td></td>
    tableRow.replaceChild(td, targetAttribute);// Replace DOM element with text input field

    /////////////////
    // Building input validation error message within "td:nth-child(3)"
    var message = alertMsg("message-"+attribute);

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
        selector = document.getElementById("edit-"+attribute);
        validation = EditAttributeValidate(attribute,selector);
            if (validation.validated) {
                form.action = "index.php?page=editattribute";
                form.method = "post";
            } else {
                event.preventDefault();// Disable default form submit action
                input.style.border = "2px solid red";
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
// Javascript function for allowing users to request attributes to be changed.
function requestEditAttribute(tableRow, attribute) {

    hidePreviousMessages()// Hide any previous messages

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
    input.value = ( targetAttribute.innerText.includes('Not defined') ) ? "" : targetAttribute.innerText;
    input.placeholder = ( targetAttribute.innerText.includes('Not defined') ) ? "" : targetAttribute.innerText;
    input.style = "height:100%;";
    td.appendChild(input);// Wrap text input field in <td></td>
    tableRow.replaceChild(td, targetAttribute);// Replace DOM element with text input field

    /////////////////
    // Building input validation error message within "td:nth-child(3)"
    var message = alertMsg("message-"+attribute);

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
        selector = document.getElementById("edit-"+attribute);
        validation = EditAttributeValidate(attribute,selector);
            if (validation.validated) {
                form.action = "index.php?page=requestedit";
                form.method = "post";
            } else {
                event.preventDefault();// Disable default form submit action
                input.style.border = "2px solid red";
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
// Javascript function for controlling editing of Organizational Unit.
function editOrgUnit(tableRow, attribute) {

    hidePreviousMessages()// Hide any previous messages

    var targetAttribute = tableRow.querySelector("td:nth-child(1)");// Get attribute cell
    var targetEditButton = tableRow.querySelector("td:nth-child(2)");// Get edit button cell

    /////////////////
    // Save form selector
    form = document.editOU;

    /////////////////
    // Org Unit validation
    org_panel = document.getElementById("org-unit-selection")

    /////////////////
    // Building attribute user text input field within "td:nth-child(2)"
    var ouPicker = document.createElement('select');// Create text input field <button></button>
    ouPicker.appendChild(document.createElement('option'));// Create blank <option></option> to be used as the placeholder option
    ouPicker.id = "edit-org_unit";
    ouPicker.name="org_unit";
    ouPicker.className = "form-control select2-org_unit";
    ouPicker.style = "width:100%;";
    targetAttribute.replaceWith(ouPicker);
    $.ajax({// Do AJAX call to get JSON data FIRST
        type: 'GET',
        url: 'ajax.php',
        dataType: 'json',
        data: {request: 'org_units'},
    }).done(function (response) {// THEN add JSON response to Select2 Options
        $('.select2-org_unit').select2({
            theme: "bootstrap4",
            placeholder: 'Choose an Organizational Unit',
            data: response,
        });
    });

    /////////////////
    // Building input validation error message within "td:nth-child(3)"
    var message = alertMsg("message-"+attribute);

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
        selector = document.getElementById("edit-"+attribute);
        validation = EditAttributeValidate(attribute,selector);
            if (validation.validated) {
                form.action = "index.php?page=editattribute";
                form.method = "post";
            } else {
                event.preventDefault();// Disable default form submit action
                org_panel.style.border = "2px solid red";
                message.innerHTML = validation.message;
                org_panel.insertAdjacentElement('afterend',message);
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
    // Adding save/cancel buttons "td:nth-child(2)"
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

}


/////////////////
// Javascript function for deleting groups from an LDAP account.
function deleteGroup(td, groupDN) {

    hidePreviousMessages()// Hide any previous messages

    event.preventDefault();// Disable default form submit action
    cancelButton = td.firstChild;// Get cancel button selector
    form = document.edit_groups;// Save form selector

    /////////////////
    // Convert delete button to cancel button
    cancelButton.classList.remove('fa-trash');// Remove + icon
    cancelButton.classList.add('fa-remove');// Replace with x icon
    cancelButton.onclick = function(){
        console.log("Exiting");
    };

    /////////////////
    // Building edit submit/save button within "td:nth-child(4)"
    var confirmText = document.createElement('span');
    confirmText.innerHTML = "Are you sure?";
    confirmText.style.cssText = `
        letter-spacing: -.05rem;
        font-size: 12px;
        vertical-align: middle;
        color: #e90000;
    `;

    /////////////////
    // Building edit submit/save button within "td:nth-child(4)"
    var saveButton = document.createElement('button');// Create text input field <button></button>
    saveButton.name = "deleteGroup";// sets $_POST['deleteGroup]
    saveButton.value = groupDN;// sets value for $_POST['deleteGroup]
    saveButton.type = "submit";
    saveButton.style = "border:none;background:none;font-size:18px;color:green;";
    saveButton.className = "fa fad fa-check";
    saveButton.onclick = function(event){
        form.action = "index.php?page=editattribute";
        form.method = "post";
        // event.preventDefault();
        // console.log("index.php?page=editattribute");
    }

    td.prepend(saveButton);// prepend saveButton
    td.prepend(confirmText);

}


/////////////////
// Javascript function for bulk-editing groups on an LDAP account.
function editGroups(table, dn, button) {

    event.preventDefault();// Disable default form submit action
    form = document.edit_groups;// Save form selector

    hidePreviousMessages()// Hide any previous messages

    /////////////////
    // Replace bulk-edit button with cancel edit button
    button.classList.add('btn-danger');// Change button backgroupd to red
    button.firstChild.classList.remove('fa-plus');// Remove + icon
    button.firstChild.classList.add('fa-remove');// Replace with x icon
    button.innerHTML = button.innerHTML.replace('Add groups','Cancel');// Change button text
    button.onclick = function(event){
        console.log('Exiting');
    }

    /////////////////
    // Building attribute user text input field within "td:nth-child(2)"
    var groupPicker = document.createElement('select');// Create text input field <button></button>
    groupPicker.name="ldap_groups[]";
    groupPicker.className = "form-control select2-groups";
    groupPicker.style = "width:100%; height:30px;";
    if ( table ) { table.replaceWith(groupPicker); }
    $.ajax({// STEP1: Do AJAX call to get JSON data FIRST
        type: 'GET',
        url: 'ajax.php',
        dataType: 'json',
        data: {request: 'groups'},
    }).done(function (groups) {// THEN add JSON response to Select2 Options
        
        // STEP 2: Initialize the Select2 object with all available groups
        var groupSelect = $('.select2-groups').select2({
            placeholder: 'Add or remove groups',
            multiple: true,
            data: groups,
        });

        // STEP 3: Do second AJAX call to get existing group memberships and pre-select them
        $.ajax({
            type: 'GET',
            url: 'ajax.php',
            dataType: 'json',
            data: {request: 'group-memberships', dn: dn},
        }).done(function (memberships) {// THEN add JSON response to Select2 Options
            preselected = new Array(); var i = 0;// Initialize some variables
            memberships.forEach(function(m) {// Loop through existing group memberships
                if (groupSelect.find("option[value='" + m.id + "']").length) {// if the option exists
                    preselected[++i] = m.id;// Add it to pre-selection and move to next index
                } else {// Create a new option for it
                    var option = new Option(m.text, m.id, true, true);// Create Option object
                    groupSelect.append(option).trigger('change');// Append the new option
                    preselected[++i] = m.id;// Add it to pre-selection and move to next index
                }
            });
            groupSelect.val(preselected).trigger('change');// Trigger UI update for preselections
        });

    });

    /////////////////
    // Building edit submit/save button
    var saveButton = document.createElement('button');// Create text input field <button></button>
    saveButton.type = "submit";
    saveButton.name = "saveGroups";// sets $_POST['saveGroups]
    saveButton.className = "btn btn-success";
    saveButton.innerHTML = '<i class="fa fa-fw fa-save"></i> Save';
    saveButton.style = "margin-top:15px;margin-left:10px;";
    saveButton.onclick = function(event){
        form.action = "index.php?page=editattribute";
        form.method = "post";
    }
    button.insertAdjacentElement('afterend',saveButton);

}