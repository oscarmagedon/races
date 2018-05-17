<style>
.races-form{
    width: 400px;
}
.races-form th {
    text-align: right;
}
.races-form td {
    text-align: left;
}
#RaceNumber{
	width:60px;
	font-size:130%;
}
#RaceRaceDate{
	width:100px;
}
.ShowLocalTime{
    font-size: 130%;
}
.loadimg {
    display: none;
}

</style>
<script type="text/javascript">
    var $calcUrl = '<?= $html->url(array('action' => 'getmygmt')) ?>',
        $htrGmt  = '<?= $this->data['Hipodrome']['htgmt'] ?>';
$(function(){
	
    $("#RaceRaceDate").datepicker({dateFormat:"yy-mm-dd"});
    

    $(".race-time").find('select').change(function (){
       
        $time = $("#RaceRaceTimeHour").find(':selected').text() + '-' + 
                $("#RaceRaceTimeMin").find(':selected').val() +
                $("#RaceRaceTimeMeridian").find(':selected').val();
         
        //console.log($time);
        getLocalTime($time,$htrGmt);
    });
    
});

//FUNCION QUE CARGA LAS CARRERAS EN LA TABLA
function getLocalTime(time,gmt)
{
    $.ajax({
        url         : $calcUrl + '/' + time + '/' + gmt,
        type        : 'get',
        dataType    : 'json',
        
        beforeSend : function () {
            $('.loadimg').show();              
        },
        success: function(data){

            //console.log(html);
            $("#RaceLocalTime").val(data.valraw);
            $(".ShowLocalTime").html(data.valform);
            $('.loadimg').hide();
        },
        error: function(){
            alert('ERROR Cargando Carreras.');
        }
        
    });
}

</script>
<div class="races-form" style="background-color:#FFF; height:100%;">
	<?php 
	echo $form->create('Race');
	echo $form->input('id');
	?>
	<table>
		<tr>
			<th>Hipodromo</th>
			<td><?php echo $form->input('hipodrome_id',array('label'=>false)) ?></td>
		</tr>
		<tr>
			<th>Numero</th>
			<td><?php echo $form->input('number',array('label'=>false)) ?></td>
		</tr>
		<tr>
			<th>Fecha</th>
			<td><?php echo $form->input('race_date',array('type'=>'text','label'=>false)) ?></td>
		</tr>
		<tr>
			<th>Hora Carr.</th>
			<td><?php echo $form->input('race_time',array('label'=> false,'div'=>'race-time')) ?></td>
		</tr>
        <tr>
			<th>
                <?php echo $html->image('loading_small.gif', array('class'=>'loadimg')) ?>
                Hora Local.
            </th>
			<td>
                <span class="ShowLocalTime">
                    <?= $dtime->time_to_human($this->data['Race']['local_time']) ?>
                </span>
                <?php 
                echo $form->input('local_time',array(
                        'label'=> false, 'readonly' => true,'type' => 'hidden'
                    ));
                
                ?></td>
		</tr>
	</table>	
<?php 
echo $form->end()
?>
</div>