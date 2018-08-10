<?php
require('fpdf.php');

$price = '$100';
$teacher_name = 'Teacher Joe';
$class_name = 'Fun Class';
$school_name = 'Cool School';

$pdf = new FPDF();
$pdf->SetMargins(20, 20);
$pdf->AddPage();
$pdf->Image('logo.png',140,30,0);
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,10,'Your STEM Playground Sponsorship Receipt',0,1);
$pdf->SetFont('Arial','',12);
$pdf->MultiCell(120,5,"Thank you for sponsoring a class with STEM Playground! Your sponsorship defrays the cost of STEM Playground's materials for the class, but also provides the students with \"Mission Accomplished\" dog tags to give them recognition for their participation in STEM.",0,'L');
$pdf->Ln();
$pdf->MultiCell(120,5,"Your sponsorship also allows STEM Playground to continue to produce top-level content and expand our reach into under- served areas badly in need of inexpensive opportunities to engage in STEM.",0,'L');
$pdf->Ln();
$pdf->MultiCell(0,5,"Your sponsorship will also be featured on our website and on your sponsored class's Activity Board, where the teacher can access his or her activities. Your generosity will not go unrecognized.",0,'L');
$pdf->Ln();
$pdf->MultiCell(0,5,"As of early 2017, STEM Playground is a registered 501 (c)(3) organization. As such, your ".$price." sponsorship is fully tax deductible in the US.",0,'L');$pdf->Ln();
$pdf->MultiCell(0,5,"Please see below for your sponsorship details and receipt.",0,'L');
$pdf->Ln();
$pdf->Cell(0,5,'Sponsored Teacher: '.$teacher_name,0,1);
$pdf->Cell(0,5,'Sponsored Class: '.$class_name,0,1);
$pdf->Cell(0,5,'Sponsored School: '.$school_name,0,1);
$pdf->Ln();
$pdf->Cell(0,5,'Sponsorship Amount: '.$price,0,1);
$pdf->Cell(0,5,'STEM Playground EIN: XX-XXXX762',0,1);
$pdf->Ln();
$pdf->Cell(0,5,'Thank you for your generosity in supporting your sponsored class!',0,1);
$pdf->Output();