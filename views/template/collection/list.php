<h2>Collection Requests (<?php echo count($collections); ?> Outstanding)</h2>
<table class="table table-striped table-responsive">
    <thead>
    <tr>
        <th class="col-sm-1">ID</th>
        <th class="col-sm-1">Date</th>
        <th class="col-sm-1">Customer</th>
        <th class="col-sm-1">Email</th>
        <th class="col-sm-1">Contact Name</th>
        <th class="col-sm-1">Phone</th>
        <th class="col-sm-1">Address</th>
        <th class="col-sm-1">Town</th>
        <th class="col-sm-1">County</th>
        <th class="col-sm-1">Postcode</th>
        <th class="col-sm-1">Weight</th>
        <th class="col-sm-1">Units</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($collections as $key => $collection) {
        echo '<tr><td>';
        foreach ($collection as $item) {
            echo $item . '</td><td>';
        }
        echo '</td></tr>';
    }
    ?>
    </tbody>
</table>
