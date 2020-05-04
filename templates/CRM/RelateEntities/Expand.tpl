{crmScript file='js/crm.expandRow.js'}
{literal}
<script type="text/javascript">
  CRM.$(function($) {
    let rows = {/literal}{$rows|@json_encode}{literal};
    $('table tr th:first').before('<th></th>');
    $.each(rows, function(index, value) {
      $('table tr#financial_type-' + value['id'] + ' td:first').before(value['expand']);
    });
  });
</script>
{/literal}
