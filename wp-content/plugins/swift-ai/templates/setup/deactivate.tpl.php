<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
?>
<header class="swift3-setup-top">
      <img id="swift3-logo" src="<?php echo SWIFT3_URL;?>assets/images/logo.svg" width="182" height="40" alt="Swift Performance">
</header>
<main class="swift3-setup-main">
      <section class="swift3-setup-card swift3-setup-deactivate" aria-labelledby="swift3-setup-title">
            <h1 id="swift3-setup-title"><?php esc_html_e('Deactivate Swift Performance', 'swift3');?></h1>
            <ul id="swift3-setup-deactivate">
                  <li>
                        <a href="<?php echo wp_nonce_url(admin_url('plugins.php?swift3-deactivate=temporary'), 'swift3-deactivate')?>" class="swift3-setup-option">
                              <span class="swift3-setup-option-title"><?php esc_html_e('Temporary deactivation', 'swift3');?></span>
                              <span class="swift3-setup-option-text"><?php esc_html_e('Keep settings, cache and optimized images', 'swift3');?></span>
                        </a>
                  </li>
                  <li>
                        <a href="<?php echo wp_nonce_url(admin_url('plugins.php?swift3-deactivate=uninstall'), 'swift3-deactivate')?>" class="swift3-setup-option is-danger">
                              <span class="swift3-setup-option-title"><?php esc_html_e('Deactivate and uninstall', 'swift3');?></span>
                              <span class="swift3-setup-option-text"><?php esc_html_e('All your settings, and optimized images will be lost', 'swift3');?></span>
                        </a>
                  </li>
            </ul>
            <a href="<?php echo admin_url('plugins.php');?>" class="swift3-btn swift3-btn-light swift3-cancel-deactivate"><?php esc_html_e('Cancel', 'swift3');?></a>
      </section>
</main>
