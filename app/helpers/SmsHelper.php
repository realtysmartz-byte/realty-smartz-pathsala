<?php
namespace App\Helpers;

/**
 * Pluggable SMS Architecture Helper
 * Allows seamless activation of SMS OTP via a single configuration setting.
 * When SMS_OTP_ENABLED is false (default), operations log cleanly to storage/logs/sms.log.
 */
class SmsHelper {
    public static function isEnabled(): bool {
        return (bool) config('SMS_OTP_ENABLED', false);
    }

    /**
     * Send OTP via configured SMS provider
     *
     * @param string $mobile 10-digit Indian mobile number
     * @param string $otp 6-digit OTP code
     * @param string $type verification | login | reset
     * @return array ['sent' => bool, 'provider' => string, 'message' => string]
     */
    public static function sendOtp(string $mobile, string $otp, string $type = 'verification'): array {
        $cleanMobile = preg_replace('/\D/', '', $mobile);
        $provider = config('SMS_PROVIDER', 'log'); // log | fast2sms | msg91 | twilio
        $message = "Your Realty Smartz Pathshala {$type} OTP is: {$otp}. Valid for 10 minutes. Do not share this code.";

        // Always log for auditing & development testing
        self::logSms($cleanMobile, $otp, $provider, $message);

        if (!self::isEnabled()) {
            return [
                'sent'     => false,
                'provider' => 'simulated',
                'message'  => 'SMS OTP is disabled in configuration. Verification routed via registered email.'
            ];
        }

        try {
            switch ($provider) {
                case 'fast2sms':
                    return self::sendFast2Sms($cleanMobile, $otp);
                case 'msg91':
                    return self::sendMsg91($cleanMobile, $otp);
                case 'twilio':
                    return self::sendTwilio($cleanMobile, $message);
                default:
                    return [
                        'sent'     => true,
                        'provider' => 'log',
                        'message'  => 'Simulated SMS OTP logged to storage/logs/sms.log'
                    ];
            }
        } catch (\Throwable $e) {
            error_log("[SmsHelper Error] Failed to send SMS to {$cleanMobile}: " . $e->getMessage());
            return [
                'sent'     => false,
                'provider' => $provider,
                'message'  => $e->getMessage()
            ];
        }
    }

    private static function sendFast2Sms(string $mobile, string $otp): array {
        $apiKey = config('FAST2SMS_API_KEY', '');
        if (empty($apiKey)) {
            return ['sent' => false, 'provider' => 'fast2sms', 'message' => 'API key missing'];
        }

        $url = 'https://www.fast2sms.com/dev/bulkV2';
        $payload = [
            'variables_values' => $otp,
            'route'            => 'otp',
            'numbers'          => $mobile,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                "authorization: {$apiKey}",
                "Content-Type: application/json"
            ],
            CURLOPT_TIMEOUT        => 10
        ]);

        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($res, true);
        $success = ($httpCode === 200 && ($json['return'] ?? false) === true);

        return [
            'sent'     => $success,
            'provider' => 'fast2sms',
            'message'  => $json['message'][0] ?? ($success ? 'OTP sent' : 'Fast2SMS dispatch failed')
        ];
    }

    private static function sendMsg91(string $mobile, string $otp): array {
        $authKey = config('MSG91_AUTH_KEY', '');
        $templateId = config('MSG91_TEMPLATE_ID', '');
        if (empty($authKey) || empty($templateId)) {
            return ['sent' => false, 'provider' => 'msg91', 'message' => 'MSG91 config missing'];
        }

        $url = "https://control.msg91.com/api/v5/otp?template_id={$templateId}&mobile=91{$mobile}&authkey={$authKey}&otp={$otp}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 10
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'sent'     => ($httpCode === 200),
            'provider' => 'msg91',
            'message'  => ($httpCode === 200) ? 'OTP sent successfully' : 'MSG91 dispatch failed'
        ];
    }

    private static function sendTwilio(string $mobile, string $message): array {
        $sid = config('TWILIO_SID', '');
        $token = config('TWILIO_TOKEN', '');
        $from = config('TWILIO_FROM', '');

        if (empty($sid) || empty($token) || empty($from)) {
            return ['sent' => false, 'provider' => 'twilio', 'message' => 'Twilio credentials missing'];
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => "{$sid}:{$token}",
            CURLOPT_POSTFIELDS     => http_build_query([
                'To'   => '+91' . $mobile,
                'From' => $from,
                'Body' => $message
            ]),
            CURLOPT_TIMEOUT        => 10
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'sent'     => ($httpCode >= 200 && $httpCode < 300),
            'provider' => 'twilio',
            'message'  => ($httpCode >= 200 && $httpCode < 300) ? 'SMS dispatched' : 'Twilio dispatch failed'
        ];
    }

    private static function logSms(string $mobile, string $otp, string $provider, string $message): void {
        $logDir = STORAGE_PATH . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/sms.log';
        $entry = sprintf("[%s] [SMS] Mobile: %s | OTP: %s | Provider: %s | Enabled: %s | Text: %s\n",
            date('Y-m-d H:i:s'),
            $mobile,
            $otp,
            $provider,
            self::isEnabled() ? 'YES' : 'NO',
            $message
        );
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
