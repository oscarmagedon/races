function calculate(amount,type,cou){
	var quant = 1;
	//alert("amo: " + amount + " - tipo: " + type + " - caballos: " + cou)	
	if(type == "WP" || type == "WS")
		quant = 2;
		
	if(type == "WPS")
		quant = 3;
	
	if(type == "EXA" || type == "TRI" || type == "SUP"){
		var boxes = new Object();
		$("#posiciones").find("tr").each(function(){
			var posit = $(this).attr('title');
			var coun = 0;
			boxes[posit] = new Object();
			$(this).find(".added").each(function(){
				boxes[posit][coun] = $(this).text();
				coun = coun + 1;
			});
		});
		//console.log(boxes);
		/*
		$.each(boxes, function (ind,vals){
			$.each(vals, function (indx,val){
				//alert("la " + ind + " > "+ indx +": "+ val);
			});
			//en esta parte comprobar cuantas posiciones son 
			//y despues hacer las boxes pa ver cuantas posibles hay
			//buscando si existe ya en esa probabilidad y colocandolo de una
		});*/
	}
	var total = quant * amount * cou;
	$("#TicketPrizeit").val(total);	
}

function change_hipo(hip,pre){
	$("#RacesSpace").html(load_img);
	$("#RacesSpace").load(load_races + "/" + hip + "/" + dat, function(){
		$("#TicketRaceId").change(function(){
			var race_id = $(this).attr('value');
			change_race(race_id);
		});
		if(pre)
			$("#TicketRaceId").val(race_pre)
	});
}

function change_race(race_id,pre){
	$("#div_horses").html(load_img);
	$("#div_horses").load(load_horses + "/" + race_id , function(){
		$('.horse_check').hide();
		$(".row_horse").mouseover(function(){
            
			$(this).find('td').addClass('row_over');
		
        }).mouseout(function(){
		
            $(this).find('td').removeClass('row_over');
		
        }).click(function(){
			
            if($("#TicketPlayTypeId").val() == "" ){
				err_line(2,"Escoja un tipo de jugada",$("#mess_bet"));
			}else{
				var typeBet = $("#TicketPlayTypeId").val();
				var chk = $(this).find('.horse_check');
				var img = $(this).find('.ui_check');
				
				if(chk.attr('checked') != "checked"){
					if(typeBet < 7){
						chk.attr('checked',true);
						img.addClass('ui_checked');
					}else{
						hnum = chk.parents('.row_horse').find('.num_horse').attr('id');
						hid = chk.attr('value');
						put_position(hnum,hid);
					}
					co = co + 1;
				}else{
					if(typeBet < 7){
						chk.attr('checked',false);	
						img.removeClass('ui_checked');
					}
					co = co - 1;
				}
			}
		});
	});
}

function to_set_details(type_id){	
	switch(type_id){
		case '7':
			tohtml =  construct_table(2);
			break;
		case '8':
			tohtml =  construct_table(3);
			break;
		case '9':
			tohtml =  construct_table(4);
			break;
		default:
			tohtml =  "Seleccione los caballos";
			break;
	}
	
	$("#horse_set").html(tohtml);
	
	$(".row_pos").css('cursor','pointer').click(function(){
		var act_elem = $("#posiciones").find(".actual");
		act_elem.removeClass('actual');
		
		$(this).find('td').addClass('actual');
	});
}

function construct_table(rows){
	tohtml = "<table id='posiciones'>";
	for(i = 1; i <= rows; i = i + 1){
		clsactual = "";
		if (i == 1)
			clsactual = " actual";
		
		tohtml = tohtml + "<tr class='row_pos' title='" + i + "'><th style='width:20px' id='"+ i +"_title'>"+ i +"&ordf;</th>" +
		"<td id='"+ i +"_positioner' class='set_pos" + clsactual + "'>&nbsp;</td></tr>";
	}
	tohtml = tohtml + "</table>";
	return tohtml;
}

function put_position(horsnum,horseid){
	var act_elem = $("#posiciones").find(".actual");
	var posi = act_elem.attr('id').split('_');
	
	if(act_elem.find("#pos" + horseid).length != 0){	
		err_line(2,"El caballo ya esta agregado en la posicion.");
	}else{
		the_box[horseid] = horsnum;
		var horse_toput = "<div class='added' id='pos" + horseid + "'>" +
		"<input class='pos_spec' id='pos_" + ind + "' name='data[Horse][" + ind + "][position]' " +
		"value='"+ posi[0] +"' type='hidden'>" + horsnum +
		"<input name='data[Horse][" + ind + "][horse_id]' value='" + horseid + "' " +
		"class='horse_check' type='hidden'></div>";
		act_elem.append(horse_toput);
		ind = ind + 1;
	}	
}

function pass_next(){
	var act_elem = $("#posiciones").find(".actual");
	
	act_elem.removeClass('actual');
	
	if(act_elem.parents('tr').next('tr').length){
		act_elem.parents('tr').next('tr').find('td').addClass('actual');
	}else{
		$("#1_positioner").addClass('actual');
	}
}

function reset_positions(){
	the_box = Object();
	ind = 0;
}

function box_all(){
	var ind = 0;
	$(".set_pos").html("");	
	co = 0;
	$.each(the_box,function(index,value){
		
		$(".set_pos").each(function(e){	
			var posi = $(this).attr('id').split('_');
			
			var my_box = "<div class='added' id='pos" + index + "'>" +
			"<input id='pos_" + ind + "' name='data[Horse][" + ind + "][position]' " +
				"value='"+ posi[0] +"' type='hidden'>" + value +
			"<input name='data[Horse][" + ind + "][horse_id]' value='" + index + "' " +
					"class='horse_check' type='hidden'></div>";
					
			$(this).append(my_box);
			co ++;
			ind ++;
		});
		
	});
}

function err_line(type,mess,elem){
	elem = $(elem);
	if(type == 1){ 
		premess = "<b>ATENCION: </b>";
		//elem.css({'background':'#C9D4EF','color':'blue','border-color':'blue', 'border-radius': '4px'});
		elem.addClass('message-blue');
	}else{ 
		premess = "<b>ERROR: </b>";
		//elem.css({'background':'#FCBFC4','color':'red','border-color':'red'});
		elem.addClass('message-red');
	}

	elem.html(premess + mess);
	elem.fadeIn('slow');
	setTimeout(function (){ elem.fadeOut('slow')},3000);
}