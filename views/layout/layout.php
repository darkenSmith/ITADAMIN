<?php
if(!isset($_SESSION)){
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
    <?php require_once LAYOUT_DIR . 'head.php'; ?>
    <body>
    <?php require_once( LAYOUT_DIR . 'header.php'); ?>
    <div class="<?php echo $app->container; ?>">
        <?php
        if($layout == 1){ // two column layout
            require_once(VIEW_DIR . 'routes.php');
        }elseif($layout == 2){ // single column layout
            require_once(VIEW_DIR . 'routes.php');
        }else{ ?>
            <h3>Permission Denied</h3>
            <p>Please contact support and quote error number:</p>
            <p>U<?php echo $_SESSION['user']['id'].'-R'.$_SESSION['user']['role_id'].'-S'.$app->structureId; ?></p>
        <?php } ?>
        </div>
    <?php require_once( LAYOUT_DIR . 'footer.php' ); ?>
    </body>
</html>
