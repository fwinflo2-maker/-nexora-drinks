// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('code').value=="")
           {
            alert('BV Definir le code.');
			document.getElementById('code').focus();
			return false;
           }	 
	
  if(document.getElementById('libelle').value=="")
           {
            alert('BV saisir le libelle.');
			document.getElementById('libelle').focus();
			return false;
           }
 if(isNaN(document.getElementById('RetFiscPro').value)||(document.getElementById('RetFiscPro').value==""))
           {
            alert('Le taux Ret. Fisc. Pro. est un numerique!');
			document.getElementById('RetFiscPro').focus();
			return false;
           }
 if(isNaN(document.getElementById('tva').value)||(document.getElementById('tva').value==""))
           {
            alert('La TVA est un numerique!');
			document.getElementById('tva').focus();
			return false;
           }
    if(document.getElementById('statut').value=="")
           {
            alert('BV choisir le statut.');
			document.getElementById('statut').focus();
			return false;
           }
	return true;
 }
