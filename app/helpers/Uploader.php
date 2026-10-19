<?php
namespace App\Helpers;

use App\Config\Database;
use Exception;
use finfo;

class Uploader {
    public const MAX_BYTES = 2097152; // Exactly 2 MB

    public const ALLOWED_MIMES = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/jpg'       => 'jpg',
        'image/pjpeg'     => 'jpg',
        'image/png'       => 'png',
        'image/x-png'     => 'png',
        'image/webp'      => 'webp',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
    ];

    /**
     * Upload and process document
     *
     * @param array $file $_FILES['input_name']
     * @param int $userId
     * @param string $docType (e.g. 'profile_photo', 'last_study', 'offer_letter', etc.)
     * @return array Document metadata
     * @throws Exception
     */
    public static function process(array $file, int $userId, string $docType): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("The uploaded file exceeds the 2 MB size limit.");
            case UPLOAD_ERR_PARTIAL:
                throw new Exception("The file was only partially uploaded. Please try again.");
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file was uploaded.");
            default:
                throw new Exception("An unknown error occurred during upload.");
        }

        $clientReportedSizeBefore = (int)($_POST['size_before'] ?? 0);
        $sizeBefore = ($clientReportedSizeBefore > 0) ? $clientReportedSizeBefore : (int)$file['size'];
        $tmpPath = $file['tmp_name'];
        $origName = basename($file['name']);

        // Verify MIME type using finfo, mime_content_type, or file extension fallback
        $realMime = null;
        if (class_exists(\finfo::class)) {
            try {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $realMime = $finfo->file($tmpPath) ?: null;
            } catch (\Throwable $fe) {
                $realMime = null;
            }
        }
        if (empty($realMime) && function_exists('mime_content_type')) {
            $realMime = @mime_content_type($tmpPath) ?: null;
        }
        if (empty($realMime)) {
            $realMime = $file['type'] ?? null;
        }

        if (empty($realMime) || !array_key_exists($realMime, self::ALLOWED_MIMES)) {
            $extGuess = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $extToMime = [
                'pdf'  => 'application/pdf',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'webp' => 'image/webp',
                'doc'  => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            if (isset($extToMime[$extGuess])) {
                $realMime = $extToMime[$extGuess];
            } else {
                throw new Exception("Invalid file type" . ($realMime ? " ({$realMime})" : "") . ". Allowed formats: PDF, JPG, PNG, WEBP, DOC, DOCX.");
            }
        }

        $ext = self::ALLOWED_MIMES[$realMime] ?? 'bin';
        $storedName = sprintf('%d_%s_%s.%s', $userId, $docType, bin2hex(random_bytes(10)), $ext);
        
        $uploadDir = STORAGE_PATH . '/uploads';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }
        @chmod($uploadDir, 0777);

        // Fallback to public storage if primary is not writable
        if (!is_writable($uploadDir)) {
            $altUploadDir = (defined('PUBLIC_PATH') ? PUBLIC_PATH : (ROOT_PATH . '/public')) . '/storage/uploads';
            if (!is_dir($altUploadDir)) {
                @mkdir($altUploadDir, 0777, true);
            }
            if (is_writable($altUploadDir)) {
                $uploadDir = $altUploadDir;
            }
        }

        $destPath = $uploadDir . '/' . $storedName;

        // Perform compression if image
        $isImage = str_starts_with($realMime, 'image/');
        if ($isImage) {
            $compressedOk = Compressor::compressImage($tmpPath, $destPath, 1600, self::MAX_BYTES);
            if (!$compressedOk || !file_exists($destPath) || filesize($destPath) > self::MAX_BYTES) {
                if (file_exists($destPath)) @unlink($destPath);
                throw new Exception("Image exceeds 2 MB even after compression. Please resize your photo.");
            }
        } else {
            // PDF or DOC/DOCX
            if ((int)$file['size'] > self::MAX_BYTES) {
                if ($realMime === 'application/pdf') {
                    $compressedPdf = self::compressPdfWithGhostscript($tmpPath, $destPath);
                    if (!$compressedPdf || !file_exists($destPath) || filesize($destPath) > self::MAX_BYTES) {
                        if (file_exists($destPath)) @unlink($destPath);
                        throw new Exception("PDF exceeds the strict 2 MB limit (" . round($file['size'] / 1048576, 2) . " MB). Please compress your PDF before uploading.");
                    }
                } else {
                    throw new Exception("Document exceeds the 2 MB limit. Please compress it and try again.");
                }
            } else {
                $saved = is_uploaded_file($tmpPath) 
                    ? @move_uploaded_file($tmpPath, $destPath) 
                    : @copy($tmpPath, $destPath);
                if (!$saved) {
                    throw new Exception("Failed to store uploaded file. Please check server permissions on {$uploadDir}.");
                }
            }
        }

        $sizeAfter = file_exists($destPath) ? (int) filesize($destPath) : 0;
        if ($sizeAfter === 0 || $sizeAfter > self::MAX_BYTES) {
            if (file_exists($destPath)) @unlink($destPath);
            throw new Exception("File could not be saved properly or exceeds the 2 MB storage limit.");
        }

        // Database versioning & persistence
        $db = Database::getConnection();

        // Check if previous document exists for this user and docType
        $stmtOld = $db->prepare("SELECT * FROM `documents` WHERE `user_id` = :uid AND `document_type` = :dtype AND `is_archived` = 0 LIMIT 1");
        $stmtOld->execute([':uid' => $userId, ':dtype' => $docType]);
        $oldDoc = $stmtOld->fetch();

        $version = 1;
        if ($oldDoc) {
            $version = (int)$oldDoc['version'] + 1;
            // Archive old doc into document_versions
            $stmtVer = $db->prepare("INSERT INTO `document_versions`
                (`document_id`, `user_id`, `document_type`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size`, `version`, `replaced_by`)
                VALUES (:doc_id, :uid, :dtype, :orig, :stored, :path, :mime, :size, :ver, :rep_by)");
            $stmtVer->execute([
                ':doc_id'  => $oldDoc['id'],
                ':uid'     => $userId,
                ':dtype'   => $oldDoc['document_type'],
                ':orig'    => $oldDoc['original_name'],
                ':stored'  => $oldDoc['stored_name'],
                ':path'    => $oldDoc['file_path'],
                ':mime'    => $oldDoc['mime_type'],
                ':size'    => $oldDoc['file_size_after'],
                ':ver'     => $oldDoc['version'],
                ':rep_by'  => $userId
            ]);

            // Update active document row
            $stmtUpd = $db->prepare("UPDATE `documents` SET
                `original_name` = :orig,
                `stored_name` = :stored,
                `file_path` = :path,
                `mime_type` = :mime,
                `file_size_before` = :before,
                `file_size_after` = :after,
                `version` = :ver,
                `status` = 'pending',
                `rejection_reason` = NULL,
                `uploaded_at` = NOW()
                WHERE `id` = :id");
            $stmtUpd->execute([
                ':orig'   => $origName,
                ':stored' => $storedName,
                ':path'   => $destPath,
                ':mime'   => $realMime,
                ':before' => $sizeBefore,
                ':after'  => $sizeAfter,
                ':ver'    => $version,
                ':id'     => $oldDoc['id']
            ]);

            $docId = (int)$oldDoc['id'];
        } else {
            // Insert new document record
            $stmtIns = $db->prepare("INSERT INTO `documents`
                (`user_id`, `document_type`, `original_name`, `stored_name`, `file_path`, `mime_type`, `file_size_before`, `file_size_after`, `version`, `status`, `uploaded_at`)
                VALUES (:uid, :dtype, :orig, :stored, :path, :mime, :before, :after, :ver, 'pending', NOW())");
            $stmtIns->execute([
                ':uid'    => $userId,
                ':dtype'  => $docType,
                ':orig'   => $origName,
                ':stored' => $storedName,
                ':path'   => $destPath,
                ':mime'   => $realMime,
                ':before' => $sizeBefore,
                ':after'  => $sizeAfter,
                ':ver'    => $version
            ]);
            $docId = (int)$db->lastInsertId();
        }

        return [
            'id'               => $docId,
            'user_id'          => $userId,
            'document_type'    => $docType,
            'original_name'    => $origName,
            'stored_name'      => $storedName,
            'file_path'        => $destPath,
            'mime_type'        => $realMime,
            'file_size_before' => $sizeBefore,
            'file_size_after'  => $sizeAfter,
            'version'          => $version,
            'download_url'     => url('/document/download/' . $docId)
        ];
    }

    /**
     * Compress PDF with Ghostscript if available
     */
    private static function compressPdfWithGhostscript(string $source, string $target): bool {
        $gsCommands = ['gs', 'gswin64c', 'gswin32c'];
        foreach ($gsCommands as $cmd) {
            $test = @exec("{$cmd} --version 2>&1");
            if (!empty($test) && is_numeric(substr(trim($test), 0, 1))) {
                $escapedSource = escapeshellarg($source);
                $escapedTarget = escapeshellarg($target);
                $execCmd = "{$cmd} -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile={$escapedTarget} {$escapedSource}";
                @exec($execCmd);
                if (file_exists($target) && filesize($target) > 0) {
                    return true;
                }
            }
        }
        return false;
    }
}
