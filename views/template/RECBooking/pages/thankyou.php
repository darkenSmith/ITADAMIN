
  


    <title>Thank You</title>


<script type='text/javascript'>


  $(document).ready(function(){

    $('#done').hide();
 var custreq = '<?php echo $_GET['id'] ?>';
     $("#reqid").val(custreq);
    // alert(custreq);

    var t = '<?= json_encode($_GET) ?>'

    console.log(t);

    $("#consub").click(function(){


     

 
       // alert("hello");

        var req = $("#reqid").val();

      //  alert(req);






                     $.ajax({
                url: "/RS/updateconf/", // Url to which the request is send
                type: "POST",             // Type of request to be send, called as method
                data: {
               
                    req : req
                }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                      // To send DOMDocument or non processed data file it is set to false

                success: function(data){   // A function to be called if request succeeds
            
              // alert('success');
              // alert(data);
                  // $("#image_preview").html(data);
                }
              });

              $("#reqconf").hide();
              $("#done").show();


    });

  });


</script>



</head>
<body>
<br/>

<div align='center'>
<div class="jumbotron text-xs-center">
  <h1 class="display-3">Thank You!</h1>
  <p id='done' class="lead"><strong>Your confirmation has been received.</strong></p>
  <div id=reqconf>
  <p class="lead"><strong>Please enter your request id to confirm.</strong></p>
  <br/>
<b>Enter: </b><input type=text id='reqid'>
<button class='btn btn-success btn-sm' type='submit' id='consub'> Submit </button>
</div>
  <hr>
  <p>
    Having trouble? <a href="">Contact us</a>
  </p>
  <p class="lead">
    <a class="btn btn-primary btn-sm" href="https://www.stonegroup.co.uk/" role="button">Continue to homepage</a>
  </p>
</div>
</div>

</body>







