<?php
include "config.inc.php";
include "database.php";

?>
<!DOCTYPE HTML>
<html lang="de" >
<head>
<meta charset="UTF-8">
<title>Alkomat</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<link href="css/jquery.mobile-1.3.0.min.css" rel="stylesheet" type="text/css"/>
<script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="js/jquery.mobile-1.3.0.min.js"></script>
<script type="text/javascript">
    var anzahlInListe=0;
	
$(document).ready( function(event) {

	anzahlInListe=<?php get_anzahl()?>;
	
  $('div#p1').on('pagebeforeshow',function(event, ui){
		berechneBAK();
   });
   

   
  $('.berechnen').on('tap',function(event){
		berechneBAK();
   });	   
  $('#reset').on('tap',function(event){
		$(".ui-li-count").text(0);
   });
  $('#speichern').on('tap',function(event){
		addDrink();
   });
   
	erstellListe(); 
	
	
   $('#delete').on('tap',function(event){
		// Sucht alle Einträge und löscht diese, geht auch mit localStorage.clear()
		
		while(anzahlInListe>0) {
			localStorage.removeItem(localStorage.key(anzahlInListe-1));
			$('#getraenkeliste').children("li").last().remove();
			anzahlInListe--;
		}
		
		$.mobile.changePage("#p2", {transition:"slide",reverse: true});
   });	
   

  $('#p1').on('swipeleft',function(event){
		$.mobile.changePage("#p2", {transition:"slide"});
  });
   $('#p2').on('swipeleft',function(event){
		$.mobile.changePage("#p3", {transition:"slide"});
  });
   $('#p3').on('swiperight',function(event){
		$.mobile.changePage("#p2", {transition:"slide",reverse: true});
  });
    $('#p2').on('swiperight',function(event){
		$.mobile.changePage("#p1", {transition:"slide",reverse: true});
  });
  
});

function erstellListe() {
		
	for( var i=0;i<anzahlInListe; i++) {
		var list=localStorage.getItem("getraenk"+String(i));
		$('#getraenkeliste').append(list);
	}
	registerListCounter();
}

function registerListCounter() {
  $("#getraenkeliste li").off();
  $("#getraenkeliste li").on('tap',function(event) {
		var obj=$(".ui-li-count", this);
		obj.text(1+parseInt(obj.text()));
   });
   $("#getraenkeliste li").on('taphold',function(event) {
		$(".ui-li-count", this).text(0);
   });
}

function berechneBAK() {
		var alkoholmenge=0;
		var eintrag=$("#getraenkeliste li");
		eintrag.each( function() {
			var m=$(this).attr("data-menge");
			var g=$(this).attr("data-gehalt");
			var n=$(".ui-li-count", this).text();
			alkoholmenge=alkoholmenge+parseFloat(m)*parseFloat(g)*parseInt(n)*8;
		});
		var gewicht=parseFloat($('#gewicht').val());
		var geschlechtfaktor=($(':radio:checked').val())=="choice-1"?0.75:0.6;
		var bak=alkoholmenge/(gewicht*geschlechtfaktor);
		$("#menge").text("Alkoholmenge in Gramm: "+kaufm(alkoholmenge));
		$("#bak").text("Blutakoholkonzentration in Promille: "+kaufm(bak));
}

function kaufm(x) {
  var k = (Math.round(x * 100) / 100).toString();
  k += (k.indexOf('.') == -1)? '.00' : '00';
  return k.substring(0, k.indexOf('.') + 3);
}

function addDrink() {

	var m=$('#menge_neu').val();
	var g=$('#gehalt_neu').val();
	var b=$('#bezeichnung').val();
	var list="<li data-menge=\""+m+"\" data-gehalt=\""+g+"\">"+b+"<span class=\"ui-li-count\">0</span></li>"
	
	localStorage.setItem("getraenk"+anzahlInListe, list);
	$('#getraenkeliste').append(list);
	anzahlInListe++;
	$('#getraenkeliste').listview('refresh');
	registerListCounter();
	$.mobile.changePage("#p2", {transition:"slide",reverse: true});
}
</script>

</head>
<body>
<!-- Start page 1-->
<div data-role="page" id="p1" data-theme="a">
	<div data-role="header">
		<h1>Promille berechnen</h1>
	</div><!-- Ende header -->
	<div data-role="content">
		<p>Berechnung der Blutalkohokonzentration in Promille.</p>
		
			<div data-role="fieldcontain">
				<label for="gewicht">Körpergewicht in kg:</label>
				<input type="number" name="gewicht" id="gewicht" value="70"  />
			</div>
			<div data-role="fieldcontain" data-type="horizontal">
				<fieldset data-role="controlgroup" data-type="horizontal">
					<legend>Geschlecht:</legend>

						<input type="radio" name="radio-choice-1" id="radio-choice-1" value="choice-1" checked="checked" />
						<label for="radio-choice-1">Männlich</label>

						<input type="radio" name="radio-choice-1" id="radio-choice-2" value="choice-2"  />
						<label for="radio-choice-2">Weiblich</label>

				</fieldset>
			</div>
		<ul data-role="listview" data-inset="true" class="ergebnisliste">
			<li id="menge">Alkoholmenge in Gramm: <li>
			<li id="bak">Blutakoholkonzentration in Promille: <li>
		</ul>
				
	</div><!-- Ende content -->
	<div data-role="footer">
		<div data-role="navbar">
			<ul>
					<li class="berechnen" ><a href="#p1"  data-icon="check"> Neu berechnen</a></li>
					<li><a href="#p2" data-transition="slide" data-icon="arrow-r"> Getränke auswählen</a></li>			
			</ul>
		</div>	
	</div><!-- Ende footer -->
</div><!-- Ende page 1 -->

<!-- Start page 2-->
<div data-role="page" id="p2" data-theme="a">
	<div data-role="header">
		<h1>Getränke auswählen</h1>
	</div><!-- Ende header -->
	<div data-role="content">
		<p>Was hast du getrunken?</p>
		
			
		<ul data-role="listview" id="getraenkeliste">
			<li data-menge="0.5" data-gehalt="5">Flasche Bier 0,5 l<span class="ui-li-count">0</span></li>
			<li data-menge="0.33" data-gehalt="5">Flasche Bier 0,33 l<span class="ui-li-count">0</span></li>
			<li data-menge="0.1" data-gehalt="10">Glas Wein 0,1l<span class="ui-li-count">0</span></li>
			<li data-menge="0.25" data-gehalt="10">Glas Wein 0,25l<span class="ui-li-count">0</span></li>
			<li data-menge="0.02" data-gehalt="40">Glas Schnaps 2cl<span class="ui-li-count">0</span></li>
			<li data-menge="0.25" data-gehalt="0.3">Orangensaft 0,25 l<span class="ui-li-count">0</span></li>
			<li data-menge="0.5" data-gehalt="0">Fanta 0,5l<span class="ui-li-count">0</span></li>
		</ul>
			
		
				
	</div><!-- Ende content -->
	<div data-role="footer">
		<div data-role="navbar">
			<ul>
				<li><a href="#p1" data-transition="slide" data-direction="reverse" data-icon="arrow-l">übernehmen</a></li>
				<li><a href="#" id="reset" data-icon="delete">rücksetzten</a></li>
				<li><a href="#p3" data-transition="slide" data-icon="arrow-r">Neues Getränk anlegen</a></li>			
			</ul>
		</div>
	</div><!-- Ende footer -->
</div><!-- Ende page 2 -->

<!-- Start page 3-->
<div data-role="page" id="p3" data-theme="a">
	<div data-role="header">
		<h1>Neues Getränk anlegen</h1>
	</div><!-- Ende header -->
	<div data-role="content">
		<p>Neues Getränk anlegen:</p>
			<div data-role="fieldcontain">
				<label for="bezeichnung">Name des Getränks</label>
				<input type="text" name="bezeichnung" id="bezeichnung" value=""  />
			</div>		
			<div data-role="fieldcontain">
				<label for="menge_neu">Menge in Litern:</label>
				<input type="number" name="menge_neu" id="menge_neu" value=""  />
			</div>
			<div data-role="fieldcontain">
				<label for="gehalt_neu">Alkoholkonzentration in %:</label>
				<input type="number" name="gehalt_neu" id="gehalt_neu" value=""  />
			</div>
				
	</div><!-- Ende content -->
	<div data-role="footer">
		<div data-role="navbar">
			<ul>
				<li><a href="#p2" data-transition="slide" data-direction="reverse" data-icon="arrow-l">abbrechen</a></li>
				<li ><a href="#" id="speichern" data-icon="check">speichern</a></li>
				<li ><a href="#" id="delete" data-icon="delete">Lokal löschen</a></li>
			</ul>
		</div>
	</div><!-- Ende footer -->
</div><!-- Ende page 3 -->
</body>
</html>