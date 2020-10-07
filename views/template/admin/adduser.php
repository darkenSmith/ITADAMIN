<?php
/*----------------------------------------------------------------------------------------------------
Change Log
Date			tag						Ticket				By						Description
------------------------------------------------------------------------------------------------------
11/06/17	Created											andy.Simpson	Created by PhpStorm
04/10/17	last_edited									andy.simpson	Last Save date on server
06/04/18	tidy												neil.Baker		General formatting and added a header
09/04/18  customerSelect              neil.Baker    Added new option to assign a customer to a user based on their role assignment
------------------------------------------------------------------------------------------------------*/
?>
<script type="text/javascript">
  jQuery(document).ready(function() {

    // Change tag: customerSelect
    // Show or hide customer selection on page load based on role_id
    jQuery('#role_id').on('change',function(){
      var role_id = jQuery('#role_id').val();

      if (role_id === "3" || role_id === "4") {
        jQuery('#customer_selection').show();
      } else {
        jQuery('#customer_selection').hide();
      }
    });
    // End change tag: customerSelect

    jQuery('#add').on('click',function(){
      var firstname = jQuery('#firstname').val();
      var lastname  = jQuery('#lastname').val();
      var role_id   = jQuery('#role_id').val();
      var email     = jQuery('#email').val();
      var username  = jQuery('#email').val();

      // Change tag: customerSelect
      var customer_id = jQuery('#customer_id').val();
      if (customer_id == "" && (role_id === "3" || role_id === "4")) {
        alert('You need to select a Customer for the user account');
        return;
      }
      // End change tag

      jQuery.ajax({
        url: "/admin/addUserPost",
        type: "POST",
        data: {
            role_id: role_id,
            firstname: firstname,
            username: username,
            lastname: lastname,
            email: email,
            customer_id: customer_id
        },
        success: function (data) {
            jQuery('#result').show();
            jQuery('#result').html(data);
        }
      });
    });
  });
</script>

<div id="result" style="display: none"></div>

<H1>Add a new user</h1>

<div class="row form-horizontal">
  <div class="form-group">
    <label for="firstname" class="col-sm-2 col-sm-offset-2 control-label">First Name:</label>
    <div class="col-sm-4">
        <input type="text" name="firstname" id="firstname" class="form-control" required>
    </div>
  </div>
  <div class="form-group">
    <label for="lastname" class="col-sm-2 col-sm-offset-2 control-label">Last Name:</label>
    <div class="col-sm-4">
      <input type="text" name="lastname" id="lastname" class="form-control" required>
    </div>
  </div>
  <div class="form-group">
    <label for="email" class="col-sm-2 col-sm-offset-2 control-label">Email:</label>
    <div class="col-sm-4">
      <input type="text" name="email" id="email" class="form-control" required>
    </div>
  </div>

<?php
if($_SESSION['user']['role_id'] == 1){
  // Allow admin users only to grant access levels
  echo '
  <div class="form-group">
    <label for="role" class="col-sm-2 col-sm-offset-2 control-label">Access Level:</label>
    <div class="col-sm-4">
      <select name="role_id" id="role_id" class="form-control">
      ';
      foreach($roles  as $role ) {
        echo '<option value="'.$role->id.'">'.$role->name,'</option>';
      }
      echo '
      </select>
    </div>
  </div>
  ';
}

// Change tag: customerSelect
if ($_SESSION["user"]["role_id"] == 1 || $_SESSION["user"]["role_id"] == 2) {
  // Only allow StoneAdmin or StoneStaff to change customer assignment for user
  echo "
  <div class='form-group' id='customer_selection' style='display:none;'>
    <label for='customer_id' class='col-sm-2 col-sm-offset-2 control-label'>Customer Name:</label>
    <div class='col-sm-4'>
      <select name='customer_id' id='customer_id' class='form-control' multiple>
      <option value=''>Choose Customer</option>
      ";
      foreach($customers  as $customer ) {
          echo '<option value="'.$customer->id.'">'.$customer->company_name,'</option>';
      }
      echo "
      </select>
    </div>
  </div>
  ";
} else {
  echo "<input type='hidden' name='customer_id' value=''>";
}

// End change tag
?>

  <div class="row">
    <hr/>
    <button type="submit" id="add" class="btn btn-success col-sm-12 col-md-offset-4 col-md-4">Add</button>
  </div>
</div>
