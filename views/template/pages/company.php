<?php
if($company->auth){
?>
<div class="row">
    <h2><?php echo $data->company_name;?></h2>
</div>

<div class="row">
    <h3 class="text-left">Recycling Collection Details</h3>
    <p>Please note a Red Cross indicates that the file still needs to be uploaded for the relevant order.</p>


    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-sm-1">Date</th>
            <th class="col-sm-2">Order Number</th>
            <th class="col-sm-5">Collection Location </th>
            <?php if($data->cod_required == 1){ ?>
                <th class="col-sm-1">COD Status</th>
            <?php }
            if($data->amr_required == 1){ ?>
                <th class="col-sm-1">AMR Status</th>
            <?php }
            if($data->rebate_required == 1){?>
                <th class="col-sm-1">Rebate Status</th>
            <?php }?>
            <th class="col-sm-1">View Order</th>
            <!--                <th class="col-sm-1">PO Number</th>-->
        </tr>
        </thead>
        <?php foreach ($collections as $collection){
            $date = strtotime($collection->actual_delivery_date);
            $date = date('d/m/Y',$date);

            echo '<tr>
                <td>'.$date.'</td>
                <td>'.$collection->sales_order_number.'</td>
                <td>'.$collection->address1.' '.$collection->address2.' '.$collection->address3.' '.$collection->address4. '<td>';
            if($data->cod_required == 1) {
                if ( isset( $collection->files['disposal'] ) ) {
                    echo '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
                } else {
                    echo '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
                }
                echo '</td>';
            }
            if($data->amr_required == 1) {
                echo '<td>';
                if ( isset( $collection->files['asset'] ) ) {
                    echo '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
                } else {
                    echo '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
                }
                echo '</td>';
            }
            if($data->rebate_required == 1) {
                echo '<td>';
                if(isset($collection->files['rebate'])){
                    echo '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
                }else{
                    echo '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
                }
                echo '</td>';
            }
                echo '<td><a href="/order/view/'.$collection->id.'" class="btn btn-success">View Order</a></td>  
            </tr>';
        } ?>
    </table>

</div>
<hr>
<div class="row">
    <h3>Collections By Location</h3>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-3">Location Name</th>
            <th class="col-5">Address </th>
            <th class="col-1">Postcode</th>
            <th class="col-2">Phone</th>
            <th class="col-1">Collections</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($summary as $item){
            echo '<tr>
            <td>'.$item->location_name.'</td>
            <td>'.$item->address1.' '.$item->address2.' '.$item->address3.' '.$item->address4.'</td>
            <td>'.$item->postcode.'</td>
            <td>'.$item->telephone.'</td>
            <td>'.$item->collections.'</td>
        </tr>';
        } ?>
        </tbody>
</div>
<?php }else{ ?>

    <div class="row">
        <h1>Not Authorised</h1>
        <p>You are not authorised to view this page. If you believe this is an error, please contact your account manager>
    </div>
<?php }

