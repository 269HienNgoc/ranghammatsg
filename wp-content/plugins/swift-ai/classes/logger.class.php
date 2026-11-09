<?php

class Swift3_Logger {

      public static $logs = array();

      public function __construct(){
            self::load();
            add_action('shutdown', array($this, 'save'));
            add_action('admin_notices', array(__CLASS__, 'admin_notices'));
      }

      public function save(){
            update_option('swift3_log', (array)self::$logs, false);
      }

      public static function load(){
            self::$logs = array_filter((array)get_option('swift3_log'));
      }
      public static function get_logs($group){
            $result = array();
            foreach (self::$logs as $hash => $entry){
                  if (!empty($entry) && $entry['group'] == $group){
                        $result[$hash] = $entry;
                  }
            }
            return $result;
      }
      public static function log($data, $group, $id){
            $hash = hash('crc32', $id);
            self::$logs[$hash] = array('data' => $data, 'group' => $group);
      }
      public static function rlog($id, $is_hash = false){
            $hash = ($is_hash ? $id : hash('crc32', $id));
            if (isset(self::$logs[$hash])){
                  unset(self::$logs[$hash]);
            }
      }
      public static function rlogs($group){
            foreach(self::get_logs($group) as $hash => $entry){
                  unset(self::$logs[$hash]);
            }
      }
      public static function get_groupped_entries(){
            $groupped = array();

            foreach (self::$logs as $key => $log_entry){
                  $groupped[$log_entry['group']][$key] = $log_entry;
            }

            return $groupped;
      }

      public static function get_issues(){
            $issues = array();
            $groupped = self::get_groupped_entries();

            foreach ($groupped as $group => $log_entries){
                  switch($group){
                        case 'api-error':
                              $errors = self::count_by($log_entries, 'error');
                              $issues[] = array(
                                    'key' => 'group/' . $group,
                                    'severity' => 'error',
                                    'title' => __('API connection error', 'swift3'),
                                    'summary' => key($errors),
                                    'chips' => (count($errors) > 1 ? $errors : array())
                              );
                              break;
                        case 'prebuild-failed':
                        case 'optimization-failed':
                              $count = count($log_entries);
                              $rows = array();
                              foreach ($log_entries as $log_entry){
                                    $rows[] = array(self::relative_url($log_entry['data']['url']), $log_entry['data']['error']);
                              }
                              $issues[] = array(
                                    'key' => 'group/' . $group,
                                    'severity' => 'warning',
                                    'title' => ($group == 'prebuild-failed' ? sprintf(_n('Prebuild failed on %d page', 'Prebuild failed on %d pages', $count, 'swift3'), $count) : sprintf(_n('Optimization failed on %d page', 'Optimization failed on %d pages', $count, 'swift3'), $count)),
                                    'summary' => self::join_counts(self::count_by($log_entries, 'error')),
                                    'columns' => array(__('Page', 'swift3'), __('Reason', 'swift3')),
                                    'rows' => $rows,
                                    'action' => 'try-again'
                              );
                              break;
                        case 'image-optimization-failed':

                              $rows = $pages = $urls = array();
                              foreach ($log_entries as $log_entry){
                                    $page_id = (isset($log_entry['data']['page_id']) ? $log_entry['data']['page_id'] : '');
                                    $pages[$page_id] = true;
                                    if (count($rows) < 8){
                                          if (!isset($urls[$page_id])){
                                                $urls[$page_id] = (!empty($page_id) ? (string)Swift3_Warmup::get_url_by_id($page_id) : '');
                                          }
                                          $page = $urls[$page_id];
                                    }
                                    else {
                                          $page = '';
                                    }
                                    $rows[] = array(self::relative_path($log_entry['data']['file']), (!empty($page) ? self::relative_url($page) : '–'), self::image_error($log_entry['data']['error']));
                              }
                              $count = count($log_entries);
                              $errors = array();
                              foreach ($log_entries as $log_entry){
                                    $error = self::image_error($log_entry['data']['error']);
                                    $errors[$error] = (isset($errors[$error]) ? $errors[$error] + 1 : 1);
                              }
                              arsort($errors);
                              $issues[] = array(
                                    'key' => 'group/' . $group,
                                    'severity' => 'error',
                                    'title' => __('Image optimization failed', 'swift3'),
                                    'summary' => sprintf(_n('%1$d image on %2$d page', '%1$d images on %2$d pages', $count, 'swift3'), $count, count($pages)),
                                    'chips' => $errors,
                                    'columns' => array(__('File', 'swift3'), __('Page', 'swift3'), __('Reason', 'swift3')),
                                    'rows' => $rows
                              );
                              break;
                        case 'plugin-deactivated':
                              $deactivated_plugins = array();
                              foreach ($log_entries as $log_key => $log_entry){
                                    $deactivated_plugins[] = $log_entry['data']['plugin'];
                              }
                              $issues[] = array(
                                    'key' => 'group/' . $group,
                                    'severity' => 'info',
                                    'title' => _n('Conflicting plugin deactivated', 'Conflicting plugins deactivated', count($deactivated_plugins), 'swift3'),
                                    'summary' => implode(', ', $deactivated_plugins)
                              );
                              break;
                        default:
                              foreach ($log_entries as $log_key => $log_entry){
                                    $message = (isset($log_entry['data']['message']) ? $log_entry['data']['message'] : (isset($log_entry['data']['error']) ? $log_entry['data']['error'] : ''));
                                    if (!empty($message)){
                                          $issues[] = array(
                                                'key' => 'id/' . $log_key,
                                                'severity' => (in_array($group, array('license-error', 'cache-folder-not-writable')) ? 'error' : 'warning'),
                                                'title' => $message
                                          );
                                    }
                              }
                              break;
                  }
            }
            if (Swift3_Config::$is_development_mode){
                  $expiry = swift3_get_option('development-mode');
                  $time_format = get_option('time_format');
                  $the_day = (date('z') == date('z', $expiry) ? __('today', 'swift3') : __('tomorrow'));
                  $issues[] = array(
                        'key' => 'id/devmode',
                        'severity' => 'info',
                        'title' => __('Development Mode is active', 'swift3'),
                        'summary' => sprintf(__('Caching and optimization are suspended until %s.', 'swift3'), wp_date($time_format, $expiry) . ' ' . $the_day),
                        'permanent' => true
                  );
            }

            return $issues;
      }
      public static function count_issues(){
            return count(self::get_issues());
      }
      public static function get_messages(){
            $output = array();
            foreach (self::get_issues() as $issue){
                  $output[] = Swift3_Helper::get_template('log-entry', 'tpl', $issue);
            }
            return implode("\n", $output);
      }
      public static function count_by($log_entries, $field){
            $counts = array();
            foreach ($log_entries as $log_entry){
                  $value = (isset($log_entry['data'][$field]) ? (string)$log_entry['data'][$field] : '');
                  $counts[$value] = (isset($counts[$value]) ? $counts[$value] + 1 : 1);
            }
            arsort($counts);
            return $counts;
      }
      public static function join_counts($counts){
            $strings = array();
            foreach ($counts as $label => $count){
                  $strings[] = ($count > 1 ? sprintf('%s (%d)', $label, $count) : $label);
            }
            return implode(', ', $strings);
      }
      public static function relative_path($path){
            return ltrim(str_replace(wp_normalize_path(ABSPATH), '', wp_normalize_path((string)$path)), '/');
      }
      public static function relative_url($url){
            $relative = preg_replace('~^https?://' . preg_quote(parse_url(home_url(), PHP_URL_HOST), '~') . '~', '', (string)$url);
            return (empty($relative) ? '/' : $relative);
      }
      public static function image_error($error){
            switch ($error){
                  case 'file does not exist':
                        return __('File is missing', 'swift3');
                  case '404':
                        return __('HTTP 404', 'swift3');
                  case 'bad encoding':
                        return __('Bad encoding', 'swift3');
                  case 'unknown type':
                        return __('Unknown image type', 'swift3');
                  case 'conversion #1':
                  case 'conversion #2':
                        return __('WebP conversion failed', 'swift3');
                  default:
                        return (string)$error;
            }
      }
      public static function print_messages(){
            echo self::get_messages();
      }
      public static function admin_notices(){
            $groupped = self::get_groupped_entries();

            if (isset($groupped['license-error'])){
                  foreach ($groupped['license-error'] as $error){
                        Swift3_Helper::print_template('admin-notice', 'tpl', array('message' => $error['data']['message']));
                  }
            }

      }
}