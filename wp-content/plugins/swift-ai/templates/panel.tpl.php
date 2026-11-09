<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
?>
<header id="swift3-topbar">
      <div class="swift3-brand" role="img" aria-label="Swift Performance">
            <img src="<?php echo esc_url(SWIFT3_URL . 'assets/images/logo.svg');?>" width="182" height="40" alt="">
      </div>
      <nav class="swift3-dashboard-menu" aria-label="<?php esc_attr_e('Swift Performance sections', 'swift3');?>">
            <a href="#overview" class="swift3-dashboard-nav active"><?php esc_html_e('Overview', 'swift3');?></a>
            <a href="#configuration" class="swift3-dashboard-nav"><?php esc_html_e('Configuration', 'swift3');?></a>
            <?php if (!empty(Swift3_Config::$integrations)):?>
            <a href="#integrations" class="swift3-dashboard-nav"><?php esc_html_e('Integrations', 'swift3');?></a>
            <?php endif;?>
      </nav>
      <div class="swift3-topbar-actions">
            <span class="swift3-version">v<?php echo esc_html(Swift3::get_version());?></span>
            <?php if (Swift3::is_update_available()):?>
            <a class="swift3-update-link" href="<?php echo Swift3::get_update_link();?>"><?php echo sprintf(esc_html__('Update available: %s', 'swift3'), '<strong>' . esc_html(Swift3::is_update_available()) . '</strong>');?></a>
            <?php endif;?>
            <?php if (swift3_check_option('api_connection', 'duplex')):?>
            <span class="swift3-api-status swift3-api-ok"><span class="swift3-dot"></span><?php esc_html_e('API connected', 'swift3');?></span>
            <?php elseif (swift3_check_option('api_connection', 'simplex')):?>
            <span class="swift3-api-status swift3-api-limited"><span class="swift3-dot"></span><?php esc_html_e('API: limited connection', 'swift3');?></span>
            <?php else:?>
            <span class="swift3-api-status swift3-api-off"><span class="swift3-dot"></span><?php esc_html_e('API not connected', 'swift3');?></span>
            <?php endif;?>
            <a href="#" data-swift3="clear-cache" class="swift3-btn swift3-btn-primary"><?php esc_html_e('Clear cache','swift3');?></a>
      </div>
</header>
