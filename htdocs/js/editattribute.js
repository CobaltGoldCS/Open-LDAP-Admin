// function editattribute(tableRow, attribute, dn) {
function editattribute(tableRow, attribute) {

    var targetAttribute = tableRow.querySelector("td:nth-child(3)");// Get attribute cell
    var targetEditButton = tableRow.querySelector("td:nth-child(4)");// Get edit button cell
    
    var td = document.createElement('td');// Create <td></td>
    td.style = "padding:4px;"

    var input = document.createElement('input');// Create text input field
    input.name = "editField";
    input.className = "form-control";
    input.value = targetAttribute.innerText;
    input.placeholder = targetAttribute.innerText;
    input.style = "height:100%;"
    td.appendChild(input);// Wrap text input field in <td></td>
    tableRow.replaceChild(td, targetAttribute);// Replace DOM element with text input field

    var button = document.createElement('button');// Create text input field
    button.id = "editattribute";
    button.type = "submit";
    button.style = "border:none;background:none;color:green;";
    button.className = "fa fa-fw fa-check-square-o";

    var td2 = document.createElement('td');// Create <td></td>
    td2.appendChild(button);// Wrap text input field in <form></form>
    tableRow.replaceChild(td2, targetEditButton);// Replace DOM element with text input field

    attributeField = document.createElement('input');
    attributeField.type = 'hidden';
    attributeField.name = "attribute";
    attributeField.value = attribute;
    tableRow.appendChild(attributeField);
    
    // dnField = document.createElement('input');
    // dnField.type = 'hidden';
    // dnField.name = "dn";
    // dnField.value = dn;
    // tableRow.appendChild(dnField);

    form = document.getElementById("submitedits");
    form.action = "index.php?page=editattribute";

    // var form = document.createElement('form');// Create <form></form>
    // form.action = "index.php?page=editattribute";
    // form.method = "post";
    // console.log(tableRow.outerHTML);
    // form.innerHTML = tableRow.outerHTML;
    // console.log(tableRow.outerHTML);

    // tableRow.parentElement.replaceChild(form,tableRow);

    // tableRow.appendChild(form, tableRow);// Wrap tr field in <form></form>
    // attributeTable = document.getElementById("attributes");
    // form.appendChild(attributeTable);

    // formStart = '<form method="post" action="index.php?page=editattributed">';
    // tableRow.parentElement.insertAdjacentHTML("afterBegin",formStart);

    // formEnd = '</form>';
    // tableRow.parentElement.insertAdjacentHTML("afterEnd",formEnd);

    console.log(form);

}