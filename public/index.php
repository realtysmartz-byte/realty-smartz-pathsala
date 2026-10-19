<?php
/**
 * Realty Smartz Pathshala - Public Front Controller
 * Only this folder is exposed to the web root.
 */

declare(strict_types=1);

// Locate APP_PATH dynamically whether running in local root, cPanel public_html, or addon domains
if (!defined('APP_PATH')) {
    $candidates = [
        dirname(__DIR__) . '/app',                               // Local dev or non-separated structure
        dirname(__DIR__) . '/pathshala_app/app',                 // Primary cPanel: /home/USER/public_html -> /home/USER/pathshala_app/app
        dirname(dirname(__DIR__)) . '/pathshala_app/app',        // Addon domain: /home/USER/public_html/addon -> /home/USER/pathshala_app/app
        '/home/' . get_current_user() . '/pathshala_app/app',   // Absolute user home fallback
    ];

    $resolvedApp = null;
    foreach ($candidates as $cand) {
        if (file_exists($cand . '/config/config.php')) {
            $resolvedApp = $cand;
            break;
        }
    }

    if ($resolvedApp) {
        define('APP_PATH', $resolvedApp);
    } else {
        die("Fatal: Application configuration path [APP_PATH] could not be determined. Check your deployment directory.");
    }
}

// Vendor autoloader
$vendorAutoload = dirname(APP_PATH) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// Bootstrap config & routes
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/config/routes.php';

// Dispatch request
Router::dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
