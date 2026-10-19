<?php
namespace App\Helpers;

use ZipArchive;
use Exception;

class ExportHelper {
    /**
     * Export array of candidates to CSV with Excel UTF-8 BOM
     */
    public static function candidatesToCsv(array $candidates): string {
        $output = fopen('php://temp', 'r+');

        // Write UTF-8 BOM for Microsoft Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Headers
        fputcsv($output, [
            'Candidate ID',
            'Full Name',
            'Email',
            'Mobile',
            'Status',
            'Completion %',
            'DOB',
            'Gender',
            'City',
            'State',
            'PAN',
            'Aadhaar (Last 4)',
            'Bank Name',
            'Registered Date'
        ]);

        foreach ($candidates as $c) {
            $p = $c['profile'] ?? [];
            $b = $c['bank'] ?? [];

            fputcsv($output, [
                $c['id'],
                $c['full_name'],
                $c['email'],
                $c['mobile'],
                strtoupper(str_replace('_', ' ', $c['status'])),
                ($p['completion_percentage'] ?? 0) . '%',
                $p['dob'] ?? 'N/A',
                ucfirst($p['gender'] ?? 'N/A'),
                $p['city'] ?? 'N/A',
                $p['state'] ?? 'N/A',
                CryptoHelper::maskPan($p['pan_number'] ?? ''),
                CryptoHelper::maskAadhaar($p['aadhaar_last4'] ?? ''),
                $b['bank_name'] ?? 'N/A',
                date('d-m-Y H:i', strtotime($c['created_at']))
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Create ZIP archive containing all uploaded documents of a candidate
     */
    public static function createCandidateDocumentsZip(array $documents, string $candidateName): string {
        $tempZip = tempnam(sys_get_temp_dir(), 'cand_zip_');
        $zip = new ZipArchive();
        if ($zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Unable to create candidate ZIP package.");
        }

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $candidateName);

        foreach ($documents as $doc) {
            $filePath = $doc['file_path'];
            if (file_exists($filePath)) {
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                $entryName = sprintf(
                    "%s/%s_v%d_%s.%s",
                    $cleanName,
                    $doc['document_type'],
                    $doc['version'],
                    pathinfo($doc['original_name'], PATHINFO_FILENAME),
                    $ext
                );
                $zip->addFile($filePath, $entryName);
            }
        }

        $zip->close();
        $zipContent = file_get_contents($tempZip);
        @unlink($tempZip);

        return $zipContent;
    }
}
