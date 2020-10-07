<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Upload New File</h3>
    </div>
    <div class="panel-body">
        <form role="form" class="form-inline" method="post" enctype="multipart/form-data" name="form1" id="form1"
              action="/order/upload">
            <div class="form-group">
                <label for="document">File Upload</label>
                <select class="form-control" name="document">
                    <option value="Asset Management Report">Asset Management</option>
                    <option value="Blancco Report">Blancco Report</option>
                    <option value="Certificate of Disposal">Certificate of Disposal</option>
                    <option value="Rebate Report">Rebate Report</option>
                    <option value="Remarketing Report">Remarketing Report</option>
                </select>
            </div>
            <div class="form-group">
                <input name="file" type="file" id="file"/>
                <input name="order" type="hidden" id="order" value="<?php echo $order->detail->sales_order_number; ?>"/>
                <input name="id" type="hidden" id="id" value="<?php echo $id; ?>"/>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>
