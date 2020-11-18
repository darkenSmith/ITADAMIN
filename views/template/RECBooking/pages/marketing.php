</head>
<style>


#msgbox {

margin:5rem auto;
	width: 750px;
}

textarea#styled {
	width: 600px;
	height: 120px;
	border: 3px solid #cccccc;
	padding: 5px;
	font-family: Tahoma, sans-serif;
	background-image: url(bg.gif);
	background-position: bottom right;
	background-repeat: no-repeat;
}


</style>
<body>
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


    <div id="msgbox">
    <label for="styled"> Push Notification to All users: </label>
    <textarea id="styled" cols="30" rows="25"></textarea>
</br>
    <button type="submit" id="send" class="btn btn-success">Push</button>

</div>




</body>







