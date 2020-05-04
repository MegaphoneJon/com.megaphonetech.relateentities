<div class="crm-accordion-wrapper crm-search_filters-accordion">
  <a accesskey="N" href="{crmURL p='civicrm/relatedentities/add' q="action=add&reset=1&entityTable=$entityTable&entityId=$entityId"}" class="button"><span><i class="crm-i fa-plus-circle"></i> {ts}New Entity relation{/ts}</span></a>
</div>
</br></br>
{include file="CRM/common/enableDisableApi.tpl"}
{foreach from=$relatedEntities item="relatedEntity" key="entityRef"}
<h3>{ts}{$relatedEntity.label}{/ts}</h3>
<table id='related-entities' class="related-entities-selector-{$entityTable}-{$entityId} crm-ajax-table"
data-ajax="{crmURL p="civicrm/ajax/relatedentities" q="entityTable=$entityTable&entityId=$entityId&entityTableB=$entityRef"}"
dataref='{$entityRef}'>
  <thead>
  <tr>
    {foreach from=$relatedEntity.columns item="header" key="key"}
      <th data-data="{$key}" class="crm-related-entities-{$key}">{ts}{$header}{/ts}</th>
    {/foreach}
    <th data-data="links" data-orderable="false" class="crm-related-entities-links">&nbsp;</th>
  </tr>
  </thead>
</table>
</br></br>
{/foreach}
