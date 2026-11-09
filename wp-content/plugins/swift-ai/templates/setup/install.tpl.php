<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}

$is_cache = swift3_check_option('caching', 'on');
$steps = array(
      array(($is_cache ? __('Cache', 'swift3') : __('Preload', 'swift3')), __('Every page is cached first and gets basic optimization right away.', 'swift3')),
      array(__('Main optimization', 'swift3'), __('In parallel, the most important resources of each page are optimized first.', 'swift3')),
      array(__('Fine tuning', 'swift3'), __('Less important resources follow in the background, popular pages before the rest.', 'swift3')),
);
?>
<header class="swift3-setup-top">
      <img id="swift3-logo" src="<?php echo SWIFT3_URL;?>assets/images/logo.svg" width="182" height="40" alt="Swift Performance">
</header>
<main class="swift3-setup-main">
      <section class="swift3-setup-card" aria-labelledby="swift3-setup-title">
            <h1 id="swift3-setup-title"><?php esc_html_e('Setting up Swift Performance', 'swift3');?></h1>
            <p class="swift3-setup-lead"><?php esc_html_e('Swift checks your site and prepares the optimization. This page moves on by itself when it is done.', 'swift3');?></p>
            <div class="swift3-setup-progress">
                  <div class="swift3-setup-progress-head">
                        <span id="swift3-setup-progress-text"><?php esc_html_e('Initialize...', 'swift3');?></span>
                        <span id="swift3-setup-progress-value">0%</span>
                  </div>
                  <div id="swift3-setup-progress-bar" role="progressbar" aria-labelledby="swift3-setup-title" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div id="swift3-progress-bar-inner"></div>
                  </div>
            </div>
            <ul id="swift3-setup-log" aria-live="polite"></ul>
      </section>
      <section class="swift3-setup-how" aria-labelledby="swift3-setup-how-title">
            <h2 id="swift3-setup-how-title"><?php esc_html_e('How Swift works', 'swift3');?></h2>
            <ol class="swift3-steps">
                  <?php foreach ($steps as $step):?>
                  <li>
                        <span class="swift3-step-title"><?php echo esc_html($step[0]);?></span>
                        <span class="swift3-step-text"><?php echo esc_html($step[1]);?></span>
                  </li>
                  <?php endforeach;?>
            </ol>
      </section>
</main>
