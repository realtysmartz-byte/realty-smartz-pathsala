<?php
// ================================================================
// Realty Smartz Pathshala - Case-Sensitivity Validator
// Ensures all file references match exact disk case for Linux cPanel
// ================================================================

$rootDir = realpath(dirname(__DIR__));
$errors = [];

function checkDirectoryCase($dir, &$errors) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || $item === '.git' || $item === 'node_modules' || $item === 'vendor') {
            continue;
        }
        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
        
        // Scan PHP and view files for asset references
        if (is_file($fullPath) && preg_match('/\.(php|html|css|js)$/', $item)) {
            $content = file_get_contents($fullPath);
            
            // Check asset() or url() references
            if (preg_match_all('/(?:asset|url)\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
                foreach ($matches[1] as $ref) {
                    if (str_starts_with($ref, 'http') || str_starts_with($ref, '#') || empty($ref)) {
                        continue;
                    }
                    $cleanRef = ltrim($ref, '/');
                    if ($cleanRef === '' || $cleanRef === '/') {
                        continue;
                    }
                    $potentialPath = realpath(dirname(__DIR__) . '/public/' . $cleanRef);
                    if ($potentialPath && file_exists($potentialPath)) {
                        $realBasename = basename($potentialPath);
                        $refBasename = basename($cleanRef);
                        if ($refBasename !== '' && $realBasename !== $refBasename) {
                            $errors[] = "Case mismatch in {$fullPath}: referenced '{$refBasename}' but disk has '{$realBasename}'";
                        }
                    }
                }
            }
        } elseif (is_dir($fullPath)) {
            checkDirectoryCase($fullPath, $errors);
        }
    }
}

echo "Checking file reference case consistency for Linux cPanel...\n";
checkDirectoryCase($rootDir . '/app', $errors);
checkDirectoryCase($rootDir . '/public', $errors);

if (!empty($errors)) {
    echo "FAILED: Case mismatches found:\n";
    foreach ($errors as $e) {
        echo "  - $e\n";
    }
    exit(1);
}

echo "PASSED: All referenced files match exact case on disk.\n";
exit(0);
