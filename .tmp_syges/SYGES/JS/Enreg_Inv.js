// JavaScript Document
function verif_date(input)
{
var regex = new RegExp("[/-]");
var date = input.split(regex);
var nbJours = new Array('',31,28,31,30,31,30,31,31,30,31,30,31);
var result = true;

if ( date['2']%4 == 0 && date['2']%100 > 0 || date['2']%400 == 0 )
nbJours['2'] = 29;

if( isNaN(date['2']) )
result=false;

if ( isNaN(date['1']) || date['1'] > 12 || date['1'] < 1 )
result=false;

if ( isNaN(date['0']) || date['0'] > nbJours[Math.round(date['1'])] || date['0'] < 1 )
result=false;

return result;
}
 function verif_form()
 {	 
	
  if((document.getElementById('code').value==""))
           {
            alert('BV saisir le Code.');
			document.getElementById('code').focus();
			return false;
           }

 if(isNaN(document.getElementById('caisse').value)||(document.getElementById('caisse').value==""))
           {
            alert('Le Solde Caisse est un numérique!');
			document.getElementById('caisse').focus();
			return false;
           }
 if(isNaN(document.getElementById('soldesabc').value)||(document.getElementById('soldesabc').value==""))
           {
            alert('Le Solde Brasseries est un numérique!');
			document.getElementById('soldesabc').focus();
			return false;
           }
 if(isNaN(document.getElementById('soldeom').value)||(document.getElementById('soldeom').value==""))
           {
            alert('Le Solde Orange Money est un numérique!');
			document.getElementById('soldeom').focus();
			return false;
           }
 if(isNaN(document.getElementById('soldemomo').value)||(document.getElementById('soldemomo').value==""))
           {
            alert('Le Solde Mobile Money est un numérique!');
			document.getElementById('soldemomo').focus();
			return false;
           }
 if(isNaN(document.getElementById('creditclient').value)||(document.getElementById('creditclient').value==""))
           {
            alert('Le Montant des Credits aux Clients est un numerique!');
			document.getElementById('creditclient').focus();
			return false;
           }
 if(isNaN(document.getElementById('creditemballage').value)||(document.getElementById('creditemballage').value==""))
           {
            alert('Le Montant des Credits Emballages est un numérique!');
			document.getElementById('creditemballage').focus();
			return false;
           }
 if(isNaN(document.getElementById('soldebanque').value)||(document.getElementById('soldebanque').value==""))
           {
            alert('Le Montant des soldes en banque est un numérique!');
			document.getElementById('soldebanque').focus();
			return false;
           }
 if(isNaN(document.getElementById('autrecredit').value)||(document.getElementById('autrecredit').value==""))
           {
            alert('Autres (credit) est un numérique!');
			document.getElementById('autrecredit').focus();
			return false;
           }
 if(isNaN(document.getElementById('creditsabc').value)||(document.getElementById('creditsabc').value==""))
           {
            alert('Le Montant des Credits Brasseries est un numérique!');
			document.getElementById('creditsabc').focus();
			return false;
           }
 if(isNaN(document.getElementById('creditbanque').value)||(document.getElementById('creditbanque').value==""))
           {
            alert('Le Montant des Credits en banque est un numérique!');
			document.getElementById('creditbanque').focus();
			return false;
           }
 if(isNaN(document.getElementById('ristournes').value)||(document.getElementById('ristournes').value==""))
           {
            alert('Le Montant des ristournes clients est un numérique!');
			document.getElementById('ristournes').focus();
			return false;
           }
 if(isNaN(document.getElementById('autredebit').value)||(document.getElementById('autredebit').value==""))
           {
            alert('Le Montant des autres (debit) est un numérique!');
			document.getElementById('autredebit').focus();
			return false;
           }
 if(isNaN(document.getElementById('palettebois').value)||(document.getElementById('palettebois').value==""))
           {
            alert('La quantite des palettes Bois est un numérique!');
			document.getElementById('palettebois').focus();
			return false;
           }
 if(isNaN(document.getElementById('pupalettebois').value)||(document.getElementById('pupalettebois').value==""))
           {
            alert('Le PU des palettes bois est un numérique!');
			document.getElementById('pupalettebois').focus();
			return false;
           }
 if(isNaN(document.getElementById('paletteplastique').value)||(document.getElementById('paletteplastique').value==""))
           {
            alert('La quantite des palettes plastiques est un numérique!');
			document.getElementById('paletteplastique').focus();
			return false;
           }
 if(isNaN(document.getElementById('pupaletteplastique').value)||(document.getElementById('pupaletteplastique').value==""))
           {
            alert('Le PU des palettes Plastiques est un numérique!');
			document.getElementById('pupaletteplastique').focus();
			return false;
           }
 if(isNaN(document.getElementById('emb_plein').value)||(document.getElementById('emb_plein').value==""))
           {
            alert('La quantite des emballages pleins est un numérique!');
			document.getElementById('emb_plein').focus();
			return false;
           }
 if(isNaN(document.getElementById('pu_emb_plein').value)||(document.getElementById('pu_emb_plein').value==""))
           {
            alert('Le PU des emballages pleins est un numérique!');
			document.getElementById('pu_emb_plein').focus();
			return false;
           }
 if(isNaN(document.getElementById('emb_vide').value)||(document.getElementById('emb_vide').value==""))
           {
            alert('La quantite des emballages vides est un numérique!');
			document.getElementById('emb_vide').focus();
			return false;
           }
 if(isNaN(document.getElementById('pu_emb_vide').value)||(document.getElementById('pu_emb_vide').value==""))
           {
            alert('Le PU des emballages vides est un numérique!');
			document.getElementById('pu_emb_vide').focus();
			return false;
           }

	return true;
 }
