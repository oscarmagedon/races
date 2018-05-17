<table cellpadding="0" cellspacing="0" border='1' class="table-total" style="font-size: 90%">
    <tr>
        <th>Numero</th>
        <th>Nombre</th>
        
    </tr>
    <?php
    foreach ($this->race['Horse'] as $horse) {
    ?>
        <tr>
            <td style="text-align: right; ">
            <?php 
            if($horse['enable'] == 0) {
                echo " <span style='color:Red'>(Ret.)</span>";
            }

            echo $horse['number'];
            ?>
            </td>
            <td style="text-align: left;">
                <?php echo $horse['name'] ?>
            </td>
        </tr>
    <?php 
    } 
    ?>
</table>