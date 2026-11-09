<?php

class Swift3_SureCart_Module {

      public function __construct(){
            add_action('init', array($this, 'init'));
            add_filter('swift3_avoid_lazy_iframes', function($list){
                  $checkout_page_id = get_option('surecart_checkout_page_id');
                  if (get_the_id() == $checkout_page_id){
                        return array('.');
                  }
                  return $list;
            });
      }

      public function init(){
            if(defined('SURECART_PLUGIN_FILE')){
                  add_filter('swift3_url_match_excluded', function($urls){
                        foreach (self::get_excluded_pages() as $page_id){
                              $urls[] = get_permalink($page_id);
                        }
                        return $urls;
                  });
                  Swift3_System::register_includes('surecart');
                  add_action('swift3_get_install_steps', function(){
                        Swift3_Setup::add_step(21, esc_html__('SureCart detected', 'swift3'));
                  });
            }

      }
      public static function get_excluded_pages(){
            return apply_filters('swift3_excluded_surecart_pages', Swift3_Helper::$db->get_col("SELECT option_value FROM " . Swift3_Helper::$db->options . " WHERE option_name IN ('surecart_checkout_page_id', 'surecart_dashboard_page_id') AND option_value != ''"));
      }

}

new Swift3_SureCart_Module();