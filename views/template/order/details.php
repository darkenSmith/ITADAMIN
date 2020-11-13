<?php
//var_dump($order->newFiles) ;
//$e = 1;
?>

<div class="row">
    <h3>Order Details - <?php echo $order->detail->sales_order_number; ?></h3>
    <p><?php echo $order->detail->actual_delivery_date; ?></p>
</div>
<div class="row">
    <h3 class="text-left">Products Received on order</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-sm-6">Product Type</th>
            <th class="col-sm-6">Items Received</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($order->lineItems as $line) { ?>
            <tr>
                <td><?php echo $line->product_name; ?></td>
                <td><?php echo $line->count; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<div class="row">
    <?php
    if (isset($app) && $app->canUpload()) {
        include_once('upload.php');
    }

    if ($order->files || $order->newFiles) { ?>
        <h3>Document Download</h3>
        <p>Please select download on the appropriate files you wish to store. Documents will be saved here so you can
            come back anytime to retrieve them.</p>
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="col-sm-2">Order Number</th>
                <th class="col-sm-8">File Name</th>
                <th class="col-sm-2">Download</th>
                <th class="col-sm-2">Delete</th>
            </tr>
            </thead>
            <?php
            if ($order->files) {
                foreach ($order->files as $dir => $file) {
                    $path = '/uploads/' . $dir . '/';
                    echo '<tr>';
                    echo '<td>' . $order->detail->sales_order_number . '</td>';
                    echo '<td>' . substr($file, 12) . '</td>';
                    echo '<td><a href="/order/download?file=' . base64_encode($dir . '/' . $file) . '" class="btn btn-success">Download</a> </td>';
                    if ($app->canUpload()) {
                        echo '<td><a href="/order/delete?file=' . base64_encode($dir . '/' . $file) . '&view=' . $id . '" class="btn btn-danger">Delete</a> </td>';
                    } else {
                        echo '<td></td>';
                    }
                    echo '</tr>';
                }
            }
            if ($order->newFiles) {
                foreach ($order->newFiles as $file) {
                    $path = '/uploads/pdf/';
                    echo '<tr>';
                    echo '<td>' . $order->detail->sales_order_number . '</td>';
                    echo '<td>' . $file->file_type . '</td>';
                    echo '<td><a href="/order/download?newfile=' . $file->filename .'&format='.base64_encode($file->file_format).'" class="btn btn-success">Download</a> </td>';
                    if (isset($app) && $app->canUpload()) {
                        echo '<td><a href="/order/delete?newfile=' . $file->filename . '&format='.base64_encode($file->file_format).'&view=' . $id . '" class="btn btn-danger">Delete</a>';
                    } else {
                        echo '<td></td>';
                    }
                    echo '</tr>';
                }
            }
            ?>
        </table>
    <?php } else { ?>
        <p>Documents are not yet available for this order. Click here to receive an email when the documents are
            available.</p>

    <?php } ?>
</div>
