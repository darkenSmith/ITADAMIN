<table class="table table-striped">
    <thead>
    <tr>
        <th class="col-sm-1">Date</th>
        <th class="col-sm-2">Order Number</th>
        <th class="col-sm-5">Collection Location</th>
        <?php if ($data->cod_required == 1) { ?>
            <th class="col-sm-1">COD Status</th>
        <?php }
        if ($data->amr_required == 1) { ?>
            <th class="col-sm-1">AMR Status</th>
        <?php }
        if ($data->rebate_required == 1) {
            ?>
            <th class="col-sm-1">Rebate Status</th>
        <?php } ?>
        <th class="col-sm-1">View Order</th>
        <!--                <th class="col-sm-1">PO Number</th>-->
    </tr>
    </thead>
    <?php foreach ($collections as $collection) {
        $date = strtotime($collection->actual_delivery_date);
        $date = date('d/m/Y', $date);

        echo '<tr>
                <td>' . $date . '</td>
                <td>' . $collection->sales_order_number . '</td>
                <td>' . $collection->address1 . ' ' . $collection->address2 . ' ' . $collection->address3 . ' ' . $collection->address4 . ' ' . $collection->postcode . '</td>';
        if ($data->cod_required == 1) {
            echo '<td>';

            if (isset($collection->files['disposal'])) {
                echo '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
            } else {
                echo '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
            }
            echo '</td>';
        }
        if ($data->amr_required == 1) {
            echo '<td>';
            if (isset($collection->files['asset'])) {
                echo '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
            } else {
                echo '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
            }
            echo '</td>';
        }
        if ($data->rebate_required == 1) {
            echo '<td>';
            if (isset($collection->files['rebate'])) {
                echo '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
            } else {
                echo '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
            }
            echo '</td>';
        }
        echo '<td><a href="/order/view/' . $collection->id . '" class="btn btn-success">View Order</a></td>  
            </tr>';
    } ?>
</table>
