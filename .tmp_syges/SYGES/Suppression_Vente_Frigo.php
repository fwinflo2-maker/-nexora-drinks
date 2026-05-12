<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"))
{
	include("Connexion.php");
	include("fonctions.php");
	$sql2 = " select *  from sortie_stock_frigo  where id_sortiestock='".$_GET['Vte']."'";
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

<form action="CTRL/Ctrl_Supp_Vente_Frigo.php" method="post">
<fieldset style=" width:1050px;"><legend>Informations sur la vente à supprimer</legend>
<table >
<tr>
	<td><label for="codevente"> Code * </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo $rslt2['ID_SORTIESTOCK']; ?>"/></td>
    <td><label for="date_vente"> Date vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:200px;background:#ECECEC;" value="<?php echo dateFormatFrancais($rslt2['DATESORTIESTOCK']); ?>" readonly="readonly"/> </td>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="" style="width:200px; background:#ECECEC;" value="<?php echo $rslt2['OBSERVATION']; ?>" readonly="readonly"/></td>
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
			
	$sql='SELECT  ARTICLE.ID_ARTICLE, ARTICLE.LIBELLE, ARTICLE.MARQUE, ARTICLEVENDU_FRIGO.ID_ARTICLE,ARTICLEVENDU_FRIGO.QTESORTIE, ARTICLEVENDU_FRIGO.ID_SORTIESTOCK,ARTICLEVENDU_FRIGO.PRIXVENTE, ARTICLEVENDU_FRIGO.OBSERVATION FROM ARTICLE, ARTICLEVENDU_FRIGO WHERE ARTICLE.ID_ARTICLE=ARTICLEVENDU_FRIGO.ID_ARTICLE AND ARTICLEVENDU_FRIGO.ID_SORTIESTOCK="'.$_GET['Vte'].'" ORDER BY ARTICLEVENDU_FRIGO.ID_ARTICLE ' ;
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
		 }
				?>
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
