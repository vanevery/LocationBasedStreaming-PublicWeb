// This is a node server that works with a mongo db to store and serve streamer information

// Add the correct info
var mongo_user = "username";
var mongo_user_pass = "password";
var mongo_server_port = "localhost:27017"

var http = require("http");
var url = require("url");
var mongoose = require("mongoose");

mongoose.connect("mongodb://" + mongo_user + ":" + mongo_user_pass + "@localhost:27017/test");

var db = mongoose.connection;
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback () {
	console.log("Connected to Database");
});

/*
var nameSchema = new mongoose.Schema({
		name: String
});
var name = mongoose.model('Name',nameSchema);
*/

var locationSchema = new mongoose.Schema(
	{
		uuid: String,
		lat: String,
		lon: String,
		date: Date
	}
);
var location = mongoose.model('Location',locationSchema);


function onRequest(request, response) {
	
	console.log("Request received");
	
	var urlParts = url.parse(request.url, true);  // true, parse the query string as well with querystring
    
    console.log("Request for " + urlParts.pathname + " received.");
    //console.log("Query " + urlParts.query);

	if (urlParts.pathname == "/update") {

		if (typeof(urlParts.query.uuid) !== 'undefined' &&
			typeof(urlParts.query.lat) !== 'undefined' &&
			typeof(urlParts.query.lon) !== 'undefined')
		{
			console.log("uuid " + urlParts.query.uuid);

			var locationData = {uuid: urlParts.query.uuid,
								lat: urlParts.query.lat,
								lon: urlParts.query.lon,
								date: Date.now()};

			var newLocation = new location(locationData);
			newLocation.save();
		
			console.log("Wrote to " + locationData.uuid + " Database");
			
			response.writeHead(200, {"Content-Type": "text/plain"});
			response.write("Wrote to " + locationData.uuid + " Database");
			response.end();

		}		
		
	} else if (urlParts.pathname == "/list") {
	
		console.log("Current date: " + Date.now());
	
		//var query = location.find();
		// Need to have distinct UUIDs in query
		// Need to take in timeout time, right now hardcoded for 5 minutes which is how often users update
		//query.where('date').gte(Date.now() - 1000*60*5).exec(
		
		location.find().where('date').gte(Date.now() - 1000*60*5).exec(
			function (err, foundloc) {
				if (err) console.log(err);
				
				response.writeHead(200, {"Content-Type": "text/plain", 'Access-Control-Allow-Origin': "*"});
				response.write(JSON.stringify(foundloc));
				response.end();
			  	
  				console.log("Database returned: " + foundloc);
			}
		);
	}
}

var server = http.createServer(onRequest);
server.listen(8888);
console.log("Server running");



	
