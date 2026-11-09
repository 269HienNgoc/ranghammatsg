<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
Swift3_Dashboard::get_status();
$status = Swift3_Dashboard::$status;
$issues = Swift3_Logger::count_issues();
$overall = (int)$status['overall'];
$value = round($overall / 100 * 138.23, 2);
$failed_images = count(Swift3_Logger::get_logs('image-optimization-failed'));
$dashboard = add_query_arg('page','swift3', admin_url('tools.php'));
?>
<div class="swift3-mini-status-container swift3-tb" data-percentage="<?php echo $overall;?>" data-state="<?php echo ($overall >= 100 ? 'optimized' : 'optimizing');?>">
      <div class="swift3-tb-head">
            <div class="swift3-tb-ring">
                  <svg viewBox="0 0 56 56" aria-hidden="true"><circle class="swift3-tb-ring-track" cx="28" cy="28" r="22"></circle><circle class="swift3-tb-ring-value" cx="28" cy="28" r="22" stroke-dasharray="<?php echo $value;?> 138.23"></circle></svg>
                  <span><?php echo $overall;?></span>
            </div>
            <div class="swift3-tb-heading">
                  <span class="swift3-tb-title"><?php esc_html_e('Whole site', 'swift3');?></span>
                  <span class="swift3-tb-meta"><?php echo esc_html($status['cache']['info']);?></span>
            </div>
      </div>
      <ul class="swift3-tb-steps swift3-tb-values">
            <li><span><?php esc_html_e('Cache', 'swift3');?></span><span class="swift3-cache-percentage"><?php echo esc_html($status['cache']['percentage']);?>%</span></li>
            <li><span><?php esc_html_e('Main optimization', 'swift3');?></span><span class="swift3-main-thread-percentage"><?php echo esc_html($status['main-thread']);?>%</span></li>
            <li class="<?php echo ($status['fine-tuning'] < 100 ? 'is-queued' : '');?>"><span><?php esc_html_e('Fine tuning', 'swift3');?></span><span class="swift3-fine-tuning-percentage"><?php echo esc_html($status['fine-tuning']);?>%</span></li>
            <?php if (swift3_check_option('optimize-images', 'on')):?>
            <li><span><?php esc_html_e('Images', 'swift3');?></span><span><span class="swift3-image-info"><?php echo esc_html($status['images']);?></span> <?php esc_html_e('optimized', 'swift3');?><?php if ($failed_images > 0):?>, <span class="swift3-tb-failed"><?php echo esc_html(sprintf(__('%d failed', 'swift3'), $failed_images));?></span><?php endif;?></span></li>
            <?php endif;?>
      </ul>
      <?php if ($issues > 0):?>
      <a class="swift3-tb-issues" href="<?php echo esc_url($dashboard);?>"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="8" r="6"></circle><path d="M8 5v3.5M8 11h.01"></path></svg><?php echo esc_html(sprintf(_n('%d issue needs attention', '%d issues need attention', $issues, 'swift3'), $issues));?></a>
      <?php endif;?>
      <div class="swift3-tb-actions">
            <div><a href="#" class="swift3-tb-btn" data-swift3="clear-all-cache"><?php esc_html_e('Clear all cache', 'swift3');?></a></div>
      </div>
      <div class="swift3-tb-links">
            <span><a href="#" data-swift3="check-status"><?php esc_html_e('Check status', 'swift3');?></a></span>
            <a href="<?php echo esc_url($dashboard);?>"><?php esc_html_e('Open dashboard', 'swift3');?></a>
      </div>
</div>
