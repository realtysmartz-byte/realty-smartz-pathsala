<?php
namespace App\Helpers;

use ZipArchive;
use Exception;

class DocxHelper {
    /**
     * Generate .docx document for a candidate profile
     */
    public static function generateCandidateDocx(array $candidate): string {
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Unable to create DOCX archive.");
        }

        // [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // word/document.xml
        $p = $candidate['profile'] ?? [];
        $name = htmlspecialchars($candidate['full_name'] ?? '');
        $email = htmlspecialchars($candidate['email'] ?? '');
        $mobile = htmlspecialchars($candidate['mobile'] ?? '');
        $status = strtoupper(str_replace('_', ' ', $candidate['status'] ?? ''));

        $father = htmlspecialchars($p['father_name'] ?? 'N/A');
        $mother = htmlspecialchars($p['mother_name'] ?? 'N/A');
        $dob = htmlspecialchars($p['dob'] ?? 'N/A');
        $gender = htmlspecialchars(ucfirst($p['gender'] ?? 'N/A'));
        $pan = htmlspecialchars(CryptoHelper::maskPan($p['pan_number'] ?? ''));
        $aadhaar = htmlspecialchars(CryptoHelper::maskAadhaar($p['aadhaar_last4'] ?? ''));
        $permAddr = htmlspecialchars($p['permanent_address'] ?? ($p['address'] ?? ($p['current_address'] ?? 'N/A')));
        $city = htmlspecialchars($p['city'] ?? '');
        $state = htmlspecialchars($p['state'] ?? '');
        $pin = htmlspecialchars($p['pincode'] ?? '');
        $emg = $candidate['emergency'] ?? [];
        $emgContact = !empty($emg['contact_name']) ? htmlspecialchars($emg['contact_name'] . ' (' . ($emg['relationship'] ?? 'Contact') . ') - ' . ($emg['mobile'] ?? '')) : 'N/A';

        $docXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
    <w:p>
      <w:pPr><w:jc w:val="center"/></w:pPr>
      <w:r><w:rPr><w:b/><w:sz w:val="36"/><w:color w:val="9A6410"/></w:rPr><w:t>REALTY SMARTZ PATHSHALA</w:t></w:r>
    </w:p>
    <w:p>
      <w:pPr><w:jc w:val="center"/></w:pPr>
      <w:r><w:rPr><w:i/><w:sz w:val="22"/><w:color w:val="7A7164"/></w:rPr><w:t>Candidate Onboarding Profile Dossier</w:t></w:r>
    </w:p>
    <w:p><w:r><w:t></w:t></w:r></w:p>
    <w:p>
      <w:r><w:rPr><w:b/><w:sz w:val="26"/><w:color w:val="2E2A24"/></w:rPr><w:t>Candidate: ' . $name . '</w:t></w:r>
    </w:p>
    <w:p>
      <w:r><w:rPr><w:sz w:val="22"/><w:color w:val="7A7164"/></w:rPr><w:t>Email: ' . $email . ' | Mobile: ' . $mobile . ' | Status: ' . $status . '</w:t></w:r>
    </w:p>
    <w:p><w:r><w:t>-----------------------------------------------------------------------------------------------------</w:t></w:r></w:p>
    <w:p>
      <w:r><w:rPr><w:b/><w:sz w:val="24"/><w:color w:val="9A6410"/></w:rPr><w:t>1. Personal and Identity Details</w:t></w:r>
    </w:p>
    <w:p><w:r><w:t>Father\'s Name: ' . $father . '</w:t></w:r></w:p>
    <w:p><w:r><w:t>Mother\'s Name: ' . $mother . '</w:t></w:r></w:p>
    <w:p><w:r><w:t>Date of Birth: ' . $dob . ' | Gender: ' . $gender . '</w:t></w:r></w:p>
    <w:p><w:r><w:t>PAN: ' . $pan . ' | Aadhaar: ' . $aadhaar . '</w:t></w:r></w:p>
    <w:p><w:r><w:t>Residential / Permanent Address: ' . $permAddr . ', ' . $city . ', ' . $state . ' - ' . $pin . '</w:t></w:r></w:p>
    <w:p><w:r><w:t>Emergency Contact: ' . $emgContact . '</w:t></w:r></w:p>
    <w:p><w:r><w:t>-----------------------------------------------------------------------------------------------------</w:t></w:r></w:p>
    <w:p>
      <w:r><w:rPr><w:b/><w:sz w:val="24"/><w:color w:val="9A6410"/></w:rPr><w:t>2. Declaration</w:t></w:r>
    </w:p>
    <w:p>
      <w:r><w:rPr><w:i/><w:color w:val="7A7164"/></w:rPr><w:t>Candidate has declared and digitally accepted that all submitted details and documents are authentic and verified.</w:t></w:r>
    </w:p>
  </w:body>
</w:document>';
        $zip->addFromString('word/document.xml', $docXml);

        $zip->close();
        $content = file_get_contents($tempFile);
        @unlink($tempFile);
        return $content;
    }
}
