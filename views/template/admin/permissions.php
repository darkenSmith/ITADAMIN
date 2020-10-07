<div>
    This is not yet finished, need to pull through a role and associate with the permissions below.
</div>

<form class="form" method="post" action="/admin/managePermissions">
    <?php foreach ($sections as $key => $section) { ?>
        <div class="panel panel-primary">
            <div class="panel-heading" role="tab" id="heading<?php echo $key; ?>">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                       href="#collapse<?php echo $key; ?>" aria-expanded="false" aria-controls="collapse<?php echo $key; ?>">
                        <?php echo ucwords($key); ?></a>
                </h4>
            </div>
            <div id="collapse<?php echo $key; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $key; ?>">
                <div class="panel-body">

                    <div class="form-group">
                        <ul class="list-inline">
                            <?php foreach ( $section as $item ) {
                                if(in_array($item->id,$allowed)) {
                                    $checked = 'checked';
                                }else{
                                    $checked = '';
                                }
                                ?>
                                <li class="col-xs-12 col-md-4 col-lg-3">
                                    <div class="checkbox">
                                        <label>
                                            <input name="allowed[]" value="<?php echo $item->id; ?>"
                                                   type="checkbox" <?php echo $checked; ?>><?php echo $item->friendly; ?>
                                        </label>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <button type="submit" class="btn btn-success">Submit</button>
</form>
