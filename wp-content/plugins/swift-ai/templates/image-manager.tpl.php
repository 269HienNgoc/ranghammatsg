<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}

$i18n = array(
      'optimized' => __('Optimized', 'swift3'),
      'linked' => __('Existing WebP', 'swift3'),
      'failed' => __('Failed', 'swift3'),
      'broken' => __('Broken link', 'swift3'),
      'regenerate' => __('Regenerate', 'swift3'),
      'delete' => __('Delete', 'swift3'),
      'confirm' => __('Delete?', 'swift3'),
      'items' => __('%d items', 'swift3'),
      'images' => __('%d images', 'swift3'),
      'empty' => __('No optimized images here.', 'swift3'),
      'regenerated' => __('Image regenerated.', 'swift3'),
      'deleted' => __('Image deleted.', 'swift3'),
      'deleted-purged' => __('Image deleted, %d cached pages purged.', 'swift3'),
      'no-source' => __('The original is unknown (it had a query string), delete it and it will be optimized again.', 'swift3'),
      'root' => __('All images', 'swift3'),
      'external' => __('External images', 'swift3'),
);
?>
<dialog id="swift3-image-manager" class="swift3-modal" aria-labelledby="swift3-image-manager-title" data-i18n="<?php echo esc_attr(wp_json_encode($i18n));?>">
      <div class="swift3-modal-head">
            <h2 id="swift3-image-manager-title"><?php esc_html_e('Optimized images', 'swift3');?></h2>
            <input type="search" class="swift3-im-filter" placeholder="<?php esc_attr_e('Filter this folder', 'swift3');?>" aria-label="<?php esc_attr_e('Filter this folder', 'swift3');?>">
            <button type="button" class="swift3-modal-close" data-swift3-modal-close aria-label="<?php esc_attr_e('Close', 'swift3');?>"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 4l8 8M12 4l-8 8"></path></svg></button>
      </div>
      <nav class="swift3-im-crumbs" aria-label="<?php esc_attr_e('Folder', 'swift3');?>"></nav>
      <div class="swift3-im-list" aria-live="polite"></div>
      <div class="swift3-im-foot">
            <span class="swift3-im-count"></span>
            <button type="button" class="swift3-btn swift3-btn-s swift3-im-more swift3-hidden"><?php esc_html_e('Load more', 'swift3');?></button>
      </div>
</dialog>
