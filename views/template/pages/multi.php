<?php
$companies = $info->company->companies;
?>
<div class="row">
    <h2>Select Account</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-10">Company Name</th>
            <th class="col-2"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($companies as $company) {
            echo '<tr>
                <td>' . $company->data->company_name . '</td>
                <td><a href="company/view/' . $company->data->id . '" class="btn btn-success">Go</a></td> 
            </tr>';

        } ?>
        </tbody>
    </table>
</div>
