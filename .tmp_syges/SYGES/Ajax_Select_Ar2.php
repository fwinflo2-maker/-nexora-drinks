<?php
	include("Connexion.php");
?>
    <td colspan="2" align="right"><select name="codeart2" id="codeart2" style="width:200px;">
     <?php
	 $sql = " select id_article ,marque,libelle,qtestock from article  where libelle like '%".$_POST['libar2']."%' and statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."' >";
		 echo $rslt["libelle"].' (Stock :'.$rslt["qtestock"].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>

