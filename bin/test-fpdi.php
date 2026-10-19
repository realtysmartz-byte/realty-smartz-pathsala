<?php
require_once __DIR__ . '/../vendor/autoload.php';
$pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, 'Hello World TCPDF FPDI');
$out = $pdf->Output('', 'S');
echo "SUCCESS: Generated PDF bytes = " . strlen($out) . " (starts with " . substr($out, 0, 5) . ")\n";
