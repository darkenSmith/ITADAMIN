<table class="table table-striped">
    <thead>
    <tr>
        <th class="col-3">Location Name</th>
        <th class="col-5">Address</th>
        <th class="col-1">Postcode</th>
        <th class="col-2">Phone</th>
        <th class="col-1">Collections</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($summary as $item) {
        echo '<tr>
            <td>' . $item->location_name . '</td>
            <td>' . $item->address1 . ' ' . $item->address2 . ' ' . $item->address3 . ' ' . $item->address4 . '</td>
            <td>' . $item->postcode . '</td>
            <td>' . $item->telephone . '</td>
            <td>' . $item->collections . '</td>
        </tr>';
    } ?>
    </tbody>
</table>