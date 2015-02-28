var https = require('https');
var fs = require('fs');
var querystring = require('querystring');
var password = 'fakepassword';
var secret_password = 'bikebike';

var options = {
  passphrase: 'fakepassword',
  key: fs.readFileSync('./ssl/privkey.pem'),
  cert: fs.readFileSync('./ssl/cacert.pem')
};

var mailman = require('https');
var mailman_options = {
  	hostname: 'wvcompletestreets.org',
  	port: 443,
  	path: '/',
  	method: 'POST',
  	ca: fs.readFileSync('/etc/ssl/certs/demo-cert.pem')
};

var server = https.createServer(options, function (request, response) {

   response.writeHead(200, { 'Content-Type': 'application/json' });
	
	request.on('data',function(message){

		var data = JSON.parse(message);
		var subscribees_upload = data.first_name + ' ' + data.last_name + ' <' + data.email + '>';
		var url_object;
		var member_query;
		if (data.subscribe === 'subscribe') {		
			url_object = {
				subscribe_or_invite: 0,
				send_welcome_msg_to_this_batch: 1,
				notification_to_list_owner: 1,
				adminpw: password,
				subscribees_upload: subscribees_upload
			};
			member_query = '/mailman/admin/ybdb-devel/members/add?' + querystring.stringify(url_object);
		} else {
			url_object = {
				send_unsub_ack_to_this_batch: 1,
				send_unsub_notifications_to_list_owner: 1,
				unsubscribees_upload: data.email,
				adminpw: password
			};			
			member_query = '/mailman/admin/ybdb-devel/members/remove?' + querystring.stringify(url_object);
		}
		
		mailman_options.path = member_query;

		if (secret_password === data.password) { 
				
			// post to mailman server			
			var req = mailman.request(mailman_options, function(res) {
			  console.log("statusCode: ", res.statusCode);
			  console.log("headers: ", res.headers);
			
			  res.on('data', function(d) {
			    //req.write(d);
			  });
			});
	
			req.end();
			
			req.on('error', function(e) {
			  console.error(e);
			});
			
		} // secret_password matches

		response.write('ok');
	
 	}); 

	request.on('end',function(){
 		response.end();
 	});
	
 	server.on('error', function (e) {
 		console.error(e);
 	});
 	

});  // end email connector server
server.listen(Number(process.argv[2]));
