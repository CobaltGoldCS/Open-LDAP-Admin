{if isset($createaccountresult) and $createaccountresult neq 'success'}
    <div class="alert alert-danger" style="margin:15px;"><i class="fa fa-fw fa-exclamation-triangle"></i> LDAP Error: {$createaccountresult}.</div>
{/if}
<form class="form-row" id="newaccount" autocomplete="off">

    <div class="display col-md-7">{* Column 1 *}

        {* Attribute Entry *}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title"><i class="fa fa-fw fa-user"></i>{$msg_newaccountdetails}</p>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                    {foreach $card_items as $item}
                    {$attribute=$attributes_map.{$item}.attribute}
                    {$type=$attributes_map.{$item}.type}
                    {$faclass=$attributes_map.{$item}.faclass}
                    {$admineditable=$attributes_map.{$item}.admineditable}
                    {$usereditable=$attributes_map.{$item}.usereditable}

                    {* Programming Note: between <tr></tr> tag gets overwritten by editattribute.js upon click event *}
                     <tr id="attribute-{$attribute}">
                        <th class="text-center">
                                <i class="fa fa-fw fa-{$faclass}"></i>
                            </th>
                            <th class="hidden-xs" style="overflow:hidden;text-overflow:ellipsis;white-space: nowrap;">
                                {$msg_label_{$item}}
                            </th>
                            <td style="padding: 4px;">
                                <div class="input-group" style="width:100%;">
                                {* <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span> *}
                                    <input type="text" name="{$attribute}" id="{$attribute}" class="form-control" style="border-radius:3px;" placeholder="{$attribute}" />
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                    </table>
                </div>
            </div>
        </div>
        
        <div class="form-group text-center">
            <button type="submit" id="submit-account" class="btn btn-success" style="width:100%;height:60px;" onclick="return newaccount();">
                <i class="fa fa-fw fa-check-square-o"></i> {$msg_newaccountconfirm}
            </button>
        </div>

    </div>{* End Column 1 *}

    <div class="col-md-5">{* Column 2 *}

        {* Org Unit Selection *}    
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-sitemap"></i>
                    {$msg_setorgunit}
                </p>
            </div>

            <div class="panel-body" id="org-unit-selection">
                <select id="org_unit" name="org_unit" class="form-control select2-org_unit">
                    <option value="" selected disabled hidden>Choose an Organizational Unit</option>
                    {foreach $org_tree as $ou}
                        <option value="{$ou.dn}">{$ou.option}</option>
                    {/foreach}
                </select>
            </div>

        </div>

        {* Group Selection *}    
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-group"></i>
                    {$msg_setgroup}
                </p>
            </div>

            <div class="panel-body">
                <select id="ldap_groups" name="ldap_groups[]" data-placeholder="Choose some groups..." class="form-control select2-groups" multiple>
                    {foreach $ldap_groups as $group}{if $group.dn}
                        <option value="{$group.dn}">{$group.option}</option>
                    {/if}{/foreach}
                </select>
            </div>

        </div>

        {* Password Entry *}
        <div class="panel panel-info">
            <div class="panel-heading text-center">
                <p class="panel-title">
                    <i class="fa fa-fw fa-check-circle"></i>
                    {$msg_setpassword}
                </p>
            </div>

            <div class="panel-body">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-fw fa-lock"></i></span>
                            <input type="password" id="newpassword" name="newpassword" class="form-control" placeholder="{$msg_passwordrequired}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-fw fa-check"></i></span>
                            <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" placeholder="{$msg_confirmpassword}" />
                        </div>
                    </div>
            </div>
        </div>

    </div>{* End Column 2 *}
    
</form>