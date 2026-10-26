<?php
namespace App\Helpers;

use App\Models\InterviewApplication;
use App\Models\User;
use App\Models\Profile;
use App\Models\Setting;
use Exception;

/**
 * Telegram Bot Automation Helper
 * 
 * Provides automated real-time alerts to a Telegram Group / Channel
 * whenever a candidate submits an application or test.
 */
class TelegramService {

    /**
     * Check if Telegram notifications are configured and enabled
     */
    public static function isEnabled(): bool {
        $enabled = (int) Setting::get('telegram_notifications_enabled', 1);
        return ($enabled === 1) && !empty(self::getBotToken()) && !empty(self::getChatId());
    }

    /**
     * Get active Telegram Bot Token (from DB settings with fallback to config / .env)
     */
    public static function getBotToken(): string {
        $token = trim((string) Setting::get('telegram_bot_token', ''));
        if (empty($token) && function_exists('config')) {
            $token = trim((string) config('TELEGRAM_BOT_TOKEN', ''));
        }
        if (empty($token) && !empty($_ENV['TELEGRAM_BOT_TOKEN'])) {
            $token = trim((string) $_ENV['TELEGRAM_BOT_TOKEN']);
        }
        return $token;
    }

    /**
     * Get active Telegram Chat ID (Group ID or Channel Username)
     */
    public static function getChatId(): string {
        $chatId = trim((string) Setting::get('telegram_chat_id', ''));
        if (empty($chatId) && function_exists('config')) {
            $chatId = trim((string) config('TELEGRAM_CHAT_ID', ''));
        }
        if (empty($chatId) && !empty($_ENV['TELEGRAM_CHAT_ID'])) {
            $chatId = trim((string) $_ENV['TELEGRAM_CHAT_ID']);
        }
        return $chatId;
    }

    /**
     * Send a formatted message to Telegram
     *
     * @param string $message Formatted text (HTML by default)
     * @param string|null $chatId Target Chat ID or group ID
     * @param string|null $token Bot Token
     * @param string $parseMode 'HTML' or 'MarkdownV2'
     * @return array ['success' => bool, 'message' => string, 'response' => mixed]
     */
    public static function sendMessage(string $message, ?string $chatId = null, ?string $token = null, string $parseMode = 'HTML'): array {
        $token = $token ?: self::getBotToken();
        $chatId = $chatId ?: self::getChatId();

        if (empty($token)) {
            return [
                'success' => false,
                'error'   => 'Telegram Bot Token is missing. Please configure it in Admin Settings.'
            ];
        }

        if (empty($chatId)) {
            return [
                'success' => false,
                'error'   => 'Telegram Chat ID (Group ID) is missing. Please configure it in Admin Settings.'
            ];
        }

        $url = "https://api.telegram.org/bot" . urlencode($token) . "/sendMessage";

        $payload = [
            'chat_id'                  => $chatId,
            'text'                     => $message,
            'parse_mode'               => $parseMode,
            'disable_web_page_preview' => false
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            self::log("cURL error: {$err}");
            return [
                'success' => false,
                'error'   => "Connection error to Telegram API: {$err}"
            ];
        }

        $json = json_decode((string)$response, true);

        if ($httpCode >= 200 && $httpCode < 300 && !empty($json['ok'])) {
            self::log("Message dispatched to {$chatId} successfully.");
            return [
                'success'  => true,
                'message'  => 'Message delivered to Telegram successfully.',
                'response' => $json['result'] ?? []
            ];
        }

        $description = $json['description'] ?? "HTTP error {$httpCode}: {$response}";
        self::log("Telegram API rejected message to {$chatId}: {$description}");

        return [
            'success' => false,
            'error'   => "Telegram API error: {$description}",
            'code'    => $httpCode
        ];
    }

    /**
     * Send Real-Time Alert for New Interview Application
     *
     * @param int|array $application Application ID or full application row
     * @return array
     */
    public static function sendInterviewCandidateAlert($application): array {
        if (!self::isEnabled()) {
            return [
                'success' => false,
                'message' => 'Telegram notifications disabled or credentials missing.'
            ];
        }

        $app = is_array($application) ? $application : InterviewApplication::findById((int)$application);
        if (!$app) {
            return [
                'success' => false,
                'error'   => 'Interview application not found.'
            ];
        }

        // Safe HTML Sanitization for Telegram
        $esc = function($val, $default = 'N/A') {
            $s = trim((string)($val ?? ''));
            return !empty($s) ? htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $default;
        };

        $id = (int)$app['id'];
        $ref = $esc($app['application_ref'] ?? ('RSC-INT-' . str_pad($id, 4, '0', STR_PAD_LEFT)));
        $name = $esc($app['full_name'] ?? '');
        $post = $esc($app['post_applied'] ?? '');
        $segment = $esc($app['applied_for_type'] ?? 'Commercial');
        $mobile = $esc($app['mobile'] ?? '');
        $email = $esc($app['email'] ?? '');
        $hrName = $esc($app['hr_name'] ?? '');
        $expType = ucfirst($esc($app['experience_type'] ?? 'Fresher'));
        $yearsExp = !empty($app['years_in_real_estate']) ? " (" . $esc($app['years_in_real_estate']) . " yrs in RE)" : "";
        $location = $esc($app['current_location'] ?? 'Not specified');
        $education = $esc($app['higher_education'] ?? 'Not specified');
        $vehicle = ucfirst($esc($app['vehicle_available'] ?? 'no'));
        $immediate = ucfirst($esc($app['immediate_joiner'] ?? 'yes'));
        $lastCo = $esc($app['last_company_hr_tl'] ?? '');
        $leaveReason = $esc($app['reason_to_leave'] ?? '');
        $hasRef = strtolower(trim((string)($app['has_references'] ?? ''))) === 'yes' ? 'Yes' : 'No';
        $refDetails = $esc($app['reference_details'] ?? '');

        // Generate clickable Admin link
        $baseUrl = function_exists('config') ? rtrim((string)config('APP_URL', 'https://realtysmartzpathshala.in'), '/') : 'https://realtysmartzpathshala.in';
        $adminLink = "{$baseUrl}/admin/interview-candidates/{$id}";

        // Construct clean, beautifully structured HTML message
        $html = "🏢 <b>REALTY SMARTZ — NEW INTERVIEW CANDIDATE</b>\n";
        $html .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $html .= "📋 <b>Ref ID:</b> <code>{$ref}</code>\n";
        $html .= "👤 <b>Candidate Name:</b> <b>{$name}</b>\n";
        $html .= "💼 <b>Post Applied For:</b> {$post}\n";
        $html .= "⏳ <b>Experience Status:</b> <b>{$expType}</b>{$yearsExp}\n";
        $html .= "🏬 <b>Applied Segment:</b> <b>{$segment}</b>\n";
        $html .= "📞 <b>Mobile:</b> <code>{$mobile}</code>\n";
        $html .= "✉️ <b>Email:</b> {$email}\n";
        $html .= "👩‍💼 <b>Assigned HR:</b> {$hrName}\n";
        $html .= "🎓 <b>Education:</b> {$education}\n";
        $html .= "📍 <b>Location:</b> {$location}\n";
        $html .= "🚗 <b>Vehicle:</b> {$vehicle}\n";

        if (strtolower($app['experience_type'] ?? '') === 'experience') {
            $companyName = !empty($app['employment_rows'][0]['organisation']) ? $esc($app['employment_rows'][0]['organisation']) : ($lastCo !== 'N/A' ? $lastCo : '');
            if (!empty($companyName)) {
                $html .= "🏢 <b>Company Name:</b> <b>{$companyName}</b>\n";
            }
            if (!empty($app['employment_rows'][0]['designation'])) {
                $html .= "🏷️ <b>Designation:</b> " . $esc($app['employment_rows'][0]['designation']) . "\n";
            }
            if (!empty($app['employment_rows'][0]['salary'])) {
                $sal = $esc($app['employment_rows'][0]['salary']);
                $html .= "💰 <b>Last Salary:</b> {$sal}\n";
            }
            if (!empty($app['employment_rows'][0]['duration'])) {
                $html .= "⏱️ <b>Duration:</b> " . $esc($app['employment_rows'][0]['duration']) . "\n";
            }
            if ($lastCo !== 'N/A' && !empty($lastCo) && $lastCo !== $companyName) {
                $html .= "👥 <b>Last Co. HR & TL:</b> {$lastCo}\n";
            }
            if ($leaveReason !== 'N/A' && !empty($leaveReason)) {
                $html .= "🚪 <b>Reason to Leave:</b> {$leaveReason}\n";
            }
        }

        if ($hasRef === 'Yes' && $refDetails !== 'N/A' && !empty($refDetails)) {
            $html .= "👥 <b>References:</b> {$refDetails}\n";
        }

        $html .= "⚡ <b>Immediate Joiner:</b> {$immediate}\n";
        $html .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $html .= "🔗 <a href=\"{$adminLink}\">👉 Open Candidate in Admin Portal</a>\n";
        $html .= "🕒 <i>Submitted on: " . date('d M Y, h:i A') . "</i>";

        return self::sendMessage($html);
    }

    /**
     * Send Real-Time Alert for Candidate Onboarding, Test Completion, or Document Submission
     *
     * @param int $userId Candidate User ID
     * @param string $event 'registered' | 'test_completed' | 'submitted'
     * @return array
     */
    public static function sendStudentCandidateAlert(int $userId, string $event = 'registered'): array {
        if (!self::isEnabled()) {
            return [
                'success' => false,
                'message' => 'Telegram notifications disabled or credentials missing.'
            ];
        }

        try {
            $user = User::findById($userId);
            if (!$user) {
                return ['success' => false, 'error' => 'User not found'];
            }

            $profile = Profile::findByUserId($userId) ?? [];
            $esc = function($val, $default = 'N/A') {
                $s = trim((string)($val ?? ''));
                return !empty($s) ? htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $default;
            };

            $name = $esc($user['full_name'] ?? '');
            $father = $esc($user['father_name'] ?? ($profile['father_name'] ?? ''));
            $cid = $esc($profile['candidate_id'] ?? ('RSC-' . str_pad($userId, 4, '0', STR_PAD_LEFT)));
            $mobile = $esc($user['mobile'] ?? '');
            $email = $esc($user['email'] ?? '');
            $city = $esc($profile['city'] ?? ($profile['current_city'] ?? 'Gurugram'));
            $status = ucfirst(str_replace('_', ' ', $esc($user['status'] ?? 'registered')));
            $candType = ucfirst(str_replace('_', ' ', $esc($user['candidate_type'] ?? 'onboarding')));
            $source = $esc($user['source'] ?? 'Direct');

            $baseUrl = function_exists('config') ? rtrim((string)config('APP_URL', 'https://realtysmartzpathshala.in'), '/') : 'https://realtysmartzpathshala.in';
            $adminLink = "{$baseUrl}/admin/candidates/{$userId}";

            if ($event === 'test_completed') {
                $html = "📝 <b>REALTY SMARTZ — APTITUDE TEST COMPLETED</b>\n";
            } elseif ($event === 'submitted') {
                $html = "📋 <b>REALTY SMARTZ — ONBOARDING DOCUMENTS SUBMITTED</b>\n";
            } else {
                $html = "🎓 <b>REALTY SMARTZ — NEW CANDIDATE ONBOARDED</b>\n";
            }

            $html .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $html .= "📋 <b>Candidate ID:</b> <code>{$cid}</code>\n";
            $html .= "👤 <b>Full Name:</b> <b>{$name}</b>\n";
            if (!empty($father) && $father !== 'N/A') {
                $html .= "👨‍👧 <b>Father's Name:</b> {$father}\n";
            }
            $html .= "📞 <b>Mobile:</b> <code>{$mobile}</code>\n";
            $html .= "✉️ <b>Email:</b> {$email}\n";
            $html .= "📍 <b>City:</b> {$city}\n";
            $html .= "🏷️ <b>Candidate Type:</b> <b>{$candType}</b>\n";
            $html .= "📌 <b>Source:</b> {$source}\n";
            $html .= "📊 <b>Status:</b> <b>{$status}</b>\n";

            // If test completed, attach score metrics
            if ($event === 'test_completed') {
                try {
                    $db = \App\Config\Database::getConnection();
                    $stmtAttempt = $db->prepare("SELECT * FROM `test_attempts` WHERE `user_id` = :uid ORDER BY `id` DESC LIMIT 1");
                    $stmtAttempt->execute([':uid' => $userId]);
                    $attempt = $stmtAttempt->fetch();
                    if ($attempt) {
                        $score = $attempt['score'] ?? 0;
                        $total = $attempt['total_questions'] ?? 15;
                        $pct = round((float)($attempt['percentage'] ?? 0), 1);
                        $passed = !empty($attempt['passed']) ? '✅ PASSED' : '❌ NEEDS REVIEW';
                        $html .= "🎯 <b>Test Score:</b> <b>{$score}/{$total} ({$pct}%)</b> — {$passed}\n";
                    }
                } catch (\Throwable $e) {}
            }

            $html .= "━━━━━━━━━━━━━━━━━━━━━━━━\n";
            $html .= "🔗 <a href=\"{$adminLink}\">👉 View Candidate in Admin Portal</a>\n";
            $html .= "🕒 <i>Event at: " . date('d M Y, h:i A') . "</i>";

            return self::sendMessage($html);
        } catch (\Throwable $e) {
            self::log("Failed student alert ({$userId}): " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Test Connection to Telegram Bot & Target Group Chat
     *
     * @param string|null $token
     * @param string|null $chatId
     * @return array ['success' => bool, 'message' => string, 'bot' => array|null]
     */
    public static function testConnection(?string $token = null, ?string $chatId = null): array {
        $token = trim((string)($token ?: self::getBotToken()));
        $chatId = trim((string)($chatId ?: self::getChatId()));

        if (empty($token)) {
            return [
                'success' => false,
                'error'   => 'Bot Token is required for testing.'
            ];
        }

        if (empty($chatId)) {
            return [
                'success' => false,
                'error'   => 'Telegram Chat ID (Group ID) is required for testing.'
            ];
        }

        // Step 1: Verify Bot Token via getMe
        $getMeUrl = "https://api.telegram.org/bot" . urlencode($token) . "/getMe";
        $ch = curl_init($getMeUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        $meRes = curl_exec($ch);
        $meErr = curl_error($ch);
        curl_close($ch);

        if ($meErr) {
            return [
                'success' => false,
                'error'   => "Network error connecting to Telegram: {$meErr}"
            ];
        }

        $meJson = json_decode((string)$meRes, true);
        if (empty($meJson['ok']) || empty($meJson['result'])) {
            $desc = $meJson['description'] ?? 'Invalid Telegram Bot Token';
            return [
                'success' => false,
                'error'   => "Bot Token Verification Failed: {$desc}"
            ];
        }

        $botInfo = $meJson['result'];
        $botName = htmlspecialchars($botInfo['first_name'] ?? 'Realty Smartz Bot', ENT_QUOTES, 'UTF-8');
        $botUsername = htmlspecialchars($botInfo['username'] ?? 'bot', ENT_QUOTES, 'UTF-8');

        // Step 2: Dispatch Test Message to Group Chat ID
        $testMessage = "🤖 <b>Realty Smartz Pathshala — Telegram Bot Connected!</b>\n\n";
        $testMessage .= "✅ <b>Bot:</b> {$botName} (@{$botUsername})\n";
        $testMessage .= "📡 <b>Group Chat ID:</b> <code>" . htmlspecialchars($chatId, ENT_QUOTES, 'UTF-8') . "</code>\n\n";
        $testMessage .= "🎉 <b>Automation is LIVE!</b>\n";
        $testMessage .= "Whenever a candidate fills out the interview or registration form, complete details will instantly arrive in this group.";

        $sendResult = self::sendMessage($testMessage, $chatId, $token);

        if (!$sendResult['success']) {
            return [
                'success'      => false,
                'bot_name'     => $botName,
                'bot_username' => $botUsername,
                'error'        => "Bot is valid (@{$botUsername}), but could not send message to Group '{$chatId}'. Make sure: 1) The bot is added as a member or admin in the group, and 2) The Chat ID is correct (groups usually start with -100). Telegram said: " . ($sendResult['error'] ?? 'Unknown error')
            ];
        }

        return [
            'success'      => true,
            'bot_name'     => $botName,
            'bot_username' => $botUsername,
            'message'      => "Success! Test message dispatched to your Telegram group via @{$botUsername}."
        ];
    }

    /**
     * Fetch recent chats/groups where the bot has received updates
     *
     * @param string|null $token
     * @return array
     */
    public static function getRecentChats(?string $token = null): array {
        $token = trim((string)($token ?: self::getBotToken()));
        if (empty($token)) {
            return [];
        }

        $url = "https://api.telegram.org/bot" . urlencode($token) . "/getUpdates";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        $json = json_decode((string)$res, true);
        if (empty($json['ok']) || empty($json['result']) || !is_array($json['result'])) {
            return [];
        }

        $chats = [];
        foreach ($json['result'] as $up) {
            $chat = $up['message']['chat'] ?? $up['my_chat_member']['chat'] ?? $up['channel_post']['chat'] ?? null;
            if ($chat && isset($chat['id'])) {
                $cid = (string)$chat['id'];
                $title = $chat['title'] ?? $chat['first_name'] ?? $chat['username'] ?? ('Chat ' . $cid);
                $type = $chat['type'] ?? 'group';
                $chats[$cid] = [
                    'id'    => $cid,
                    'title' => $title,
                    'type'  => $type
                ];
            }
        }
        return array_values($chats);
    }

    /**
     * Internal audit logging
     */
    public static function log(string $message): void {
        $logDir = defined('STORAGE_PATH') ? STORAGE_PATH . '/logs' : __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $line = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        @file_put_contents($logDir . '/telegram.log', $line, FILE_APPEND);
    }
}
