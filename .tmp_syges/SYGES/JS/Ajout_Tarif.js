// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('categorie').value=="")
           {
            alert('BV Choisir la Categorie.');
			document.getElementById('categorie').focus();
			return false;
           }
  if(document.getElementById('codeart').value=="")
           {
            alert('BV choisir l\'Article.');
			document.getElementById('codeart').focus();
			return false;
           }
 if(isNaN(document.getElementById('prixvente').value)||(document.getElementById('prixvente').value=="")||(document.getElementById('prixvente').value==0))
           {
            alert('Le prix de vente est un numérique diff de zero!');
			document.getElementById('prixvente').focus();
			return false;
           }
 	return true;
 }
