<script type="text/javascript">
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
</script>


{if empty($entry) or $entry.count eq 0} {* If there are no entries to be displayed, show welcome page *}
<a href="index.php">
    <img src="{$logo}" alt="{$msg_title}" class="logo img-responsive center-block" />
</a>

<div class="alert alert-success">{$msg_welcome}</div>
{else} {* Else display the entry *}
<div class="row">
    <div class="display col-md-6">{* Column 1 *}

        {* Display Attributes *}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-{$attributes_map.{$card_title}.faclass}"></i>
                    {$entry.{$attributes_map.{$card_title}.attribute}.0}
                </p>
            </div>

            <div class="panel-body">

                <form id="submitedits" method="post">
                    <div class="table-responsive">
                    <table id="attributes" class="table table-striped table-hover">
                    {foreach $card_items as $item}
                    {$attribute=$attributes_map.{$item}.attribute}
                    {$type=$attributes_map.{$item}.type}
                    {$faclass=$attributes_map.{$item}.faclass}

                    {if !({$entry.$attribute.0}) && ! $show_undef}
                        {continue}
                    {/if}
                    <tr>
                        <th class="text-center">
                                <i class="fa fa-fw fa-{$faclass}"></i>
                            </th>
                            <th class="hidden-xs">
                                {$msg_label_{$item}}
                            </th>
                            <td>
                            {if ({$entry.$attribute.0})}
                                {foreach $entry.{$attribute} as $value}
                                {include 'value_displayer.tpl' value=$value type=$type truncate_value_after=10000}
                                {/foreach}
                            {else}
                                <i>{$msg_notdefined}</i><br />
                            {/if}
                            </td>
                            <td>
                            {if ({$entry.$attribute.0})}
                                {foreach $item as $value}
                                    {* <button type="submit" style="border:none;background:none;" class="fa fa-fw fa-edit" onclick="editattribute(this.parentElement.parentElement,'{$value}','{$dn}')"></button> *}
                                    <button type="submit" style="border:none;background:none;" class="fa fa-fw fa-edit" onclick="editattribute(this.parentElement.parentElement,'{$value}')"></button>
                                {/foreach}
                            {/if}
                            </td>
                        </tr>
                    {/foreach}
                    </table>
                    </div>
                <input type="hidden" name="dn" value="{$dn}">
                </form>
            
            </div>
        </div>

        {* Display Account Status *}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-info-circle"></i>
                    {$msg_accountstatus}
                </p>
            </div>

            <div class="panel-body">

                <div class="table-responsive">
                <table class="table table-striped table-hover">
                {foreach $password_items as $item}
                {$attribute=$attributes_map.{$item}.attribute}
                {$type=$attributes_map.{$item}.type}
                {$faclass=$attributes_map.{$item}.faclass}

                {if !({$entry.$attribute.0}) && ! $show_undef}
                    {continue}
                {/if}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_{$item}}
                        </th>
                        <td class="col-md-6">
                        {if ({$entry.$attribute.0})}
                            {foreach $entry.{$attribute} as $value}
                            {include 'value_displayer.tpl' value=$value type=$type truncate_value_after=10000}
                            {/foreach}
                        {else}
                            <i>{$msg_notdefined}</i><br />
                        {/if}
                        </td>
                    </tr>
                {/foreach}
                {if {$display_password_expiration_date} and {$ldapExpirationDate}}
                    <tr>
                        <th class="col-md-6">
                            {$msg_label_expirationdate}
                        </th>
                        <td class="col-md-6">
                            {include 'value_displayer.tpl' value=$ldapExpirationDate type="date" truncate_value_after=10000}
                        </td>
                    </tr>
                {/if}
                </table>
                </div>

            </div>
        </div>

    </div>{* End Column 1 *}
    
    <div class="col-md-6">{* Column 2 *}

        {if $use_checkpassword and $isadmin and $displayname neq $entry.cn.0}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-check-circle"></i>
                    {$msg_checkpassword}
                </p>
            </div>
    
             <div class="panel-body">
    
                 <form id="checkpassword" method="post" action="index.php?page=checkpassword">
                     {if $checkpasswordresult eq 'passwordrequired'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
                     {/if}
                     {if $checkpasswordresult eq 'ldaperror'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordinvalid}</div>
                     {/if}
                     {if $checkpasswordresult eq 'passwordok'}
                     <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_passwordok}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <div class="input-group">
                             <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
                             <input type="password" name="currentpassword" id="currentpassword" class="form-control" placeholder="{$msg_currentpassword}" />
                         </div>
                     </div>
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                         </button>
                     </div>
                </form>
            </div>
        </div>
        {/if}

        {if $use_resetpassword}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-repeat"></i>
                    {$msg_resetpassword}
                </p>
            </div>

             <div class="panel-body">

                 <form id="resetpassword" method="post" action="index.php?page=resetpassword">
                     {if $resetpasswordresult eq 'passwordrequired'}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrequired}</div>
                     {/if}
                     {if $resetpasswordresult eq 'passwordrefused'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_passwordrefused}</div>
                     {/if}
                     {if $resetpasswordresult eq 'passwordchanged'}
                     <div class="alert alert-success"><i class="fa fa-fw fa-check"></i> {$msg_passwordchanged}</div>
                     {/if}
                     {if $posthookresult}
                     <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-triangle"></i> {$posthookresult}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <div class="input-group">
                             <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
                             <input type="password" name="newpassword" id="newpassword" class="form-control" placeholder="{$msg_newpassword}" />
                         </div>
                     </div>
                     <div class="form-groupi row">
                         <div class="col-md-9"><p>{$msg_forcereset}</p></div>
                         <div class="col-md-3 text-right">
                             <div class="btn-group" data-toggle="buttons">
                                 <label class="btn btn-primary{if $resetpassword_reset_default} active{/if}">
                                 {if $resetpassword_reset_default}
                                     <input type="radio" name="pwdreset" id="true" value="true" checked /> {$msg_true}
                                 {else}
                                     <input type="radio" name="pwdreset" id="true" value="true" /> {$msg_true}
                                 {/if}
                                 </label>
                                 <label class="btn btn-primary{if !$resetpassword_reset_default} active{/if}">
                                 {if !$resetpassword_reset_default}
                                     <input type="radio" name="pwdreset" id="false" value="false" checked /> {$msg_false}
                                 {else}
                                     <input type="radio" name="pwdreset" id="false" value="false" /> {$msg_false}
                                 {/if}
                                 </label>
                             </div>
                         </div>
                     </div>
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-check-square-o"></i> {$msg_submit}
                         </button>
                     </div>
                </form>
            </div>
        </div>
        {/if}

        {if $isLocked}
        <div class="panel panel-danger">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_accountlocked}
                </p>
            </div>

             <div class="panel-body">
                 {if $unlockDate}
                 <p>{$msg_unlockdate} {$unlockDate|date_format:{$date_specifiers}}</p>
                 {/if}
                 {if $use_unlockaccount}
                 <form id="unlockaccount" method="post" action="index.php?page=unlockaccount">
                     {if $unlockaccountresult eq 'ldaperror'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotunlocked}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-unlock"></i> {$msg_unlockaccount}
                         </button>
                     </div>
                 </form>
                 {/if}
            </div>
        </div>
        {/if}

        {if !$isLocked}
        <div class="panel panel-success">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-check-square-o"></i>
                    {$msg_accountunlocked}
                </p>
            </div>

             {if $use_lockaccount}
             <div class="panel-body">
                 <form id="lockaccount" method="post" action="index.php?page=lockaccount">
                     {if $lockaccountresult eq 'ldaperror'}
                     <div class="alert alert-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_accountnotlocked}</div>
                     {/if}
                     <input type="hidden" name="dn" value="{$dn}" />
                     <div class="form-group">
                         <button type="submit" class="btn btn-success">
                             <i class="fa fa-fw fa-lock"></i> {$msg_lockaccount}
                         </button>
                     </div>
                 </form>
            </div>
            {/if}
        </div>
        {/if}

        {if $isExpired}
        <div class="panel panel-danger">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-exclamation-triangle"></i>
                    {$msg_passwordexpired}
                </p>
            </div>
        </div>
        {/if}
   </div>{* End Column 2 *}
</div>
{/if}