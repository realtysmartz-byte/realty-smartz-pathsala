<?php
/**
 * Telegram Bot CLI Diagnostic & Test Utility
 *
 * Usage:
 *   php bin/test_telegram.php
 *   php bin/test_telegram.php --token="BOT_TOKEN" --chat_id="-100xxxxxxxxx"
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI execution only.');
}

require_once __DIR__ . '/../app/config/config.php';

$options = getopt('', ['token:', 'chat_id:']);
$token = $options['token'] ?? null;
$chatId = $options['chat_id'] ?? null;

echo "========================================================\n";
echo "  Realty Smartz Pathshala — Telegram Bot Test Utility   \n";
echo "========================================================\n\n";

if (!$token) {
    $token = \App\Helpers\TelegramService::getBotToken();
    echo "Bot Token (from Settings): " . ($token ? (substr($token, 0, 10) . '...' . substr($token, -6)) : '[NOT SET]') . "\n";
} else {
    echo "Bot Token (from Argument): " . substr($token, 0, 10) . '...' . substr($token, -6) . "\n";
}

if (!$chatId) {
    $chatId = \App\Helpers\TelegramService::getChatId();
    echo "Group Chat ID (from Settings): " . ($chatId ?: '[NOT SET]') . "\n";
} else {
    echo "Group Chat ID (from Argument): " . $chatId . "\n";
}

echo "\nTesting connection to Telegram API...\n";

$res = \App\Helpers\TelegramService::testConnection($token, $chatId);

if ($res['success']) {
    echo "✅ SUCCESS!\n";
    echo "Bot Name: " . ($res['bot_name'] ?? 'N/A') . "\n";
    echo "Bot Username: @" . ($res['bot_username'] ?? 'N/A') . "\n";
    echo "Message: " . $res['message'] . "\n";
    echo "Check your Telegram Group now!\n";
} else {
    echo "❌ FAILED!\n";
    if (!empty($res['bot_username'])) {
        echo "Bot Verified: @" . $res['bot_username'] . "\n";
    }
    echo "Error: " . ($res['error'] ?? 'Unknown error') . "\n";
    echo "\nTroubleshooting checklist:\n";
    echo "1. Verify your Bot Token created via @BotFather.\n";
    echo "2. Add your Bot as a member/admin in the target Telegram Group.\n";
    echo "3. Verify the Group Chat ID (for supergroups, it typically starts with -100).\n";
}
echo "========================================================\n";
