<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['base_url'] = 'http://kelompok10-kelas-b.tekkom.web.id/';

$config['index_page'] = '';  // kosongkan karena pakai .htaccess

$config['uri_protocol']    = 'REQUEST_URI';
$config['url_suffix']      = '';
$config['language']        = 'english';
$config['charset']         = 'UTF-8';
$config['enable_hooks']    = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array']     = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']   = 'c';
$config['function_trigger']     = 'm';
$config['directory_trigger']    = 'd';
$config['error_prefix']    = '<p>';
$config['error_suffix']    = '</p>';
$config['log_threshold']   = 0;
$config['log_path']        = '';
$config['log_file_extension'] = '';
$config['log_file_permissions'] = 0644;
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['cache_path']      = '';
$config['cache_query_string'] = FALSE;
$config['encryption_key']  = 'ecommerce_kampus_unsoed_2024_secret';
$config['sess_driver']     = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_samesite']    = 'Lax';
$config['sess_expiration']  = 7200;
$config['sess_save_path']   = NULL;
$config['sess_match_ip']    = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;
$config['cookie_prefix']   = '';
$config['cookie_domain']   = '';
$config['cookie_path']     = '/';
$config['cookie_secure']   = FALSE;
$config['cookie_httponly']  = FALSE;
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection']  = TRUE;
$config['csrf_token_name']  = 'csrf_token';
$config['csrf_cookie_name'] = 'csrf_cookie';
$config['csrf_expire']      = 7200;
$config['csrf_regenerate']  = TRUE;
$config['csrf_exclude_uris'] = array('auth/google/callback');
$config['output_compression'] = FALSE;
$config['rewrite_short_tags'] = FALSE;
$config['time_reference'] = 'local';
$config['compress_output'] = FALSE;
$config['master_lang']     = 'english';
$config['mime_types']      = array();
$config['proxy_ips']       = '';
