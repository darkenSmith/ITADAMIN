<?php
/*----------------------------------------------------------------------------------------------------
Change Log
Date			tag						  Ticket			 By              Description
------------------------------------------------------------------------------------------------------
11/06/17	Created											 andy.Simpson    Created by PhpStorm
04/10/17	last_edited									 andy.simpson	   Last Save date on server
06/04/18	tidy												 neil.Baker      General formatting and added a header
06/04/18  customerSelect               neil.baker      Changes to jQuery to manage display of the customer selection options
------------------------------------------------------------------------------------------------------*/
?>
<script type="text/javascript">
  jQuery(document).ready(function() {

    // Change tag: customerSelect
    // Show or hide customer selection on page load based on role_id
    <?php
    if ($user->role_id == 3 || $user->role_id == 4) {
      ?>
      jQuery('#customer_selection').show();
      <?php
    } else {
      ?>
      jQuery('#customer_selection').hide();
      <?php
    }
    ?>

    jQuery('#role_id').on('change',function(){
      var role_id = jQuery('#role_id').val();

      if (role_id === "3" || role_id === "4") {
        jQuery('#customer_selection').show();
      } else {
        jQuery('#customer_selection').hide();
      }
    });
    // End change tag: customerSelect

    jQuery('#edit').on('click',function(){
      var firstname   = jQuery('#firstname').val();
      var lastname    = jQuery('#lastname').val();
      var role_id     = jQuery('#role_id').val();
      var email       = jQuery('#email').val();
      var id          = "<?php echo $user->id; ?>";

      <?php if($user->page == 'profile'){ ?>
          var password      = jQuery('#password').val();
          var confirmation  = jQuery('#confirmation').val();
      <?php }else{ ?>
          var password      = '';
          var confirmation  = '';
      <?php }?>

      // Change tag: customerSelect
      var customer_id = jQuery('#customer_id').val();
      if (customer_id == "" && (role_id === "3" || role_id === "4")) {
        alert('You need to select a Customer for the user account');
        return;
      }
      // End change tag

      if(password == confirmation) {
        if (id) {
          jQuery.ajax({
            url: "/admin/editUserPost",
            type: "POST",
            data: {
                role_id: role_id,
                firstname: firstname,
                lastname: lastname,
                password: password,
                confirmation: confirmation,
                email: email,
                id: id,
                customer_id: customer_id
            },
            success: function (data) {
                jQuery('#result').show();
                jQuery('#result').html(data);
            }
          });
        }
      }else{
          alert('Password Confirmation does not match your new password.');
      }
    });
  });
</script>

<div id="result" style="display: none"></div>

<H1>Editing a user</h1>

<div class="row form-horizontal">
        <div class="form-group">
            <label for="firstname" class="col-sm-2 col-sm-offset-2 control-label">First Name:</label>
            <div class="col-sm-4">
                <input type="text" name="firstname" id="firstname" value="<?php echo $user->firstname; ?>" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label for="lastname" class="col-sm-2 col-sm-offset-2 control-label">Last Name:</label>
            <div class="col-sm-4">
                <input type="text" name="lastname" id="lastname" value="<?php echo $user->lastname; ?>" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-sm-2 col-sm-offset-2 control-label">Email:</label>
            <div class="col-sm-4">
                <input type="text" name="email" id="email" value="<?php echo $user->email; ?>" class="form-control" required>
            </div>
        </div>

    <?php
    // Access level onmly editable by StoneAdmin on the user edit page
    if($_SESSION['user']['role_id'] == 1 && $user->page != 'profile'){
      echo '
      <div class="form-group">
        <label for="role" class="col-sm-2 col-sm-offset-2 control-label">Access Level:</label>
        <div class="col-sm-4">
          <select name="role_id" id="role_id" class="form-control">
          ';
          foreach($roles  as $role ) {
            echo '<option value="'.$role->id.'" '.($role->id == $user->role_id ? "selected" : "").'>'.$role->name,'</option>';
          }
          echo '
          </select>
        </div>
      </div>
      ';
    }

    // Change tag: customerSelect
    // Check if user is CustomerAdmin or CustomerStaff
    $customer_array = array();

    if ($user->role_id == 3 || $user->role_id == 4) {
      // Get customer list for user
      if (isset($user->customers)) {
        foreach ($user->customers as $customer) {
          $customer_array[] = $customer->id;
        }
      }
    }

    if ($_SESSION["user"]["role_id"] == 1 || $_SESSION["user"]["role_id"] == 2) {
      // Only allow StoneAdmin or StoneStaff to change customer assignment for user
      echo "
      <div class='form-group' id='customer_selection'>
        <label for='customer_id' class='col-sm-2 col-sm-offset-2 control-label'>Customer Name:</label>
        <div class='col-sm-4'>
          <select name='customer_id' id='customer_id' class='form-control' multiple>
          <option value=''>Choose Customer</option>
          ";
          foreach($customers  as $customer ) {
              echo '<option value="'.$customer->id.'" '.(in_array($customer->id,$customer_array) ? "selected" : "").'>'.$customer->company_name,'</option>';
          }
          echo "
          </select>
        </div>
      </div>
      ";
    } else {
      echo "<input type='hidden' name='customer_id' value='".implode(",",$customer_array)."'>";
    }
    // End change tag

    // Allow password change only on the profile page and not editing users
    if($user->page == 'profile') {
      echo '
      <div class="form-group">
        <label for="password" class="col-sm-2 col-sm-offset-2 control-label">New Password:</label>
        <div class="col-sm-4">
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
      </div>
      <div class="form-group">
        <label for="confirmation" class="col-sm-2 col-sm-offset-2 control-label">Confirm Password:</label>
        <div class="col-sm-4">
          <input type="password" name="confirmation" id="confirmation" class="form-control" required>
        </div>
      </div>
      ';
    }
    ?>
        <div class="row">
        <hr/>
        <button type="submit" id="edit" class="btn btn-success col-sm-12 col-md-offset-4 col-md-4">Update</button>
    </div>
</div>
