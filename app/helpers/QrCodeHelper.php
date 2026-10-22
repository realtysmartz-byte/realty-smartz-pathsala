<?php
namespace App\Helpers;

use Exception;

class QrCodeHelper {

    /**
     * Get clean base application URL
     */
    public static function getBaseUrl(): string {
        $configured = config('APP_URL');
        if (!empty($configured) && !str_contains($configured, 'localhost') && !str_contains($configured, '127.0.0.1')) {
            return rtrim($configured, '/');
        }

        // Check if server host is set (e.g. from web request)
        if (!empty($_SERVER['HTTP_HOST'])) {
            $proto = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ||
                     (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http';
            return $proto . '://' . $_SERVER['HTTP_HOST'];
        }

        if (!empty($configured)) {
            return rtrim($configured, '/');
        }

        return 'https://realtysmartzpathshala.in';
    }

    /**
     * URL for QR Code 1 (Candidate Onboarding)
     */
    public static function getOnboardingUrl(): string {
        return self::getBaseUrl() . '/register?source=qr_onboarding';
    }

    /**
     * URL for QR Code 2 (Aptitude Test Only)
     */
    public static function getAptitudeUrl(): string {
        return self::getBaseUrl() . '/aptitude/register';
    }

    /**
     * Generate SVG string for given text/URL
     *
     * @param string $text
     * @param int $w Module width
     * @param int $h Module height
     * @param string $color Hex color
     * @return string Valid SVG markup
     */
    public static function getSvg(string $text, int $w = 6, int $h = 6, string $color = '#2E2A24'): string {
        self::ensureTcpdfBarcode();
        $barcode = new \TCPDF2DBarcode($text, 'QRCODE,H');
        return (string) $barcode->getBarcodeSVGcode($w, $h, $color);
    }

    /**
     * Generate binary PNG string
     *
     * @param string $text
     * @param int $w
     * @param int $h
     * @param array|string $color Color RGB array or hex string e.g. [31, 92, 74] or '#1F5C4A'
     * @return string Binary PNG string
     */
    public static function getPng(string $text, int $w = 8, int $h = 8, array|string $color = [31, 92, 74]): string {
        if (is_string($color)) {
            $hex = ltrim($color, '#');
            if (strlen($hex) === 6) {
                $color = [
                    hexdec(substr($hex, 0, 2)),
                    hexdec(substr($hex, 2, 2)),
                    hexdec(substr($hex, 4, 2))
                ];
            } else {
                $color = [31, 92, 74];
            }
        }
        self::ensureTcpdfBarcode();
        $barcode = new \TCPDF2DBarcode($text, 'QRCODE,H');
        return (string) $barcode->getBarcodePngData($w, $h, $color);
    }

    /**
     * Generate base64 Data URI suitable for <img src="...">
     */
    public static function getPngDataUri(string $text, int $w = 8, int $h = 8): string {
        $png = self::getPng($text, $w, $h);
        return 'data:image/png;base64,' . base64_encode($png);
    }

    private static function ensureTcpdfBarcode(): void {
        if (!class_exists('TCPDF2DBarcode')) {
            $path = ROOT_PATH . '/vendor/tecnickcom/tcpdf/tcpdf_barcodes_2d.php';
            if (file_exists($path)) {
                require_once $path;
            } else {
                throw new Exception("TCPDF 2D Barcode library not found at {$path}");
            }
        }
    }
}
