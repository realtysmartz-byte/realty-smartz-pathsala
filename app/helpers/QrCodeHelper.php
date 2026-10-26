<?php
namespace App\Helpers;

use Exception;

class QrCodeHelper {

    /**
     * Get clean base application URL for QR codes.
     * Always returns a publicly reachable domain so that mobile camera scanning works seamlessly.
     */
    public static function getBaseUrl(): string {
        $qrBase = config('QR_BASE_URL');
        if (!empty($qrBase)) {
            return rtrim($qrBase, '/');
        }

        $configured = config('APP_URL');
        if (!empty($configured) && !str_contains($configured, 'localhost') && !str_contains($configured, '127.0.0.1')) {
            return rtrim($configured, '/');
        }

        // Check if server host is set and is NOT localhost/127.0.0.1
        if (!empty($_SERVER['HTTP_HOST']) && !str_contains($_SERVER['HTTP_HOST'], 'localhost') && !str_contains($_SERVER['HTTP_HOST'], '127.0.0.1')) {
            $proto = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') ||
                     (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ? 'https' : 'http';
            return $proto . '://' . $_SERVER['HTTP_HOST'];
        }

        // Default to public production domain
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
     * Generate SVG string for given text/URL with solid white background and quiet zone.
     * Uses Level 'M' (Medium ~15%) and pure solid black #000000 for instant, universal recognition
     * on all native phone cameras (iOS, Samsung, Xiaomi, Vivo, Oppo) and Google Lens.
     *
     * @param string $text Target URL or string
     * @param int $moduleSize Module width/height in px
     * @param int $marginModules Quiet zone in modules (minimum 4 per ISO/IEC 18004)
     * @param string $color Hex color (defaults to #000000 for 100% optical contrast)
     * @param string $level Error correction level: 'L', 'M', 'Q', 'H' (defaults to 'M')
     * @return string Valid SVG markup
     */
    public static function getSvg(string $text, int $moduleSize = 6, int $marginModules = 4, string $color = '#000000', string $level = 'M'): string {
        self::ensureTcpdfBarcode();
        $barcode = new \TCPDF2DBarcode($text, 'QRCODE,' . strtoupper($level));
        $array = $barcode->getBarcodeArray();

        if (empty($array['bcode'])) {
            return (string) $barcode->getBarcodeSVGcode($moduleSize, $moduleSize, $color);
        }

        $numCols = (int) $array['num_cols'];
        $numRows = (int) $array['num_rows'];
        $totalCols = $numCols + ($marginModules * 2);
        $totalRows = $numRows + ($marginModules * 2);

        $width = $totalCols * $moduleSize;
        $height = $totalRows * $moduleSize;

        $svg = '<?xml version="1.0" standalone="no"?>' . "\n";
        $svg .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . "\n";
        $svg .= '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" version="1.1" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">' . "\n";
        $svg .= "\t" . '<desc>' . htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8') . '</desc>' . "\n";
        $svg .= "\t" . '<rect width="100%" height="100%" fill="#FFFFFF" />' . "\n";
        $svg .= "\t" . '<g id="elements" fill="' . htmlspecialchars($color, ENT_QUOTES | ENT_XML1, 'UTF-8') . '" stroke="none">' . "\n";

        for ($r = 0; $r < $numRows; ++$r) {
            for ($c = 0; $c < $numCols; ++$c) {
                if (!empty($array['bcode'][$r][$c])) {
                    $x = ($c + $marginModules) * $moduleSize;
                    $y = ($r + $marginModules) * $moduleSize;
                    $svg .= "\t\t" . '<rect x="' . $x . '" y="' . $y . '" width="' . $moduleSize . '" height="' . $moduleSize . '" />' . "\n";
                }
            }
        }

        $svg .= "\t" . '</g>' . "\n";
        $svg .= '</svg>' . "\n";

        return $svg;
    }

    /**
     * Generate binary PNG string with solid white background and 4-module quiet zone.
     * Uses Level 'M' and pure solid black [0, 0, 0] for maximum scannability on all devices.
     *
     * @param string $text Target URL or string
     * @param int $moduleSize Size of each QR module in pixels
     * @param int $marginModules Quiet zone margin in modules (minimum 4)
     * @param array|string $color Color RGB array or hex string (defaults to [0, 0, 0])
     * @param string $level Error correction level: 'L', 'M', 'Q', 'H' (defaults to 'M')
     * @return string Binary PNG string
     */
    public static function getPng(string $text, int $moduleSize = 16, int $marginModules = 4, array|string $color = [0, 0, 0], string $level = 'M'): string {
        if (is_string($color)) {
            $hex = ltrim($color, '#');
            if (strlen($hex) === 6) {
                $color = [
                    hexdec(substr($hex, 0, 2)),
                    hexdec(substr($hex, 2, 2)),
                    hexdec(substr($hex, 4, 2))
                ];
            } else {
                $color = [0, 0, 0];
            }
        }

        self::ensureTcpdfBarcode();
        $barcode = new \TCPDF2DBarcode($text, 'QRCODE,' . strtoupper($level));
        $array = $barcode->getBarcodeArray();

        if (empty($array['bcode']) || !function_exists('imagecreatetruecolor')) {
            return (string) $barcode->getBarcodePngData($moduleSize, $moduleSize, $color);
        }

        $numCols = (int) $array['num_cols'];
        $numRows = (int) $array['num_rows'];
        $totalCols = $numCols + ($marginModules * 2);
        $totalRows = $numRows + ($marginModules * 2);

        $width = $totalCols * $moduleSize;
        $height = $totalRows * $moduleSize;

        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $fg = imagecolorallocate($im, $color[0], $color[1], $color[2]);

        // Solid crisp white background (no transparency, high optical contrast)
        imagefill($im, 0, 0, $white);

        for ($r = 0; $r < $numRows; ++$r) {
            for ($c = 0; $c < $numCols; ++$c) {
                if (!empty($array['bcode'][$r][$c])) {
                    $x1 = ($c + $marginModules) * $moduleSize;
                    $y1 = ($r + $marginModules) * $moduleSize;
                    $x2 = $x1 + $moduleSize - 1;
                    $y2 = $y1 + $moduleSize - 1;
                    imagefilledrectangle($im, $x1, $y1, $x2, $y2, $fg);
                }
            }
        }

        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);

        return $pngData;
    }

    /**
     * Generate base64 Data URI suitable for <img src="...">
     */
    public static function getPngDataUri(string $text, int $moduleSize = 8, int $marginModules = 4): string {
        $png = self::getPng($text, $moduleSize, $marginModules);
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
