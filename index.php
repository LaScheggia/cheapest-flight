<?php

class Airport {
  public int $id;
  public string $name;
  public string $code;
  public float $lat;
  public float $lng;
}

class Flight {
  public string $code_departure;
  public string $code_arrival;
  public int $price;
}

function array_copy($arr) {
  $newArray = array();
  foreach($arr as $key => $value) {
      if(is_array($value)) $newArray[$key] = array_copy($value);
      else if(is_object($value)) $newArray[$key] = clone $value;
      else $newArray[$key] = $value;
  }
  return $newArray;
}

function getAirports() {
  $airports = array();

  $mxp = new Airport();
  $mxp->id = 0;
  $mxp->name = "Milan Malpensa";
  $mxp->code = "MXP";
  $mxp->lat = 0.0;
  $mxp->lng = 0.0;

  $lin = new Airport();
  $lin->id = 1;
  $lin->name = "Milan Linate";
  $lin->code = "LIN";
  $lin->lat = 0.0;
  $lin->lng = 0.0;

  $bgy = new Airport();
  $bgy->id = 2;
  $bgy->name = "Bergamo Orio al Serio";
  $bgy->code = "BGY";
  $bgy->lat = 0.0;
  $bgy->lng = 0.0;

  array_push($airports, $mxp, $lin, $bgy);

  return $airports;
}

function getFlights() {
  $flights = array();

  $flight01 = new Flight();
  $flight01->code_departure = "MXP";
  $flight01->code_arrival = "LIN";
  $flight01->price = 1;

  $flight02 = new Flight();
  $flight02->code_departure = "LIN";
  $flight02->code_arrival = "BGY";
  $flight02->price = 1;
  
  $flight12 = new Flight();
  $flight12->code_departure = "MXP";
  $flight12->code_arrival = "BGY";
  $flight12->price = 3;

  array_push($flights, $flight01, $flight02, $flight12);

  return $flights;
}

function getAirportIndexByCode($airports, $code) {
  for ($i = 0; $i < sizeof($airports); $i++) {
    if ($airports[$i]->code == $code) {
      return $i;
    }
  }

  return -1;
}

function transposeFlights($airports, $flights) {
  $transposed = array();

  foreach ($flights as $flight) {
    array_push($transposed, array(
      getAirportIndexByCode($airports, $flight->code_departure),
      getAirportIndexByCode($airports, $flight->code_arrival),
      $flight->price
    ));
  }

  return $transposed;
}

// num_airports = total number of airports
// flights = array of triple (from id, to id, price)
// src = airport id
// dest = airport id
// stops = max k stops
function cheapestFlights(int $num_airports, array $flights, int $src, int $dest, int $stops) {
  $dp = array_fill(0, $num_airports, INF);
  $dp[$src] = 0;

  for ($i = 0; $i < $stops + 1; $i++) {
    $dp_tmp = array_copy($dp);

    for ($j = 0; $j < sizeof($flights); $j++) {
      $flight = $flights[$j];
      $dp_tmp[$flight[1]] = min($dp_tmp[$flight[1]], $dp[$flight[0]] + $flight[2]);
    }
    $dp = array_copy($dp_tmp);
  }

  if ($dp[$dest] != INF) {
    return $dp[$dest];
  } else {
    return -1;
  }
}

$airports = getAirports();
$flights = getFlights();
$flights_transposed = transposeFlights($airports, $flights);

$flight_price = -1;

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['searchFlights'])) {
  $flight_price = cheapestFlights(sizeof($airports), $flights_transposed, $_POST["src"], $_POST["dest"], $_POST["stops"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flight Search</title>
</head>
<body>
  <form action="index.php" method="POST">
    <label for="departure_airport">From</label>
    <select name="src" id="src">
      <?php
        for ($i = 0; $i < sizeof($airports); $i++) {
          echo "<option value=\"".$i."\">".$airports[$i]->name."</option>";
        }
      ?>
    </select>

    <label for="arrival_airport">To</label>
    <select name="dest" id="dest">
      <?php
        for ($i = 0; $i < sizeof($airports); $i++) {
          echo "<option value=\"".$i."\">".$airports[$i]->name."</option>";
        }
      ?>
    </select>
    
    <label for="stops">Stops Number</label>
    <input type="number" name="stops" value="0" min="0" />

    <input type="submit" name="searchFlights" value="Search" />
  </form>

  <p>
    <?php if ($flight_price > -1) {
      echo "Flight Price: ".$flight_price."€";
    } else {
      echo "No Flight Available!";
    } ?>
  </p>
</body>
</html>