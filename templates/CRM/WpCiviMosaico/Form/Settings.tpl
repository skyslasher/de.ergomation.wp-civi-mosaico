{crmScope extensionKey='de.ergomation.wp-civi-mosaico'}
  <div class="crm-block crm-form-block crm-flexmailer-form-block">
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>

    <table class="form-layout">
      <tr class="crm-wp-civi-mosaico-form-block-embed_images">
        <td class="label">{$form.wp_civi_mosaico_embed_images.html}</td>
        <td>{$form.wp_civi_mosaico_embed_images.label} {help id=wp_civi_mosaico_embed_images}</td>
        </td>
      </tr>
    </table>

    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
  </div>
{/crmScope}
