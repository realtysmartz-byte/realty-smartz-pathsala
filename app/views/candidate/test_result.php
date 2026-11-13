<?php
$passed = !empty($attempt['passed']);
if ($passed) {
    require __DIR__ . '/test_success.php';
} else {
    require __DIR__ . '/test_failed.php';
}
