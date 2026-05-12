// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('codevente').value=="")
           {
            alert('BV choisir le code de l\'article.');
			document.getElementById('codevente').focus();
			return false;
           }
  if(document.getElementById('codeart').value=="")
           {
            alert('BV choisir le code de l\'article.');
			document.getElementById('codeart').focus();
			return false;
           }
 if(isNaN(document.getElementById('qtevendu').value)||(document.getElementById('qtevendu').value=="")||(document.getElementById('qtevendu').value==0))
           {
            alert('Le quantite vendu est un numérique et diff de zero!');
			document.getElementById('qtevendu').focus();
			return false;
           }
 	return true;
 }
