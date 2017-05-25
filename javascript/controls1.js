function add_county(){
	var length = document.forms[0].county_aoi.length;
	var previous = "";
	document.forms[0].counties.checked = true;
	document.forms[0].county_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].county_aoi[i].checked){
			var selected = document.forms[0].county_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_item').value = 'county';
	parent.map.document.getElementById('county_ajax').value = previous;
	parent.map.document.getElementById('county_aoi').value = previous;
	parent.map.document.getElementById('county_pdf').value = previous;
	loadlayers();
}
function add_wtshd(){
	var length = document.forms[0].wtshd_aoi.length;
	var previous = "";
	document.forms[0].basins_river.checked = true;
	document.forms[0].wtshds_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].wtshd_aoi[i].checked){
			var selected = document.forms[0].wtshd_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_item').value = 'basin';
	parent.map.document.getElementById('basin_ajax').value = previous;
	parent.map.document.getElementById('basin_aoi').value = previous;
	parent.map.document.getElementById('basin_pdf').value = previous;
	loadlayers();
}
function add_state(){
	var length = document.forms[0].state_aoi.length;
	var previous = "";
	document.forms[0].states.checked = true;
	document.forms[0].states_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].state_aoi[i].checked){
			var selected = document.forms[0].state_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('state_ajax').value = previous;
	parent.map.document.getElementById('state_aoi').value = previous;
	parent.map.document.getElementById('state_pdf').value = previous;
	loadlayers();
}
function add_owner(){
	var length = document.forms[0].owner_aoi.length;
	var previous = "";
	//document.forms[0].states.checked = true;
	//document.forms[0].states_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].owner_aoi[i].checked){
			var selected = document.forms[0].owner_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_item').value = 'owner_desc';
	parent.map.document.getElementById('owner_ajax').value = previous;
	parent.map.document.getElementById('owner_aoi').value = previous;
	parent.map.document.getElementById('owner_pdf').value = previous;
	loadlayers();
}
function add_manage(){
	var length = document.forms[0].manage_aoi.length;
	var previous = "";
	//document.forms[0].states.checked = true;
	//document.forms[0].states_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].manage_aoi[i].checked){
			var selected = document.forms[0].manage_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_item').value = 'manager_de';
	parent.map.document.getElementById('manage_ajax').value = previous;
	parent.map.document.getElementById('manage_aoi').value = previous;
	parent.map.document.getElementById('manage_pdf').value = previous;
	loadlayers();
}
function add_bcr(){
	var length = document.forms[0].bcr_aoi.length;
	var previous = "";
	document.forms[0].bcr.checked = true;
	document.forms[0].bcr_tab2.checked = true;
	for (var i=0;  i<length; i++){
		if(document.forms[0].bcr_aoi[i].checked){
			var selected = document.forms[0].bcr_aoi[i].value;
			if (previous.length == 0){
				previous = selected;
			}else{
				previous = previous + ":" + selected;
			}
		}
	}
	parent.map.document.getElementById('query_item').value = 'bcr';
	parent.map.document.getElementById('bird_consv_ajax').value = previous;
	parent.map.document.getElementById('bird_consv_aoi').value = previous;
	parent.map.document.getElementById('bird_consv_pdf').value = previous;
	loadlayers();
}


function add_ecosys(){
	if(document.forms[0].ecosys.checked){
		parent.map.document.getElementById('ecosys_aoi').value = "1";
		parent.map.document.getElementById('ecosys_ajax').value = "1";
	}else{
		parent.map.document.getElementById('ecosys_aoi').value = "";
		parent.map.document.getElementById('ecosys_ajax').value = "";
	}
	loadlayers();
}
function aoi_pre_sub(){
	if(parent.map.document.forms[0].owner.value == '' &&
	parent.map.document.forms[0].manage.value == '' &&
	parent.map.document.forms[0].bird_consv.value == '' &&
	parent.map.document.forms[0].county.value == '' &&
	parent.map.document.forms[0].state.value == '' &&
	parent.map.document.forms[0].ecosys.value == '' &&
	parent.map.document.forms[0].basin.value == ''){
		alert('must select AOI before submitting')
	} else {
		parent.map.document.getElementById('aoi_type').value = 'predefined';
		parent.map.document.getElementById('zoom').value = '1';
		parent.map.document.getElementById('mode').value = "pan";
		parent.map.document.getElementById('fm1').submit();
	}
}

function pre_reset(){
	for( var i = 0; i < document.forms[0].county_aoi.length; i++) document.forms[0].county_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].wtshd_aoi.length; i++) document.forms[0].wtshd_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].state_aoi.length; i++) document.forms[0].state_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].owner_aoi.length; i++) document.forms[0].owner_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].manage_aoi.length; i++) document.forms[0].manage_aoi[i].checked = false;
	for( var i = 0; i < document.forms[0].bcr_aoi.length; i++) document.forms[0].bcr_aoi[i].checked = false;
	parent.map.clear_aois();
}
function cust_start(){
	parent.map.draw();
}
function aoi_cust_sub(){
	if((parent.map.posix.length < 3)){
		alert('must select AOI before submitting')
	} else {
		parent.map.document.getElementById('aoi_type').value = 'custom';
		parent.map.document.getElementById('click_val_x').value = parent.map.posix;
		parent.map.document.getElementById('click_val_y').value = parent.map.posiy;
		parent.map.document.getElementById('fm1').action = "map2.php";
		parent.map.document.getElementById('fm1').target = "map";
		parent.map.document.getElementById('zoom').value = '1';
		parent.map.document.getElementById('mode').value = "pan";
		parent.map.document.getElementById('fm1').submit();
	}
}
function cust_reset(){
	parent.map.posix.length = 0;
	parent.map.posiy.length = 0;
	parent.map.jg_box.clear();
}
function show_county(){
	// alert('hello');
	if(document.forms[0].county_tab2.checked){
		document.forms[0].counties.checked = true;
		parent.map.document.getElementById('query_item').value = 'county';
	}else{
		document.forms[0].counties.checked = false;
	}
	loadlayers();
}
function show_basin(){
	if(document.forms[0].wtshds_tab2.checked){
		document.forms[0].basins_river.checked = true;
		parent.map.document.getElementById('query_item').value = 'basin';
	}else{
		document.forms[0].basins_river.checked = false;
	}
	loadlayers();
}
function show_state(){
	if(document.forms[0].states_tab2.checked){
		document.forms[0].states.checked = true;
	}else{
		document.forms[0].states.checked = false;
	}
	loadlayers();
}

function show_bcr(){
	if(document.forms[0].bcr_tab2.checked){
		document.forms[0].bcr.checked = true;
		parent.map.document.getElementById('query_item').value = 'bcr';
	}else{
		document.forms[0].bcr.checked = false;
	}
	loadlayers();
}
function upload(){
	window.open("../upload.php","", "height=300,width=550")

}