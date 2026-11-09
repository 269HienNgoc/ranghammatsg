<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
?>
<div class="swift3-settings-tab" data-id="uth-scripts">
      <div class="swift3-config-box">
            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Excluded Scripts', 'swift3');?></h4>
                        <?php esc_html_e('Scripts listed here run where they are, as the page has them: Swift does not delay them or load them later. Write one script URL, URL part or script ID per line, for an inline script a part of its code.', 'swift3');?><br><br>
                        <?php esc_html_e('Use it for a script that has to run while the page loads, eg one that processes the images as they appear. If it needs another script (eg jQuery), exclude that one too. For example:', 'swift3');?>
                        <i>/wp-content/themes/my-theme/js/custom.js</i> <?php esc_html_e('or', 'swift3');?>
                        <i>my-script-js</i>
                  </div>

                  <div class="swift3-settings-wrapper-inner">
                        <textarea name="excluded-scripts" class="swift3-manual-trigger"><?php echo esc_textarea(swift3_get_option('excluded-scripts'));?></textarea>
                        <a href="#" class="swift3-save-trigger swift3-btn swift3-btn-light swift3-disabled" data-for="excluded-scripts"><?php esc_html_e('Update changes', 'swift3');?></a>
                  </div>
            </div>
      </div>
</div>
