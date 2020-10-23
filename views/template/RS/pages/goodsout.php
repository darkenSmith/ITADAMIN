    <script src="/assets/js/printThis/printThis.js"></script>
    <link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="/assets/css/imgupload.css" rel="stylesheet">
    <script src="/assets/js/popper.js"></script>

    <script type="text/javascript">
        $(document).ready(function ($) {
            $('#loadclose').hide();
            $('#close').on('click', function () {
                $('#adding').hide();
                $('#loadclose').show();
                $('#add').hide();
            });

            $('#btncloseload').on('click', function () {
                var loadid = $('#loadnumid').val();
                var despatchdate = $('#desp').val();
                var company = $('#company').val();

                $.ajax({
                    url: "/RS/closeLoad/",
                    type: "POST",
                    data: {
                        loadid: loadid,
                        despatchdate: despatchdate,
                        company: company
                    }, success: function (data) {
                        console.log(data);
                        alert('success');
                        location.reload("/RS/Goodsout/");
                    }
                });
            });

            $('#add').on('click', function () {
                var loadnum = $('#loadnum').val();
                var wgt = $('#wgt').val();
                var Type = $('#Type').val();
                var Supplier = $('#Supplier').val();
                var palletnum = $('#Pallet-num').val();

                $.ajax({
                    url: "/RS/goodsInAdd/",
                    type: "POST",
                    data: {
                        loadnum: loadnum,
                        wgt: wgt,
                        Type: Type,
                        Supplier: Supplier,
                        palletnum: palletnum
                    },
                    success: function (data) {
                        console.log(data);
                        alert('success');
                        location.reload("/RS/Goodsout/");
                    }
                });
            });
        });
    </script>

<div class="container">

    <br><br>
    <table class='table'>
        <thead>
        <tr>
            <th> Loadnum</th>
            <th> Wgt</th>
            <th> Type</th>
            <th> Supplier</th>
            <th> Pallet-num</th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($totalloads as $pallet) {
            echo "<tr> 
                <td> " . $pallet['loadnum'] . " </td>
                <td> " . $pallet['wgt'] . " </td>
                <td> " . $pallet['type'] . " </td>
                <td> " . $pallet['supplier'] . " </td>
                <td> " . $pallet['pallets'] . " </td>
            </tr>";
        }
        ?>

        </tbody>
    </table>

    <?php
    echo "
   <table class='table'>
   <thead>
   <tr>";
    foreach ($loadlist as $loaddet) {
        echo "<th> " . $loaddet['loadnum'] . " </th>";
    }

    echo "</tr></thead><tbody><tr>";

    foreach ($loadlist as $loaddet) {
        echo "<td> " . $loaddet['totalwgt'] . " </td>";
    }

    echo "</tr><tr>";

    foreach ($loadlist as $loaddet) {
        echo "<td> " . $loaddet['company'] . " </td>";
    }

    echo "</tr><tr>";

    foreach ($loadlist as $loaddet) {
        echo "<td> " . $loaddet['despatch_date'] . " </td>";
    }

    echo "</tr><tr>";

    foreach ($loadlist as $loaddet) {
        echo "<td> " . $loaddet['staus'] . " </td>";
    }

    echo "</tr></tbody></table>"
    ?>

    <br>

    <table class='table' id='adding'>
        <thead>
        <tr>
            <th> Loadnum</th>
            <th> Wgt</th>
            <th> Type</th>
            <th> Supplier</th>
            <th> Pallet-num</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><input type='number' id='loadnum'/></td>
            <td><input type='number' id='wgt'/></td>
            <td><input type='text' id='Type'/></td>
            <td><input type='text' id='Supplier'/></td>
            <td><input type='number' id='Pallet-num'/></td>
        </tr>
        </tbody>
    </table>
    <input type='submit' id='add' value='Add' class='btn btn-success'/>
    <input type='submit' id='close' value='Close Load' class='btn btn-warning'/>

    <table class='table' id='loadclose'>
        <thead>
        <tr>
            <th> Loadnum</th>
            <th> Company</th>
            <th> despatch Date</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><input type='number' id='loadnumid'/></td>
            <td><input type='text' id='company'/></td>
            <td><input type='date' id='desp'/></td>
            <td><input type='button' value='submit' class='btn btn-success' id='btncloseload'/>
        </tr>
        </tbody>
    </table>
</div>
