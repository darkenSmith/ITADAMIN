<?php
if ($user) {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#reset').on('click', function () {
                var password = jQuery('#password').val();
                var conf = jQuery('#conf').val();
                var user = jQuery('#user').val();
                if (password == conf) {
                    jQuery.ajax({
                        url: "/login/change",
                        type: "POST",
                        data: {
                            password: password,
                            user: user
                        },
                        success: function (data) {
                            jQuery('#result').html(data);
                            jQuery('#result').show();
                            jQuery('#signinheader').show();
                            jQuery('#signin').show();
                            jQuery('#resetheader').hide();
                            jQuery('#passwordentry').hide();
                        }
                    });
                } else {
                    alert('Passwords do not match');
                }
            });
            jQuery('#login').on('click', function () {
                password = jQuery('#password').val();
                username = jQuery('#username').val();

                if (username.length < 3) {
                    alert('Please enter your username');
                    jQuery('#username').focus();
                } else if (password.length < 3) {
                    alert('Please enter your password');
                    jQuery('#password').focus();
                } else {
                    jQuery.ajax({
                        url: "/login/ajax?isAjax=1",
                        type: "POST",
                        data: "username=" + username + "&password=" + password,
                        success: function (data) {
                            if (data == 1) {
                                document.location = '/'
                            } else {
                                eval(data);
                                console.log(data);
                            }
                        }
                    });
                }
            });
        });

    </script>
    <div class="row">
        <div class="col-xs-12 col-md-offset-4 col-md-4" id="resetheader">
            <h2>Reset Password</h2>
        </div>
        <div id="result" class="col-xs-12 col-md-offset-4 col-md-4" style="display: none"></div>
        <div class="col-xs-12 col-md-offset-4 col-md-4" id="signinheader" style="display: none;">
            <h2>Sign In</h2>
        </div>
    </div>
    <div class="row" id="passwordentry">
        <div class="col-xs-12 col-md-offset-4 col-md-4">
            <input type="hidden" id="user" class="form-control" value="<?php echo $user->id; ?>">
            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Password" required autofocus>
            </div>
            <div class="form-group">
                <label for="conf" class="sr-only">Password Confirmation</label>
                <input type="password" id="conf" class="form-control" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block" id="reset">Update Password</button>
            </div>
        </div>
    </div>
    <div class="row" id="signin" style="display: none">
        <div class="col-xs-12 col-md-offset-4 col-md-4">
            <div class="form-group">
                <label for="username" class="sr-only">Username</label>
                <input type="text" id="username" class="form-control" placeholder="Username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block" id="login">Sign in</button>
            </div>
        </div>
    </div>

    <?php
} else {
    echo 'Your password link has expired, please click here to request a new one.' . $user;
}
?>
