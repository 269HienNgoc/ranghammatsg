<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
?>
<?php
$segmented = function($name, $label, $labels){
      $current = (int)swift3_get_option($name);
      echo '<div class="swift3-segmented" role="radiogroup" aria-label="' . esc_attr($label) . '">';
      foreach ($labels as $value => $text){
            $id = 'swift3-' . $name . '-' . $value;
            echo '<input type="radio" class="swift3-auto-trigger" id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" value="' . (int)$value . '"' . ($current == $value ? ' checked' : '') . '><label for="' . esc_attr($id) . '">' . esc_html($text) . '</label>';
      }
      echo '</div>';
};
$uth_sections = array(
      'uth-caching' => array(__('Caching', 'swift3'), __('Exclusions, query strings, cookies, GZIP, browser cache', 'swift3')),
      'uth-media' => array(__('Media', 'swift3'), __('Lazyload, image sizing, LCP, CLS, DOM rendering', 'swift3')),
      'uth-scripts' => array(__('Scripts', 'swift3'), __('Excluded scripts', 'swift3')),
      'uth-fragments' => array(__('Fragments', 'swift3'), __('Collage, shortcode fragments', 'swift3')),
      'uth-navigation' => array(__('Navigation', 'swift3'), __('Prefetching the next page', 'swift3')),
      'uth-core' => array(__('Core', 'swift3'), __('HTTP request cache', 'swift3')),
      'uth-system' => array(__('System', 'swift3'), __('License, rewrite rules, admin toolbar, API connection', 'swift3')),
);
?>
<div class="swift3-dashboard-section swift3-hidden" data-id="configuration">
      <div id="swift3-general-settings">
            <div class="swift3-page-intro">
                  <h1><?php esc_html_e('Configuration', 'swift3')?></h1>
                  <p><?php esc_html_e('Changes are saved right away.', 'swift3');?></p>
            </div>
            <div class="swift3-config-grid">
                  <div class="swift3-config-groups">
                        <section class="swift3-section" aria-labelledby="swift3-group-caching">
                              <div class="swift3-section-head"><h2 id="swift3-group-caching"><?php esc_html_e('Caching', 'swift3');?></h2></div>
                              <div class="swift3-config-box">
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Cache', 'swift3');?></h4>
                                                <?php esc_html_e('Static HTML version of every page. Turn it off only when a server level cache does this.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-caching" name="caching" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('caching'), 'on');?>><label for="swift3-caching"><span class="screen-reader-text"><?php esc_html_e('Cache', 'swift3');?></span></label>
                                          </div>
                                    </div>
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Development mode', 'swift3');?></h4>
                                                <?php esc_html_e('Pauses caching and optimization for two hours, or until you turn it off.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-development-mode" name="development-mode" value="on"<?php echo (swift3_check_option('development-mode', time(), '>') ? ' checked' : '');?>><label for="swift3-development-mode"><span class="screen-reader-text"><?php esc_html_e('Development mode', 'swift3');?></span></label>
                                          </div>
                                    </div>
                              </div>
                        </section>

                        <section class="swift3-section" aria-labelledby="swift3-group-code">
                              <div class="swift3-section-head"><h2 id="swift3-group-code"><?php esc_html_e('CSS and JavaScript', 'swift3');?></h2></div>
                              <div class="swift3-config-box">
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Optimize CSS', 'swift3');?></h4>
                                                <?php esc_html_e('CSS and font delivery. Recommended for the best results.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <?php $segmented('optimize-css', __('Optimize CSS', 'swift3'), array(__('Off', 'swift3'), __('Critical CSS', 'swift3'), __('+ Fonts', 'swift3')));?>
                                          </div>
                                    </div>
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Optimize JS', 'swift3');?></h4>
                                                <?php esc_html_e('Faster JavaScript parsing and execution on every page.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-optimize-js" name="optimize-js" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('optimize-js'), 'on');?>><label for="swift3-optimize-js"><span class="screen-reader-text"><?php esc_html_e('Optimize JS', 'swift3');?></span></label>
                                          </div>
                                    </div>
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('JS delivery', 'swift3');?></h4>
                                                <?php esc_html_e('When the page scripts run on cached pages.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <?php $segmented('js-delivery', __('JS delivery', 'swift3'), array(__('Off', 'swift3'), __('After load', 'swift3'), __('Smart', 'swift3')));?>
                                          </div>
                                    </div>
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Code optimizer', 'swift3');?> <span class="swift3-tag"><?php esc_html_e('Experimental', 'swift3');?></span></h4>
                                                <?php esc_html_e('Deactivates plugins a request doesn\'t need, for a faster WP Admin, AJAX and frontend.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-code-optimizer" name="code-optimizer" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('code-optimizer'), 'on');?>><label for="swift3-code-optimizer"><span class="screen-reader-text"><?php esc_html_e('Code optimizer', 'swift3');?></span></label>
                                          </div>
                                    </div>
                              </div>
                        </section>

                        <section class="swift3-section" aria-labelledby="swift3-group-media">
                              <div class="swift3-section-head"><h2 id="swift3-group-media"><?php esc_html_e('Media', 'swift3');?></h2></div>
                              <div class="swift3-config-box">
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Optimize images', 'swift3');?></h4>
                                                <?php esc_html_e('Optimize every image and generate its WebP version on demand.', 'swift3');?>
                                                <div class="swift3-button-wrapper">
                                                      <a href="#" class="swift3-btn swift3-btn-s" data-swift3-image-manager><?php esc_html_e('Manage optimized images', 'swift3');?></a>
                                                      <a href="#" class="swift3-link" data-swift3="delete-images"><?php esc_html_e('Delete optimized images', 'swift3');?></a>
                                                </div>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-optimize-images" name="optimize-images" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('optimize-images'), 'on');?>><label for="swift3-optimize-images"><span class="screen-reader-text"><?php esc_html_e('Optimize images', 'swift3');?></span></label>
                                          </div>
                                    </div>
                                    <div class="swift3-settings-wrapper">
                                          <div class="swift3-settings-description">
                                                <h4><?php esc_html_e('Optimize iframes', 'swift3');?></h4>
                                                <?php esc_html_e('Embedded videos, maps and other iframes.', 'swift3');?>
                                          </div>
                                          <div class="swift3-settings-wrapper-inner">
                                                <?php $segmented('optimize-iframes', __('Optimize iframes', 'swift3'), array(__('Off', 'swift3'), __('Lazy load', 'swift3'), __('Lazy + poster', 'swift3')));?>
                                          </div>
                                    </div>
                              </div>
                        </section>
                  </div>

                  <aside class="swift3-section" aria-labelledby="swift3-uth-title">
                        <div class="swift3-section-head"><h2 id="swift3-uth-title"><?php esc_html_e('Under the hood', 'swift3');?></h2></div>
                        <nav class="swift3-uth-list" aria-label="<?php esc_attr_e('Advanced settings', 'swift3');?>">
                              <?php foreach ($uth_sections as $id => $section):?>
                              <a href="#<?php echo esc_attr($id);?>" class="swift3-dashboard-nav"><span class="swift3-uth-name"><?php echo esc_html($section[0]);?></span><span class="swift3-uth-text"><?php echo esc_html($section[1]);?></span></a>
                              <?php endforeach;?>
                        </nav>
                        <div class="swift3-uth-buttons">
                              <a href="#" class="swift3-btn" data-swift3="purge-cache"><?php esc_html_e('Purge cache', 'swift3');?></a>
                              <a href="#" class="swift3-btn" data-swift3="reset-cache"><?php esc_html_e('Reset cache', 'swift3');?></a>
                        </div>
                  </aside>
            </div>
      </div>

      <div id="swift3-advanced-settings">
            <div class="swift3-dashboard-section-heading">
                  <h2><?php esc_html_e('Under the hood', 'swift3')?></h2>
                  <div class="swift3-advanced-settings-buttons">
                        <a href="#configuration" class="swift3-dashboard-nav swift3-btn"><?php esc_html_e('General settings','swift3');?></a>
                        <a href="#" class="swift3-btn swift3-btn-light" data-swift3="purge-cache"><?php esc_html_e('Purge Cache', 'swift3');?></a>
                        <a href="#" class="swift3-btn swift3-btn-light" data-swift3="reset-cache"><?php esc_html_e('Reset Cache', 'swift3');?></a>
                  </div>
            </div>

            <ul class="swift3-advanced-settings-menu">
                  <li><a href="#uth-caching" class="swift3-dashboard-nav active"><?php esc_html_e('Caching', 'swift3');?></a></li>
                  <li><a href="#uth-media" class="swift3-dashboard-nav active"><?php esc_html_e('Media', 'swift3');?></a></li>
                  <li><a href="#uth-fragments" class="swift3-dashboard-nav active"><?php esc_html_e('Fragments', 'swift3');?></a></li>
                  <li><a href="#uth-navigation" class="swift3-dashboard-nav"><?php esc_html_e('Navigation', 'swift3');?></a></li>
                  <li><a href="#uth-core" class="swift3-dashboard-nav"><?php esc_html_e('Core', 'swift3');?></a></li>
                  <li><a href="#uth-system" class="swift3-dashboard-nav"><?php esc_html_e('System', 'swift3');?></a></li>
            </ul>
            <div class="swift3-advanced-settings-tabs">
                  <?php Swift3_Helper::print_template('under-the-hood/cache');?>
                  <?php Swift3_Helper::print_template('under-the-hood/media');?>
                  <?php Swift3_Helper::print_template('under-the-hood/scripts');?>
                  <?php Swift3_Helper::print_template('under-the-hood/fragments');?>
                  <?php Swift3_Helper::print_template('under-the-hood/navigation');?>
                  <?php Swift3_Helper::print_template('under-the-hood/core');?>
                  <?php Swift3_Helper::print_template('under-the-hood/system');?>
            </div>
      </div>
</div>

<div class="swift3-dashboard-section swift3-hidden" data-id="integrations">
      <div class="swift3-dashboard-section-heading">
            <h2><?php esc_html_e('Integrations', 'swift3')?></h2>
      </div>

      <ul class="swift3-advanced-settings-menu">
            <?php foreach (Swift3_Config::$integrations as $key => $data):?>
                  <li><a href="#int-<?php echo esc_attr($key);?>" class="swift3-dashboard-nav active"><?php echo esc_html($data['title']);?></a></li>
            <?php endforeach;?>
      </ul>
      <div class="swift3-advanced-settings-tabs">
            <?php foreach (Swift3_Config::$integrations as $key => $data):?>
                  <?php call_user_func($data['callback']);?>
            <?php endforeach;?>
      </div>


</div>