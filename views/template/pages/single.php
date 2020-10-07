<?php
$data = $info->company->companies[0]->data;
$summary = $info->company->companies[0]->summary;
$collections = $info->company->companies[0]->collections;
?>
<div class="row">
    <h2><?php echo $data->company_name; ?></h2>
</div>

<div class="row">
    <h3 class="text-left">Recycling Collection Details</h3>
    <p>Please note a Red Cross indicates that the file still needs to be uploaded for the relevant order.</p>
    <?php include TEMPLATE_DIR . 'pages/collections.php'; ?>
</div>

<hr>
<div class="row">
    <h3>Number of collections for each location</h3>
    <?php include TEMPLATE_DIR . 'pages/summary.php'; ?>

</div>
