<?php

add_action('wp_head', function(){

      if (apply_filters('swift3_skip_optimizer', false) || isset($_GET['preview']) || isset($_GET['nocache']) || isset($_GET['swift-test-page'])){
            return;
      }

      if (class_exists('COMPLIANZ')){
            echo '<style>body:not(.swift-js) .cmplz-cookiebanner{display:none};</style>';
      }
},8);
