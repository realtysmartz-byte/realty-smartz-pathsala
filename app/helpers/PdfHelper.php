<?php
namespace App\Helpers;

use setasign\Fpdi\Tcpdf\Fpdi;
use App\Config\Database;
use Exception;

class PdfHelper {

    /**
     * Generate Cover Page / Profile Summary for a candidate
     */
    private static function renderProfileSummaryPage(Fpdi $pdf, array $candidate, array $tocEntries = []): void {
        $pdf->AddPage('P', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(false);

        // Header Top: Brand Accent Bar
        $pdf->SetFillColor(31, 92, 74); // #1F5C4A (forest green brand)
        $pdf->Rect(0, 0, 210, 8, 'F');

        // Logo
        $logoPath = PUBLIC_PATH . '/assets/img/logo/Pathshala.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 12, 38, 0, 'PNG');
        } else {
            $pdf->SetFont('helvetica', 'B', 15);
            $pdf->SetTextColor(31, 92, 74);
            $pdf->SetXY(15, 14);
            $pdf->Cell(60, 8, 'REALTY SMARTZ PATHSHALA', 0, 0, 'L');
        }

        // Header Right: Title & Date
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(38, 51, 45); // #26332D text
        $pdf->SetXY(100, 12);
        $pdf->Cell(95, 6, 'CANDIDATE DOSSIER', 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(109, 118, 111); // #6D766F muted
        $pdf->SetXY(100, 18);
        $pdf->Cell(95, 5, 'Generated: ' . date('d M Y, H:i') . ' IST', 0, 1, 'R');

        // Thin Separator Line
        $pdf->SetDrawColor(226, 221, 208); // #E2DDD0 line
        $pdf->SetLineWidth(0.3);
        $pdf->Line(15, 27, 195, 27);

        // Candidate Identification Header Card
        $pdf->SetFillColor(247, 245, 239); // #F7F5EF bg
        $pdf->RoundedRect(15, 31, 180, 26, 3, '1111', 'F');

        // Name & Chips
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetTextColor(38, 51, 45);
        $pdf->SetXY(20, 34);
        $pdf->Cell(110, 7, $candidate['full_name'], 0, 0, 'L');

        // Chips: ID & Reg Date
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetFillColor(226, 238, 232); // #E2EEE8 brand soft
        $pdf->SetTextColor(31, 92, 74);
        $pdf->SetXY(140, 35);
        $pdf->Cell(24, 6, 'ID #' . sprintf('%04d', $candidate['id']), 0, 0, 'C', true);

        $pdf->SetFillColor(239, 235, 224); // #EFEBE0
        $pdf->SetTextColor(109, 118, 111);
        $pdf->SetXY(166, 35);
        $regDate = !empty($candidate['created_at']) ? date('d M Y', strtotime($candidate['created_at'])) : '-';
        $pdf->Cell(25, 6, $regDate, 0, 1, 'C', true);

        // Contact info in card
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(109, 118, 111);
        $pdf->SetXY(20, 43);
        $contactText = 'Email: ' . ($candidate['email'] ?? '-') . '   |   Mobile: ' . ($candidate['mobile'] ?? '-') . '   |   WhatsApp: ' . ($candidate['whatsapp'] ?? ($candidate['mobile'] ?? '-'));
        $pdf->Cell(170, 5, $contactText, 0, 0, 'L');

        // Section: Personal Details
        $y = 62;
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(31, 92, 74);
        $pdf->SetXY(15, $y);
        $pdf->Cell(180, 6, 'PERSONAL DETAILS', 0, 1, 'L');

        $pdf->SetDrawColor(226, 221, 208);
        $pdf->Line(15, $y + 6, 195, $y + 6);

        $profile = $candidate['profile'] ?? [];
        $y = 71;

        $fields = [
            ['Father\'s Name', $candidate['father_name'] ?? ($profile['father_name'] ?? '-'), 'Date of Birth', $profile['dob'] ? date('d M Y', strtotime($profile['dob'])) : '-'],
            ['Gender', ucfirst($profile['gender'] ?? '-'), 'Pincode', $profile['pincode'] ?? '-'],
            ['City', $profile['city'] ?? '-', 'State', $profile['state'] ?? '-'],
            ['Address', trim(($profile['address_line1'] ?? '') . ' ' . ($profile['address_line2'] ?? '')) ?: '-', 'Profile Status', !empty($candidate['is_guest']) ? 'Guest' : 'Registered Candidate'],
            ['Aadhaar Number', !empty($profile['aadhaar_masked']) ? $profile['aadhaar_masked'] : (!empty($profile['aadhaar_last4']) ? 'XXXX XXXX ' . $profile['aadhaar_last4'] : '-'), 
             'PAN Number', !empty($profile['pan_masked']) ? $profile['pan_masked'] : (!empty($profile['pan_last4']) ? 'XXXXX' . $profile['pan_last4'] : '-')]
        ];

        foreach ($fields as $row) {
            $pdf->SetFont('helvetica', '', 8.5);
            $pdf->SetTextColor(109, 118, 111);
            $pdf->SetXY(15, $y);
            $pdf->Cell(40, 5, $row[0], 0, 0, 'L');

            $pdf->SetFont('helvetica', 'B', 8.5);
            $pdf->SetTextColor(38, 51, 45);
            $pdf->SetXY(55, $y);
            $pdf->Cell(50, 5, $row[1], 0, 0, 'L');

            $pdf->SetFont('helvetica', '', 8.5);
            $pdf->SetTextColor(109, 118, 111);
            $pdf->SetXY(110, $y);
            $pdf->Cell(35, 5, $row[2], 0, 0, 'L');

            $pdf->SetFont('helvetica', 'B', 8.5);
            $pdf->SetTextColor(38, 51, 45);
            $pdf->SetXY(145, $y);
            $pdf->Cell(50, 5, $row[3], 0, 1, 'L');

            $y += 6.5;
        }

        // Section: Aptitude Assessment Summary
        $y += 3;
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(31, 92, 74);
        $pdf->SetXY(15, $y);
        $pdf->Cell(180, 6, 'APTITUDE TEST EVALUATION', 0, 1, 'L');

        $pdf->Line(15, $y + 6, 195, $y + 6);
        $y += 10;

        $test = $candidate['test_attempt'] ?? null;
        if ($test && isset($test['score'])) {
            $testFields = [
                ['Score', $test['score'] . ' / ' . ($test['total_questions'] ?? 15), 'Result', !empty($test['passed']) ? 'Passed' : 'Not passed'],
                ['Percentage', round($test['percentage'] ?? 0, 1) . '%', 'Set Used', $test['set_code'] ?? ($test['set_name'] ?? 'Set A')],
                ['Date Taken', !empty($test['created_at']) ? date('d M Y, H:i', strtotime($test['created_at'])) : '-', 'Time Taken', !empty($test['time_spent_seconds']) ? sprintf('%dm %ds', floor($test['time_spent_seconds'] / 60), $test['time_spent_seconds'] % 60) : '-']
            ];
            foreach ($testFields as $row) {
                $pdf->SetFont('helvetica', '', 8.5);
                $pdf->SetTextColor(109, 118, 111);
                $pdf->SetXY(15, $y);
                $pdf->Cell(40, 5, $row[0], 0, 0, 'L');

                $pdf->SetFont('helvetica', 'B', 8.5);
                $pdf->SetTextColor(38, 51, 45);
                $pdf->SetXY(55, $y);
                $pdf->Cell(50, 5, $row[1], 0, 0, 'L');

                $pdf->SetFont('helvetica', '', 8.5);
                $pdf->SetTextColor(109, 118, 111);
                $pdf->SetXY(110, $y);
                $pdf->Cell(35, 5, $row[2], 0, 0, 'L');

                $pdf->SetFont('helvetica', 'B', 8.5);
                $pdf->SetTextColor(38, 51, 45);
                $pdf->SetXY(145, $y);
                $pdf->Cell(50, 5, $row[3], 0, 1, 'L');

                $y += 6.5;
            }
        } else {
            $pdf->SetFont('helvetica', 'I', 9);
            $pdf->SetTextColor(109, 118, 111);
            $pdf->SetXY(15, $y);
            $pdf->Cell(180, 5, 'Test not taken yet.', 0, 1, 'L');
            $y += 8;
        }

        // Section: Table of Contents / Document Index
        if (!empty($tocEntries)) {
            $y += 4;
            $pdf->SetFont('helvetica', 'B', 10.5);
            $pdf->SetTextColor(31, 92, 74);
            $pdf->SetXY(15, $y);
            $pdf->Cell(180, 6, 'TABLE OF CONTENTS & ATTACHED DOCUMENTS', 0, 1, 'L');

            $pdf->Line(15, $y + 6, 195, $y + 6);
            $y += 9;

            // TOC Header
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetTextColor(109, 118, 111);
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 5, 'DOCUMENT NAME', 0, 0, 'L');
            $pdf->Cell(65, 5, 'FILE NAME', 0, 0, 'L');
            $pdf->Cell(30, 5, 'UPLOAD DATE', 0, 0, 'L');
            $pdf->Cell(15, 5, 'PAGE', 0, 1, 'R');
            $y += 5.5;

            $pdf->SetFont('helvetica', '', 8.5);
            $pdf->SetTextColor(38, 51, 45);

            foreach ($tocEntries as $item) {
                if ($y > 275) {
                    $pdf->AddPage('P', 'A4');
                    $y = 20;
                }
                $pdf->SetXY(15, $y);
                $pdf->Cell(70, 5.5, $item['title'], 'B', 0, 'L');
                $pdf->Cell(65, 5.5, $item['file_name'], 'B', 0, 'L');
                $pdf->Cell(30, 5.5, $item['uploaded_at'], 'B', 0, 'L');
                $pdf->Cell(15, 5.5, (string)$item['page_no'], 'B', 1, 'R');
                $y += 6;
            }
        }

        // Bottom confidentiality notice
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(109, 118, 111);
        $pdf->SetXY(15, 283);
        $pdf->Cell(180, 4, 'Confidential & Proprietary - Realty Smartz Pathshala Candidate Verification System', 0, 0, 'C');
    }

    /**
     * Fix EXIF orientation on JPEGs
     */
    private static function fixOrientation(string $filePath): string {
        if (!function_exists('exif_read_data')) return $filePath;
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg'])) return $filePath;
        
        $exif = @exif_read_data($filePath);
        if (!$exif || empty($exif['Orientation'])) return $filePath;
        
        $orientation = $exif['Orientation'];
        if ($orientation == 1) return $filePath;

        $image = @imagecreatefromjpeg($filePath);
        if (!$image) return $filePath;

        switch ($orientation) {
            case 3: $image = imagerotate($image, 180, 0); break;
            case 6: $image = imagerotate($image, -90, 0); break;
            case 8: $image = imagerotate($image, 90, 0); break;
            default: return $filePath;
        }

        $cacheDir = STORAGE_PATH . '/cache';
        @mkdir($cacheDir, 0755, true);
        $tmpRotated = $cacheDir . '/rot_' . uniqid() . '.jpg';
        imagejpeg($image, $tmpRotated, 95);
        imagedestroy($image);
        return $tmpRotated;
    }

    /**
     * Attempt to normalize PDF using Ghostscript if available
     */
    private static function tryGhostscriptNormalize(string $filePath): ?string {
        $gsCommands = ['gs', 'gswin64c', 'gswin32c'];
        $cacheDir = STORAGE_PATH . '/cache';
        @mkdir($cacheDir, 0755, true);
        $outPath = $cacheDir . '/gs_' . uniqid() . '.pdf';

        foreach ($gsCommands as $cmd) {
            $exec = sprintf('%s -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s 2>&1',
                escapeshellcmd($cmd),
                escapeshellarg($outPath),
                escapeshellarg($filePath)
            );
            @exec($exec, $output, $returnCode);
            if ($returnCode === 0 && file_exists($outPath) && filesize($outPath) > 0) {
                return $outPath;
            }
        }
        return null;
    }

    /**
     * Generate Single Candidate Profile (Cover / Profile Only PDF)
     */
    public static function generateCandidateProfile(array $candidate): string {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        self::renderProfileSummaryPage($pdf, $candidate, []);
        return $pdf->Output('', 'S');
    }

    /**
     * Generate Full Candidate File (ONE PDF: Cover + Table of Contents + All Uploaded Documents)
     */
    public static function generateFullCandidateFile(array $candidate): string {
        @ini_set('memory_limit', '256M');
        @set_time_limit(60);

        $documents = $candidate['documents'] ?? [];

        // 1. Calculate Document Page Offsets for Accurate Table of Contents
        $tocEntries = [];
        $currentPage = 2; // Page 1 is the cover/summary

        $helperFpdi = new Fpdi();
        foreach ($documents as $doc) {
            $filePath = $doc['file_path'] ?? '';
            $docType = $doc['document_type'] ?? 'Document';
            $docTitle = ucwords(str_replace('_', ' ', $docType));
            $fileName = $doc['original_name'] ?? ($doc['file_name'] ?? 'document');
            $uploadDate = !empty($doc['uploaded_at']) ? date('d M Y', strtotime($doc['uploaded_at'])) : '-';

            $pagesInDoc = 1;
            if (!empty($filePath) && file_exists($filePath)) {
                $mime = strtolower($doc['mime_type'] ?? mime_content_type($filePath));
                if (str_contains($mime, 'pdf') || str_ends_with(strtolower($filePath), '.pdf')) {
                    try {
                        $pagesInDoc = max(1, (int)$helperFpdi->setSourceFile($filePath));
                    } catch (\Throwable $e) {
                        $pagesInDoc = 1;
                    }
                }
            }

            $tocEntries[] = [
                'doc_id' => $doc['id'] ?? 0,
                'title' => $docTitle,
                'file_name' => substr($fileName, 0, 32),
                'uploaded_at' => $uploadDate,
                'page_no' => $currentPage,
                'pages' => $pagesInDoc,
                'file_path' => $filePath,
                'mime_type' => $doc['mime_type'] ?? ''
            ];

            $currentPage += $pagesInDoc;
        }
        unset($helperFpdi);

        // 2. Initialize Master PDF
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator('Realty Smartz Pathshala');
        $pdf->SetAuthor('Realty Smartz Pathshala Portal');
        $pdf->SetTitle(($candidate['full_name'] ?? 'Candidate') . ' - Full Verification Dossier');

        // Page 1: Branded cover / profile page with Table of Contents
        self::renderProfileSummaryPage($pdf, $candidate, $tocEntries);

        // 3. Process each document, one after the other
        foreach ($tocEntries as $item) {
            $filePath = $item['file_path'];
            $docTitle = $item['title'];
            $uploadDate = $item['uploaded_at'];
            $tmpFilesToUnlink = [];

            if (empty($filePath) || !file_exists($filePath)) {
                // Placeholder page for missing file
                $pdf->AddPage('P', 'A4');
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->SetTextColor(31, 92, 74);
                $pdf->SetXY(20, 30);
                $pdf->Cell(0, 10, $docTitle, 0, 1);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->SetTextColor(109, 118, 111);
                $pdf->MultiCell(170, 6, "File unavailable or removed from local storage.\nUpload Date: " . $uploadDate);
                continue;
            }

            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $isPdf = ($ext === 'pdf' || str_contains($item['mime_type'], 'pdf'));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) || str_starts_with($item['mime_type'], 'image/');

            if ($isPdf) {
                $imported = false;
                try {
                    $pageCount = $pdf->setSourceFile($filePath);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $tplId = $pdf->importPage($pageNo);
                        $size = $pdf->getTemplateSize($tplId);
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        
                        // Header banner on merged PDF page
                        $pdf->SetFont('helvetica', 'B', 8);
                        $pdf->SetTextColor(109, 118, 111);
                        $pdf->SetXY(10, 3);
                        $pdf->Cell(0, 5, $docTitle . '   |   Uploaded ' . $uploadDate, 0, 0, 'L');

                        $pdf->useTemplate($tplId, 0, 8, $size['width'], $size['height'] - 8);
                    }
                    $imported = true;
                } catch (\Throwable $e) {
                    // Try Ghostscript normalization if standard import fails
                    $gsFile = self::tryGhostscriptNormalize($filePath);
                    if ($gsFile && file_exists($gsFile)) {
                        $tmpFilesToUnlink[] = $gsFile;
                        try {
                            $pageCount = $pdf->setSourceFile($gsFile);
                            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                                $tplId = $pdf->importPage($pageNo);
                                $size = $pdf->getTemplateSize($tplId);
                                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                                
                                $pdf->SetFont('helvetica', 'B', 8);
                                $pdf->SetTextColor(109, 118, 111);
                                $pdf->SetXY(10, 3);
                                $pdf->Cell(0, 5, $docTitle . '   |   Uploaded ' . $uploadDate, 0, 0, 'L');

                                $pdf->useTemplate($tplId, 0, 8, $size['width'], $size['height'] - 8);
                            }
                            $imported = true;
                        } catch (\Throwable $e2) {
                            $imported = false;
                        }
                    }
                }

                if (!$imported) {
                    // Fallback placeholder page naming the file, saying included in ZIP export
                    $pdf->AddPage('P', 'A4');
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->SetTextColor(31, 92, 74);
                    $pdf->SetXY(20, 40);
                    $pdf->Cell(0, 10, $docTitle, 0, 1);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->SetTextColor(109, 118, 111);
                    $pdf->MultiCell(170, 6, "This PDF document requires external viewing and has been included in the Candidate Documents (ZIP) export package.\n\nFile: " . basename($filePath) . "\nUpload Date: " . $uploadDate);
                }
            } elseif ($isImage) {
                // Image placing: fix EXIF rotation, fit and center on A4 page
                $processedImagePath = $filePath;
                if ($ext === 'webp') {
                    $cacheDir = STORAGE_PATH . '/cache';
                    @mkdir($cacheDir, 0755, true);
                    $pngPath = $cacheDir . '/conv_' . uniqid() . '.png';
                    $im = @imagecreatefromwebp($filePath);
                    if ($im) {
                        imagepng($im, $pngPath);
                        imagedestroy($im);
                        $processedImagePath = $pngPath;
                        $tmpFilesToUnlink[] = $pngPath;
                    }
                } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                    $rotPath = self::fixOrientation($filePath);
                    if ($rotPath !== $filePath) {
                        $processedImagePath = $rotPath;
                        $tmpFilesToUnlink[] = $rotPath;
                    }
                }

                $pdf->AddPage('P', 'A4');

                // Header banner
                $pdf->SetFont('helvetica', 'B', 8.5);
                $pdf->SetTextColor(109, 118, 111);
                $pdf->SetXY(15, 10);
                $pdf->Cell(180, 5, $docTitle . '   |   Uploaded ' . $uploadDate, 0, 0, 'L');
                $pdf->SetDrawColor(226, 221, 208);
                $pdf->Line(15, 16, 195, 16);

                // Calculate fitted dimensions
                $imgInfo = @getimagesize($processedImagePath);
                if ($imgInfo && $imgInfo[0] > 0 && $imgInfo[1] > 0) {
                    $imgW = $imgInfo[0];
                    $imgH = $imgInfo[1];
                    $maxW = 180; // mm
                    $maxH = 250; // mm

                    $scale = min($maxW / $imgW, $maxH / $imgH);
                    $renderW = $imgW * $scale;
                    $renderH = $imgH * $scale;
                    $renderX = 15 + ($maxW - $renderW) / 2;
                    $renderY = 22 + ($maxH - $renderH) / 2;

                    $pdf->Image($processedImagePath, $renderX, $renderY, $renderW, $renderH);
                } else {
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->SetTextColor(109, 118, 111);
                    $pdf->SetXY(20, 50);
                    $pdf->Cell(0, 10, 'Unable to render image thumbnail.', 0, 1);
                }
            } else {
                // Non-image, non-pdf (e.g. DOCX / DOC / TXT)
                $pdf->AddPage('P', 'A4');
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->SetTextColor(31, 92, 74);
                $pdf->SetXY(20, 40);
                $pdf->Cell(0, 10, $docTitle, 0, 1);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->SetTextColor(109, 118, 111);
                $pdf->MultiCell(170, 6, "This document is in a format (" . strtoupper($ext) . ") that requires external application viewing and is included in the Candidate Documents (ZIP) export package.\n\nFile: " . basename($filePath) . "\nUpload Date: " . $uploadDate);
            }

            // Cleanup any temporary files and free memory
            foreach ($tmpFilesToUnlink as $tmpF) {
                if (file_exists($tmpF)) {
                    @unlink($tmpF);
                }
            }
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Generate Aptitude Assessment Scorecard PDF
     *
     * @param array $attempt Test attempt record
     * @param array $user Candidate/User record
     * @param array $categoryBreakdown Category breakdown items
     * @return string Binary PDF string
     */
    public static function generateScorecard(array $attempt, array $user, array $categoryBreakdown = []): string {
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage('P', 'A4');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(false);

        // Header Top: Brand Accent Bar (#9A6410)
        $pdf->SetFillColor(154, 100, 16); // #9A6410 Warm Gold / Amber Accent
        $pdf->Rect(0, 0, 210, 8, 'F');

        // Logo
        $logoPath = PUBLIC_PATH . '/assets/img/logo/Pathshala.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 15, 13, 36, 0, 'PNG');
        } else {
            $pdf->SetFont('helvetica', 'B', 15);
            $pdf->SetTextColor(154, 100, 16);
            $pdf->SetXY(15, 14);
            $pdf->Cell(60, 8, 'REALTY SMARTZ', 0, 0, 'L');
        }

        // Header Right: Title & Assessment Label
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->SetTextColor(46, 42, 36); // #2E2A24 text
        $pdf->SetXY(80, 13);
        $pdf->Cell(115, 6, 'OFFICIAL APTITUDE SCORECARD', 0, 1, 'R');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(115, 107, 96); // #736B60 muted
        $pdf->SetXY(80, 19);
        $pdf->Cell(115, 5, 'Realty Smartz Pathshala Candidate Evaluation', 0, 1, 'R');

        $pdf->SetXY(80, 24);
        $attemptDate = !empty($attempt['completed_at']) ? date('d M Y, H:i', strtotime($attempt['completed_at'])) : date('d M Y, H:i');
        $pdf->Cell(115, 5, 'Date of Assessment: ' . $attemptDate . ' IST', 0, 1, 'R');

        // Separator Line
        $pdf->SetDrawColor(226, 221, 208); // #E2DDD0 line
        $pdf->SetLineWidth(0.4);
        $pdf->Line(15, 31, 195, 31);

        // Candidate Information Card (#F7F5EF bg)
        $pdf->SetFillColor(247, 245, 239); // #F7F5EF
        $pdf->RoundedRect(15, 35, 180, 36, 3, '1111', 'F');

        $candidateName = $user['full_name'] ?? ($attempt['guest_name'] ?? 'Candidate');
        $candidateEmail = $user['email'] ?? ($attempt['guest_email'] ?? 'N/A');
        $candidateMobile = $user['mobile'] ?? ($attempt['guest_mobile'] ?? 'N/A');
        $candidateId = 'CAND-' . str_pad((string)($user['id'] ?? $attempt['user_id'] ?? 1), 5, '0', STR_PAD_LEFT);
        $attemptIdStr = 'ATT-' . str_pad((string)($attempt['id'] ?? 1), 6, '0', STR_PAD_LEFT);
        $setCode = $attempt['set_code'] ?? ($attempt['set_name'] ?? 'Set A');

        // Candidate Name
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetXY(20, 38);
        $pdf->Cell(110, 7, $candidateName, 0, 0, 'L');

        // Status Badge in Candidate Card
        $passed = !empty($attempt['passed']);
        $pdf->SetFont('helvetica', 'B', 8.5);
        if ($passed) {
            $pdf->SetFillColor(220, 242, 225); // soft green
            $pdf->SetTextColor(34, 107, 53);   // forest green
            $pdf->SetXY(135, 38);
            $pdf->Cell(55, 6, ' PASSED / RECOMMENDED ', 0, 1, 'C', true);
        } else {
            $pdf->SetFillColor(253, 238, 237); // soft red
            $pdf->SetTextColor(163, 58, 42);   // red
            $pdf->SetXY(135, 38);
            $pdf->Cell(55, 6, ' NOT PASSED ', 0, 1, 'C', true);
        }

        // Details Row 1
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(20, 48);
        $pdf->Cell(25, 5, 'Candidate ID:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->Cell(45, 5, $candidateId, 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->Cell(25, 5, 'Email Address:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->Cell(65, 5, $candidateEmail, 0, 1, 'L');

        // Details Row 2
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(20, 56);
        $pdf->Cell(25, 5, 'Contact Phone:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->Cell(45, 5, $candidateMobile, 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->Cell(25, 5, 'Assessment Set:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->Cell(65, 5, $setCode . ' (' . $attemptIdStr . ')', 0, 1, 'L');

        // Key Performance Metrics Cards
        $score = round((float)($attempt['score'] ?? 0), 2);
        $totalQuestions = (int)($attempt['total_questions'] ?? count($categoryBreakdown) * 5 ?: 15);
        $percentage = round((float)($attempt['percentage'] ?? (($totalQuestions > 0) ? ($score / $totalQuestions * 100) : 0)), 1);
        $totalAnswered = (int)($attempt['total_answered'] ?? 0);
        $totalCorrect = (int)($attempt['total_correct'] ?? 0);
        $totalWrong = (int)($attempt['total_wrong'] ?? 0);

        // Time Taken Calculation
        $timeStr = 'N/A';
        if (!empty($attempt['started_at']) && !empty($attempt['completed_at'])) {
            $durSec = max(0, strtotime($attempt['completed_at']) - strtotime($attempt['started_at']));
            $m = floor($durSec / 60);
            $s = $durSec % 60;
            $timeStr = sprintf('%02d min %02d sec', $m, $s);
        }

        // 3 Score Summary Metric Boxes (x=15, w=57 each, gap=4.5)
        // Box 1: Score & Total Marks
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(226, 221, 208);
        $pdf->RoundedRect(15, 77, 57, 30, 2.5, '1111', 'DF');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(17, 80);
        $pdf->Cell(53, 4, 'APTITUDE MARKS', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetTextColor(154, 100, 16);
        $pdf->SetXY(17, 86);
        $pdf->Cell(53, 8, $score . ' / ' . $totalQuestions, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(17, 97);
        $pdf->Cell(53, 4, 'Total Questions: ' . $totalQuestions, 0, 1, 'C');

        // Box 2: Percentage & Accuracy
        $pdf->RoundedRect(76.5, 77, 57, 30, 2.5, '1111', 'DF');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(78.5, 80);
        $pdf->Cell(53, 4, 'SCORE PERCENTAGE', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 17);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetXY(78.5, 86);
        $pdf->Cell(53, 8, $percentage . '%', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(78.5, 97);
        $pdf->Cell(53, 4, 'Benchmark: 50% Minimum', 0, 1, 'C');

        // Box 3: Test Result & Duration
        $pdf->RoundedRect(138, 77, 57, 30, 2.5, '1111', 'DF');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(140, 80);
        $pdf->Cell(53, 4, 'RESULT STATUS', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->SetTextColor($passed ? 34 : 163, $passed ? 107 : 58, $passed ? 53 : 42);
        $pdf->SetXY(140, 86);
        $pdf->Cell(53, 8, $passed ? 'QUALIFIED' : 'NOT QUALIFIED', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(140, 97);
        $pdf->Cell(53, 4, 'Time Taken: ' . $timeStr, 0, 1, 'C');

        // Section Title: Performance Breakdown
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetXY(15, 113);
        $pdf->Cell(180, 6, 'DOMAIN / CATEGORY PERFORMANCE BREAKDOWN', 0, 1, 'L');

        // Category Breakdown Table
        $tableY = 121;
        $pdf->SetFillColor(240, 237, 228); // #F0EDE4 table header
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetDrawColor(226, 221, 208);

        // Header Row
        $pdf->SetXY(15, $tableY);
        $pdf->Cell(65, 7, '  Assessment Domain / Topic', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Questions', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Attempted', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Correct', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Score Earned', 1, 1, 'C', true);

        $tableY += 7;
        $pdf->SetFont('helvetica', '', 8.5);

        $sumQ = 0;
        $sumAns = 0;
        $sumCorr = 0;
        $sumScore = 0.0;

        if (!empty($categoryBreakdown)) {
            $isAlt = false;
            foreach ($categoryBreakdown as $cat) {
                $cName = $cat['category_name'] ?? 'Domain';
                $cTotal = (int)($cat['total_in_cat'] ?? 0);
                $cAns = (int)($cat['answered_in_cat'] ?? 0);
                $cCorr = (int)($cat['correct_in_cat'] ?? 0);
                $cScore = round((float)($cat['score_in_cat'] ?? $cCorr), 2);

                $sumQ += $cTotal;
                $sumAns += $cAns;
                $sumCorr += $cCorr;
                $sumScore += $cScore;

                if ($isAlt) {
                    $pdf->SetFillColor(250, 249, 245);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }

                $pdf->SetXY(15, $tableY);
                $pdf->Cell(65, 6.5, '  ' . $cName, 1, 0, 'L', true);
                $pdf->Cell(25, 6.5, (string)$cTotal, 1, 0, 'C', true);
                $pdf->Cell(30, 6.5, (string)$cAns, 1, 0, 'C', true);
                $pdf->Cell(30, 6.5, (string)$cCorr, 1, 0, 'C', true);
                $pdf->Cell(30, 6.5, $cScore . ' marks', 1, 1, 'C', true);

                $tableY += 6.5;
                $isAlt = !$isAlt;
            }
        } else {
            // Fallback single line if breakdown empty
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetXY(15, $tableY);
            $pdf->Cell(65, 6.5, '  General Aptitude & Logical Reasoning', 1, 0, 'L', true);
            $pdf->Cell(25, 6.5, (string)$totalQuestions, 1, 0, 'C', true);
            $pdf->Cell(30, 6.5, (string)$totalAnswered, 1, 0, 'C', true);
            $pdf->Cell(30, 6.5, (string)$totalCorrect, 1, 0, 'C', true);
            $pdf->Cell(30, 6.5, $score . ' marks', 1, 1, 'C', true);
            $tableY += 6.5;
            $sumQ = $totalQuestions;
            $sumAns = $totalAnswered;
            $sumCorr = $totalCorrect;
            $sumScore = $score;
        }

        // Table Total Row
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetFillColor(245, 242, 235);
        $pdf->SetXY(15, $tableY);
        $pdf->Cell(65, 7, '  TOTAL OVERALL', 1, 0, 'L', true);
        $pdf->Cell(25, 7, (string)$sumQ, 1, 0, 'C', true);
        $pdf->Cell(30, 7, (string)$sumAns, 1, 0, 'C', true);
        $pdf->Cell(30, 7, (string)$sumCorr, 1, 0, 'C', true);
        $pdf->Cell(30, 7, round($sumScore, 2) . ' marks', 1, 1, 'C', true);
        $tableY += 12;

        // Anti-Cheat & Integrity Record
        $pdf->SetFont('helvetica', 'B', 10.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetXY(15, $tableY);
        $pdf->Cell(180, 6, 'ASSESSMENT INTEGRITY & PROCTORING RECORD', 0, 1, 'L');
        $tableY += 7;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->RoundedRect(15, $tableY, 180, 24, 2.5, '1111', 'DF');

        $tabSwitches = (int)($attempt['tab_switch_count'] ?? 0);
        $fsExits = (int)($attempt['fullscreen_exit_count'] ?? 0);

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(20, $tableY + 4);
        $pdf->Cell(45, 5, 'Proctoring System:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->Cell(45, 5, 'Real-time Browser Event Monitoring', 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->Cell(45, 5, 'Tab Switch Violations:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor($tabSwitches === 0 ? 34 : 163, $tabSwitches === 0 ? 107 : 58, $tabSwitches === 0 ? 53 : 42);
        $pdf->Cell(45, 5, $tabSwitches . ' detected', 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(20, $tableY + 12);
        $pdf->Cell(45, 5, 'Assessment Environment:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->Cell(45, 5, 'Enforced Server-side Timer', 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->Cell(45, 5, 'Integrity Status:', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(34, 107, 53);
        $pdf->Cell(45, 5, ($tabSwitches <= 2 && $fsExits <= 2) ? 'VERIFIED COMPLIANT' : 'FLAGGED FOR REVIEW', 0, 1, 'L');

        $tableY += 30;

        // Security / Verification Footer Box
        $pdf->SetFillColor(247, 245, 239);
        $pdf->RoundedRect(15, $tableY, 180, 24, 2.5, '1111', 'F');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetXY(20, $tableY + 3);
        $pdf->Cell(120, 5, 'AUTHENTICITY & SYSTEM VERIFICATION', 0, 0, 'L');

        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(20, $tableY + 9);
        $verificationHash = strtoupper(hash('crc32b', $attemptIdStr . '_' . $candidateId . '_' . $score));
        $pdf->MultiCell(125, 4, "This is an official computer-generated score document issued by Realty Smartz Pathshala assessment system. No physical signature is required.\nSystem Verification Reference: RSP-EVAL-" . $verificationHash . " | Generated: " . date('Y-m-d H:i:s') . " IST");

        // Authorized Stamp Block on right
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(154, 100, 16);
        $pdf->SetXY(145, $tableY + 4);
        $pdf->Cell(45, 5, 'REALTY SMARTZ', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(46, 42, 36);
        $pdf->SetXY(145, $tableY + 9);
        $pdf->Cell(45, 4, 'Academic & HR Council', 0, 1, 'C');
        $pdf->SetTextColor(34, 107, 53);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetXY(145, $tableY + 14);
        $pdf->Cell(45, 4, '[ SYSTEM VERIFIED ]', 0, 1, 'C');

        // Confidentiality Notice at bottom of page
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(115, 107, 96);
        $pdf->SetXY(15, 283);
        $pdf->Cell(180, 4, 'Confidential - Realty Smartz Pathshala Recruitment & Aptitude Testing Division', 0, 0, 'C');

        return $pdf->Output('', 'S');
    }
}
