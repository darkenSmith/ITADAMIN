<script>
    jQuery(document).ready(function () {
        jQuery('#refresh').on('click', function () {
            jQuery.ajax({
                type: 'POST',
                url: '/company/refresh',
                success: function () {
                    location.reload();
                }
            });
        });
    });
</script>

<div class="row">
    <h2>Recycling Customers</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-sm-8">Location Name</th>
            <th class="col-sm-2">View</th>
            <th class="col-sm-2">Remove</th>
        </tr>
        </thead>
        <?php foreach ($info->company as $collection) {
            echo '<tr>
                <td>' . $collection->company_name . '</td>
                <td><a href="company/view/' . $collection->id . '" class="btn btn-success">View Customer</a></td>
                <td><a href="company/view/' . $collection->id . '" class="btn btn-danger">Unassign</a></td>
            </tr>';
        } ?>
    </table>
</div>

<div class="row">
    <h2>Unallocated Customers</h2>
    <form role="form" class="form-inline" method="post" enctype="multipart/form-data" name="form1" id="form1"
          action="/company/claim">
        <div class="form-group">
            <label for="company">Company Name</label>
            <select class="form-control" name="company">
                <?php foreach ($info->unallocated as $unallocated) {
                    echo '<option value="' . $unallocated->id . '">' . $unallocated->company_name . '</option>';
                } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Claim Customer</button>
    </form>
    <button id="refresh" class="btn btn-sml btn-info">Refresh List</button>
</div>