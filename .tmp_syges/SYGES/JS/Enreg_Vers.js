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
   if(!verif_date(document.getElementById('date').value))
           {
            alert('Date du versement  incorrecte.');
			document.getElementById('date').focus();
			return false;
           }
  if(document.getElementById('vendeur').value=="")
           {
            alert('BV Choisir le Vendeur.');
			document.getElementById('vendeur').focus();
			return false;
           }	 

 if(isNaN(document.getElementById('Montant').value)||(document.getElementById('Montant').value==""))
           {
            alert('Le Montant versé est un numérique!');
			document.getElementById('Montant').focus();
			return false;
           }
  if(document.getElementById('codeemb').value=="")
           {
            alert('BV choisir le code de l\'emballage.');
			document.getElementById('codeemb').focus();
			return false;
           }	
		   
 if(isNaN(document.getElementById('qte').value)||(document.getElementById('qte').value==""))
           {
            alert('Le quantite  emballage 1 est un numérique !');
			document.getElementById('qte').focus();
			return false;
           }
if(isNaN(document.getElementById('qte2').value))
           {
            alert('Le quantite  emballage 2 est un numérique!');
			document.getElementById('qte2').focus();
			return false;
           }
 if(isNaN(document.getElementById('qte3').value))
           {
            alert('Le quantite  emballage 3 est un numérique!');
			document.getElementById('qte3').focus();
			return false;
           }
  if((document.getElementById('codeemb').value==document.getElementById('codeemb2').value)&&(document.getElementById('qte2').value!=""))
           {
            alert('Emballage 1 identique a  l\'emballage 2.');
			document.getElementById('codeemb2').focus();
			return false;
           }
  if((document.getElementById('codeemb2').value==document.getElementById('codeemb3').value)&&(document.getElementById('qte3').value!="")&&(document.getElementById('qte2').value!=""))
           {
            alert('Emballage 2 identique a  l\'emballage 3.');
			document.getElementById('codeemb3').focus();
			return false;
           }
if((document.getElementById('codeemb').value==document.getElementById('codeemb3').value)&&(document.getElementById('qte3').value!=""))
           {
            alert('Emballage 1 identique a  l\'emballage 3.');
			document.getElementById('codeemb3').focus();
			return false;
           }
 	return true;
 }
