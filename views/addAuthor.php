<SCRIPT language="JavaScript">
	function Submit()
	{
		var xmlhttp =  new XMLHttpRequest();
		xmlhttp.open('POST', 'ajax/addAuthorAction.php', false);
		
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				if (xmlhttp.status == 200) {
					var response = xmlhttp.responseXML;
				
					var firstname = response.getElementsByTagName('firstname')[0].innerHTML;
					var lastname = response.getElementsByTagName('lastname')[0].innerHTML;
					var authorid = response.getElementsByTagName('authorid')[0].innerHTML;
					
					document.getElementById("authorAdded").innerHTML = "<b>" + firstname + " " + lastname + "</b> successfully added to database.";
					
					authorAdded(firstname, lastname, authorid);
				}
			}
        }
		
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xmlhttp.setRequestHeader('Cookie', 'placeholder');
		xmlhttp.setRequestHeader('Cookie', 'user='+'<?php echo $userCookie; ?>');
		xmlhttp.send('firstname=' + document.getElementById('firstname').value + '&lastname=' + document.getElementById('lastname').value+'');
	}
</SCRIPT>

<form>

<h3 style="padding-bottom: 10px">Add Author to Database:</h3>
<p>First: <input type="text" name="firstname" id="firstname" size="10"/> Last: <input type="text" name="lastname" id="lastname" size="10"/></p>
<p><input type="button" value="Add" onclick="Submit()" style="margin: 5px" /></p>

<p id="authorAdded" style="color: red"> </p>

</form>