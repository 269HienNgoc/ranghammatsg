<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}

$url = Swift3_Helper::get_current_url();
$page = Swift3_Dashboard::page_status($url);
$duplex = swift3_check_option('api_connection', 'duplex');
$path = Swift3_Logger::relative_url($url);

$state_labels = array(
      'optimized' => __('Optimized', 'swift3'),
      'optimizing' => __('Optimizing', 'swift3'),
      'not-cached' => __('Not cached yet', 'swift3'),
      'not-cacheable' => __('Not cached', 'swift3'),
);
$steps = array();
if ($page['cacheable']){
      $pending = ($page['cached'] ? 'queued' : 'waiting');
      $steps[__('Cache', 'swift3')] = ($page['cached'] ? 'done' : 'waiting');
      if (swift3_check_option('optimize-css', 0, '>') && $duplex){
            $steps[__('Critical CSS', 'swift3')] = ($page['optimized'] ? 'done' : $pending);
      }
      if (swift3_check_option('optimize-css', 1, '>') && $duplex){
            $steps[__('Fonts', 'swift3')] = ($page['optimized'] ? 'done' : $pending);
      }
      if (swift3_check_option('optimize-js', 'on') || swift3_check_option('js-delivery', 0, '>')){
            $steps[__('Scripts', 'swift3')] = ((swift3_check_option('api_connection', 'simplex') || swift3_check_option('js-delivery', 2, '!=') || $page['optimized']) ? 'done' : 'partial');
      }
      if (swift3_check_option('optimize-images', 'on')){
            $steps[__('Images', 'swift3')] = (!$page['queued_images'] ? 'done' : 'queued');
      }
      if (swift3_check_option('optimize-iframes', 0, '>') && $duplex){
            $steps[__('Iframes', 'swift3')] = ((swift3_check_option('optimize-iframes', 1) || $page['optimized']) ? 'done' : $pending);
      }
      if (swift3_check_option('optimize-rendering', 'on') && $duplex){
            $steps[__('LCP', 'swift3')] = ($page['optimized'] ? 'done' : $pending);
      }
      if (swift3_check_option('optimize-cls', 'on') && $duplex){
            $steps[__('Layout shift (CLS)', 'swift3')] = ($page['optimized'] ? 'done' : $pending);
      }
      if (swift3_check_option('optimize-dom-rendering', 'on') && $duplex){
            $steps[__('DOM rendering', 'swift3')] = ($page['optimized'] ? 'done' : $pending);
      }
}
$step_labels = array('done' => __('Done', 'swift3'), 'queued' => __('Queued', 'swift3'), 'partial' => __('Partial', 'swift3'), 'waiting' => __('Waiting', 'swift3'));
$value = round($page['percentage'] / 100 * 138.23, 2);
?>
<div class="swift3-mini-status-container swift3-tb" data-percentage="<?php echo (int)$page['percentage'];?>" data-state="<?php echo esc_attr($page['state']);?>">
      <div class="swift3-tb-head">
            <?php if ($page['cacheable']):?>
            <div class="swift3-tb-ring">
                  <svg viewBox="0 0 56 56" aria-hidden="true"><circle class="swift3-tb-ring-track" cx="28" cy="28" r="22"></circle><circle class="swift3-tb-ring-value" cx="28" cy="28" r="22" stroke-dasharray="<?php echo $value;?> 138.23"></circle></svg>
                  <span><?php echo (int)$page['percentage'];?></span>
            </div>
            <?php endif;?>
            <div class="swift3-tb-heading">
                  <span class="swift3-tb-title"><?php echo esc_html($state_labels[$page['state']]);?></span>
                  <span class="swift3-tb-path" title="<?php echo esc_attr($url);?>"><?php echo esc_html($path);?></span>
                  <?php if ($page['cached'] && !empty($page['cts'])):?>
                  <span class="swift3-tb-meta"><?php echo esc_html(sprintf(__('Cached %s ago', 'swift3'), human_time_diff($page['cts'])));?></span>
                  <?php endif;?>
            </div>
      </div>

      <?php if (!$page['cacheable']):?>
      <div class="swift3-tb-section">
            <p class="swift3-tb-text"><?php esc_html_e('Swift doesn\'t cache or optimize this page.', 'swift3');?></p>
            <?php if (!empty($page['notes'])):?>
            <ul class="swift3-tb-notes">
                  <?php foreach ($page['notes'] as $note):?>
                  <li><?php echo esc_html($note);?></li>
                  <?php endforeach;?>
            </ul>
            <?php endif;?>
      </div>
      <div class="swift3-tb-links">
            <a href="<?php echo esc_url(add_query_arg('page','swift3', admin_url('tools.php')) . '#uth-caching');?>"><?php esc_html_e('Cache settings', 'swift3');?></a>
            <a href="<?php echo esc_url(add_query_arg('nocache', mt_rand(0,PHP_INT_MAX), $url))?>" target="_blank"><?php esc_html_e('Unoptimized version', 'swift3');?></a>
      </div>
      <?php else:?>
      <ul class="swift3-tb-steps">
            <?php foreach ($steps as $label => $step):?>
            <li class="is-<?php echo esc_attr($step);?>"><span><?php echo esc_html($label);?></span><span class="swift3-tb-step-state"><?php echo esc_html($step_labels[$step]);?></span></li>
            <?php endforeach;?>
      </ul>
      <div class="swift3-tb-actions">
            <?php if (!$page['cached']):?>
            <div><a href="#" class="swift3-tb-btn swift3-tb-btn-primary" data-swift3="optimize-single" data-url="<?php echo esc_attr($url);?>"><?php esc_html_e('Cache this page now', 'swift3');?></a></div>
            <?php elseif (!$page['optimized']):?>
            <div><a href="#" class="swift3-tb-btn swift3-tb-btn-primary" data-swift3="optimize-single" data-url="<?php echo esc_attr($url);?>"><?php esc_html_e('Optimize this page now', 'swift3');?></a></div>
            <div><a href="#" class="swift3-tb-btn" data-swift3="purge-cache-single" data-url="<?php echo esc_attr($url);?>"><?php esc_html_e('Purge cache and re-optimize', 'swift3');?></a></div>
            <?php else:?>
            <div><a href="#" class="swift3-tb-btn" data-swift3="clear-cache-single" data-url="<?php echo esc_attr($url);?>"><?php esc_html_e('Clear cache', 'swift3');?></a></div>
            <div><a href="#" class="swift3-tb-btn" data-swift3="purge-cache-single" data-url="<?php echo esc_attr($url);?>"><?php esc_html_e('Purge cache and re-optimize', 'swift3');?></a></div>
            <?php endif;?>
      </div>
      <div class="swift3-tb-links">
            <span><a href="#" data-swift3="check-status" data-url="<?php echo esc_attr($url);?>"><?php esc_html_e('Check status', 'swift3');?></a></span>
            <a href="<?php echo esc_url(add_query_arg('nocache', mt_rand(0,PHP_INT_MAX), $url))?>" target="_blank"><?php esc_html_e('Unoptimized version', 'swift3');?></a>
      </div>
      <?php endif;?>
</div>
