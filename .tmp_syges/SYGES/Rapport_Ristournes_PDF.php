<?php
session_start();
if (isset($_SESSION['habilitation']) && ($_SESSION['habilitation'] == "Administrateur" || $_SESSION['habilitation'] == "Gerant" || $_SESSION['habilitation'] == "Comptable")) {
    include('fpdf.php');
    include('Connexion.php');
    include('fonctions.php');

    // Période
    $debut = '2026-01-01';
    $fin   = '2026-03-31';

    // ==========================================================
    // Lecture PARAMETRE
    // ==========================================================
    $tva = 0; $tauxretfiscpro = 0; $tauxristournesht = 0; $tauxpsaristournes = 0;
    $sqlp = $DataBase->query('SELECT * FROM PARAMETRE');
    while ($p = $sqlp->fetch()) {
        $tva               = (float)$p['TVA'];
        $tauxretfiscpro    = (float)$p['TAUXRETFISCPRO'];
        $tauxristournesht  = (float)$p['TAUXRISTOURNESHT'];
        $tauxpsaristournes = (float)$p['PSARISTOURNES'];
    }

    // CA HT
    $caht = 0;
    $rca = $DataBase->query('SELECT SUM(LIQUIDEHT) AS T FROM APPROVISIONNEMENT
        WHERE DATE_APPRO BETWEEN "' . $debut . '" AND "' . $fin . '" AND STATUT="V"')->fetch();
    $caht = (float)$rca['T'];
    $catva = $caht * $tva / 100;
    $cattc = $caht + $catva;

    // ==========================================================
    // Classe FPDF étendue (pied de page + en-tête section)
    // ==========================================================
    class PDF_Ristourne extends FPDF {
        function Footer() {
            $this->SetY(-12);
            $this->SetFont('Arial', 'I', 7);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(0, 5, utf8_decode('Rapport généré automatiquement — formules conformes à Consultation_Ristourne.php'), 0, 0, 'L');
            $this->Cell(0, 5, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
            $this->SetTextColor(0, 0, 0);
        }
        function SectionTitle($txt) {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(60, 90, 140);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(0, 7, utf8_decode($txt), 1, 1, 'L', 1);
            $this->SetTextColor(0, 0, 0);
            $this->SetFillColor(255, 255, 255);
        }
        function FormulaBox($lines) {
            $this->SetFont('Courier', '', 8);
            $this->SetFillColor(245, 245, 230);
            $this->SetDrawColor(180, 180, 150);
            foreach ($lines as $line) {
                $this->Cell(0, 5, $line, 'LR', 1, 'L', 1);
            }
            $this->Cell(0, 0, '', 'T');
            $this->Ln(3);
            $this->SetDrawColor(0, 0, 0);
            $this->SetFillColor(255, 255, 255);
        }
    }

    $pdf = new PDF_Ristourne('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(true, 18);
    $pdf->AddPage();
    $pdf->SetTitle(utf8_decode('Rapport Ristournes Explicatif'));
    $pdf->SetAuthor('SYGES');

    // ==========================================================
    // EN-TÊTE
    // ==========================================================
    $pdf->Image('IMG\logo.jpg', 15, 12, 180, 30);
    $pdf->Ln(32);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, utf8_decode('AVOIR DE PARTICIPATION RISTOURNES (SUR ACHATS)'), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, utf8_decode('Rapport explicatif détaillé'), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, utf8_decode('Période : Du ' . dateFormatFrancais($debut) . ' Au ' . dateFormatFrancais($fin)), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 5, utf8_decode('Date édition : ') . date('d/m/Y H:i'), 0, 1, 'C');
    $pdf->Ln(4);

    // ==========================================================
    // SECTION 1 — PARAMÈTRES UTILISÉS
    // ==========================================================
    $pdf->SectionTitle('1. PARAMÈTRES DE CALCUL (table PARAMETRE)');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(255, 255, 200);
    $pdf->Cell(70, 6, utf8_decode('PARAMÈTRE'), 1, 0, 'L', 1);
    $pdf->Cell(30, 6, 'VALEUR', 1, 0, 'C', 1);
    $pdf->Cell(80, 6, utf8_decode('SIGNIFICATION'), 1, 1, 'L', 1);
    $pdf->SetFont('Arial', '', 8);

    $params = [
        ['TVA',               $tva,               'Taxe sur la Valeur Ajoutée'],
        ['TAUXRETFISCPRO',    $tauxretfiscpro,    'Retenue Fiscale Provisionnelle'],
        ['TAUXRISTOURNESHT',  $tauxristournesht,  'Taux Réduction Ristourne HT'],
        ['PSARISTOURNES',     $tauxpsaristournes, 'PSA Ristournes (Prélèv. CA)'],
    ];
    foreach ($params as $row) {
        $isZero = ($row[1] == 0);
        $pdf->Cell(70, 6, $row[0], 1, 0, 'L');
        if ($isZero) { $pdf->SetTextColor(200, 0, 0); $pdf->SetFont('Arial', 'B', 8); }
        $pdf->Cell(30, 6, number_format($row[1], 2, ',', ' ') . ' %' . ($isZero ? ' [!]' : ''), 1, 0, 'C');
        $pdf->SetTextColor(0, 0, 0); $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(80, 6, utf8_decode($row[2]), 1, 1, 'L');
    }
    if ($tauxristournesht == 0 || $tauxpsaristournes == 0) {
        $pdf->SetTextColor(200, 0, 0);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->Cell(0, 5, utf8_decode('[!] Avertissement : taux à zéro → la section "Rétrocession PSA" sera nulle.'), 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
    }
    $pdf->Ln(4);

    // ==========================================================
    // SECTION 2 — CHIFFRE D'AFFAIRES
    // ==========================================================
    $pdf->SectionTitle('2. CHIFFRE D\'AFFAIRES (Approvisionnements validés)');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(60, 6, 'CA HT', 1, 0, 'C', 1);
    $pdf->Cell(60, 6, 'TVA (' . $tva . '%)', 1, 0, 'C', 1);
    $pdf->Cell(60, 6, 'CA TTC', 1, 1, 'C', 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(60, 7, number_format($caht, 0, ',', ' ') . ' F', 1, 0, 'C');
    $pdf->Cell(60, 7, number_format($catva, 0, ',', ' ') . ' F', 1, 0, 'C');
    $pdf->Cell(60, 7, number_format($cattc, 0, ',', ' ') . ' F', 1, 1, 'C');
    $pdf->Ln(2);
    $pdf->FormulaBox([
        'CA_HT  = SUM(APPROVISIONNEMENT.LIQUIDEHT) WHERE STATUT="V"  période',
        '       = ' . number_format($caht, 0, ',', ' ') . ' F',
        'TVA    = CA_HT x ' . $tva . ' / 100  = ' . number_format($catva, 0, ',', ' ') . ' F',
        'CA_TTC = CA_HT + TVA                 = ' . number_format($cattc, 0, ',', ' ') . ' F',
    ]);

    // ==========================================================
    // SECTION 3 — ACHATS PAR ARTICLE
    // ==========================================================
    $pdf->SectionTitle('3. ACHATS ET RISTOURNES PAR ARTICLE');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(70, 6, utf8_decode('LIBELLÉ ARTICLE'), 1, 0, 'L', 1);
    $pdf->Cell(20, 6, utf8_decode('QTÉ REÇUE'), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'TAUX HT (F)', 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'TAUX TTC (F)', 1, 0, 'C', 1);
    $pdf->Cell(40, 6, 'VALEUR (F)', 1, 1, 'C', 1);

    $pdf->SetFont('Arial', '', 7);
    $TT_valeurttcR = 0;
    $TT_qterecu    = 0;
    $coef_ttc      = (100 + $tva + $tauxretfiscpro) / 100;

    $sql2 = 'SELECT DISTINCT AR.ID_ARTICLE FROM ARTICLE_RECU AR, APPROVISIONNEMENT A
             WHERE AR.ID_APPRO=A.ID_APPRO
             AND A.DATE_APPRO BETWEEN "' . $debut . '" AND "' . $fin . '"
             AND A.STATUT="V" ORDER BY AR.ID_ARTICLE';
    $rep2 = $DataBase->query($sql2);
    while ($r2 = $rep2->fetch()) {
        $qterecu = 0;
        $sql3 = 'SELECT AR.QTERECU FROM ARTICLE_RECU AR, APPROVISIONNEMENT A
                 WHERE AR.ID_APPRO=A.ID_APPRO
                 AND A.DATE_APPRO BETWEEN "' . $debut . '" AND "' . $fin . '"
                 AND AR.ID_ARTICLE="' . $r2['ID_ARTICLE'] . '"
                 AND A.STATUT="V"';
        $rep3 = $DataBase->query($sql3);
        while ($r3 = $rep3->fetch()) $qterecu += $r3['QTERECU'];

        $libelle = '';
        $tauxar  = 0;
        $rep4 = $DataBase->query('SELECT LIBELLE, TAUXRISTOURNE FROM ARTICLE WHERE ID_ARTICLE="' . $r2['ID_ARTICLE'] . '"');
        while ($r4 = $rep4->fetch()) {
            $libelle = $r4['LIBELLE'];
            $tauxar  = $r4['TAUXRISTOURNE'];
        }
        $tauxttc = $coef_ttc * $tauxar;
        $valeurR = round($tauxttc) * round($qterecu);
        $TT_valeurttcR += $valeurR;
        $TT_qterecu    += $qterecu;

        $pdf->Cell(70, 5, utf8_decode($libelle), 1, 0, 'L');
        $pdf->Cell(20, 5, number_format($qterecu, 0, ',', ' '), 1, 0, 'C');
        $pdf->Cell(25, 5, number_format($tauxar, 0, ',', ' '), 1, 0, 'C');
        $pdf->Cell(25, 5, number_format($tauxttc, 0, ',', ' '), 1, 0, 'C');
        $pdf->Cell(40, 5, number_format($valeurR, 0, ',', ' '), 1, 1, 'C');
    }

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(70, 6, 'TOTAUX', 1, 0, 'R', 1);
    $pdf->Cell(20, 6, number_format($TT_qterecu, 0, ',', ' '), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, '', 1, 0, 'C', 1);
    $pdf->Cell(25, 6, '', 1, 0, 'C', 1);
    $pdf->Cell(40, 6, number_format($TT_valeurttcR, 0, ',', ' ') . ' F', 1, 1, 'C', 1);
    $pdf->Ln(2);
    $pdf->FormulaBox([
        'Pour chaque article :',
        '  taux_TTC = ((100 + TVA + TAUXRETFISCPRO) / 100) x ARTICLE.TAUXRISTOURNE',
        '           = ((100 + ' . $tva . ' + ' . $tauxretfiscpro . ') / 100) x taux_HT',
        '           = ' . number_format($coef_ttc, 4, ',', ' ') . ' x taux_HT',
        '  valeurR  = round(qte_recue) x round(taux_TTC)',
        '',
        'Sigma valeurR = ' . number_format($TT_valeurttcR, 0, ',', ' ') . ' F  (Total Ristournes TTC)',
    ]);

    // ==========================================================
    // SECTION 4 — PARTICIPATION RISTOURNE
    // ==========================================================
    $valeurristourneht = 0;
    if ((100 + $tauxretfiscpro + $tva) > 0) {
        $valeurristourneht = (100 * $TT_valeurttcR) / (100 + $tauxretfiscpro + $tva);
    }
    $tvaristourne = ($valeurristourneht * $tva) / 100;
    $retfiscpro_v = ($valeurristourneht * $tauxretfiscpro) / 100;

    $pdf->SectionTitle('4. PARTICIPATION RISTOURNE (décomposition HT / TVA / Retenue)');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(45, 6, 'VALEUR HT', 1, 0, 'C', 1);
    $pdf->Cell(45, 6, 'TVA (' . $tva . '%)', 1, 0, 'C', 1);
    $pdf->Cell(45, 6, 'RETENU FISC (' . $tauxretfiscpro . '%)', 1, 0, 'C', 1);
    $pdf->Cell(45, 6, 'TOTAL TTC', 1, 1, 'C', 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(45, 7, number_format($valeurristourneht, 0, ',', ' ') . ' F', 1, 0, 'C');
    $pdf->Cell(45, 7, number_format($tvaristourne, 0, ',', ' ') . ' F', 1, 0, 'C');
    $pdf->Cell(45, 7, number_format($retfiscpro_v, 0, ',', ' ') . ' F', 1, 0, 'C');
    $pdf->Cell(45, 7, number_format($TT_valeurttcR, 0, ',', ' ') . ' F', 1, 1, 'C');
    $pdf->Ln(2);
    $verif = $valeurristourneht + $tvaristourne + $retfiscpro_v;
    $pdf->FormulaBox([
        'valeur_HT = (100 x Sigma valeurR) / (100 + TAUXRETFISCPRO + TVA)',
        '          = (100 x ' . number_format($TT_valeurttcR, 0, ',', ' ') . ') / (100 + ' . $tauxretfiscpro . ' + ' . $tva . ')',
        '          = ' . number_format($valeurristourneht, 2, ',', ' ') . ' F',
        '',
        'TVA_rist  = valeur_HT x ' . $tva . ' / 100         = ' . number_format($tvaristourne, 2, ',', ' ') . ' F',
        'RetFisc   = valeur_HT x ' . $tauxretfiscpro . ' / 100         = ' . number_format($retfiscpro_v, 2, ',', ' ') . ' F',
        '',
        'Verification : HT + TVA + RetFisc = ' . number_format($verif, 0, ',', ' ') . ' F  (~ Sigma valeurR)',
    ]);

    // ==========================================================
    // SECTION 5 — RÉTROCESSION PSA
    // ==========================================================
    $valeurhtPSA          = $caht * $tauxpsaristournes / 100;
    $valeurristournestaux = $valeurristourneht * $tauxristournesht / 100;
    $valeurepargneachat   = $valeurhtPSA - $valeurristournestaux;

    $pdf->SectionTitle('5. RÉTROCESSION PSA CLIENTS');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(30, 6, 'CA HT', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'TAUX PSA RIST.', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, utf8_decode('PRÉLÈVEMENT/CA'), 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'TOTAL RIST. HT', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'MOINS ' . $tauxristournesht . '%', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, utf8_decode('REMB. ÉPARGNE'), 1, 1, 'C', 1);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(30, 6, number_format($caht, 0, ',', ' '), 1, 0, 'C');
    $pdf->Cell(30, 6, number_format($tauxpsaristournes, 1, ',', ' ') . '%', 1, 0, 'C');
    $pdf->Cell(30, 6, number_format($valeurhtPSA, 0, ',', ' '), 1, 0, 'C');
    $pdf->Cell(30, 6, number_format($valeurristourneht, 0, ',', ' '), 1, 0, 'C');
    $pdf->Cell(30, 6, number_format($valeurristournestaux, 0, ',', ' '), 1, 0, 'C');
    $pdf->Cell(30, 6, number_format($valeurepargneachat, 0, ',', ' '), 1, 1, 'C');
    $pdf->Ln(2);
    $pdf->FormulaBox([
        'PRELEVEMENT_CA      = CA_HT x PSARISTOURNES / 100',
        '                    = ' . number_format($caht, 0, ',', ' ') . ' x ' . $tauxpsaristournes . ' / 100',
        '                    = ' . number_format($valeurhtPSA, 0, ',', ' ') . ' F',
        '',
        'RIST_HT_REDUCTION   = valeur_HT x TAUXRISTOURNESHT / 100',
        '                    = ' . number_format($valeurristourneht, 0, ',', ' ') . ' x ' . $tauxristournesht . ' / 100',
        '                    = ' . number_format($valeurristournestaux, 0, ',', ' ') . ' F',
        '',
        'REMB_EPARGNE_ACHAT  = PRELEVEMENT_CA - RIST_HT_REDUCTION',
        '                    = ' . number_format($valeurepargneachat, 0, ',', ' ') . ' F',
    ]);

    // ==========================================================
    // SECTION 6 — NET À PAYER (décomposition)
    // ==========================================================
    $retenues = 0;
    $regul    = 0;
    $ttristournesnettes = $TT_valeurttcR + $valeurepargneachat - $retenues + $regul;

    $pdf->SectionTitle('6. TOTAL RISTOURNES NETTES À PAYER');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(120, 6, utf8_decode('Sigma valeurR (Total Ristournes TTC)'), 1, 0, 'L');
    $pdf->Cell(60, 6, '+ ' . number_format($TT_valeurttcR, 0, ',', ' ') . ' F', 1, 1, 'R');
    $pdf->Cell(120, 6, utf8_decode('REMB_EPARGNE_ACHAT'), 1, 0, 'L');
    $pdf->Cell(60, 6, '+ ' . number_format($valeurepargneachat, 0, ',', ' ') . ' F', 1, 1, 'R');

    $pdf->SetTextColor(120, 120, 120);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(120, 6, utf8_decode('RETENUES (frigo + DA + CGA) — non gérées dans ce PDF'), 1, 0, 'L');
    $pdf->Cell(60, 6, '- 0 F', 1, 1, 'R');
    $pdf->Cell(120, 6, utf8_decode('RÉGULARISATIONS (rist + PSA + DA + CGA) — non gérées'), 1, 0, 'L');
    $pdf->Cell(60, 6, '+ 0 F', 1, 1, 'R');
    $pdf->SetTextColor(0, 0, 0);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(180, 180, 180);
    $pdf->Cell(120, 9, utf8_decode('NET À PAYER'), 1, 0, 'R', 1);
    $pdf->SetFillColor(255, 102, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(60, 9, number_format($ttristournesnettes, 0, ',', ' ') . ' FCFA', 1, 1, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Ln(3);
    $pdf->FormulaBox([
        'NET = Sigma valeurR + REMB_EPARGNE_ACHAT - RETENUES + REGULARISATIONS',
        '    = ' . number_format($TT_valeurttcR, 0, ',', ' ') . ' + ' . number_format($valeurepargneachat, 0, ',', ' ') . ' - 0 + 0',
        '    = ' . number_format($ttristournesnettes, 0, ',', ' ') . ' FCFA',
    ]);

    $pdf->Output('Ristournes_Jan_Mars_2026_Explicatif.pdf', 'D');
} else {
    header('Location: Form_Connexion.php');
    exit();
}
?>
