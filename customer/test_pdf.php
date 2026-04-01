<?php
require_once "../vendor/dompdf/autoload.inc.php";
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml("<h1>Hello PDF</h1>");
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("test.pdf", ["Attachment"=>true]);
?>