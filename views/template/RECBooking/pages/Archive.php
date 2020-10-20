<br> <br><br>
<br>

<style>
    #opt {
        padding: 20px;
        border-style: solid;
        border-width: 2px;
        border-color: #d8d8d8;
    }

    #tbldata th {
        background-color: #eee;
        border-top: 1px solid #fff;
    }

    #tbldata th:hover {
        background-color: #ccc;
    }

    #tbldata th {
        background-color: #fff;
    }

    #tbldata th:hover {
        cursor: pointer;
    }

    /* Sortable tables */
    table.sortable thead {
        background-color: #eee;
        color: #666666;
        font-weight: bold;
        cursor: default;
    }
</style>


<script type="text/javascript">
    $(document).ready(function () {


        var click = 0;
        var click2 = 0;
        var click3 = 0;

        $('.amrsubc').hide();
        $('#rebarea').hide();
        $('#invarea').hide();


        var message = '';
        var arr = [];
        var arr2 = [];


        function getcheckedreq() {


        }

        $('#tbldata tr:has(td)').find('.checkboxes').change(function () {


            if ($(this).prop('checked') == true) {

                arr.push($(this).val());
                arr2.push($(this).closest('tr').find('.ord').text());

                console.log(arr);
                console.log(arr2);

            }
            if ($(this).prop('checked') == false) {

                arr.pop($(this).val());
                arr2.pop($(this).closest('tr').find('.ord').text());

                console.log(arr);
                console.log(arr2);

            }


        });


        $('#delDone').on('click', function () {
            var typecol = 'collection';
            $.ajax({
                type: "POST",
                url: "/RS/delrequestmulti/",
                data: {
                    typecol: typecol,
                    arr2: arr2
                },
                success: function (data) {
                    // alert(data);
                    alert("The collection have been Reverted back to booking");

                }
            });
            location.reload(true);


        });


        $('#undone').on('click', function () {

            $.ajax({
                type: "POST",
                url: "/RS/Undonebtn/",
                data: {arr: arr},
                success: function (data) {
                    // alert(data);
                    alert("The collection have been Reverted back to booking");
                }
            });

            location.reload(true);

        });


        $("a.tooltipLink").tooltip();

        $('#search').keyup(function () {
            search_table($(this).val());
        });

        function search_table(value) {
            $('#tbldata tbody tr').each(function () {
                var found = 'false';
                $(this).each(function () {
                    if ($(this).text().toLowerCase().indexOf(value.toLowerCase()) >= 0) {
                        found = 'true';
                    }
                });
                if (found == 'true') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }


        $("select.filter").change(function () {
            var selectedCountry = $(".filter option:selected").val();
        });


        $('#rebtn').on('click', function () {
            click2++;
            $('#rebarea').show();
            if (click2 > 1) {
                $('#rebarea').hide();
                click2 = 0;

            }
        });

        $('#invbtn').on('click', function () {
            click3++;
            $('#invarea').show();

            if (click3 > 1) {

                $('#invarea').hide();
                click3 = 0;
            }
        });


        $('#amrnew').on('click', function () {
            click++;
            $('.amrsubc').show();
            if (click > 1) {
                $('.amrsubc').hide();
                click = 0;

            }
        });


        $('#amrsubmit').on('click', function () {


            if (arr.length === 0) {

                alert('nothing checked');
            } else {

                message = $('#amrnote').val();
                var type = 'amr';
                //alert(arr);


                $.ajax({
                    type: "POST",
                    url: "/RS/AMRs/",
                    cache: true,
                    data: {
                        type: type,
                        arr: arr,
                        message: message
                    },

                    success: function (data) {
                        // alert(data);
                        alert("Updated AMR");
                    }
                });

                location.reload(true);

            }

        });


        $('#rebsub').on('click', function () {


            if (arr.length === 0) {

                alert('nothing checked');
            } else {

                message = $('#rebmess').val();
                var type = 'rebate';
                alert(arr);
                $.ajax({
                    type: "POST",
                    url: "/RS/addtoRebate/",
                    cache: true,
                    data: {
                        arr2: arr2,
                        message: message
                    },

                    success: function (data) {
                        alert(data);
                        alert("Updated");
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "/RS/AMRs/",
                    cache: true,
                    data: {
                        type: type,
                        arr: arr,
                        message: message
                    },

                    success: function (data) {
                        // alert(data);
                        alert("Updated AMR");
                    }
                });

                location.reload(true);

            }

        });


        $('#invsub').on('click', function () {

            if (arr.length === 0) {

                alert('nothing checked');

            } else {

                message = $('#invmess').val();

                // alert(arr);


                $.ajax({
                    type: "POST",
                    url: "/RS/invRebate/",
                    cache: true,
                    data: {
                        arr2: arr2,
                        message: message
                    },

                    success: function (data) {
                        // alert(data);
                        alert("Updated invoice");
                    }
                });


                location.reload(true);

            }

        });


        $('#Cod').on('click', function () {


            if (arr.length === 0) {

                alert('nothing checked');
            } else {

                // alert('hit');
                // alert(arr);
                var type = 'cod';
                message = 'YES';

                $.ajax({
                    type: "POST",
                    url: "/RS/AMRs/",
                    cache: true,
                    data: {
                        type: type,
                        arr: arr,
                        message: message
                    },

                    success: function (data) {
                        // alert(data);
                        alert("Updated COD");
                    }
                });


                location.reload(true);
            }


        });


    });


</script>


<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="/RS/booking/">Booked Collection</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="/RS/arc/">Archive</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/rebatepage/">Rebates</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/RGR/">Recycling Goods Receipting </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/companynote/">Company Notes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href=".\admin\home\">Back</a>
    </li>
</ul>

<h1 class="page-header"> Stone Computers: Recycling System <small>test</small></h1>


<div id="opt">
    <br>
    <hr>
    <div align="center"><label> Search <a data-toggle="tooltip" class="tooltipLink"
                                          data-original-title="filter's data in visble table.">
                <span class="glyphicon glyphicon-info-sign"></span></a></label>
        <input type="text" name="search" id="search" class="form-control"/>
    </div>
    <br/>

    <form method='post' action="/RS/arc/">
        <div class="form-row" align="left">


            <br>

            <div class="col">
                <label>order num:</label>
                <input type="text" name="ent"
                       value="<?php echo(isset($_POST["ent"]) && $_POST["ent"] != "" ? $_POST["ent"] : ""); ?>">
            </div>


            <div class="col">
                <label>postcode:</label>
                <input type="text" name="postcode"
                       value="<?php echo(isset($_POST["postcode"]) && $_POST["postcode"] != "" ? $_POST["postcode"] : ""); ?>">
            </div>

            <div class="col">
                <label>RequestID:</label>
                <input type="text" name="id"
                       value="<?php echo(isset($_POST["id"]) && $_POST["id"] != "" ? $_POST["id"] : ""); ?>">
            </div>

            <div class="col">
                <label>Select filter:</label>
                <select name="filter">
                    <option value="request_ID">RequestID</option>
                    <option value="ORD">SalesOrderNumber</option>
                    <option value="Request_date_added">Date Added</option>
                    <option value="collection_date">Collection Date</option>
                    <option value="Customer_email">Customer eMail</option>
                </select>
                <select name="filter2">

                    <option value="ASC">ASC</option>
                    <option value="DESC">DESC</option>
                    <option selected value="DESC">Order type</option>


                </select>
            </div>

            <br>
            <div class="col">
                <label>Collection Date:</label>
                <input type="date" name="collectdate"
                       value="<?php echo(isset($_POST["collectdate"]) && $_POST["collectdate"] != "" ? $_POST["collectdate"] : ""); ?>">
            </div>
            <Br>
            <label>Area:</label>
            <select name="areafilter">
                <option selected value="%"> Default</option>

                <?php

                foreach ($areas as $row) {
                    echo "<option value='" . $row['name'] . "'>" . $row['val'] . "</option>";
                }


                ?>
            </select>
            <br>
            <br>
    </form>

    <div id='subsearch'>
        <input type="submit" value="Submit" class="btn btn-success">

        <a data-toggle="tooltip" class="tooltipLink" data-original-title="Submit your search.">
            <span class="glyphicon glyphicon-info-sign"></span></a>
    </div>


    <br>

    <div class="btn-group" role="group" aria-label="buttons">
        <div id='delsec'>
            <button id='delDone' class='btn btn-warning'>Delete</button>
            <a data-toggle="tooltip" class="tooltipLink"
               data-original-title="select checkbox of line you want to delete.">
                <span class="glyphicon glyphicon-info-sign"></span></a>
        </div>
        <br>
        <div id='undonesec'>
            <button id='undone' class='btn btn-info'>Un-Collected</button>
            <a data-toggle="tooltip" class="tooltipLink" data-original-title="Revert back to booking stage.">
                <span class="glyphicon glyphicon-info-sign"></span></a>
        </div>
        <br>
        <table class="table" cellspacing='2' width='100%'>
            <tr>
                <td>
                    <button id='Cod' class='btn btn-info'>COD - Sent</button>
                    <button id='amrnew' class='btn btn-primary'>AMR</button>
                    <br><br>
                    <input type="text" id="amrnote" class='amrsubc' placeholder="AMR info here"/> <br>
                    <input id="amrsubmit" type="submit" value="Submit" class="btn btn-success amrsubc"/>
                </td>

                <td>
                    <button id='rebtn' class='btn btn-warning'>Rebate</button>
                    <br><br>
                    <div id='rebarea'>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-gbp"></span></span>
                            <input type="number" id="rebmess" class='rebatemes' placeholder="Rebate Ammount"/><br>
                        </div>
                        <input id="rebsub" type="submit" value="Submit" class="btn btn-success rebsub"/>
                    </div>
                </td>


                <td>
                    <button id='invbtn' class='btn btn-warning'>Invoice(DONT USE)</button>
                    <br><br>
                    <div id='invarea'>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></span>
                            <input type="text" id="invmess" class='invmes' placeholder="Invoice Number"/><br>
                        </div>
                        <input id="invsub" type="submit" value="Submit" class="btn btn-success invsub"/>
                    </div>


                </td>

            </tr>

        </table>
    </div>
</div>


<hr>


<br>

<div align='center'>
    <table id='tbldata' class='sortable table  table-striped'>
        <thead>
        <tr>
            <TH></TH>
            <th>OrderNum</th>
            <th>RequestID</th>
            <th>CRM Number</th>
            <th hidden>Requestdate</th>
            <th>Collection Date</th>
            <th> Name</th>
            <th hidden> Tel</th>
            <th hidden> Town</th>
            <th hidden> Stat notes</th>
            <th hidden> IS REBATE</th>
            <th> Rebate</th>
            <th hidden> County</th>
            <th hidden> Postcode</th>
            <th> TFT</th>
            <th> TFT TV</th>
            <th> PC</th>
            <th> PC APPLE</th>
            <th> AIO</th>
            <th> AIO APPLE</th>
            <th> LAPTOP</th>
            <th> LAPTOP APPLE</th>
            <th> SERVER</th>
            <th> TABLET</th>
            <th> TABLET APPLE</th>
            <th> SMART PHONE</th>
            <th> PHONE APPLE</th>
            <th> Non Smart</th>
            <th> PRINTERS</th>
            <th> MFD PRINTERS</th>
            <th hidden> CRT TFT</th>
            <th>CRT</th>
            <th> Scanners</th>
            <th> Batt - Haz</th>
            <th> Batt - Non Haz</th>
            <th> PROJECTOR</th>
            <th> Thin Client</th>
            <th> Switch</th>
            <th> Smartboard</th>
            <th> HDD</th>
            <th hidden> Status</th>
            <th> Totalunits</th>
            <th> Weight</th>
            <th> Qualifying</th>
            <th> Non-Qualifying</th>
            <th> COD</th>
            <th> AMR</th>
            <th> invoice Number</th>
            <th> invoice Date</th>
            <th>RGR Date</th>
            <th> Owner</th>
            <th> RPT</th>
            <th> Email</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($arctable as $row) {
            $modtime = date("d/m/Y", strtotime($row['modifed_date']));

            echo "
  <tr>
  <td><input type='checkbox' class='checkboxes' value='" . $row["id"] . "'></td>
    <td class='ord'>" . $row["ordern"] . "</td>
    <td class='req'><a target='_blank' href='/RS/detialdoc?rowid=" . $row["id"] . " '>" . $row["id"] . "</a></td>
    <td>" . $row["crm"] . "</td>
    <td hidden>" . date("d/m/Y", strtotime($row['requestdate'])) . "</td>
    <td>" . date("d/m/Y", strtotime($row['coldate'])) . "</td>
    <td>" . $row["name"] . "</td>
    <td hidden>" . $row["tel"] . "</td>
    <td hidden>" . $row["town"] . "</td>
    <td hidden>" . $row["upnotes"] . "</td>
    <td hidden>" . $row["is_Rebate"] . "</td>
    <td>" . $row["Rebate"] . "</td>
    <td hidden>" . $row["county"] . "</td>
    <td hidden>" . $row["postcod"] . "</td>
    <td>" . $row["tft"] . "</td>
    <td>" . $row["tfttv"] . "</td>
    <td>" . $row["pc"] . "</td>
    <td>" . $row["pcapp"] . "</td>
    <td>" . $row["aio"] . "</td>
    <td>" . $row["aioapp"] . "</td>
    <td>" . $row["lap"] . "</td>
    <td>" . $row["lapapp"] . "</td>
    <td>" . $row["serv"] . "</td>
    <td>" . $row["tab"] . "</td>
    <td>" . $row["apptab"] . "</td>
    <td>" . $row["sphone"] . "</td>
    <td>" . $row["appphone"] . "</td>
    <td>" . $row["nonsmart"] . "</td>
    <td>" . $row["Printers"] . "</td>
    <td>" . $row["mfdp"] . "</td>
    <td hidden>" . $row["crtmon"] . "</td>
    <td>" . $row["crttv"] . "</td>
    <td>" . $row["scanner"] . "</td>
    <td>" . $row["bathaz"] . "</td>
    <td>" . $row["nonbathaz"] . "</td>
    <td>" . $row["proj"] . "</td>
    <td>" . $row["thin"] . "</td>
    <td>" . $row["switch"] . "</td>
    <td>" . $row["Smartboard"] . "</td>
    <td>" . $row["HDD"] . "</td>
    <td hidden>" . $row["Status"] . "</td>
    <td>" . $row["coltotalunits"] . "</td>
    <td>" . $row["coltotalwgt"] . "</td>
    <td>" . $row["commis"] . "</td>
    <td>" . $row["noncommis"] . "</td>
    <td>" . $row["cod"] . "</td>
    <td>" . $row["AMR_Comp"] . ".</td>
    <td>" . $row['invoiceAmt'] . "</td>
    <td>" . $row['invoicedate'] . "</td>
    <td>" . $modtime . "</td>
    <td>" . $row["owner"] . "</td>
    <td>" . $row["rpt"] . "</td>
    <td>" . $row["email"] . "</td>
  </tr>
  ";
        }

        ?>
        </tbody>
    </table>
</div>


 


