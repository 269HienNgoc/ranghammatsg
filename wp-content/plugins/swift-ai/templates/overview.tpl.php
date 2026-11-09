<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}

$status = Swift3_Dashboard::$status;
$ring = function($percentage){
      return round(max(0, min(100, (float)$percentage)) / 100 * 138.23, 2) . ' 138.23';
};
$failed_images = count(Swift3_Logger::get_logs('image-optimization-failed'));
$issues = Swift3_Logger::get_issues();
$is_cache = swift3_check_option('caching', 'on');
$is_rendering = (swift3_check_option('optimize-rendering', 'on') || swift3_check_option('optimize-cls', 'on'));
$levels = array(
      'css' => array(__('Off', 'swift3'), __('Critical CSS', 'swift3'), __('Full', 'swift3')),
      'js' => array(__('Off', 'swift3'), __('After load', 'swift3'), __('Smart', 'swift3')),
      'iframes' => array(__('Off', 'swift3'), __('Lazy load', 'swift3'), __('Lazy + poster', 'swift3')),
);
$features = array(
      array(__('Cache', 'swift3'), $is_cache, ($is_cache ? __('On', 'swift3') : __('Preload only', 'swift3')), __('A static copy of every page, served without running WordPress.', 'swift3')),
      array(__('Critical CSS & fonts', 'swift3'), swift3_check_option('optimize-css', 0, '>'), $levels['css'][min(2, (int)swift3_get_option('optimize-css'))], __('Above-the-fold CSS inline, the rest loads without blocking.', 'swift3')),
      array(__('JavaScript delivery', 'swift3'), swift3_check_option('js-delivery', 0, '>'), $levels['js'][min(2, (int)swift3_get_option('js-delivery'))], __('Scripts run when the page actually needs them.', 'swift3')),
      array(__('Images', 'swift3'), swift3_check_option('optimize-images', 'on'), (swift3_check_option('optimize-images', 'on') ? __('On', 'swift3') : __('Off', 'swift3')), __('Compressed, resized to their display size and served as WebP.', 'swift3')),
      array(__('Iframes', 'swift3'), swift3_check_option('optimize-iframes', 0, '>'), $levels['iframes'][min(2, (int)swift3_get_option('optimize-iframes'))], __('Videos and maps load on demand, behind a poster image.', 'swift3')),
      array(__('LCP & CLS', 'swift3'), $is_rendering, ($is_rendering ? __('On', 'swift3') : __('Off', 'swift3')), __('The largest image loads first and nothing shifts while loading.', 'swift3')),
      array(__('DOM rendering', 'swift3'), swift3_check_option('optimize-dom-rendering', 'on'), (swift3_check_option('optimize-dom-rendering', 'on') ? __('On', 'swift3') : __('Off', 'swift3')), __('The browser skips off-screen sections until they scroll into view.', 'swift3')),
      array(__('Code optimizer', 'swift3'), swift3_check_option('code-optimizer', 'on'), (swift3_check_option('code-optimizer', 'on') ? __('On', 'swift3') : __('Off', 'swift3')), __('Deactivates plugins a request doesn\'t need.', 'swift3')),
);
$features_on = count(array_filter($features, function($feature){
      return $feature[1];
}));
$steps = array(
      array(($is_cache ? __('Cache', 'swift3') : __('Preload', 'swift3')), __('Every page is cached first and gets basic optimization right away.', 'swift3')),
      array(__('Main optimization', 'swift3'), __('In parallel, the most important resources of each page are optimized first.', 'swift3')),
      array(__('Fine tuning', 'swift3'), __('Less important resources follow in the background, popular pages before the rest.', 'swift3')),
);
$lead_done = __('Every page is cached and fully optimized.', 'swift3');
$lead_progress = __('Optimization is %d%% complete. The rest is running in the background.', 'swift3');
?>
<div class="swift3-overview">
      <div class="swift3-page-intro">
            <h1><?php esc_html_e('Overview', 'swift3');?></h1>
            <p data-swift3-lead data-done="<?php echo esc_attr($lead_done);?>" data-progress="<?php echo esc_attr($lead_progress);?>"><?php echo esc_html((int)$status['overall'] >= 100 ? $lead_done : sprintf($lead_progress, (int)$status['overall']));?></p>
      </div>

      <section class="swift3-section" aria-labelledby="swift3-status-title">
            <div class="swift3-section-head">
                  <h2 id="swift3-status-title"><?php esc_html_e('Status', 'swift3');?></h2>
            </div>
            <div class="swift3-metrics">
            <div class="swift3-metric">
                  <svg class="swift3-ring" viewBox="0 0 56 56" aria-hidden="true"><circle class="swift3-ring-track" cx="28" cy="28" r="22"></circle><circle class="swift3-ring-value" data-ring="cache" cx="28" cy="28" r="22" stroke-dasharray="<?php echo esc_attr($ring($status['cache']['percentage']));?>"></circle></svg>
                  <div class="swift3-metric-text">
                        <span class="swift3-metric-label"><?php echo (swift3_check_option('caching', 'on') ? esc_html__('Cache', 'swift3') : esc_html__('Preload', 'swift3'));?></span>
                        <span class="swift3-metric-value swift3-cache-percentage"><?php echo esc_html($status['cache']['percentage']);?>%</span>
                        <span class="swift3-metric-caption swift3-cache-info"><?php echo esc_html($status['cache']['info']);?></span>
                  </div>
            </div>
            <div class="swift3-metric">
                  <svg class="swift3-ring" viewBox="0 0 56 56" aria-hidden="true"><circle class="swift3-ring-track" cx="28" cy="28" r="22"></circle><circle class="swift3-ring-value" data-ring="main-thread" cx="28" cy="28" r="22" stroke-dasharray="<?php echo esc_attr($ring($status['main-thread']));?>"></circle></svg>
                  <div class="swift3-metric-text">
                        <span class="swift3-metric-label"><?php esc_html_e('Main optimization', 'swift3');?></span>
                        <span class="swift3-metric-value swift3-main-thread-percentage"><?php echo esc_html($status['main-thread']);?>%</span>
                        <span class="swift3-metric-caption"><?php esc_html_e('Most important resources', 'swift3');?></span>
                  </div>
            </div>
            <div class="swift3-metric">
                  <svg class="swift3-ring" viewBox="0 0 56 56" aria-hidden="true"><circle class="swift3-ring-track" cx="28" cy="28" r="22"></circle><circle class="swift3-ring-value" data-ring="fine-tuning" cx="28" cy="28" r="22" stroke-dasharray="<?php echo esc_attr($ring($status['fine-tuning']));?>"></circle></svg>
                  <div class="swift3-metric-text">
                        <span class="swift3-metric-label"><?php esc_html_e('Fine tuning', 'swift3');?></span>
                        <span class="swift3-metric-value swift3-fine-tuning-percentage"><?php echo esc_html($status['fine-tuning']);?>%</span>
                        <span class="swift3-metric-caption"><?php esc_html_e('Less important resources', 'swift3');?></span>
                  </div>
            </div>
            <div class="swift3-metric">
                  <svg class="swift3-ring" viewBox="0 0 56 56" aria-hidden="true"><circle class="swift3-ring-track" cx="28" cy="28" r="22"></circle><circle class="swift3-ring-value" cx="28" cy="28" r="22" stroke-dasharray="<?php echo esc_attr($ring(swift3_check_option('optimize-images', 'on') ? 100 : 0));?>"></circle><?php if ($failed_images > 0):?><circle class="swift3-ring-failed" cx="28" cy="28" r="22" stroke-dasharray="0 132.7 5.53 138.23"></circle><?php endif;?></svg>
                  <div class="swift3-metric-text">
                        <span class="swift3-metric-label"><?php esc_html_e('Images', 'swift3');?></span>
                        <span class="swift3-metric-value swift3-image-info"><?php echo esc_html($status['images']);?></span>
                        <span class="swift3-metric-caption"><?php esc_html_e('optimized', 'swift3');?><?php if ($failed_images > 0):?>, <a href="#swift3-issues" class="swift3-failed-link"><?php echo esc_html(sprintf(__('%d failed', 'swift3'), $failed_images));?></a><?php endif;?></span>
                  </div>
            </div>
      </div>
      </section>

      <section id="swift3-issues" class="swift3-section" aria-labelledby="swift3-issues-title">
            <div class="swift3-section-head">
                  <h2 id="swift3-issues-title"><?php esc_html_e('Needs attention', 'swift3');?></h2>
                  <span class="swift3-count" data-swift3-issue-count><?php echo count($issues);?></span>
                  <a href="#" class="swift3-section-action" data-swift3-dismiss-all><?php esc_html_e('Dismiss all', 'swift3');?></a>
            </div>
            <div class="swift3-panel">
                  <div id="swift3-notice-wrap">
                        <?php Swift3_Logger::print_messages();?>
                  </div>
                  <div class="swift3-issues-empty">
                        <span class="swift3-clear-icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 8.5l3 3 6-7"></path></svg></span>
                        <span class="swift3-notice-text">
                              <span class="swift3-notice-title"><?php esc_html_e('All clear', 'swift3');?></span>
                              <span class="swift3-notice-summary"><?php esc_html_e('No errors or warnings. New ones show up here.', 'swift3');?></span>
                        </span>
                  </div>
            </div>
      </section>

      <section class="swift3-section" aria-labelledby="swift3-features-title">
            <div class="swift3-section-head">
                  <h2 id="swift3-features-title"><?php esc_html_e('Optimizations', 'swift3');?></h2>
                  <span class="swift3-section-note"><?php echo esc_html(sprintf(__('%1$d of %2$d switched on', 'swift3'), $features_on, count($features)));?></span>
                  <a href="#configuration" class="swift3-dashboard-nav swift3-link"><?php esc_html_e('Configure', 'swift3');?></a>
            </div>
            <ul class="swift3-features">
                  <?php foreach ($features as $feature):?>
                  <li class="swift3-feature <?php echo ($feature[1] ? 'is-on' : 'is-off');?>">
                        <span class="swift3-feature-head"><span class="swift3-dot"></span><span class="swift3-feature-name"><?php echo esc_html($feature[0]);?></span><span class="swift3-feature-value"><?php echo esc_html($feature[2]);?></span></span>
                        <span class="swift3-feature-text"><?php echo esc_html($feature[3]);?></span>
                  </li>
                  <?php endforeach;?>
            </ul>
      </section>

      <section class="swift3-section swift3-tips" aria-labelledby="swift3-tips-title">
            <div class="swift3-section-head">
                  <h2 id="swift3-tips-title"><?php esc_html_e('How Swift works', 'swift3');?></h2>
                  <a href="#" class="swift3-section-action" data-swift3-hide-tips><?php esc_html_e('Hide tips', 'swift3');?></a>
            </div>
            <ol class="swift3-steps">
                  <?php foreach ($steps as $step):?>
                  <li>
                        <span class="swift3-step-title"><?php echo esc_html($step[0]);?></span>
                        <span class="swift3-step-text"><?php echo esc_html($step[1]);?></span>
                  </li>
                  <?php endforeach;?>
            </ol>
      </section>
</div>
