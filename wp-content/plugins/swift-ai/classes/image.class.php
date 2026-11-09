<?php

class Swift3_Image {

      public static function optimize_queue(){
            $start = time();
            $image_queue = (array)get_option('swift3_image_queue');
            foreach ($image_queue as $priority_batch_key => $priority_batch){
                  foreach ((array)$priority_batch as $page_batch_key => $page_batch){
                        $ready = false;
                        foreach ((array)$page_batch as $image_key => $image){
                              $webp = self::get_webp_path($image);
                              if (self::has_webp_path($image) && !file_exists($webp)){
                                    self::webp($image, $webp, $page_batch_key);
                                    Swift3_Analytics::record('image');
                              }
                              $ready = ($ready || file_exists($webp));
                              unset($image_queue[$priority_batch_key][$page_batch_key][$image_key]);
                              update_option('swift3_image_queue', $image_queue, false);
                        }
                        unset($image_queue[$priority_batch_key][$page_batch_key]);

                        if ($ready){
                              Swift3_Warmup::update_url($page_batch_key, array('status' => -2));
                        }
                        update_option('swift3_image_queue', $image_queue, false);
                  }
                  unset($image_queue[$priority_batch_key]);
                  update_option('swift3_image_queue', $image_queue, false);
            }
      }


      public static function webp($file, $output_file, $page_id){

            if (is_link($output_file)){
                  @unlink($output_file);
            }

            if (preg_match('~^(https?:)?//~', $file)){
                  $file = preg_replace('~^//~', 'http://', $file);
                  $rel_path = parse_url($file, PHP_URL_PATH);
                  $root_path = Swift3_Helper::get_root_path();

                  if (is_file($root_path . $rel_path)){
                        $file = $root_path . $rel_path;
                  }
                  else {
                        $response = wp_remote_get(html_entity_decode($file), array('sslverify' => false, 'timeout' => 15,'user-agent' => Swift3_Helper::ua_string('image-optimizer'), 'headers' => array('Referer' => home_url())));
                        if (!is_wp_error($response)){

                              if ($response['response']['code'] != 200){
                                    if ($response['response']['code'] == 404){
                                          Swift3_Logger::log(array('file' => $file, 'error' => '404', 'page_id' => $page_id), 'image-optimization-failed', $file);
                                          self::copy(SWIFT3_DIR . 'assets/images/missing.webp', $output_file);
                                    }
                                    else {
                                          Swift3_Logger::log(array('file' => $file, 'error' => 'HTTP error: ' . $response['response']['code'], 'page_id' => $page_id), 'image-optimization-failed', $file);
                                          self::copy(SWIFT3_DIR . 'assets/images/encode-error.webp', $output_file);
                                    }
                                    return false;
                              }
                              $type = strtolower(trim(current(explode(';', (string)wp_remote_retrieve_header($response, 'content-type')))));

                              if (strpos($type, 'image/') !== false){
                                    $ext = preg_replace('~^image/~', '', $type);
                                    $tmp_file = trailingslashit(sys_get_temp_dir()) . hash('crc32', $file) . '.' .$ext;
                                    file_put_contents($tmp_file, $response['body']);

                                    $file = $tmp_file;
                              }
                              else {
                                    Swift3_Logger::log(array('file' => $file, 'error' => 'bad encoding', 'page_id' => $page_id), 'image-optimization-failed', $file);
                                    self::copy(SWIFT3_DIR . 'assets/images/encode-error.webp', $output_file);
                                    return false;
                              }
                        }
                        else {
                              Swift3_Logger::log(array('file' => $file, 'error' => 'Network error: ' .  $response->get_error_message(), 'page_id' => $page_id), 'image-optimization-failed', $file);
                              self::copy(SWIFT3_DIR . 'assets/images/encode-error.webp', $output_file);
                              return false;
                        }
                  }
            }
            else {
                  $root_path = Swift3_Helper::get_root_path();
                  $file = $root_path . parse_url($file, PHP_URL_PATH);
            }

            $file = urldecode($file);
            $basedir = dirname($output_file);
            if (!file_exists($basedir)){
                  mkdir($basedir, 0777, true);
            }

            if (!is_file($file)) {
                  // Not exists
                  Swift3_Logger::log(array('file' => $file, 'error' => 'file does not exist', 'page_id' => $page_id), 'image-optimization-failed', $file);
                  self::copy(SWIFT3_DIR . 'assets/images/missing.webp', $output_file);
                  return false;
            }

            if (!empty($file) && file_exists($file . '.webp') && is_file($file . '.webp')){
                  self::copy($file . '.webp', $output_file);
                  return true;
            }

            else if (($sibling = preg_replace('~\.(png|jpe?g|gif)$~i', '.webp', $file)) !== $file && file_exists($sibling)){
                  self::copy($sibling, $output_file);
                  return true;
            }

            $file_type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($file_type == 'webp'){
                  if (isset($tmp_file)){
                        @copy($file, $output_file);
                        @unlink($tmp_file);
                        return true;
                  }
                  self::copy($file, $output_file);
                  return true;
            }
            if ($file_type == 'gif'){
                  $response = Swift3::get_module('api')->request('gif2webp', array(
                        'data' => base64_encode(file_get_contents($file))
                  ));

                  if (!is_wp_error($response)){
                        file_put_contents($output_file, base64_decode($response['body']));
                        return true;
                  }
                  else {
                        Swift3_Logger::log(array('file' => $file, 'error' => 'API error: ' .  $response->get_error_message()), 'image-optimization-failed', $file);
                        self::copy(SWIFT3_DIR . 'assets/images/convert-error.webp', $output_file);
                        return false;
                  }
            }

            if (!Swift3_Helper::check_constant('DISABLE_LOCAL_WEBP') && class_exists('Imagick')){
                  $supported_types = \Imagick::queryformats('*');

                  if (in_array('WEBP', $supported_types)){
                        $optimizer = 'imagick';
                  }
            }

            if (!isset($optimizer)){
                  if (!Swift3_Helper::check_constant('DISABLE_LOCAL_WEBP') && function_exists('imagewebp')){
                        $optimizer = 'gd';
                  }
                  else {
                        $optimizer = 'api';
                  }
            }
            if ($optimizer == 'gd') {
                  self::intermission();

                  switch ($file_type) {
                        case 'jpeg':
                        case 'jpg':
                        $image = imagecreatefromjpeg($file);
                        break;

                        case 'png':
                        $image = imagecreatefrompng($file);
                        imagepalettetotruecolor($image);
                        imagealphablending($image, true);
                        imagesavealpha($image, true);
                        break;

                        default:
                        Swift3_Logger::log(array('file' => $file, 'error' => 'unknown type', 'page_id' => $page_id), 'image-optimization-failed', $file);
                        self::copy(SWIFT3_DIR . 'assets/images/convert-error.webp', $output_file);
                        return false;
                  }

                  if (isset($tmp_file) && file_exists($tmp_file)){
                        unlink($tmp_file);
                  }
                  if (empty($image)){
                        Swift3_Logger::log(array('file' => $file, 'error' => 'conversion #1', 'page_id' => $page_id), 'image-optimization-failed', $file);
                        self::copy(SWIFT3_DIR . 'assets/images/convert-error.webp', $output_file);
                        return false;
                  }
                  $result = imagewebp($image, $output_file, SWIFT3_WEBP_QUALITY);
                  if (false === $result) {
                        Swift3_Logger::log(array('file' => $file, 'error' => 'conversion #2', 'page_id' => $page_id), 'image-optimization-failed', $file);
                        self::copy(SWIFT3_DIR . 'assets/images/convert-error.webp', $output_file);
                        return false;
                  }
                  imagedestroy($image);
                  return true;
            }
            else if ($optimizer == 'imagick') {
                  self::intermission();

                  try {
                        $image = new Imagick();
                        $image->readImage($file);

                        if (isset($tmp_file) && file_exists($tmp_file)){
                              unlink($tmp_file);
                        }


                        $image->setImageFormat('webp');
                        $image->setImageCompressionQuality(SWIFT3_WEBP_QUALITY);
                        $image->setOption('webp:lossless', (SWIFT3_WEBP_QUALITY >= 100 ? 'true' : 'false'));

                        $image->writeImage($output_file);
                        return true;
                  }
                  catch (Exception $e){
                        if (isset($tmp_file) && file_exists($tmp_file)){
                              unlink($tmp_file);
                        }
                        Swift3_Logger::log(array('file' => $file, 'error' => 'Imagick: ' . $e->getMessage(), 'page_id' => $page_id), 'image-optimization-failed', $file);
                  }
            }
            else {
                  $response = Swift3::get_module('api')->request('webp', array(
                        'data' => base64_encode(file_get_contents($file))
                  ));

                  if (!is_wp_error($response)){
                        file_put_contents($output_file, base64_decode($response['body']));
                        return true;
                  }
                  else {
                        Swift3_Logger::log(array('file' => $file, 'error' => 'API error: ' .  $response->get_error_message(), 'page_id' => $page_id), 'image-optimization-failed', $file);
                  }
            }

            self::copy(SWIFT3_DIR . 'assets/images/convert-error.webp', $output_file);
            return false;
      }
      public static function delete_image($file){
            $maybe_webp = self::get_webp_path(str_replace(ABSPATH, '', $file));
            if (file_exists($maybe_webp)){
                  @unlink($maybe_webp);
            }

            return $file;
      }
      public static function delete_images(){
            Swift3_Logger::rlogs('image-optimization-failed');
            Swift3_Helper::delete_files(self::get_image_basedir());
      }
      public static function manager_list($dir = '', $offset = 0, $limit = 200){
            $dir = trim(str_replace('\\', '/', $dir), '/');
            $path = self::manager_resolve($dir, true);
            if ($path === false && $dir !== ''){
                  return new WP_Error('invalid', __('This folder does not exist.', 'swift3'));
            }

            $folders = $images = array();
            $entries = ($path !== false ? @scandir($path) : false);
            foreach ((is_array($entries) ? $entries : array()) as $entry){
                  if ($entry === '.' || $entry === '..'){
                        continue;
                  }
                  $abs = $path . $entry;
                  if (is_dir($abs) && !is_link($abs)){
                        $folders[] = array(
                              'name' => $entry,
                              'dir' => ltrim($dir . '/' . $entry, '/'),
                              'items' => max(0, count((array)@scandir($abs)) - 2)
                        );
                  }
                  else if (preg_match('~\.webp$~', $entry)){
                        $images[] = $entry;
                  }
            }
            usort($folders, function($a, $b){
                  if (ctype_digit($a['name']) && ctype_digit($b['name'])){
                        return (int)$b['name'] - (int)$a['name'];
                  }
                  return strnatcasecmp($a['name'], $b['name']);
            });
            natcasesort($images);
            $images = array_values($images);

            $result = array(
                  'dir' => $dir,
                  'crumbs' => array(),
                  'folders' => ($offset == 0 ? $folders : array()),
                  'images' => array(),
                  'total' => count($images),
                  'next' => (count($images) > $offset + $limit ? $offset + $limit : false)
            );

            $crumb = '';
            foreach (array_filter(explode('/', $dir), 'strlen') as $part){
                  $crumb = ltrim($crumb . '/' . $part, '/');
                  $result['crumbs'][] = array('name' => ($crumb === '__e' ? __('External images', 'swift3') : $part), 'dir' => $crumb);
            }

            foreach (array_slice($images, $offset, $limit) as $entry){
                  $result['images'][] = self::manager_info(ltrim($dir . '/' . $entry, '/'));
            }

            return $result;
      }
      public static function manager_info($rel){
            $abs = self::get_image_basedir() . $rel;
            $source = self::manager_source($rel);

            if (is_link($abs)){
                  $real = realpath($abs);
                  if (!file_exists($abs) || strpos((string)$real, trailingslashit((string)realpath(sys_get_temp_dir()))) === 0){
                        $status = 'broken';
                  }
                  else if (strpos((string)$real, (string)realpath(SWIFT3_DIR . 'assets/images')) === 0){
                        $status = 'failed';
                  }
                  else {
                        $status = 'linked';
                  }
            }
            else {
                  $status = 'optimized';
            }

            $original_size = false;
            if (!empty($source['path']) && strpos($source['path'], '//') !== 0){
                  $original = urldecode(Swift3_Helper::get_root_path() . ltrim($source['path'], '/'));
                  $original_size = (file_exists($original) ? filesize($original) : false);
            }

            return array(
                  'rel' => $rel,
                  'name' => $source['name'],
                  'source' => $source['path'],
                  'url' => self::get_image_reluri($abs),
                  'status' => $status,
                  'size' => (file_exists($abs) ? filesize($abs) : 0),
                  'original_size' => $original_size,
                  'time' => (file_exists($abs) ? filemtime($abs) : 0),
                  'can_regenerate' => ($source['path'] !== false)
            );
      }
      public static function manager_source($rel){
            $path = preg_replace('~\.webp$~', '', $rel);
            $has_query = false;
            if (preg_match('~(__pf_[0-9a-f]{8})?-(png|jpe?g|gif)$~', $path, $matches)){
                  $has_query = !empty($matches[1]);
                  $path = substr($path, 0, -strlen($matches[0])) . '.' . $matches[2];
            }

            if (strpos($path, '__e/') === 0){
                  $path = '//' . substr($path, 4);
            }
            else {
                  $path = '/' . $path;
            }

            return array(
                  'name' => basename($path),
                  'path' => ($has_query ? false : $path)
            );
      }
      public static function manager_resolve($rel, $is_dir = false){
            $rel = trim(str_replace('\\', '/', (string)$rel), '/');
            if (preg_match('~(^|/)\.\.?(/|$)~', $rel) || strpos($rel, "\0") !== false){
                  return false;
            }

            $basedir = self::get_image_basedir();
            if ($is_dir){
                  $abs = trailingslashit($basedir . $rel);
                  $real = realpath($abs);
                  return ($real !== false && is_dir($real) && strpos(trailingslashit($real), trailingslashit(realpath($basedir))) === 0 ? $abs : false);
            }
            $abs = $basedir . $rel;
            $real_dir = realpath(dirname($abs));
            if (!preg_match('~\.webp$~', $rel) || $real_dir === false || strpos(trailingslashit($real_dir), trailingslashit(realpath($basedir))) !== 0){
                  return false;
            }
            return ((file_exists($abs) || is_link($abs)) ? $abs : false);
      }
      public static function manager_delete($rel){
            $abs = self::manager_resolve($rel);
            if ($abs === false){
                  return new WP_Error('invalid', __('This image does not exist anymore.', 'swift3'));
            }

            $uri = parse_url(self::get_image_reluri($abs), PHP_URL_PATH);
            @unlink($abs);
            self::manager_forget_errors($rel);

            return array('purged' => self::purge_pages_using($uri));
      }
      public static function manager_regenerate($rel){
            $abs = self::manager_resolve($rel);
            $source = self::manager_source($rel);
            if ($abs === false || $source['path'] === false){
                  return new WP_Error('invalid', __('The original of this image is unknown, it can\'t be regenerated.', 'swift3'));
            }

            @unlink($abs);
            clearstatcache();
            self::manager_forget_errors($rel);
            self::webp($source['path'], $abs, -1);
            clearstatcache();

            return self::manager_info($rel);
      }
      public static function manager_forget_errors($rel){
            $source = self::manager_source($rel);
            if ($source['path'] === false){
                  return;
            }
            if (strpos($source['path'], '//') === 0){
                  Swift3_Logger::rlog('http:' . $source['path']);
                  Swift3_Logger::rlog('https:' . $source['path']);
            }
            else {
                  Swift3_Logger::rlog(urldecode(Swift3_Helper::get_root_path() . ltrim($source['path'], '/')));
            }
      }
      public static function purge_pages_using($uri){
            $cache = Swift3::get_module('cache');
            $basedir = $cache->get_basedir();
            if (empty($uri) || !file_exists($basedir)){
                  return 0;
            }

            $needles = array_unique(array($uri, str_replace('/', '\/', $uri), rawurldecode($uri)));
            $scheme = (parse_url(home_url(), PHP_URL_SCHEME) ?: 'https');
            $urls = array();

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basedir, FilesystemIterator::SKIP_DOTS));
            foreach ($files as $file){
                  if (!preg_match('~\.(html|css)$~', $file->getFilename())){
                        continue;
                  }
                  $content = @file_get_contents($file->getPathname());
                  $found = false;
                  foreach ($needles as $needle){
                        if ($content !== false && strpos($content, $needle) !== false){
                              $found = true;
                              break;
                        }
                  }
                  if (!$found){
                        continue;
                  }
                  $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($basedir)));
                  $rel = preg_replace('~/(_q|__index|__data)(/.*)?$~', '/', $rel);
                  $parts = explode('/', $rel, 2);
                  $urls[$scheme . '://' . $parts[0] . '/' . ltrim(isset($parts[1]) ? $parts[1] : '', '/')] = true;
            }

            foreach (array_keys($urls) as $url){
                  Swift3_Cache::purge_object($url);
            }

            return count($urls);
      }
      public static function get_webp_path($path){
            $path = Swift3_Helper::normalize_url($path);
            $url_parts = parse_url(urldecode($path));
            $postfix = (!empty($url_parts['query']) ? '__pf_' . hash('crc32', $url_parts['query']) : '');

            if ($url_parts !== false && isset($url_parts['path'])){
                  if (preg_match('~^(https?:)?//~', $path)){
                        return self::get_image_basedir() . '__e/' . $url_parts['host'] . preg_replace('~\.(png|jpe?g|gif)~', $postfix . '-$1', html_entity_decode($url_parts['path'])) . '.webp';
                  }
                  else {
                        return self::get_image_basedir() . preg_replace('~\.(png|jpe?g|gif)~', $postfix . '-$1', $url_parts['path']) . '.webp';
                  }
            }
            return $path;
      }

      public static function has_webp_path($path){
            return (strpos(self::get_webp_path($path), self::get_image_basedir()) === 0);
      }

      public static function get_image_basedir(){
            return apply_filters('swift3_image_basedir', WP_CONTENT_DIR . '/swift-ai/images/');
      }

      public static function get_image_reluri($file = ''){
            $basedir = self::get_image_basedir();
            return apply_filters('swift3_image_reluri', WP_CONTENT_URL . '/swift-ai/images/') . ltrim(str_replace('%2F','/',rawurlencode(str_replace($basedir, '', $file))), '/');
      }

      public static function queue_image($path, $priority = 6, $page_id = -1){
            $path = Swift3_Helper::normalize_url(trim($path));
            if (!self::has_webp_path($path)){
                  return;
            }
            $image_id = hash('crc32', $path);
            $image_queue = (array)get_option('swift3_image_queue');
            $image_queue[$priority][$page_id][$image_id] = $path;
            update_option('swift3_image_queue', $image_queue, false);
      }

      public static function has_queued($key){
            $id = Swift3_Warmup::get_id($key);

            foreach ((array)get_option('swift3_image_queue') as $priority_batch){
                  if (!empty($priority_batch[$id])){
                        return true;
                  }
            }
            return false;
      }

      public static function is_image_dir_exists(){
            $dir = self::get_image_basedir();
            if (file_exists($dir)){
                  return true;
            }
            else if (is_writable(WP_CONTENT_DIR)){
                  mkdir ($dir, 0777, true);
                  return true;
            }
            else {
                  Swift3_Logger::log(array('message' => sprintf(__('WP content directory (%s) is not writable for WordPress. Please change the permissions and try again.', 'swift3'), WP_CONTENT_DIR)), 'cache-folder-not-writable', 'wp-content-folder');
                  return false;
            }
      }

      public static function handle_upload($upload){
            $file = (is_array($upload) ? $upload['file'] : $upload);
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), array('jpg', 'jpeg', 'gif', 'png'))){
                  $path = str_replace(ABSPATH, '', $file);
                  self::queue_image($path);
            }

            return $upload;
      }

      public static function copy($source, $dest){
            if (!file_exists($source) || !is_file($source)){
                  return;
            }
            $basedir = dirname($dest);
            if (!file_exists($basedir)){
                  mkdir($basedir, 0777, true);
            }
            if (is_link($dest)){
                  @unlink($dest);
            }

            @$symlink = (!function_exists('symlink') || Swift3_Helper::check_constant('DISABLE_SYMLINK') ? false : symlink(self::relative_path(dirname($dest), $source), $dest));
            if (!$symlink){
                  @copy($source, $dest);
            }
      }
      public static function relative_path($from_dir, $target){
            $from = array_values(array_filter(explode('/', (realpath($from_dir) ?: $from_dir))));
            $to = array_values(array_filter(explode('/', (realpath($target) ?: $target))));
            while (!empty($from) && !empty($to) && $from[0] === $to[0]){
                  array_shift($from);
                  array_shift($to);
            }
            return str_repeat('../', count($from)) . implode('/', $to);
      }

      public static function intermission(){
            if (SWIFT3_IMAGE_INTERMISSION >= 1000000){
                  sleep(round(SWIFT3_IMAGE_INTERMISSION/1000000));
            }
            else {
                  usleep(SWIFT3_IMAGE_INTERMISSION);
            }
      }
}

?>