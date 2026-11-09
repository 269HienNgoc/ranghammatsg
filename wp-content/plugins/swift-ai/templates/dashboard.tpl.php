<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
?>
<div id="swift3-app">
      <?php Swift3_Helper::print_template('panel');?>
      <div id="swift3-dashboard-wrap">
            <div class="swift3-dashboard-section" data-id="overview">
                  <?php Swift3_Helper::print_template('overview');?>
            </div>
            <?php Swift3_Helper::print_template('config');?>
      </div>
      <?php Swift3_Helper::print_template('image-manager');?>
</div>
<div class="swift3-toast-container"></div>
<div class="swift3-toast-icons-container">
      <div class="swift3-toast-icon-error">
            <?php Swift3_Helper::print_svg('icons/error');?>
      </div>
      <div class="swift3-toast-icon-info">
            <?php Swift3_Helper::print_svg('icons/info');?>
      </div>
      <div class="swift3-toast-icon-success">
            <?php Swift3_Helper::print_svg('icons/success');?>
      </div>
</div>
