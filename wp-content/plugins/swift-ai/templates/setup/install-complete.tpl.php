<?php
if (!defined('ABSPATH')){
      die('Keep Calm and Carry On');
}
$resources = array(
      array('https://go.swiftperformance.io/documentation/', __('Check documentation', 'swift3'), '<path d="M4 15.5V4.5a1.5 1.5 0 0 1 1.5-1.5H16v11H5.5A1.5 1.5 0 0 0 4 15.5zm0 0A1.5 1.5 0 0 0 5.5 17H16"></path><path d="M7.5 6.5h5"></path>'),
      array('https://go.swiftperformance.io/blog/', __('Check Swift Blog', 'swift3'), '<rect x="4" y="3" width="12" height="14" rx="2"></rect><path d="M7 7h6M7 10h6M7 13h3.5"></path>'),
      array('https://go.swiftperformance.io/fbgroup/', __('Join Facebook community', 'swift3'), '<circle cx="8" cy="7" r="3"></circle><path d="M3 16.5c.7-2.7 2.7-4.3 5-4.3s4.3 1.6 5 4.3"></path><path d="M13.2 4.4a2.6 2.6 0 0 1 0 5"></path><path d="M14.6 12.4c1.3.5 2.2 1.8 2.6 3.6"></path>'),
      array('https://go.swiftperformance.io/share-your-results/', __('Share your results', 'swift3'), '<circle cx="14.5" cy="5" r="2"></circle><circle cx="5.5" cy="10" r="2"></circle><circle cx="14.5" cy="15" r="2"></circle><path d="M7.3 9l5.4-3M7.3 11l5.4 3"></path>'),
);
?>
<header class="swift3-setup-top">
      <img id="swift3-logo" src="<?php echo SWIFT3_URL;?>assets/images/logo.svg" width="182" height="40" alt="Swift Performance">
</header>
<main class="swift3-setup-main">
      <section class="swift3-setup-card swift3-setup-complete" aria-labelledby="swift3-setup-title">
            <div class="swift3-setup-complete-text">
                  <span class="swift3-setup-check" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 8.5l3 3 6-7"></path></svg></span>
                  <h1 id="swift3-setup-title"><?php esc_html_e('Swift Performance install is complete', 'swift3');?></h1>
                  <p class="swift3-setup-lead"><?php esc_html_e('Swift is now optimizing your pages in the background. You can follow the progress on the dashboard.', 'swift3');?></p>
                  <a href="<?php echo menu_page_url('swift3', false);?>" class="swift3-btn swift3-btn-primary swift3-btn-xl"><?php esc_html_e('Go to dashboard', 'swift3');?></a>
                  <ul class="swift3-resource-list">
                        <?php foreach ($resources as $resource):?>
                        <li>
                              <a href="<?php echo esc_url($resource[0]);?>" target="_blank" rel="noopener">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $resource[2];?></svg>
                                    <span><?php echo esc_html($resource[1]);?></span>
                              </a>
                        </li>
                        <?php endforeach;?>
                  </ul>
            </div>
            <div class="swift3-setup-video">
                  <iframe src="https://go.swiftperformance.io/install-complete-video/" title="<?php esc_attr_e('Swift Performance video', 'swift3');?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
      </section>
</main>
