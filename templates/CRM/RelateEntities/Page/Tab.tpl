{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/RelateEntities/Form/RelatedEntity.tpl"}
{else}
<div class="crm-related-entities-{$context}">
  {include file="CRM/RelateEntities/Common.tpl"}
</div>
{/if}
