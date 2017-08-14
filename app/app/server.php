<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);
ini_set('default_charset', 'utf-8');


// database connection details
//echo 'Current PHP version: ' . phpversion(). '<br />';

$servername = "dc1-live-db3";
$username = "songkick3-ro";
$password = "4oT8dh34Eg7J";
$database = "songkicker_production";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$ago = date('Y-m-d H:i:s', strtotime('-24 hour'));
$output = '';

$sql='select 
	e.id as sk_event_id, 
	a.name as artist,
	a.id as artist_id,
	e.date as date,
	e.created_on as created,
	e.type as type,
	v.name as venue,
	v.id as venue_id,
	p.billing as billing, 
	c.name as city,
	s.name as state, 
	co.name as country,
	se.name as series
from 
	events e 
inner join performances as p on e.id = p.event_id
inner join artists a on p.artist_id = a.id
inner join venues v on e.venue_id = v.id
inner join cities c on v.city_id = c.id
inner join states s on c.state_id = s.id
inner join countries co on s.country_id = co.id
left join series se on e.series_id = se.id
where 
	a.id in (SELECT id FROM artists WHERE popularity > 0.4)
	and e.created_on > "'.$ago.'"
order by 
	created DESC';

$result = $conn->query($sql);

$results = array();

if ($result) {
  while($row = $result->fetch_assoc()) {
  	
  	$artist_id = $row['artist_id'];
  	$event_id = $row['sk_event_id'];
  	$venue_id = $row['venue_id'];
  	$date = date_create($row['date']);
  	$month = date_format($date, 'M');
  	$day = date_format($date, 'd');;
  	$venue = $row['venue']; ;
  	$location = $row['city'].', '.$row['country'];
  	$created = $row['created'];
  	$artist = $row['artist'];
  	$billing = $row['billing'];
  	$type = $row['type'];
  	$series = $row['series'];
  	$kind = '';

  	if ($type == 'FestivalInstance') {
  		$kind = 'Festival';
  	} else {
  		$kind = $billing;
  	}


  	// create results array
  	$results[$event_id]['event_id'] = $event_id;
	$results[$event_id]['type'] = $type;
  	$results[$event_id]['kind'] = $kind;
  	$results[$event_id]['series'] = $series;  	
  	$results[$event_id]['artist_id'] = $artist_id;
  	$results[$event_id]['artist'] = $artist;
  	$results[$event_id]['month'] = $day;
  	$results[$event_id]['day'] = $day;
  	$results[$event_id]['venue_id'] = $venue_id;
  	$results[$event_id]['venue'] = $venue;
  	$results[$event_id]['location'] = $location;
  	$results[$event_id]['created'] = $created;

  }
}

$output = json_encode($results);


?>

<!doctype html>
<html lang=en-us>
<head>

</head>
<body>
	<pre class="dates"> <?php echo $output; ?> </pre>
</body>
</html>