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
  if(document.getElementById('codevente').value=="")
           {
            alert('BV saisir le code de la vente.');
			document.getElementById('codevente').focus();
			return false;
           }
   if(!verif_date(document.getElementById('date').value))
           {
            alert('La Date de versement est  incorrecte.');
			document.getElementById('date').focus();
			return false;
           }
 if(isNaN(document.getElementById('avance').value)||(document.getElementById('avance').value==""))
           {
            alert('L\'avance est un numérique diff de zero!');
			document.getElementById('avance').focus();
			return false;
           }
 	return true;
 }
