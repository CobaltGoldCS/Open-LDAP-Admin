{if $ldap_authentication and $authenticated}
    <div class="alert alert-primary" id="welcome-bar" {if $editattributeresult neq ""}style="display: none;"{/if}><i class="fa fa-fw fa-info-circle"></i> Welcome, {$displayname}.
    {if $isadmin}<span style="float: right;"> You have admin privileges.</span>{/if}
    </div>
{/if}

{if empty($entry) or $entry.count eq 0} {* If there are no entries to be displayed, show welcome page *}
<a href="index.php">
    <img src="{$logo}" alt="{$msg_title}" class="logo img-responsive center-block" />
</a>

<div class="alert alert-success">{$msg_welcome}</div>

{else} {* Else display the entry *}

    {if $editattributeresult neq '' and isset($msg_{$editattributeresult})}
        {if $editattributeresult eq 'successfuledit'}
            <div class="alert alert-success" id="editattributeresult">
                <i class="fa fa-fw fa-check"></i> {$msg_{$editattributeresult}}
                <span style="float: right;"><button  class="fa fad fa-remove" style="border:none;background:none;" onclick="hidePreviousMessages()"></button></span>
            </div>
        {else}
            <div class="alert alert-warning" id="editattributeresult">
                <i class="fa fa-fw fa-exclamation-triangle"></i> {$msg_{$editattributeresult}}
                <span style="float: right;"><button  class="fa fad fa-remove" style="border:none;background:none;" onclick="hidePreviousMessages()"></button></span>
            </div>
        {/if}
    {elseif $editattributeresult neq ''}
        <div class="alert alert-danger" id="editattributeresult">
            <i class="fa fa-fw fa-exclamation-triangle"></i> Error while editing attribute: {$editattributeresult}
            <span style="float: right;"><button  class="fa fad fa-remove" style="border:none;background:none;" onclick="hidePreviousMessages()"></button></span>
        </div>
    {/if}

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
                <form name="submitedits">
                    <div class="table-responsive">
                    <table class="table table-striped table-hover">
                    {foreach $card_items as $item}
                    {$attribute=$attributes_map.{$item}.attribute}
                    {$type=$attributes_map.{$item}.type}
                    {$faclass=$attributes_map.{$item}.faclass}
                    {$admineditable=$attributes_map.{$item}.admineditable}
                    {$usereditable=$attributes_map.{$item}.usereditable}

                    {if !({$entry.$attribute.0}) && ! $show_undef}
                        {continue}
                    {/if}
                    {* Programming Note: between <tr></tr> tag gets overwritten by editattribute.js upon click event *}
                     <tr id="attribute-{$attribute}">
                        <th class="text-center">
                                <i class="fa fa-fw fa-{$faclass}"></i>
                            </th>
                            <th class="hidden-xs" style="overflow:hidden;text-overflow:ellipsis;white-space: nowrap;">
                                {$msg_label_{$item}}
                            </th>
                            <td>
                            {if ({$entry.$attribute.0})}
                                {foreach $entry.{$attribute} as $value}
                                {include 'value_displayer.tpl' value=$value type=$type truncate_value_after=10000}
                                {/foreach}
                            {else}
                                <i style="color:gray;">{$msg_notdefined}</i><br />
                            {/if}
                            </td>
                            <td class="text-right">
                            {foreach $attribute as $value}
                                {if ($isadmin and $admineditable) or $usereditable}
                                <button type="submit" style="border:none;background:none;" class="fa fa-fw fa-edit" onclick="editAttribute(document.getElementById('attribute-{$value}'),'{$value}')"></button>
                                {/if}
                            {/foreach}
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
        {if $isadmin}
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
                            <i style="color:gray;">{$msg_notdefined}</i><br />
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
        {/if}

    </div>{* End Column 1 *}
    
    <div class="col-md-6">{* Column 2 *}

        {* Organizational Unit *}
        {if $isadmin}
            <div class="panel panel-info">
                
                <div class="panel-heading text-center">
                    <p class="panel-title">
                    <i class="fa fa-fw fa-{$attributes_map.{'organizationalunit'}.faclass}"></i>
                        {$msg_label_organizationalunit}
                    </p>
                </div>

                <div class="panel-body" id="org-unit-selection">

                    <form name="editOU">

                    <table class="table table-striped table-hover">
                        <tr id="organizationalunit">
                            <td class="col-md-6" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {$ou.humanreadable}
                            </td>
                            <td class="text-right">
                            {if ($isadmin and $admineditable) or $usereditable}
                                <button type="submit" style="border:none;background:none;" class="fa fa-fw fa-edit" onclick="editOrgUnit(document.getElementById('organizationalunit'),'org_unit')"></button>
                            {/if}
                            </td>
                        </tr>
                    </table>
                    
                    <input type="hidden" name="dn" value="{$dn}">
                    </form>

                </div>

            </div>
        {/if}

        {* Group Membership *}
        {if $isadmin}
            <div class="panel panel-info">

                <div class="panel-heading text-center">
                    <p class="panel-title">
                        <i class="fa fa-fw fa-group"></i>
                        {$msg_groupmembership}
                    </p>
                </div>

                <div class="panel-body">
                    <form name="edit_groups">
                        <table class="table table-striped table-hover table-responsive" id="edit_groups">
                            {if isset($groups)}
                                {foreach from=$groups item=group key=key}
                                <tr>
                                    <td class="col-md-6"><b>{$key+1}.</b> {$group.name}</td>
                                    <td class="text-right fit"><button style="border: none; background: none; font-size: 18px;" class="fa fa-fw fa-trash" onclick="deleteGroup(this.parentElement,'{$group.dn}')"></button></td>
                                </tr>
                                {/foreach}
                            {else}
                                <tr><td class="col-md-6"><i style="color:gray;">{$msg_nogroupsassigned}</i></td></tr>
                            {/if}
                        </table>
                        {if ($isadmin and $admineditable) or $usereditable}
                            <button class="btn btn-primary" style="margin-top:15px;" onclick="editGroups(document.getElementById('edit_groups'),'{$dn}',this)"><i class="fa fa-fw fa-plus"></i> Add groups</button>
                        {/if}
                        <input type="hidden" name="dn" value="{$dn}" />
                    </form>
                </div>

            </div>
        {/if}
    
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
                    {if $isadmin}
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
                    {/if}
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

{* Page specific JS *}
<script src="js/display.js"></script>