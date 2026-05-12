<?php
	include("Connexion.php");
?>
    <td colspan="2" align="right"><select name="codeart" id="codeart" style="width:200px;">
     <?php
	 $sql = " select id_article ,marque,libelle,stockfrigo from article  where libelle like '%".$_POST['libar']."%' and statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."' >";
		 echo $rslt["libelle"].' (Stock :'.$rslt["stockfrigo"].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>

