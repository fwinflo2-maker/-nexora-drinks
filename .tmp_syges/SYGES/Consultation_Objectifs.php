<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');	
	
$exercice=0;

 $sql2='SELECT * FROM PARAMETRE ' ;
 $reponse2= $DataBase->query($sql2);
 while($rslt2= $reponse2->fetch())
		{
			$exercice=$rslt2['EXERCICE'];
			$objanv=$rslt2['OBJANV'];
			$obfevr=$rslt2['OBFEVR'];
			$obmars=$rslt2['OBMARS'];
			$obavril=$rslt2['OBAVRI'];
			$obmai=$rslt2['OBMAI'];
			$objuin=$rslt2['OBJUIN'];
			$objuil=$rslt2['OBJUIL'];
			$obaout=$rslt2['OBAOUT'];
			$obsept=$rslt2['OBSEPT'];
			$obocto=$rslt2['OBOCTO'];
			$obnove=$rslt2['OBNOVE'];
			$obdece=$rslt2['OBDECE'];
			$obannu=$rslt2['OBANNU'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat d'atteinte des objectifs</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h3>Evaluation des Objectfs</h3></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>N° Ordre </h5> </td>
                <td align="center" ><h5>Mois </h5> </td>
                <td align="center" ><h5>Nbre d'Appro</h5> </td>
                <td align="center" ><h5>Nbre de Colis Realisé</h5> </td>
				<td align="center" ><h5>Objectif Mensuel</h5></td>
                <td align="center" ><h5>Taux De Réalisation</h5></td>
			</tr>
            
<?php
$couleur = "darkgray";

//Annuel
$TTNbreAppro=0;
$TTNbreColis=0;
//JANV
$NbreAppro1=0;
$NbreColis1=0;
//FEV
$NbreAppro2=0;
$NbreColis2=0;
//MARS
$NbreAppro3=0;
$NbreColis3=0;
//AVRIL
$NbreAppro4=0;
$NbreColis4=0;
//MAI
$NbreAppro5=0;
$NbreColis5=0;
//JUIN
$NbreAppro6=0;
$NbreColis6=0;
//JUILLET
$NbreAppro7=0;
$NbreColis7=0;
//AOUT
$NbreAppro8=0;
$NbreColis8=0;
//SEPT
$NbreAppro9=0;
$NbreColis9=0;
//OCT
$NbreAppro10=0;
$NbreColis10=0;
//NOV
$NbreAppro11=0;
$NbreColis11=0;
//DEC
$NbreAppro12=0;
$NbreColis12=0;

$sql='SELECT ID_APPRO, LIQUIDEHT,DATE_APPRO,NBRECOLIS FROM APPROVISIONNEMENT  WHERE STATUT="V" ORDER BY DATE_APPRO' ;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
	if (substr($rslt['DATE_APPRO'],0,4) == $exercice)
	{
		switch(substr($rslt['DATE_APPRO'],5,2))
		{
			case '01':
			{
				$NbreAppro1++;
				$NbreColis1=$NbreColis1+$rslt['NBRECOLIS'];
				break;
			}
			case '02':
			{
				$NbreAppro2++;
				$NbreColis2=$NbreColis2+$rslt['NBRECOLIS'];
				break;
			}
			case '3':
			{
				$NbreAppro3++;
				$NbreColis3=$NbreColis3+$rslt['NBRECOLIS'];
				break;
			}
			case '04':
			{
				$NbreAppro4++;
				$NbreColis4=$NbreColis4+$rslt['NBRECOLIS'];
				break;
			}	
			case '05':
			{
				$NbreAppro5++;
				$NbreColis5=$NbreColis5+$rslt['NBRECOLIS'];
				break;
			}
			case '06':
			{
				$NbreAppro6++;
				$NbreColis6=$NbreColis6+$rslt['NBRECOLIS'];
				break;
			}
			case '07':
			{
				$NbreAppro7++;
				$NbreColis7=$NbreColis7+$rslt['NBRECOLIS'];
				break;
			}
			case '08':
			{
				$NbreAppro8++;
				$NbreColis8=$NbreColis8+$rslt['NBRECOLIS'];
				break;
			}
			case '09':
			{
				$NbreAppro9++;
				$NbreColis9=$NbreColis9+$rslt['NBRECOLIS'];
				break;
			}
			case '10':
			{
				$NbreAppro10++;
				$NbreColis10=$NbreColis10+$rslt['NBRECOLIS'];
				break;
			}
			case '11':
			{
				$NbreAppro11++;
				$NbreColis11=$NbreColis11+$rslt['NBRECOLIS'];
				break;
			}
			case '12':
			{
				$NbreAppro12++;
				$NbreColis12=$NbreColis12+$rslt['NBRECOLIS'];
				break;
			}					
		}
	}
  }
		 //AFFICHAGE
			?>
			<tr>
				<td align="center">1</td></h5></a>
                <td align="center">JANVIER</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro1, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis1, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($objanv, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis1/$objanv)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            
			<tr>
				<td align="center">2</td></h5></a>
                <td align="center">FEVRIER</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro2, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis2, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obfevr, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis2/$obfevr)*100, 2, ',', ' ').'%'; ?><h5></h5></td>

			</tr>
            
            <tr>
				<td align="center">3</td></h5></a>
                <td align="center">MARS</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro3, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis3, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obmars, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis3/$obmars)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">4</td></h5></a>
                <td align="center">AVRIL</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro4, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis4, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obavril, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis4/$obavril)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">5</td></h5></a>
                <td align="center">MAI</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro5, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis5, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obmai, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis5/$obmai)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">6</td></h5></a>
                <td align="center">JUIN</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro6, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis6, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($objuin, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis6/$objuin)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">7</td></h5></a>
                <td align="center">JUILLET</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro7, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis7, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($objuil, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis7/$objuil)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">8</td></h5></a>
                <td align="center">AOUT</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro8, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis8, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obaout, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis8/$obaout)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">9</td></h5></a>
                <td align="center">SEPTEMBRE</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro9, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis9, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obsept, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis9/$obsept)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">10</td></h5></a>
                <td align="center">OCTOBRE</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro10, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis10, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obocto, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis10/$obocto)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">11</td></h5></a>
                <td align="center">NOVEMBRE</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro11, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis11, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obnove, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis11/$obnove)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
            <tr>
				<td align="center">12</td></h5></a>
                <td align="center">DECEMBRE</td></h5></a>
				<td align="center"><?php echo number_format($NbreAppro12, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($NbreColis12, 0, ',', ' '); ?><h5></h5></td>
				<td align="center"><?php echo number_format($obdece, 0, ',', ' '); ?><h5></h5></td>
                <td align="center"><?php echo number_format(($NbreColis12/$obdece)*100, 2, ',', ' ').'%'; ?><h5></h5></td>
			</tr>
			<?php
			
		 //calcul annuel
		$TTNbreAppro=$NbreAppro1+$NbreAppro2+$NbreAppro3+$NbreAppro4+$NbreAppro5+$NbreAppro6+$NbreAppro7+$NbreAppro8+$NbreAppro9+$NbreAppro10+$NbreAppro11+$NbreAppro12;
		$TTNbreColis=$NbreColis1+$NbreColis2+$NbreColis3+$NbreColis4+$NbreColis5+$NbreColis6+$NbreColis7+$NbreColis8+$NbreColis9+$NbreColis10+$NbreColis11+$NbreColis12;
?>
<tr>
	<td align="center"><a href="Evaluation_Objectifs.php"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="" align="center"><h5>Totaux : </td></h5></a>
	<td align="center"><h5><?php echo number_format($TTNbreAppro, 0, ',', ' '); ?></h5></td>
	<td align="center"><h5><?php echo number_format($TTNbreColis, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo 'Annuel : '.number_format($obannu, 0, ',', ' '); ?></h5></td>
    <td align="center"><h5><?php echo number_format(($TTNbreColis/$obannu)*100, 2, ',', ' ').'%'; ?></h5></td>
</tr>
</table>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}
?>