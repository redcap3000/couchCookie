couchCookie
___________

Installation
____________

You must create the databases: default_users , default_session
And add the simple view design doc listed below to 'default_users', default being your application prefix [APF]:

	// PUT THIS IN YOUR USERS database
	{
	   "_id": "_design/cntrl",
	   "views": {
		   "username": {
			   "map": "function(doc){ emit(doc.username, doc.password)}"
		   }
	   }
	}
	
	// An Example user
	{
	   "_id": "3",
	   "_rev": "3-ce1dea6665cc409de79606d751458281",
	   "agency_id": 1,
	   "address_id": 1,
	   "username": "admin4",
	   "password": "098f6bcd4621d373cade4e832627b4f6",
	   "email": "myemail@here.com"
	}


Usage
_____

Then, make a new couchCookie object (be sure couchCurl is included first)

	$session = new couchCookie();
	// loads and interprets post (if present), assembles input in an array
	$session = poform::load($session);
	// renders html (assembles array), should return form
	$session = poform::make($session);

	<html><head>My Web App</head>
	      <body><h1>Log in!</h1>
	      $session
	      </body>
	</html>     

