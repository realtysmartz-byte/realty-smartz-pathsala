<?php
// ================================================================
// Realty Smartz Pathshala - Production Asset Compiler & Bundler
// Strict Brand Check: Enforces zero blue & generates hashed assets
// ================================================================

$rootDir = dirname(__DIR__);
$publicDir = $rootDir . '/public/assets';
$distDir = $publicDir . '/dist';

if (!is_dir($distDir)) {
    mkdir($distDir, 0777, true);
}

echo "=== [1/4] Compiling Sass to Compressed CSS ===\n";
$cssOutput = $publicDir . '/css/style.css';
$sassCmd = "npx -y sass --style=compressed " . escapeshellarg($rootDir . '/src/scss/app.scss') . " " . escapeshellarg($cssOutput);
passthru($sassCmd, $sassCode);
if ($sassCode !== 0) {
    die("ERROR: Sass compilation failed with exit code $sassCode\n");
}

$cssContent = file_get_contents($cssOutput);
if (!$cssContent) {
    die("ERROR: Compiled CSS is empty or missing!\n");
}

echo "=== [2/4] Verifying Strict Brand Color Palette (Zero Blue Check) ===\n";
$forbiddenBlues = [
    '#0d6efd',
    '#3447e6',
    '#0a58ca',
    '13, 110, 253',
    '13,110,253'
];

foreach ($forbiddenBlues as $blue) {
    if (stripos($cssContent, $blue) !== false) {
        die("FATAL BUILD ERROR: Forbidden blue token '$blue' detected in compiled CSS!\n");
    }
}
echo "PASSED: Compiled CSS is 100% free of Bootstrap default blue.\n";

$cssHash = substr(md5($cssContent), 0, 10);
$hashedCssFile = "app.{$cssHash}.css";
file_put_contents("{$distDir}/{$hashedCssFile}", $cssContent);
echo "Generated: public/assets/dist/{$hashedCssFile} (" . round(strlen($cssContent) / 1024, 1) . " KB)\n";

echo "=== [3/4] Bundling JavaScript with Esbuild ===\n";
$jsOutput = $publicDir . '/js/main.js';
$esbuildCmd = "npx -y esbuild " . escapeshellarg($rootDir . '/src/js/app.js') . " --bundle --minify --outfile=" . escapeshellarg($jsOutput);
passthru($esbuildCmd, $esbuildCode);
if ($esbuildCode !== 0) {
    die("ERROR: Esbuild bundling failed with exit code $esbuildCode\n");
}

$jsContent = file_get_contents($jsOutput);
if (!$jsContent) {
    die("ERROR: Bundled JS is empty or missing!\n");
}

$jsHash = substr(md5($jsContent), 0, 10);
$hashedJsFile = "app.{$jsHash}.js";
file_put_contents("{$distDir}/{$hashedJsFile}", $jsContent);
echo "Generated: public/assets/dist/{$hashedJsFile} (" . round(strlen($jsContent) / 1024, 1) . " KB)\n";

echo "=== [4/4] Generating Production Manifest ===\n";
// Clean up previous hashed files
$existingFiles = glob("{$distDir}/app.*.*");
foreach ($existingFiles as $f) {
    $bn = basename($f);
    if ($bn !== $hashedCssFile && $bn !== $hashedJsFile) {
        @unlink($f);
    }
}

$manifest = [
    'css/style.css' => "dist/{$hashedCssFile}",
    'js/main.js'    => "dist/{$hashedJsFile}",
    'app.css'       => "dist/{$hashedCssFile}",
    'app.js'        => "dist/{$hashedJsFile}"
];

file_put_contents("{$distDir}/manifest.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Generated: public/assets/dist/manifest.json\n";
echo "SUCCESS: Asset pipeline build complete.\n";
