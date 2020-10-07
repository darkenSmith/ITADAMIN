<?php
//@todo also need to handle failures.
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#login').on('click', function () {
            password = jQuery('#password').val();
            username = jQuery('#username').val();
            redirect = "<?php echo $redirect; ?>";

            if (username.length < 3) {
                alert('Please enter your username');
                jQuery('#username').focus();
            } else if (password.length < 3) {
                alert('Please enter your password');
                jQuery('#password').focus();
            } else {
                jQuery.ajax({
                    url: "/login/ajax",
                    type: "POST",
                    data: "username=" + username + "&password=" + password + "&redirect=" + redirect,
                    success: function (data) {
                        if (data == 1) {
                            document.location = '<?php echo $redirect; ?>'
                        } else {
                            alert(data)
                        }
                    }
                });
            }
        });

        jQuery('#sendReminder').on('click', function () {
            var email = jQuery('#email').val();
            if (email) {
                jQuery.ajax({
                    type: 'POST',
                    url: '/login/forgot',
                    data: {
                        email: email
                    },
                    success: function (data) {
                        jQuery('#result').show();
                        jQuery('#forgotModal').modal('hide');
                        jQuery('#result').html(data);

                    }
                });
            }
        });
    });

</script>
<!-- Modal -->
<div class="modal fade" id="forgotModal" tabindex="-1" role="dialog" aria-labelledby="forgotOverlay">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Reset Password</h4>
            </div>
            <div class="modal-body">
                <form class="form" id="addressForm">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value=""/>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="sendReminder" class="btn btn-success">Reset Password</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <form class="form-signin">
        <h2 class="form-signin-heading">Please Sign In</h2>
        <div id="result" style="display: none"></div>
        <label for="username" class="sr-only">Email</label>
        <input type="email" id="username" class="form-control" placeholder="Enter your Email address" required
               autofocus>
        <label for="password" class="sr-only">Password</label>
        <input type="password" id="password" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" id="login">Sign in</button>
        <a class="btn btn-sm btn-link" id="forgot" data-toggle="modal" data-target="#forgotModal">Forgotten / Change
            Password</a>
    </form>
</div>
