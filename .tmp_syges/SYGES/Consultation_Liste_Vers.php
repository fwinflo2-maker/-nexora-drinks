<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['user']=='TOUS')
	{	
		$sql='SELECT V.NUM_VERS, V.DATE_VERS, V.MONTANT, V.VENDEUR, V.OBSERVATION, V.DATE, VD.NOM FROM VERSEMENT V, USER VD WHERE V.VENDEUR=VD.LOGIN AND V.DATE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.STATUT="V" ORDER BY V.DATE' ;
	}
	else
	{	
		$sql='SELECT V.NUM_VERS, V.DATE_VERS, V.MONTANT, V.VENDEUR, V.OBSERVATION, V.DATE, VD.NOM FROM VERSEMENT V, USER VD WHERE V.VENDEUR=VD.LOGIN AND V.DATE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND V.VENDEUR="'.$_GET['user'].'" AND V.STATUT="V" ORDER BY V.DATE' ;
	}
?>
<!DOCTYPE html PUBLIC>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation des Versements</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h3>Etat des Versements</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td><a href="Liste_Vers.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&user=<?php echo $_GET['user'];?>"/><input type="button" value="Imprimer" /></td></a>
                <td colspan="4"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
                <td colspan="2"><h4>Vendeur : <?php echo $_GET['user']; ?></h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date  </h5> </td>
                <td align="center" ><h5>N° </h5> </td>
                <td align="center" ><h5>Versement du </h5> </td>
                <td align="center" ><h5>Vendeur</h5> </td>
                <td align="center" ><h5>Montant</h5> </td>
                <td align="center" ><h5>Emballages</h5> </td>
                <td  align="center"><h5>Observation </h5></td>
			</tr>
            
<?php

$couleur = "darkgray";
$i = 0;
$nbrevers=0;
$nbreemb=0;
$TTmontant=0;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
//Ici on recupere les emballages
$listeemb="";
 $sql1='SELECT E.ID_EMBALLAGE, E.LIBELLE, EV.QTE FROM  EMBALLAGE E, EMBALLAGE_VERS EV WHERE E.ID_EMBALLAGE=EV.ID_EMBALLAGE AND EV.NUM_VERS= "'.$rslt['NUM_VERS'].'" ' ;
 $reponse1= $DataBase->query($sql1);
 while($rslt1= $reponse1->fetch())
 {
	 if ($listeemb=="")
	 {
		 $listeemb=$rslt1['LIBELLE'].' :'.$rslt1['QTE'];
	 }
	 else
	 {
		 $listeemb=$listeemb.' ; '.$rslt1['LIBELLE'].' :'.$rslt1['QTE'];
	 }
	 $nbreemb=$nbreemb+$rslt1['QTE'];	 
 } 
	 //
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE']); ?> </td>
                        <td  align="center"> <?php echo $rslt['NUM_VERS']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_VERS']); ?> </td>
                        <td  align="left"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center" width="90px"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' '); ?> </td>
						<td  align="left"> <?php echo $listeemb; ?> </td>
                        <td  align="left"> <?php echo $rslt['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbrevers++;
				$TTmontant=$TTmontant+$rslt['MONTANT'];
}
?>
<tr>


    <td align="center"><h4>Totaux  </h4></td>
     <td colspan="3" align="center"><h4>Nbre Versement(s) :  <?php echo $nbrevers; ?> </h4></td>
     <td colspan="3" align="center"><h4>Montant Total :   <?php echo number_format($TTmontant, 0, ',', ' ').' FCFA'; ?> </h4></td>
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