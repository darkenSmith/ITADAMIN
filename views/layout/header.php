<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
                <span class="icon-bar"></span> <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img src="/assets/media/logo.png" alt="" style="max-height:100px;"/></a>
        </div>

<?php
if ($app->isLoggedIn()) {
    echo "
      <div id='navbar' class='collapse navbar-collapse'>
        <ul class='nav navbar-nav'>
          <li><a href='/'>Home</a></li>
         ";

    if (isset($_SESSION)) {
        // Access for CustomerAdmin and CustomerStaff roles
        if (($_SESSION['user']['role_id'] == 3 || $_SESSION['user']['role_id'] == 4) && count($app->customerCo) > 1) {
            echo "
              <li class='dropdown'>
                <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>Switch Account <span class='caret'></span></a>
                <ul class='dropdown-menu'>
                  ";
            foreach ($app->customerCo as $customer) {
                echo '<li><a href="/company/view/' . $customer->id . '">' . $customer->company_name . '</a></li>';
            }
            echo "
                </ul>
              </li>
	            ";
        }
    }


    // Access for StoneAdmin and StoneStaff roles
    if ($_SESSION['user']['role_id'] == 1 || $_SESSION['user']['role_id'] == 2) {
        echo "
            <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>Admin <span class='caret'></span></a>
              <ul class='dropdown-menu'>
                <li><a href='/admin/profile/'>Update Profile</a></li>
                ";
        // Change tag: restrictUser
        // Restrict account creation and management to admin
        if ($_SESSION['user']['role_id'] == 1) {
            echo "
                  <li><a href='/admin/add/'>Add User</a></li>
                  <li><a href='/admin/users/'>Manage Users</a></li>
                  <li><a href='/admin/permissions/'>Manage Permissions</a></li>
                  <li><a href='/RS/booking/'>RSASv1</a></li>
                  <li><a href='/RS/approvedlist/'>Approve List</a></li>
                  
                  <li><a href='/RS/Goodsin/'>Goods in</a></li>
                  ";
        }

        echo "
              </ul>
            </li>
            ";
        // Access for StoneAdmin and StoneStaff roles
    }

    if ($_SESSION['user']['role_id'] == 7) {
        echo "
            <li class='dropdown'>
              <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>Admin <span class='caret'></span></a>
              <ul class='dropdown-menu'>
                <li><a href='/admin/profile/'>Update Profile</a></li>
                ";
        // Change tag: restrictUser
        // Restrict account creation and management to admin
        if ($_SESSION['user']['role_id'] == 7) {
            echo "
                          <li><a href='/RS/Goodsin/'>Goods in</a></li>
                          ";
        }
        echo "
              </ul>
            </li>
            ";
    } else {
        // CustomerAdmin and CustomerStaff roles
        echo "
            <li><a href='/admin/profile/'>Update Profile</a></li>
            ";
    }


    echo "
          <li><a href='/login/logout'>Log Out</a></li>
        </ul>
      </div>
      <!--/.nav-collapse -->
      ";
}
echo "
  </div>
</nav>
";
