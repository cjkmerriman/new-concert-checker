<?php
header('Access-Control-Allow-Origin: *');  
header('Content-Type: application/json; charset=utf-8');

ini_set('error_reporting', E_ALL);


// database connection details
//echo 'Current PHP version: ' . phpversion(). '<br />';

$servername = "";
$username = "";
$password = "";
$database = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("ISO-8859-1");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$ago = date('Y-m-d H:i:s', strtotime('-24 hour'));
$output = '';

$sql='
select 
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
  se.name as series,
  ti.buy_link as ticket_link,
  count(ti.buy_link) as num_ticket_links
from 
  events e 
inner join performances as p on e.id = p.event_id
inner join artists a on p.artist_id = a.id
inner join venues v on e.venue_id = v.id
inner join cities c on v.city_id = c.id
inner join states s on c.state_id = s.id
inner join countries co on s.country_id = co.id
left join tickets ti on ti.event_id = e.id and ti.status = 1
left join series se on e.series_id = se.id
where 
  e.status = 0
  and a.popularity > 0.1
  and e.created_on > "'.$ago.'"
group by
  sk_event_id
order by 
  a.popularity DESC, e.date ASC';
$result = $conn->query($sql);

$results = array();
$output = array();

if ($result) {
  while($row = $result->fetch_assoc()) {
    
    $isFestival = false;
  	$artist_id = $row['artist_id'];
  	$event_id = $row['sk_event_id'];
  	$venue_id = $row['venue_id'];
  	$date = date_create($row['date']);
  	$month = date_format($date, 'F');
  	$day = date_format($date, 'd');
    $year = date_format($date, 'Y');;
  	$venue = $row['venue'];
  	$location = utf8_decode($row['city'].', '.$row['country']);
  	$created = time_elapsed_string($row['created']);
  	$artist = utf8_decode($row['artist']);
  	$billing = $row['billing'];
  	$type = $row['type'];
  	$series = $row['series'];
  	$kind = '';
    $ticket_link = $row['ticket_link'];
    $num_ticket_links = $row['num_ticket_links'];

  	if ($type == 'FestivalInstance') {
  		$kind = 'Festival';
      $isFestival = true;
  	} else {
  		$kind = ucfirst($billing);
  	}

  	// create results array
  	$results[$event_id]['eventID'] = $event_id;
	  $results[$event_id]['type'] = $type;
  	$results[$event_id]['kind'] = $kind;
  	$results[$event_id]['series'] = $series;
    $results[$event_id]['isFestival'] = $isFestival;    
  	$results[$event_id]['artistID'] = $artist_id;
  	$results[$event_id]['artist'] = $artist;
  	$results[$event_id]['month'] = $month;
  	$results[$event_id]['day'] = $day;
    $results[$event_id]['year'] = $year;
  	$results[$event_id]['venueID'] = $venue_id;
  	$results[$event_id]['venue'] = $venue;
  	$results[$event_id]['location'] = $location;
    $results[$event_id]['ticketLink'] = $ticket_link;
    $results[$event_id]['numTicketLink'] = $num_ticket_links;
  	$results[$event_id]['created'] = $created;

    array_push($output, $results[$event_id]);


  }
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime(null, new DateTimeZone('CET'));
    $ago = new DateTime($datetime, new DateTimeZone('CET'));

    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
$output = json_encode($output);
echo $output;


?>