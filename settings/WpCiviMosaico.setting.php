<?php

return array(
    'wp_civi_mosaico_embed_images' => array(
        'group_name' => 'WP Civi Mosaico Preferences',
        'group' => 'wpcivimosaico',
        'name' => 'wp_civi_mosaico_embed_images',
        'type' => 'Boolean',
        'quick_form_type' => 'YesNo',
        'default' => 1,
        'title' => 'Embed images in emails (HTML inline images)',
        'is_domain' => 1,
        'is_contact' => 0,
        'description' => 'If selected, images get embedded into the email (inline images), not referenced as a link.',
        'help_text' => 'Enable if you want images to be embedded into the email (inline images), not referenced as a link. This increases the email size but displays images in the client right away. Do not use if you want to use tracking mechanisms.',
    )
);

?>
