<?php
/**
 * Standalone Autoloader for Realty Smartz Pathshala
 * Zero-composer setup compatible with any shared hosting / cPanel environment.
 */

spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) === 0) {
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // PHPMailer namespace
    $pmPrefix = 'PHPMailer\\PHPMailer\\';
    $pmBaseDir = __DIR__ . '/phpmailer/phpmailer/src/';
    $pmLen = strlen($pmPrefix);
    if (strncmp($pmPrefix, $class, $pmLen) === 0) {
        $relativeClass = substr($class, $pmLen);
        $file = $pmBaseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // FPDI namespace (setasign\Fpdi\)
    $fpdiPrefix = 'setasign\\Fpdi\\';
    $fpdiBaseDir = __DIR__ . '/setasign/fpdi/src/';
    $fpdiLen = strlen($fpdiPrefix);
    if (strncmp($fpdiPrefix, $class, $fpdiLen) === 0) {
        $relativeClass = substr($class, $fpdiLen);
        $file = $fpdiBaseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // TCPDF class
    if ($class === 'TCPDF' && file_exists(__DIR__ . '/tecnickcom/tcpdf/tcpdf.php')) {
        require_once __DIR__ . '/tecnickcom/tcpdf/tcpdf.php';
        return;
    }

    // FPDF fallback
    if ($class === 'FPDF' && file_exists(__DIR__ . '/fpdf/fpdf.php')) {
        require_once __DIR__ . '/fpdf/fpdf.php';
        return;
    }
});

// Pre-load FPDF if available
if (file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    require_once __DIR__ . '/fpdf/fpdf.php';
}
