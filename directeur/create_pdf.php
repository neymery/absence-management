<?php
require('../includes/db.php');
require('../includes/functions.php');
require('../fpdf.php');

if (isset($_GET['stagiaireId'], $_GET['fromDate'], $_GET['toDate'])) {
    $stagiaireId = $_GET['stagiaireId'];
    $fromDate = $_GET['fromDate'];
    $toDate = $_GET['toDate'];

    // Fetch les détails de stagiaire 
    $stmt = $conn->prepare("SELECT name, filiere, groupe FROM stagiaires WHERE cin = :stagiaireId");
    $stmt->bindParam(':stagiaireId', $stagiaireId);
    $stmt->execute();
    $stagiaire = $stmt->fetch();

    // Fetch les détails d'absence  
    $stmt = $conn->prepare("
        SELECT absence_date, hours, status
        FROM absences
        WHERE stagiaire_id = (SELECT id FROM stagiaires WHERE cin = :stagiaireId)
          AND absence_date BETWEEN :fromDate AND :toDate
    ");
    $stmt->bindParam(':stagiaireId', $stagiaireId);
    $stmt->bindParam(':fromDate', $fromDate);
    $stmt->bindParam(':toDate', $toDate);
    $stmt->execute();
    $absences = $stmt->fetchAll();

    // Créer un nouveau PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter le titre
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Rapport de l\'bsence de stagiaire ' . $stagiaire['name'], 0, 1, 'C');

    // ajouter les  détails
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Filiere: ' . $stagiaire['filiere'], 0, 1);
    $pdf->Cell(0, 10, 'Groupe: ' . $stagiaire['groupe'], 0, 1);
    $pdf->Cell(0, 10, 'Periode: ' . $fromDate . ' a ' . $toDate, 0, 1);

    // ajouter le header du tableau
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Date', 1);
    $pdf->Cell(40, 10, 'Hours', 1);
    $pdf->Cell(60, 10, 'Status', 1);
    $pdf->Ln();

    // ajouter les données
    $pdf->SetFont('Arial', '', 12);
    foreach ($absences as $absence) {
        $pdf->Cell(40, 10, $absence['absence_date'], 1);
        $pdf->Cell(40, 10, $absence['hours'], 1);
        $pdf->Cell(60, 10, $absence['status'], 1);
        $pdf->Ln();
    }

    $pdf->Output();
} else {
    echo 'input invalid';
}
