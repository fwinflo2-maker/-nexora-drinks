<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Gerant" ))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql = " select *  from client  where id_client='".$_GET['Clt']."'";
	$reponse= $DataBase->query($sql);
	$rslt= $reponse->fetch();
	$sql2 = " select *  from sortie_stock  where id_sortiestock='".$_GET['Vte']."'";
	$reponse2= $DataBase->query($sql2);
	$rslt2= $reponse2->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  de suppression d'une Vente.</title>
<style type="text/css">
label
{
	display:block;
	width:110px;
	float: left;
	}
</style>
</head>
 
<body>

<form action="CTRL/Ctrl_Supp_Vente.php" method="post">
<fieldset style=" width:1050px;"><legend>Informations sur la vente à supprimer</legend>
<table >
<tr>
	<td><label for="codevente"> Code * </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_SORTIESTOCK']; ?>"/></td>
</tr>
<tr>
   <td><label for="nomclient"> Client * </label></td>
    <td><input type="text" id="nomclient" name="nomclient"  value="<?php echo $rslt['NOM']; ?>" readonly="readonly" style="width:200px; background:#ECECEC;"/></td>
    <td><label for="date_vente"> Date vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px; background-color:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>" readonly="readonly"/></td>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="" style="width:200px; background-color:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
</tr>
<tr>
    <td colspan="6" align="right"><input type="submit" align="left" value="Supprimer" id="Supprimer" name="Supprimer"/></td>
</tr>

</table>
</fieldset>
<table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des Articles de la vente</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5>Marque </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center" ><h5>Qte </h5></td>
                <td  align="center" ><h5>Prix de Vente </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;			
$MT= 0;	
$nbrecasier=0;
$ttcolis=0;
$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE, ARTICLEVENDU.ID_ARTICLE,ARTICLEVENDU.QTESORTIE, ARTICLEVENDU.ID_SORTIESTOCK,ARTICLEVENDU.PRIXREVIENT,ARTICLEVENDU.PRIXVENTE, ARTICLEVENDU.OBSERVATION FROM ARTICLE, ARTICLEVENDU WHERE ARTICLE.ID_ARTICLE=ARTICLEVENDU.ID_ARTICLE AND ARTICLEVENDU.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY ARTICLEVENDU.ID_ARTICLE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MARQUE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTESORTIE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['PRIXVENTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$MT=$MT+$rslt3['PRIXVENTE'];
				$ttcolis=$ttcolis+$rslt3['QTESORTIE'];
								 				//on compte les casiers
				if(($rslt3['MARQUE']=="CASIER") || ($rslt3['MARQUE']=="casier")|| ($rslt3['MARQUE']=="CASIERS")|| ($rslt3['MARQUE']=="casiers"))
				{
					$nbrecasier=$nbrecasier+$rslt3['QTESORTIE'];
				}
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="8"><h4>Montant Total :  <?php echo number_format($MT, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
</table>
<table id='tconsigne' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des Consignes de la vente</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center"><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td  align="center" ><h5>Quantite </h5></td>
                <td  align="center" ><h5>PU Cons.</h5></td>
                <td  align="center" ><h5>Montant Cons.</h5></td>
			</tr>
			
<?php
$color = "darkgray";
$i = 0;
$MTC=0;			
$sql='SELECT  E.ID_EMBALLAGE, E.LIBELLE, E.MT_CONSIGNE, C.MONTANT, E.ID_EMBALLAGE ,C.ID_SORTIESTOCK ,C.QTE, C.STATUT FROM EMBALLAGE E, CONSIGNE C WHERE C.ID_EMBALLAGE =E.ID_EMBALLAGE AND C.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY C.ID_EMBALLAGE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt3= $reponse->fetch())
		{
			if ($i%2 == 0)
					$color = '#E0E0E0';
				else
					$color = "white";
				echo "<tr bgcolor=$color>";
				?>
                        <td  align="center"> <?php echo $rslt3['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['QTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MT_CONSIGNE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt3['MONTANT'].' FCFA'; ?> </td>
                <?php
				$i++;
				$MTC=$MTC+$rslt3['MONTANT'];
		 }
				?>
 <tr bgcolor="#E0E0E0"> 
    <td align="center" colspan="5"><h4>Montant Total Consigne :  <?php echo number_format($MTC, 0, ',', ' ').' Franc CFA'; ?></h4></td>
</tr>
<tr>
    <td align="center" colspan="5"><h3>Montant Total Vente :  <?php echo number_format($MTC+$MT, 0, ',', ' ').' Franc CFA'; ?></h3></td>
</tr>
</table>
</form>
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
