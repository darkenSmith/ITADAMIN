<?php
/**
 * Created by PhpStorm.
 * User: andy.simpson
 * Date: 11/06/2017
 * Time: 11:52
 */

?>
<div class="row">
    <h1><?php echo $data->heading;?>
        <a href="/admin/add/" class="btn btn-success pull-right">Add User</a></h1>
</div>
<?php if($_SESSION['user']['role_id'] == 1){ ?>
<div class="row">

	<h2>Stone Staff</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="col-sm-3">First Name </th>
                <th class="col-sm-3">Last Name</th>
                <th class="col-sm-4">Email</th>
                <th class="col-sm-2"></th>
            </tr>
        </thead>
        <?php foreach ($data->stoneUsers as $collection){
            echo '<tr>
                <td>'.$collection->firstname.'</td>
                <td>'.$collection->lastname.'</td>
                <td>'.$collection->email.'</td>
                <td><a href="/admin/edit/'.$collection->id.'" class="btn btn-default btn-block">Edit</a></td>
            </tr>';
        } ?>
    </table>
</div>
<?php } ?>

<?php //if($_SESSION['user']['role_id'] == 1 || $_SESSION['user']['role_id'] == 2){ ?>
<!--<div class="row">-->
<!--	<h2>Registered Customers</h2>-->
<!--	<table class="table table-striped">-->
<!--        <thead>-->
<!--            <tr>-->
<!--                <th class="col-sm-3">First Name </th>-->
<!--                <th class="col-sm-3">Last Name</th>-->
<!--                <th class="col-sm-4">Email</th>-->
<!--                <th class="col-sm-2"></th>-->
<!--            </tr>-->
<!--        </thead>-->
<!--        --><?php //foreach ($data->users as $collection){
//            echo '<tr>
//                <td>'.$collection->firstname.'</td>
//                <td>'.$collection->lastname.'</td>
//                <td>'.$collection->email.'</td>
//                <td><a href="/admin/edit/'.$collection->id.'" class="btn btn-default btn-block">Edit</a></td>
//            </tr>';
//        } ?>
<!--    </table>-->
<!--</div>-->
<?//  } ?>
<!---->
<?php if($_SESSION['user']['role_id'] == 3){ ?>
<!--list of company employees-->
    <p>this is a statement </p>
<!---->
<?php } ?>

