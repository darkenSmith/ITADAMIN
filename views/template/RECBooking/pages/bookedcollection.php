<?php


$filter_txt = (isset($_POST) && !empty($_POST) ? " matching your filter: " : "Requests: ");

$cust = "\"Request_id\"";

?>


<style>


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

    #tbldata td {
        white-space: nowrap;
    }

    /* Sortable tables */
    table.sortable thead {
        background-color: #eee;
        color: #666666;
        font-weight: bold;
        cursor: default;
    }

    form {
        padding: 20px;
        border-style: solid;
        border-width: 2px;
        border-color: #d8d8d8;
    }
</style>


<title>Booked Collections</title>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var stuff = [];
        var sep = 0;
        var emailstuff = [];
        var sep2 = 0;
        $('#tbldata tr:has(td)').find('.ordernum').each(function () {
            if ($(this).html().length == 0) {
                console.log($(this).find('checkboxes').html())
            }
        });

        $('#tbldata tr:has(td)').find('.approv').each(function () {
            ap = $(this).val();
            // console.log(ap);
            if (ap.trim()) {
                // is empty or whitespace
                console.log(ap);
                $(this).css("background-color", "#800080");
                $(this).css("color", "white");
            }
        });

        $('#tbldata tr:has(td)').find('.process').each(function () {
            ap2 = $(this).val();

            if (ap2.trim()) {
                console.log(ap2);
                $(this).css("background-color", "#800080");
                $(this).css("color", "white");
            }
        });



        var file = '\RS_Files\file.csv';
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

        $('.chkparent').click(function () {
            var isChecked = $(this).prop("checked");
            $('#tbldata tr:has(td)').find('input[type="checkbox"]').prop('checked', isChecked);
        });

        $('#tbldata tr:has(td)').find('input[type="checkbox"]').click(function () {
            var isChecked = $(this).prop("checked");
            var isHeaderChecked = $(".chkparent").prop("checked");

            if (isChecked == false && isHeaderChecked)
                $(".chkparent").prop('checked', isChecked);
            else {
                $('#tbldata tr:has(td)').find('input[type="checkbox"]').each(function () {
                    if ($(this).prop("checked") == false) {
                        isChecked = false;
                    } else {
                        isChecked = true;
                    }

                });
                console.log(isChecked);
                //$(".chkparent").prop('checked', isChecked);
            }
        });

        $('#tbldata tr:has(td)').find('.checkboxes').change(function () {
            var checked = $(this).prop("checked");


            var orderNum = $(this).val();
            var emailAddress = $('.emailsel_' + orderNum).html();


            if (checked == true) {
                sep + 1;
                sep2 + 1;

                var selectedRows = $('table._tm').find('tbody').find('tr').has('input[type=checkbox]:checked').find('.reqnum').toArray().map(e => e.innerHTML);

                var laststuff = selectedRows[selectedRows.length - 1];
                var test = selectedRows[selectedRows.length];

                // Neils code start
                var laststuff = orderNum;
                var lastemailstuff = emailAddress;
                // End Neils code

                stuff.push(laststuff);
                emailstuff.push(lastemailstuff);
                lastemailstuff = [];
                selectedRows = [];
                selectedemailRows = [];
                laststuff = [];
            } else {
                // Neils code start
                stuff.splice(stuff.indexOf(orderNum), 1);
                emailstuff.splice(emailstuff.indexOf(emailAddress), 1);
                // End Neils code
            }

            console.log(stuff);
        });

        $('#buttonDone').click(function () {
            console.log($(this).html());
            $.ajax({
                type: "POST",
                url: "/RS/isdone/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("The orders have been marked as Done");

                }
            });
            console.log("Orders marked as done");
            location.reload();
        });

                $('#delDone').click(function() {
                  var msg = "delete";
        $.ajax({
          type: "POST",
          url: "/RS/delrequestmulti/",
          data: {stuff : stuff,
                  msg : msg},
          success: function(data) {
            alert("The requests will be deleted");
            
          }
        });
        location.reload();
      });


        $('#confirmbtn').click(function () {
            // alert('testtest');
            $.ajax({
                type: "POST",
                url: "/Conf/confmulti/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("The requests will be confirmed");
                }
            });
            console.log("Orders marked as confirmed");
            //location.reload();
        });

        $('#chargebtn').click(function () {
            // alert('testtest');
            $.ajax({
                type: "POST",
                url: "/RS/toggleCharge/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("charge stat changed");
                }
            });
            console.log("stat changed");
            location.reload();
        });

        $('#Uncon').click(function () {
            // alert('testtest');
            $.ajax({
                type: "POST",
                url: "/Conf/unconfmulti/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("The requests will be revert booked");
                }
            });
            console.log("Orders marked as booked");
            //location.reload();
        });

        $('#Unbok').click(function () {

            alert('testtest');
            $.ajax({
                type: "POST",
                url: "/Conf/unbookmulti/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("The requests will be revert to request");
                }
            });
            console.log("Orders marked as request");
//location.reload();
        });

///on hold button
        $('#hldbtn').click(function () {

            alert('hldbutton');
            $.ajax({
                type: "POST",
                url: "/RS/onhold/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("The requests will be revert to request");
                }
            });
            console.log("Orders marked as hold");
//location.reload();
        });

        $('#buttonEmail').click(function () {
            $.ajax({
                type: "POST",
                url: "/RS/isPdf/",
                data: {stuff: stuff},
                success: function (data) {
                    // alert(data);
                    alert("Sent to Templete");

                    setTimeout(function () {
                        window.document.location = '/RS/downloadcsv/';
                    }, 10000);
                    alert("PDF and eMail sent");
                    console.log("PDF and eMail sent");
                }
            });

            return false;
        });


    });
</script>


<br> <br><br>
<br>

<div class="container">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="/RS/booking/">Booked Collection</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/arc/">Collected</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/RGR/">Recycling Goods Receipting </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/companynote/">Company Notes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/rebatepage/">Rebates</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href=".\admin\home\">Back</a>
        </li>
    </ul>

    <h1 class="page-header"> Stone Computers: Recycling System <small>test</small></h1>
    <br>
    <div align="center"><label> Search <a data-toggle="tooltip" class="tooltipLink"
                                          data-original-title="filter's data in visble table.">
                <span class="glyphicon glyphicon-info-sign"></span>
            </a></label> <input type="text" name="search" id="search" class="form-control"/>
    </div>
    <br>
    <form method='post' action="/RS/booking/">
        <div class="form-row">
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
                <label>Notes:</label>
                <input type="text" name="notesja"
                       value="<?php echo(isset($_POST["notesja"]) && $_POST["notesja"] != "" ? $_POST["notesja"] : ""); ?>">
            </div>
            <div class="col">
                <label>RequestID:</label>
                <input type="text" name="id"
                       value="<?php echo(isset($_POST["id"]) && $_POST["id"] != "" ? $_POST["id"] : ""); ?>">
            </div>
            <div class="col">
                <label>Collection Date:</label>
                <input type="date" name="collectdate"
                       value="<?php echo(isset($_POST["collectdate"]) && $_POST["collectdate"] != "" ? $_POST["collectdate"] : ""); ?>">
            </div>


            <div class="col">
                <label>Select filter:</label>
                <select name="filter">
                    <option value="request_ID">RequestID</option>
                    <option value="ORD">SalesOrderNumber</option>
                    <option value="Request_date_added">Date Added</option>
                    <option selected value="Collection Date">Collection Date</option>
                    <option value="Customer_email">Customer eMail</option>
                </select>
                <select name="filter2">

                    <option value="ASC">ASC</option>
                    <option value="DESC">DESC</option>
                    <option selected value="DESC">Order type</option>


                </select>
                <select name="filterstatus">
                    <option selected
                            value="All">
                        All
                    </option>
                    <option value="Confirmed">
                        Confirmed
                    </option>
                    <option value="Booked">
                        Booked
                    </option>
                    <option value="Requests">
                        Requests
                    </option>
                    <option value="Unbooked">
                        Cancelled
                    </option>
                    <option value="On-Hold">On-Hold</option>
                    <option value="deleted">show deleted</option>
                </select>
                <br><Br>
                <label>Area:</label>
                <select name="areafilter">
                    <option selected value="%"> Default</option>
                    <?php
                    foreach ($arealist as $area) {
                        echo "<option  value='" . $area['area1'] . "'> " . $area['area1'] . " </option>";
                    }
                    ?>
                    <option value="Empty"> Empty</option>
                </select>
                <input type='hidden' id='sort' value='asc'>

                <a data-toggle="tooltip" class="tooltipLink" data-original-title="this searches live database.">
                    <span class="glyphicon glyphicon-info-sign"></span>
                </a>
                <br>
                <input type="submit" value="Submit" class="btn btn-success">
    </form>
</div>
</div>
</div>

<br>
<div class="form-row">
    <div class="col">
        <button id='buttonDone' class='btn btn-primary'>Collected</button>
        <button id='delDone' class='btn btn-danger'>Delete</button>
        <button id='buttonEmail' class='btn btn-info'>Print</button>
        <button id='confirmbtn' class='btn btn-warning'>Confirm</button>
        <button id='chargebtn' class='btn btn-secondary'>Chargable?</button>
    </div>
    <br/>
    <div class="col">
        <button id='Unbok' class='btn btn-warning'>Un-book</button>
        <button id='Uncon' class='btn btn-danger'>Un-confirm</button>
        <button id='hldbtn' class='btn btn-warning'>On-Hold</button>


    </div>
</div>

<h2><?php echo count($bookList); ?>: Totals <?php echo $filter_txt; ?></h2>
<table id='tbldata' width='100%' class='sortable table table-striped'>
    <thead>
    <tr>
        <td><input hidden type='checkbox' class='chkparent'></td>
        <th hidden>port</th>
        <th>Request date</th>
        <th>RequestID</th>
        <th>OrderNum</th>
        <th>GDPR</th>
        <th hidden>CustID</th>
        <th>Status</th>
        <th>Vehicle</th>
        <th>Collection Date</th>
        <th>name</th>
        <th>town</th>
        <th>postcode</th>
        <th>weight</th>
        <th>totalunits</th>
        <th>Qualifying Units</th>
        <th> Charge</th>
        <th> Collection Instructions</th>
        <th> Avaiblity</th>
        <th> Approved</th>
        <th> Process</th>
        <th> EmailSent Date</th>
        <th> Survey Complete</th>
        <th> Prev Date</th>
        <th> Owner</th>
        <th><span onclick='sortTable("customer_contact");'>contact</span></th>
        <th>tel</th>
        <th><span onclick='sortTable("customer_email");'>email</span></th>
        <th hidden> Status Notes</th>
    </tr>
    </thead>
    <tbody>

    <?php
    foreach ($bookList as $b) {
        $datecheck = $b["coldate"];

        if ($datecheck === 'not set') {
            $time = '-';
        } else {
            $time = date("d/m/Y", strtotime($datecheck));
        }

        $chargereq = $b["charge"];
        if ($chargereq == 1) {
            $chargereq = 'YES';
        } else {
            $chargereq = '-';
        }

        $timesurv = date("d/m/Y", strtotime($b["emailsentdate"]));

        if ($timesurv == '01/01/1970' || $timesurv == '01/01/1900') {
            $timesurv = '-';
        } else {
            $timesurv = date("d/m/Y", strtotime($b["emailsentdate"]));
        }

        echo "
  <tr>
    <td  ><input type='checkbox' class='checkboxes' value='" . $b["id"] . "' ></td>
    <td hidden  ></td>
    <td  >" . date("d/m/Y", strtotime($b["requestdate"])) . "</td>
    <td  class='req'><a href='/RS/detialdoc?rowid=" . $b["id"] . "' target='_blank'><span class='reqnum'>" . $b["id"] . "</span></a></td>
    <td  class='ordernum'>" . $b["ord"] . "</td>
    <td  >" . $b["gdpr"] . "</td>
    <td hidden  class='crmnum'>" . $b["crm"] . "</td>
    <td  class='ordernumst'>" . $b["Stat"] . "</td>
    <td   class='tyy'> " . $b["typ"] . " </td>
    <td class='coltime' >" . $time . "</td>
    <td  >" . $b["name"] . "</td>
    <td >" . $b["town"] . "</td>
    <td >" . $b["postcode"] . "</td>
    <td > " . $b["totalweight"] . "</td>
    <td >" . $b["totalunits"] . "</td>
    <td >" . $b["commisionable"] . "</td>
    <td >" . $chargereq . "</td>
    <td ><textarea>" . $b["instructions"] . "</textarea></td>
    <td ><textarea>" . $b["request_col_date"] . "</textarea></td>
    <td ><textarea class='approv'>" . $b["approved"] . "</textarea></td>
    <td ><textarea  class='process'>" . $b["process"] . "</textarea></td>
    <td >" . $timesurv . "</td>
    <td >" . $b["scomp"] . "</td>
    <td ><textarea>" . $b["prev"] . "</textarea></td> 
    <td >" . $b["Owner"] . "</td>
    <td  >" . $b["contact"] . "</td>
    <td >" . $b["tel"] . "</td>
    <td  ><span class='emailsel_" . $b["id"] . "'>" . $b["email"] . "</span></td>
    <td hidden >" . $b["upnotes"] . "</td> 
  </tr>
  ";
    }
    echo "</tbody></table>";

    ?>
</div>
</div>

