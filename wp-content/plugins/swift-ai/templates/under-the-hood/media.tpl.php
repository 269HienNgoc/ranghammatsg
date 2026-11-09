<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
?>
<div class="swift3-settings-tab" data-id="uth-media">
      <div class="swift3-config-box">
            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Optimize images on upload', 'swift3');?></h4>
                        <?php esc_html_e('If you enable this feature, Swift Performance will optimize every image, and generated thumbnails during the upload. If you keep it disabled, then images will be optimized on demand only.', 'swift3');?>
                  </div>

                  <div class="swift3-settings-wrapper-inner">
                        <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-optimize-images-on-upload" name="optimize-images-on-upload" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('optimize-images-on-upload'), 'on');?>><label for="swift3-optimize-images-on-upload"></label>
                  </div>
            </div>

            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Excluded Images', 'swift3');?></h4>
                        <?php esc_html_e('All images listed here will be excluded from image optimization. Write one image URL or URL part per line.', 'swift3');?><br><br>
                        <?php esc_html_e('For example you can exclude a single image, or every image served by a host:', 'swift3');?>
                        <i><?php echo esc_html(home_url('/wp-content/uploads/example.jpg'));?></i> <?php esc_html_e('or', 'swift3');?>
                        <i>cdninstagram.com</i>
                  </div>

                  <div class="swift3-settings-wrapper-inner">
                        <textarea name="excluded-images" class="swift3-manual-trigger"><?php echo esc_textarea(swift3_get_option('excluded-images'));?></textarea>
                        <a href="#" class="swift3-save-trigger swift3-btn swift3-btn-light swift3-disabled" data-for="excluded-images"><?php esc_html_e('Update changes', 'swift3');?></a>
                  </div>
            </div>

            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Lazyload Images', 'swift3');?></h4>
                        <?php esc_html_e('Improve native image lazyloading for better user experience.', 'swift3');?>
                  </div>
                  <div class="swift3-settings-wrapper-inner">
                        <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-lazyload-images" name="lazyload-images" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('lazyload-images'), 'on');?>><label for="swift3-lazyload-images"></label>
                  </div>
            </div>

            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Smart Image Sizing', 'swift3');?></h4>
                        <?php esc_html_e('Automatically resize all images on demand, add missing images size attributes and use the right size for every images.', 'swift3');?>
                  </div>
                  <div class="swift3-settings-wrapper-inner">
                        <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-responsive-images" name="responsive-images" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('responsive-images'), 'on');?>><label for="swift3-responsive-images"></label>
                  </div>
            </div>

            <div data-depend-on="responsive-images" data-depend-value="on">
                  <div class="swift3-settings-wrapper">
                        <div class="swift3-settings-description">
                              <h4><?php esc_html_e('Enforce Image Sizes', 'swift3');?></h4>
                              <?php esc_html_e('In some cases, the image size is not set via CSS, but by using the original image size for the layout. If Smart Image Resizing is enabled in these situations, the layout can break. This feature enforces the use of appropriate image sizes. However, you should only enable it if you encounter layout issues without it.', 'swift3');?>
                        </div>
                        <div class="swift3-settings-wrapper-inner">
                              <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-enforce-image-size" name="enforce-image-size" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('enforce-image-size'), 'on');?>><label for="swift3-enforce-image-size"></label>
                        </div>
                  </div>
            </div>

            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Optimize Rendering', 'swift3');?></h4>
                        <?php esc_html_e('Automatically optimize first contentful paint (FCP) and largest contentful paint (LCP).', 'swift3');?>
                  </div>
                  <div class="swift3-settings-wrapper-inner">
                        <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-optimize-rendering" name="optimize-rendering" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('optimize-rendering'), 'on');?>><label for="swift3-optimize-rendering"></label>
                  </div>
            </div>

            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Optimize CLS', 'swift3');?></h4>
                        <?php esc_html_e('Automatically optimize Cumulative Layout Shift (CLS).', 'swift3');?>
                  </div>
                  <div class="swift3-settings-wrapper-inner">
                        <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-optimize-cls" name="optimize-cls" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('optimize-cls'), 'on');?>><label for="swift3-optimize-cls"></label>
                  </div>
            </div>

            <div class="swift3-settings-wrapper">
                  <div class="swift3-settings-description">
                        <h4><?php esc_html_e('Optimize DOM Rendering', 'swift3');?></h4>
                        <?php esc_html_e('Let the browser skip the rendering of page sections which are below the fold until the visitor scrolls near them. Less main thread work on load and after every DOM change. Requires Optimize CLS.', 'swift3');?>
                  </div>
                  <div class="swift3-settings-wrapper-inner">
                        <input type="checkbox" class="swift3-auto-trigger ios8-switch" id="swift3-optimize-dom-rendering" name="optimize-dom-rendering" value="on"<?php Swift3_Helper::maybe_checked(swift3_get_option('optimize-dom-rendering'), 'on');?>><label for="swift3-optimize-dom-rendering"></label>
                  </div>
            </div>

      </div>
</div>