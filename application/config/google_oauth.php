<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// =============================================
// Konfigurasi Google Sign-In (OAuth 2.0)
// PANDUAN lengkap ada di file PANDUAN_GOOGLE_SSO.md
// =============================================

$config['google_client_id']     = 'GANTI_DENGAN_CLIENT_ID_KAMU.apps.googleusercontent.com';
$config['google_client_secret'] = 'GANTI_DENGAN_CLIENT_SECRET_KAMU';
$config['google_redirect_uri']  = 'http://ecommerce_kampus.test/auth/google/callback';

// Domain email kampus yang diizinkan login
$config['allowed_domain'] = 'mhs.unsoed.ac.id';

// Endpoint Google OAuth
$config['google_auth_url']     = 'https://accounts.google.com/o/oauth2/v2/auth';
$config['google_token_url']    = 'https://oauth2.googleapis.com/token';
$config['google_userinfo_url'] = 'https://www.googleapis.com/oauth2/v2/userinfo';
