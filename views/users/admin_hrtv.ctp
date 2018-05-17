<div>
    <div class="signal-view"></div>
</div>

<script>
$(document).ready(function() {
    var $signalHrtv = "http://signal.totalhipico.net/HRTV2.html";
    $(".signal-view").html('Buscando senial...').load($signalHrtv);
});
</script>