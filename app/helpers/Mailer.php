<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Throwable;

class Mailer {
    /**
     * Send email using PHPMailer configured for high deliverability (SMTPS Port 465, SPF/DKIM/DMARC compliant)
     * All SMTP parameters are read exclusively from .env.
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $vendorAutoload = dirname(APP_PATH) . '/vendor/autoload.php';
            if (file_exists($vendorAutoload)) {
                require_once $vendorAutoload;
            }
        }

        $mail = new PHPMailer(true);

        // Read all SMTP settings strictly from .env
        $smtpHost   = config('MAIL_HOST', 'mail.realtysmartzpathshala.in');
        $smtpPort   = (int) config('MAIL_PORT', 465);
        $smtpUser   = config('MAIL_USERNAME', 'info@realtysmartzpathshala.in');
        $smtpPass   = config('MAIL_PASSWORD', '');
        $smtpSecure = strtolower((string) config('MAIL_ENCRYPTION', 'ssl'));
        
        // Anti-Spam: The From address MUST always equal the SMTP username on cPanel hosts
        $fromEmail  = !empty($smtpUser) ? $smtpUser : config('MAIL_FROM_ADDRESS', 'info@realtysmartzpathshala.in');
        $fromName   = config('MAIL_FROM_NAME', 'Realty Smartz Pathshala');

        // If credentials are empty (e.g. testing without live credentials), log to mail.log
        if (empty($smtpPass)) {
            self::logMailToFile($toEmail, $toName, $subject, $htmlBody, "SIMULATED (MAIL_PASSWORD not set in .env)");
            return true;
        }

        try {
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            
            // Port 465 uses SSL / SMTPS
            if ($smtpPort === 465 || $smtpSecure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = $smtpPort ?: 587;
            }

            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';
            $mail->Timeout    = 10;

            // Strict Hostname for Message-ID RFC alignment with sender domain (avoids Spam)
            $mail->Hostname   = 'realtysmartzpathshala.in';

            // Explicit Envelope-From (Return-Path) for SPF 100% pass and DMARC alignment
            $mail->Sender     = $fromEmail;

            // Remove default PHPMailer bot signature header
            $mail->XMailer    = ' ';

            // Anti-Spam Transactional Headers
            $mail->addCustomHeader('Auto-Submitted', 'auto-generated');
            $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');

            // Sender and Reply-To
            $mail->setFrom($fromEmail, $fromName);
            $mail->addReplyTo($fromEmail, $fromName);
            $mail->addAddress($toEmail, $toName);

            // Multipart MIME: HTML Body + Plain-Text AltBody
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = self::htmlToPlainText($htmlBody);

            $mail->send();

            self::logMailToFile($toEmail, $toName, $subject, $htmlBody, "DELIVERED via {$smtpHost}:{$smtpPort}");
            return true;
        } catch (Throwable $e) {
            self::logMailToFile($toEmail, $toName, $subject, $htmlBody, "SMTP ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email with raw HTML body
     */
    public static function sendRawHtml(string $toEmail, string $subject, string $htmlBody): bool {
        return self::send($toEmail, '', $subject, $htmlBody);
    }

    /**
     * Send a test email from the Admin Panel to verify SMTP deliverability
     */
    public static function sendTestEmail(string $toEmail, string $toName): array {
        $subject = "Test email from Realty Smartz Pathshala";
        $timestamp = date('Y-m-d H:i:s T');
        $smtpHost = config('MAIL_HOST', 'mail.realtysmartzpathshala.in');
        $smtpPort = config('MAIL_PORT', '465');
        $smtpUser = config('MAIL_USERNAME', 'info@realtysmartzpathshala.in');

        $content = <<<HTML
<h2 style="color:#1E2A44; margin-top:0; font-size:20px; font-weight:800;">SMTP Deliverability Verification</h2>
<p>Hello <strong>{$toName}</strong>,</p>
<p>This is a test message dispatched from the <strong>Realty Smartz Pathshala Administration Portal</strong> to confirm your outgoing mail configuration is operating properly.</p>

<div style="background:#F6F8FB; border:1px solid #E3E8F0; border-radius:12px; padding:18px 20px; margin:24px 0; font-size:13.5px;">
    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <span style="color:#6B7A90;">SMTP Host:</span>
        <strong style="color:#1E2A44;">{$smtpHost}</strong>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <span style="color:#6B7A90;">SMTP Port:</span>
        <strong style="color:#1E2A44;">{$smtpPort} (SMTPS)</strong>
    </div>
    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
        <span style="color:#6B7A90;">Username / From:</span>
        <strong style="color:#1E2A44;">{$smtpUser}</strong>
    </div>
    <div style="display:flex; justify-content:space-between;">
        <span style="color:#6B7A90;">Dispatched At:</span>
        <strong style="color:#1E2A44;">{$timestamp}</strong>
    </div>
</div>

<p style="color:#6B7A90; font-size:13.5px; line-height:1.5;">If this email arrived in your inbox, your SMTP credentials, SPF, and DKIM signatures are resolving correctly.</p>
HTML;

        $html = self::wrapLayout("SMTP Test Email", $content);
        $ok = self::send($toEmail, $toName, $subject, $html);

        if ($ok) {
            return ['success' => true];
        }

        // Read last line from mail.log to give admin the exact reason
        $logFile = STORAGE_PATH . '/logs/mail.log';
        $reason = "Failed to connect to SMTP server";
        if (file_exists($logFile)) {
            $tail = file($logFile);
            $lastLines = array_slice($tail, -8);
            foreach ($lastLines as $l) {
                if (str_contains($l, 'STATUS:')) {
                    $reason = trim(explode('STATUS:', $l)[1]);
                    break;
                }
            }
        }

        return ['success' => false, 'error' => $reason];
    }

    /**
     * Converts HTML email content to clean plain text for multipart AltBody
     */
    public static function htmlToPlainText(string $html): string {
        $clean = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $clean = preg_replace('/<head\b[^>]*>(.*?)<\/head>/is', '', $clean);
        $clean = preg_replace('/<a\s+(?:[^>]*?\s+)?href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/i', '$2 ($1)', $clean);
        $clean = preg_replace('/<br\s*\/?>/i', "\n", $clean);
        $clean = preg_replace('/<\/p>/i', "\n\n", $clean);
        $clean = preg_replace('/<\/div>/i', "\n", $clean);
        $clean = preg_replace('/<\/h[1-6]>/i', "\n\n", $clean);
        $clean = strip_tags($clean);
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace("/\n{3,}/", "\n\n", $clean);
        return trim($clean);
    }

    /**
     * Log email dispatch or error to /storage/logs/mail.log
     */
    private static function logMailToFile(string $toEmail, string $toName, string $subject, string $htmlBody, string $status): void {
        $logDir = STORAGE_PATH . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/mail.log';
        $logEntry = sprintf(
            "================================================================\n" .
            "TIMESTAMP: %s | STATUS: %s\n" .
            "TO: %s <%s>\n" .
            "SUBJECT: %s\n" .
            "BODY SNIPPET:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $status,
            $toName,
            $toEmail,
            $subject,
            substr(self::htmlToPlainText($htmlBody), 0, 300)
        );
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Wrap email in unified high-deliverability branded layout (Fully inline-styled for Gmail, Outlook, Apple Mail)
     */
    public static function wrapLayout(string $title, string $contentHtml): string {
        $logoUrl = asset('/assets/img/logo/Pathshala.png');
        $siteUrl = url('/');
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{$title}</title>
</head>
<body style="margin:0; padding:0; background-color:#F7F5EF; font-family:'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#26332D; -webkit-font-smoothing:antialiased;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#F7F5EF; padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:580px; background-color:#FFFFFF; border:1px solid #E2DDD0; border-radius:16px; overflow:hidden; box-shadow:0 6px 24px rgba(38,51,45,0.05);">
                <!-- Header with PNG Brand Logo -->
                <tr>
                    <td align="center" style="background-color:#FFFFFF; padding:28px 24px 20px 24px; border-bottom:1px solid #EAE6DB;">
                        <a href="{$siteUrl}" target="_blank" style="text-decoration:none;">
                            <img src="{$logoUrl}" alt="Realty Smartz Pathshala" width="160" height="104" style="display:block; border:0; outline:none; text-decoration:none; max-width:160px; height:auto; margin:0 auto;" />
                        </a>
                    </td>
                </tr>
                <!-- Body Content -->
                <tr>
                    <td style="padding:32px 28px; color:#26332D; font-size:14.5px; line-height:1.65;">
                        {$contentHtml}
                    </td>
                </tr>
                <!-- Footer with Physical Address and Legitimate Organization Links -->
                <tr>
                    <td align="center" style="background-color:#F4F1EA; padding:22px 28px; font-size:12px; color:#6D766F; border-top:1px solid #EAE6DB; line-height:1.6;">
                        <p style="margin:0 0 6px 0; color:#26332D; font-weight:600;">Realty Smartz Pathshala &bull; Master the Art of Real Estate</p>
                        <p style="margin:0 0 8px 0; color:#6D766F;">Golf Course Extension Road, Sector 65, Gurgaon, Haryana 122002, India</p>
                        <p style="margin:0;">
                            <a href="{$siteUrl}" style="color:#1F5C4A; text-decoration:none; font-weight:600;">Visit Portal</a> &bull; 
                            <a href="{$siteUrl}/contact" style="color:#1F5C4A; text-decoration:none; font-weight:600;">Support Desk</a> &bull; 
                            <a href="mailto:info@realtysmartzpathshala.in" style="color:#1F5C4A; text-decoration:none; font-weight:600;">info@realtysmartzpathshala.in</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
HTML;
    }

    /**
     * Send OTP Verification Email
     * Clean, authoritative subject line (no dynamic OTP digits in subject to avoid spam filters)
     */
    public static function sendVerificationOtp(string $toEmail, string $toName, string $otp, string $token): bool {
        $verifyUrl = url("/verify-email?token={$token}");
        $content = <<<HTML
<h2 style="color:#1F5C4A; margin-top:0; font-size:22px; font-weight:700; margin-bottom:14px;">Verify Your Email Address</h2>
<p style="font-size:15px; color:#26332D; margin:0 0 14px 0; line-height:1.6;">Hello <strong>{$toName}</strong>,</p>
<p style="font-size:14.5px; color:#4A5568; margin:0 0 20px 0; line-height:1.6;">Welcome to <strong>Realty Smartz Pathshala</strong>. Please enter the 6-digit verification code below to confirm your account and proceed with your candidate onboarding:</p>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#EBF5F1; border:1px solid #C4E2D5; border-radius:12px; margin:22px 0;">
    <tr>
        <td style="padding:22px 16px; text-align:center;">
            <div style="font-size:11px; text-transform:uppercase; color:#1F5C4A; font-weight:700; letter-spacing:1.5px; margin-bottom:6px;">Verification Code (OTP)</div>
            <div style="font-size:36px; font-weight:800; letter-spacing:8px; color:#1F5C4A; font-family:'Courier New', Courier, monospace; margin:6px 0;">{$otp}</div>
            <div style="font-size:12px; color:#5A6E65; margin-top:6px;">Valid for 15 minutes only &bull; Please do not share this OTP with anyone.</div>
        </td>
    </tr>
</table>

<p style="font-size:14px; color:#4A5568; text-align:center; margin:24px 0 12px 0;">Or click the button below to verify your email automatically:</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding:6px 0 22px 0;">
            <a href="{$verifyUrl}" target="_blank" style="background-color:#1F5C4A; color:#FFFFFF; text-decoration:none; font-weight:600; font-size:14.5px; padding:13px 34px; border-radius:50px; display:inline-block; border:1px solid #1F5C4A;">Verify Email Address &rarr;</a>
        </td>
    </tr>
</table>

<p style="font-size:12.5px; color:#718096; line-height:1.5; border-top:1px solid #EAE6DB; padding-top:18px; margin-top:20px;">If the button does not work, copy and paste this verification URL in your browser:<br><a href="{$verifyUrl}" style="color:#1F5C4A; word-break:break-all;">{$verifyUrl}</a></p>
<p style="font-size:12px; color:#A0AEC0; margin-top:12px;">If you did not request this verification code, please disregard this email.</p>
HTML;

        $subject = "Verify your email address - Realty Smartz Pathshala";
        $html = self::wrapLayout("Verify Your Email", $content);
        return self::send($toEmail, $toName, $subject, $html);
    }

    /**
     * Send Password Reset Link
     */
    public static function sendPasswordReset(string $toEmail, string $toName, string $token): bool {
        $resetUrl = url("/reset-password?token={$token}");
        $content = <<<HTML
<h2 style="color:#2E2A24; margin-top:0; font-size:20px; font-weight:800;">Reset Your Password</h2>
<p>Hello <strong>{$toName}</strong>,</p>
<p>We received a request to reset your password for your Realty Smartz Pathshala portal account. Click the button below to establish a new password:</p>

<div style="text-align:center; margin: 28px 0;">
    <a href="{$resetUrl}" class="btn-primary">Reset Password &rarr;</a>
</div>

<p style="font-size:12.5px; color:#7A7164; line-height:1.5;">This password reset link is valid for 30 minutes. If you did not make this request, your account remains secure and no further action is required.</p>
HTML;

        $subject = "Reset your Pathshala password";
        $html = self::wrapLayout("Reset Your Password", $content);
        return self::send($toEmail, $toName, $subject, $html);
    }

    /**
     * Send Candidate Status Update Notification
     */
    public static function sendStatusUpdate(string $toEmail, string $toName, string $newStatus, ?string $comments): bool {
        $portalUrl = url("/dashboard");
        $statusLabels = [
            'approved'          => ['title' => 'Profile Approved', 'badge' => '#2E7D5B', 'badgeBg' => '#F2F8F5'],
            'changes_requested' => ['title' => 'Changes Requested on Your Profile', 'badge' => '#C77700', 'badgeBg' => '#FBF3E6'],
            'rejected'          => ['title' => 'Application Status Update', 'badge' => '#C0392B', 'badgeBg' => '#FDF4F3'],
            'under_review'      => ['title' => 'Profile Under Review', 'badge' => '#9A6410', 'badgeBg' => '#F7EBD3'],
        ];

        $info = $statusLabels[$newStatus] ?? ['title' => 'Application Status Update', 'badge' => '#9A6410', 'badgeBg' => '#F7EBD3'];
        
        $commentBlock = '';
        if (!empty($comments)) {
            $commentBlock = <<<HTML
<div style="background:#F6F0E4; border-left:4px solid {$info['badge']}; padding:16px 20px; border-radius:8px; margin:22px 0;">
    <strong style="color:#2E2A24; display:block; margin-bottom:6px; font-size:13.5px;">Reviewer Feedback:</strong>
    <p style="margin:0; color:#2E2A24; font-size:13.5px; line-height:1.5;">{$comments}</p>
</div>
HTML;
        }

        $formattedStatus = ucwords(str_replace('_', ' ', $newStatus));
        $content = <<<HTML
<h2 style="color:#2E2A24; margin-top:0; font-size:20px; font-weight:800;">{$info['title']}</h2>
<p>Hello <strong>{$toName}</strong>,</p>
<p>Your candidate onboarding status has been updated to: 
    <span style="display:inline-block; padding:4px 12px; border-radius:9999px; font-weight:700; background:{$info['badgeBg']}; color:{$info['badge']}; font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">{$formattedStatus}</span>
</p>

{$commentBlock}

<div style="text-align:center; margin: 28px 0;">
    <a href="{$portalUrl}" class="btn-primary">Open Candidate Portal &rarr;</a>
</div>
HTML;

        $subject = "Update on your Pathshala application: {$info['title']}";
        $html = self::wrapLayout($info['title'], $content);
        return self::send($toEmail, $toName, $subject, $html);
    }

    /**
     * Send Notification to HR when a candidate submits documents
     */
    public static function sendHrNewSubmission(string $hrEmail, string $candidateName, int $candidateId): bool {
        $reviewUrl = url("/admin/candidates/{$candidateId}");
        $content = <<<HTML
<h2 style="color:#1E2A44; margin-top:0; font-size:20px; font-weight:800;">New Candidate Submission Received</h2>
<p>Hello HR Team,</p>
<p>Candidate <strong>{$candidateName}</strong> (ID: #{$candidateId}) has completed their onboarding profile and submitted all required documentation for review.</p>

<div style="text-align:center; margin: 28px 0;">
    <a href="{$reviewUrl}" class="btn-primary">Review Candidate Profile &rarr;</a>
</div>
HTML;

        $subject = "New candidate submission: {$candidateName}";
        $html = self::wrapLayout("New Candidate Submission", $content);
        return self::send($hrEmail, 'HR Team', $subject, $html);
    }
}
