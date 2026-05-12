<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//ici on recupere le libelle de l'emballage
	$sql3 = 'SELECT  LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$_GET['Emb'].'"';
	$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{
    		$libelle=$rslt3['LIBELLE'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat des entrées en stocks d'un emballage</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h3>Etat des entrées en stocks : <?php echo $libelle .' ('.$_GET['Emb'].')'; ?> </h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="4" align="center" ><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
<!--///////////////////////////////APPRO EMBALLAGE-->
		  <tr align="center">
          	<td colspan="4"><h4>Approvisionnements Emballages</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	 <td align="center" ><h5>Date </h5> </td>
                 <td align="center" ><h5>Approvisionnement </h5> </td>
                <td  align="center" colspan="2"><h5>Quantité </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
//Ici on recupere les quantites du meme emballage puis on somme
$qterecu=0;
$sql1 = 'SELECT  ER.ID_EMBALLAGE, ER.QTERECU, AE.ID_APPRO, AE.DATE_APPRO FROM EMBALLAGE_RECU ER, APPROEMB AE WHERE ER.ID_APPRO=AE.ID_APPRO AND AE.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ER.ID_EMBALLAGE="'.$_GET['Emb'].'" AND AE.STATUT="V"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qterecu=$qterecu+$rslt1['QTERECU'];
		
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
			else
					$couleur = "white";
			echo "<tr bgcolor=$couleur>";
			?>
            		<td  align="center"> <?php echo dateFormatFrancais($rslt1['DATE_APPRO']); ?> </td>
                	<td  align="center"> <?php echo $rslt1['ID_APPRO']; ?> </td>
                    <td  align="center" colspan="2"> <?php echo $rslt1['QTERECU']; ?> </td>
                  </tr>
            <?php
		    $i++;
			$nbre++;
		 }
?>
<tr>
	<td></td>
	<td colspan="2" align="center"><h4>Nombre d'emballage(s) : <?php echo $nbre; ?> </h4></td>
	<td align="center"><h4>Total Colis : <?php echo $qterecu; ?> </h4></td>
</tr>
<!--///////////////////////////////Consignes Approvisionnements-->
		  <tr align="center">
          	<td colspan="4"><h4>Consignes Approvisionnements </h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	 <td align="center" ><h5>Date </h5> </td>
                 <td align="center" ><h5>Approvisionnement </h5> </td>
                <td align="center" ><h5>Fournisseur</h5> </td>
                <td  align="center"><h5>Quantité </h5></td>
			</tr>
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$qte2=0;
			//Ici on recupere les quantites du meme emballage puis on somme
			$qte=0;
			$sql1 = 'SELECT  CA.ID_EMBALLAGE, CA.QTE, CA.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM CONSIGNEAPP CA, APPROVISIONNEMENT A WHERE CA.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND CA.ID_EMBALLAGE="'.$_GET['Emb'].'" AND A.STATUT="V"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte2=$qte2+$rslt1['QTE'];
			
				//ici on recupere le nom du fournisseur
				$sql2 = 'SELECT  F.NOM FROM FOURNISSEUR F, APPROVISIONNEMENT A WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND A.ID_APPRO="'.$rslt1['ID_APPRO'].'" ';
				$reponse2= $DataBase->query($sql2);
				while($rslt2= $reponse2->fetch())
				{
					$nomfssr=$rslt2['NOM'];
				}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt1['DATE_APPRO']); ?> </td>
                		<td  align="center"> <?php echo $rslt1['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo $nomfssr; ?> </td>
                        <td  align="center"> <?php echo $rslt1['QTE']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
			}
?>
<tr>
	<td></td>
	<td colspan="" align="center"><h4>Nombre d'emballage(s) : <?php echo $nbre; ?> </h4></td>
	<td align="center" colspan="2"><h4>Total Colis : <?php echo $qte2; ?> </h4></td>
</tr>

<!--///////////////////////////////Déconsignation Clients-->
		  <tr align="center">
          	<td colspan="4"><h4>Déconsignation Clients </h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date </h5> </td>
                <td align="center" ><h5>Vente</h5> </td>
                <td align="center" ><h5>Client</h5> </td>
                <td  align="center"><h5>Quantité </h5></td>
			</tr>
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$qte3=0;
			//Ici on recupere les quantites du meme emballage puis on somme
			$sql1 = 'SELECT  ID_EMBALLAGE, QTE, ID_SORTIESTOCK, DATE_DECONSIGNE FROM CONSIGNE  WHERE DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$_GET['Emb'].'" AND STATUT="Deconsigne"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte3=$qte3+$rslt1['QTE'];
			
			//ici on recupere le nom du client
			$sql2 = 'SELECT  C.NOM FROM CLIENT C, SORTIE_STOCK ST WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK="'.$rslt1['ID_SORTIESTOCK'].'"';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
    			$nomclt=$rslt2['NOM'];
			}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt1['DATE_DECONSIGNE']); ?> </td>
                		<td  align="center"> <?php echo $rslt1['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $nomclt; ?> </td>
                        <td  align="center"> <?php echo $rslt1['QTE']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td align="center"><a href="Etat_Entree_St_Emb.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Emb=<?php echo $_GET["Emb"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="" align="center"><h4>Nombre d'emballage(s) : <?php echo $nbre; ?> </h4></td>
	<td align="center" colspan="2"><h4>Total Colis : <?php echo $qte3; ?> </h4></td>
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