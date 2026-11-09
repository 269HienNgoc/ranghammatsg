<?php
      $check_rewrites = Swift3_Nginx_Module::check_rewrites();
      $rules = Swift3_Nginx_Module::get_rewrites();
      $host = parse_url(home_url(), PHP_URL_HOST);
?>
<div class="swift3-settings-tab swift3-hidden" data-id="int-nginx">
      <div class="swift3-config-box">
            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Rewrite Rules', 'swift3');?></h4>
                        <?php esc_html_e('Add rewrite rules to your nginx config file.', 'swift3');?>
                        <?php echo sprintf(esc_html__('%sClick here%s to copy the generated rewrite rules to the clipboard', 'swift3'), '<a href="#" class="swift3-copy" data-target="swift3-nginx-rules" data-message="' . esc_html__('Rules has been copied to clipboard.', 'swift3') . '">', '</a>');?>
                        <div class="swift3-button-wrapper">
                              <a href="#" class="swift3-btn swift3-btn-s" data-swift3-modal="swift3-nginx-help"><?php esc_html_e('How to add Nginx rewrites', 'swift3');?></a>
                        </div>
                        <textarea id="swift3-nginx-rules" class="swift3-hidden"><?php echo esc_textarea($rules);?></textarea>
                  </div>
                  <div class="swift3-settings-wrapper-inner swift3-dir-col">
                        <?php if ($check_rewrites == -1):?>
                              <span class="swift3-icon-softfail dashicons dashicons-dismiss"></span>
                              <span><?php esc_html_e('Nginx rules are working, but need to be updated.', 'swift3')?></span>
                        <?php elseif ($check_rewrites == 1):?>
                              <span class="swift3-icon-ok dashicons dashicons-yes-alt"></span>
                              <span><?php esc_html_e('Nginx rules are working.', 'swift3')?></span>
                        <?php else:?>
                              <span class="swift3-icon-fail dashicons dashicons-dismiss"></span>
                              <span><?php esc_html_e('Nginx rules are NOT working. Please add rules to config and reload Nginx.', 'swift3')?></span>
                        <?php endif;?>
                  </div>
            </div>
      </div>

      <dialog id="swift3-nginx-help" class="swift3-modal swift3-modal-help" aria-labelledby="swift3-nginx-help-title">
            <div class="swift3-modal-head">
                  <h2 id="swift3-nginx-help-title"><?php esc_html_e('How to add Nginx rewrites', 'swift3');?></h2>
                  <button type="button" class="swift3-modal-close" data-swift3-modal-close aria-label="<?php esc_attr_e('Close', 'swift3');?>"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 4l8 8M12 4l-8 8"></path></svg></button>
            </div>
            <div class="swift3-modal-body">
                  <p class="swift3-guide-intro"><?php esc_html_e('Nginx doesn\'t read .htaccess files, so Swift Performance can\'t add its rules by itself. Add them to the configuration of this site once, and this tab shows when they work.', 'swift3');?></p>
                  <ol class="swift3-guide">
                        <li>
                              <span class="swift3-guide-title"><?php esc_html_e('Copy the rules', 'swift3');?></span>
                              <span class="swift3-guide-text"><?php esc_html_e('They are generated from your current settings.', 'swift3');?></span>
                              <pre class="swift3-code"><?php echo esc_html($rules);?></pre>
                              <span><button type="button" class="swift3-btn swift3-btn-s swift3-copy" data-target="swift3-nginx-rules" data-message="<?php esc_attr_e('Rules has been copied to clipboard.', 'swift3');?>"><?php esc_html_e('Copy rules', 'swift3');?></button></span>
                        </li>
                        <li>
                              <span class="swift3-guide-title"><?php esc_html_e('Open the Nginx configuration of this site', 'swift3');?></span>
                              <span class="swift3-guide-text"><?php echo sprintf(esc_html__('Usually a file in %1$s or %2$s. Find the %3$s block which has %4$s.', 'swift3'), '<code>/etc/nginx/sites-available/</code>', '<code>/etc/nginx/conf.d/</code>', '<code>server { }</code>', '<code>server_name ' . esc_html($host) . '</code>');?></span>
                        </li>
                        <li>
                              <span class="swift3-guide-title"><?php esc_html_e('Paste the rules into that server block', 'swift3');?></span>
                              <span class="swift3-guide-text"><?php esc_html_e('Put them before the first location block, so the rules of Swift Performance come first.', 'swift3');?></span>
                        </li>
                        <li>
                              <span class="swift3-guide-title"><?php esc_html_e('Test the configuration and reload Nginx', 'swift3');?></span>
                              <pre class="swift3-code">nginx -t
systemctl reload nginx</pre>
                              <span class="swift3-guide-text"><?php esc_html_e('Reload only if the test says the syntax is ok.', 'swift3');?></span>
                        </li>
                        <li>
                              <span class="swift3-guide-title"><?php esc_html_e('Check the status on this tab', 'swift3');?></span>
                              <span class="swift3-guide-text"><?php esc_html_e('Reload this page: the status turns green when the rules work. When you change caching, GZIP or browser cache settings later, it asks you to update the rules.', 'swift3');?></span>
                        </li>
                  </ol>
                  <p class="swift3-guide-note"><?php esc_html_e('No access to the server configuration? Copy the rules and ask your hosting provider to add them to the server block of your site.', 'swift3');?></p>
            </div>
      </dialog>
</div>
