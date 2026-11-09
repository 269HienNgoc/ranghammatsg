<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}

$allowed_html = array('a' => array('href' => array(), 'data-swift3' => array(), 'class' => array(),'title' => array(), 'target' => array()),'br' => array(),'em' => array(),'strong' => array(), 'span' => array(), 'code' => array());
$severity = (isset($data['severity']) ? $data['severity'] : 'warning');
$has_details = (!empty($data['rows']) || !empty($data['chips']));
$permanent = (isset($data['permanent']) && $data['permanent'] === true);
$max_rows = 8;
$tag = ($has_details ? 'details' : 'div');
?>
<<?php echo $tag;?> class="swift3-notice swift3-notice-<?php echo esc_attr($severity);?>" data-notice="<?php echo esc_attr($data['key']);?>" data-hash="<?php echo esc_attr(hash('crc32', json_encode($data)));?>">
      <<?php echo ($has_details ? 'summary' : 'div');?> class="swift3-notice-head">
            <span class="swift3-notice-icon" aria-hidden="true">
                  <?php if ($severity == 'error'):?>
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="8" cy="8" r="6.2"></circle><path d="M8 5v3.5M8 11h.01"></path></svg>
                  <?php elseif ($severity == 'info'):?>
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="8" cy="8" r="6.2"></circle><path d="M8 7.2V11M8 5h.01"></path></svg>
                  <?php else:?>
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2.2l6.3 11H1.7z"></path><path d="M8 6.5v3M8 11.3h.01"></path></svg>
                  <?php endif;?>
            </span>
            <span class="swift3-notice-text">
                  <span class="swift3-notice-title"><?php echo wp_kses($data['title'], $allowed_html);?></span>
                  <?php if (!empty($data['summary'])):?>
                  <span class="swift3-notice-summary"><?php echo wp_kses($data['summary'], $allowed_html);?></span>
                  <?php endif;?>
            </span>
            <?php if (!empty($data['action']) && $data['action'] == 'try-again'):?>
            <a href="#" class="swift3-btn swift3-btn-primary swift3-btn-s" data-swift3="try-again"><?php esc_html_e('Try again', 'swift3');?></a>
            <?php endif;?>
            <?php if ($has_details):?>
            <svg class="swift3-notice-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6l4 4 4-4"></path></svg>
            <?php elseif (!$permanent):?>
            <a href="#" class="swift3-notice-dismiss" data-swift3="dismiss"><?php esc_html_e('Dismiss', 'swift3');?></a>
            <?php endif;?>
      </<?php echo ($has_details ? 'summary' : 'div');?>>
      <?php if ($has_details):?>
      <div class="swift3-notice-details">
            <?php if (!empty($data['chips'])):?>
            <div class="swift3-chips">
                  <?php foreach ($data['chips'] as $label => $count):?>
                  <span class="swift3-chip"><?php echo esc_html($label);?> <b><?php echo (int)$count;?></b></span>
                  <?php endforeach;?>
            </div>
            <?php endif;?>
            <?php if (!empty($data['rows'])):?>
            <div class="swift3-notice-table swift3-cols-<?php echo min(3, count($data['columns']));?>">
                  <?php foreach (array_slice($data['rows'], 0, $max_rows) as $row):?>
                  <div class="swift3-notice-row">
                        <?php foreach ($row as $index => $cell):?>
                        <span class="<?php echo ($index < count($row) - 1 ? 'swift3-mono' : 'swift3-muted');?>" title="<?php echo esc_attr($cell);?>"><?php echo esc_html($cell);?></span>
                        <?php endforeach;?>
                  </div>
                  <?php endforeach;?>
            </div>
            <?php endif;?>
            <div class="swift3-notice-footer">
                  <?php if (!empty($data['rows']) && count($data['rows']) > $max_rows):?>
                  <span class="swift3-muted"><?php echo esc_html(sprintf(__('and %d more', 'swift3'), count($data['rows']) - $max_rows));?></span>
                  <?php endif;?>
                  <?php if (!$permanent):?>
                  <a href="#" class="swift3-btn swift3-btn-s" data-swift3="dismiss"><?php esc_html_e('Dismiss', 'swift3');?></a>
                  <?php endif;?>
            </div>
      </div>
      <?php endif;?>
</<?php echo $tag;?>>
