<?php
$user = $_SESSION['user']['firstname'][0] . $_SESSION['user']['lastname'][0];
?>

<script src="/assets/js/sorttable.js"></script>

<Style>
    #load {
        margin-left: 30em;
    }

    #btn-group {
        padding-left: 50px;
    }

    #load p {
        position: absolute;
    }

    .loader {
        position: absolute;
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    #company {
        margin-left: 50px;
        margin-right: 50px;
        padding: 50px;
    }

    #company tr {
        margin-top: 10px;
        margin-bottom: 10px;

    }

    .nav-item {
    }

    #addnew {
        margin-left: 50px;
    }

    #company th {
        background-color: #eee;
        border-top: 1px solid #fff;
    }

    #company th:hover {
        background-color: #ccc;
    }

    #company th {
        background-color: #fff;
    }

    #company th:hover {
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
    function ready() {
        $(".loader").remove();
        $("#load").remove();

        $("#load").stop(true, true).fadeOut();
        $(".loader").stop(true, true).fadeOut();
        $("#invdate").datepicker({dateFormat: 'dd-mm-yy'});
        $("#adddates").datepicker({dateFormat: 'dd-mm-yy'});
        $("#adddates").datepicker("setDate", new Date());

        let date = '<?php echo $_SESSION['timedateinv'] ?>';
    }

    document.addEventListener("DOMContentLoaded", ready);

    $(document).ready(function () {
        $("a.tooltipLink").tooltip();
        let d = 22;

        document.getElementById("ref").onclick = function () {
            location.href = "/RS/rebatepage";
        };

        let i = 0;
        let arrcomp = [];

        $("#addinvrdate").change(function () {
            console.log(this.val());
        });

        $("#company .trstuff #btnns #edi").click(function () {
            $("#company .trstuff #btnns #edi").prop("disabled", true);
            $(this).closest("tr").find(".editgroup").prop("disabled", false);
            $(this).closest("tr").find("#btnns #sav").css('visibility', 'visible');
            $(this).hide();
        });


        $("#sub").click(function () {
            $('#addnew').removeClass('hidden');
            $("#sub").hide();

        });

        $("#upsub").click(function () {
            let addord = $("#addorde").val();
            let addmon = $("#addmon").val();
            let adddate = $("#adddates").val();
            let addby = $("#addrby").val();
            let addname = $("#addcname").val();
            let addval1 = $("#addValev").val();
            let addval2 = $("#addviev").val();
            let addval3 = $("#divexc").val();
            let addinvdate = $("#invdate").val();
            let addcin = $("#addcinv").val();
            let addstat = $("#addstat option:selected").val();
            let addcmp = $("#addcmpn").val();

            if (addval1 == '') {
                addval1 = 0;
            }

            if (addval2 == '') {
                addval2 = 0;
            }
            if (addval3 == '') {
                addval3 = 0;
            }

            $.ajax({
                type: "POST",
                url: "/RS/newrebate/",
                data: {
                    addord: addord,
                    addmon: addmon,
                    adddate: adddate,
                    addby: addby,
                    addname: addname,
                    addval1: addval1,
                    addval2: addval2,
                    addval3: addval3,
                    addinvdate: addinvdate,
                    addcin: addcin,
                    addstat: addstat,
                    addcmp: addcmp
                },
                success: function (data) {
                    alert("rebate added!");
                }
            });

            $('#addnew').addClass('hidden');
            $("#sub").show();
            location.reload(true);
        });


        $("#company .trstuff #btnns #sav").click(function () {
            $(this).closest("tr").find("#btnns #sav").css('visibility', 'hidden');
            $(this).closest("tr").find("#btnns #edi").show();

            $("#company .trstuff #btnns #edi").prop("disabled", false);
            $(this).closest("tr").find(".editgroup").prop("disabled", true);

            let id = $(this).closest("tr").find("#idnum").val();
            let ord = $(this).closest("tr").find("#ordnum").val();
            let mon = $(this).closest("tr").find("#month").val();
            let datee = $(this).closest("tr").find("#datee").val();
            let name = $(this).closest("tr").find("#name").val();
            let val1 = $(this).closest("tr").find("#vat1").val();
            let val2 = $(this).closest("tr").find("#vat2").val();
            let val3 = $(this).closest("tr").find("#vat3").val();
            let invdate = $(this).closest("tr").find("#invdate").val();
            let cinn = $(this).closest("tr").find("#cinn").val();
            let status = $(this).closest("tr").find("#status option:selected").text();

            if (val1 == '') {
                val1 = 0;
            }
            if (val2 == '') {
                val2 = 0;

            }
            if (val3 == '') {
                val3 = 0;
            }

            $.ajax({
                type: "POST",
                url: "/RS/updaterebate/",
                data: {
                    id: id,
                    ord: ord,
                    mon: mon,
                    datee: datee,
                    name: name,
                    val1: val1,
                    val2: val2,
                    val3: val3,
                    invdate: invdate,
                    cinn: cinn,
                    status: status
                },
                success: function (data) {
                }
            });

            location.reload(true);
        });


        $('#search').keyup(function () {
            search_table($(this).val());
        });

        function search_table(value) {
            $('#company tbody tr').each(function () {
                let found = 'false';
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
            let selectedCountry = $(".filter option:selected").val();
        });
    });
</script>

<div class="container">
    <br>
    <br>
    <br>
    <br>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="/RS/rebatepage/">Rebate Page</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/booking/">Booked Collection</a>
        </li>
        <li class="nav-item">
            <a class="nav-link"  href="/RS/arc/">Collected</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/RGR/">Recycling Goods Receipting </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/companynote/">Company Notes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="javascript:history.back()">Back</a>
        </li>
    </ul>
    <br> <br>
    <div id='load'>
        <div class="loader">
        </div>
        <p> loading... </p>
    </div>

    <h1 class="page-header"> Stone Computers: Recycling System <small>test</small></h1>

    <hr>
    <br>
    <br>

    <div class="form-group col-xs-12">
        <div class="centered"> <label> Search  </label></div><br>
        <form method='post' action="/RS/rebatepage/">

            <br>
            <div align="center">  <label> Filter Search Results <a data-toggle="tooltip" class="tooltipLink" data-original-title="filter's data in visble table.">
                        <span class="glyphicon glyphicon-info-sign"></span>
                    </a></label>
                <input type="text" name="search" id="search" class="form-control" />
            </div>
            <br>

            <br>

            <div align="center"> <label>Order By</label></div>
            <select  class="form-control" name="filter">
                <option value="ORD">ORD</option>
                <option value="Month">Month</option>
                <option value="date">Date Sent</option>
                <option value="RaisedBy">Collection Date</option>
                <option value="Customer Name">Customer Name</option>
                <option value="Status">Status</option>
                <option value="RebateID">RebateID</option>
            </select>

            <br>
            <input type="submit" class="btn btn-primary" value="Submit">
        </form>
    </div>

    <br>
    <br>
</div>

<br>
<br>
<div id="btn-group">
    <button type='submit' id='sub' CLASS="btn btn-primary"> Add new </button>
    <button type='submit' id='ref' CLASS="btn btn-secondary"> Refresh </button>
</div>

<div id='addnew' class='hidden'>
    <table width='100%'  class='sortable table table-striped  tb2'>
        <thead>
        <tr>
            <th> ORD </th>
            <th> Month </th>
            <th> Date Sent </th>
            <th> RaisedBy </th>
            <th> Customer Name </th>
            <th> Val Exc VAT </th>
            <th> VaL INV Exc VAT</th>
            <th hidden> Difference Exc VAT </th>
            <th> Date INV Received </th>
            <th> Comments INV Number </th>
            <th> Status </th>
            <th> CMP_Num </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><input type='text' id='addorde'> </td>
            <td><input type='text' id='addmon'> </td>
            <td><input type="text" class="datepicker"  id='adddates' ></td>
            <td><input type='text' id='addrby' value='<?php echo $user ?>'></td>
            <td><input type='text' id='addcname'> </td>
            <td><input type='text' id='addValev'> </td>
            <td><input type='text' id='addviev'> </td>
            <td hidden><input hidden type='text' id='divexc'> </td>
            <td><input type="text" class="datepicker"  id="invdate" ></td>
            <td><input type='text' id='addcinv'> </td>
            <td> <select id='addstat'>
                    <option value='nothing'> Please select a status </option>
                    <option value='Claimed'> Claimed </option>
                    <option value='Awaiting' selected> Awaiting </option>
                    <option value='Cancelled'> Cancelled </option>
                </select> </td>
            <td><input type='text' id='addcmpn'> </td>
        </tr>
        </tbody>
    </table>
    <br>

    <button type='submit' id='upsub' class="btn btn-primary"> submit </button>
</div>
<br>

<?php

require_once sprintf(
    '%s%s.%s',
    TEMPLATE_DIR,
    'RECBooking/pages/rebate-data',
    'php'
);