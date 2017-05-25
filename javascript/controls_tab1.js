//this function creates layer string from checked boxes and submits map with new layers string
function loadlayers(){
	if(document.forms[0].background[0].checked) var layer=document.forms[0].background[0].value;
	if(document.forms[0].background[1].checked) var layer=document.forms[0].background[1].value;
	if(document.forms[0].background[2].checked) var layer=document.forms[0].background[2].value;
	if(document.forms[0].steward[0].checked)  layer=layer+" ownership";
	if(document.forms[0].steward[1].checked)  layer=layer+" management";
	if(document.forms[0].steward[2].checked)  layer=layer+" status";
	if(document.forms[0].basins_river.checked) layer=layer + " wtshds";
	if(document.forms[0].cities.checked) layer=layer + " cities";
	if(document.forms[0].counties.checked) layer=layer + " counties";
	if(document.forms[0].states.checked) layer=layer + " states";
	if(document.forms[0].hydro.checked) layer=layer + " hydro";
	if(document.forms[0].roads.checked) layer=layer + " roads";
	if(document.forms[0].bcr.checked) layer=layer + " bcr";
	/*
	parent.map.document.getElementById('layers').value = layer;
	parent.map.document.getElementById('zoom').value = '1';
	parent.map.document.getElementById('fm1').action = parent.map.location;
	parent.map.document.getElementById('fm1').target = "map";
	parent.map.document.getElementById('fm1').submit();
	*/
	parent.map.document.getElementById('layers_ajax').value = layer;
	parent.map.document.getElementById('layers_pdf').value = layer;
	if(parent.map.document.getElementById('layers')){
		parent.map.document.getElementById('layers').value = layer;
	}
	if(parent.map.document.getElementById('layers_zoom')){
		parent.map.document.getElementById('layers_zoom').value = layer;
	}
	//parent.map.document.getElementById('layers_pdf').value = layer;
	parent.map.document.getElementById('zoom_ajax').value = '1';
	//parent.map.clkcntr();
	parent.map.send_ajax();
}

function show_lgnd(){
		  $("#legendtab").click();
}