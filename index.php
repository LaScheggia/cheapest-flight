<?php

class Airport
{
  public int $id;
  public string $name;
  public string $code;
  public float $lat;
  public float $lng;
}

class Flight
{
  public string $code_departure;
  public string $code_arrival;
  public int $price;
}

function array_copy($arr)
{
  $newArray = array();
  foreach ($arr as $key => $value) {
    if (is_array($value)) {
      $newArray[$key] = array_copy($value);
    } elseif (is_object($value)) {
      $newArray[$key] = clone $value;
    } else {
      $newArray[$key] = $value;
    }
  }
  return $newArray;
}

function getAirports()
{
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

function getFlights()
{
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

function getAirportIndexByCode($airports, $code)
{
  for ($i = 0; $i < sizeof($airports); $i++) {
    if ($airports[$i]->code == $code) {
      return $i;
    }
  }

  return -1;
}

function transposeFlights($airports, $flights)
{
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
// flights = array of triple (from index, to index, price)
// src = airport id
// dest = airport id
// stops = max k stops
function cheapestFlights(int $num_airports, array $flights, int $src, int $dest, int $stops)
{
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

if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['searchFlights'])) {
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
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <!-- Header -->
  <header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color:#AD1457">
      <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item ">
              <a class="nav-link active text-white" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">Flights</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="#">F A Q</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- /Navbar -->
  </header>
  <!-- /Header -->

  <main>




    <div class="overflow-hidden"  style=" background: rgb(173,20,87);
background: linear-gradient(125deg, rgba(173,20,87,0.25) 0%, rgba(255,216,233,0.25) 100%);  ">
      <div class="container-fluid col-xxl-8">
        <div class="row flex-lg-nowrap align-items-center g-5">

          <div class="order-lg-1 w-100">
            <img style="clip-path: polygon(25% 0%, 100% 0%, 100% 99%, 0% 100%);" src="https://images.unsplash.com/photo-1553619948-505cc1cdc320?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80">
          </div>


          <div class="col-lg-6 col-xl-5 text-center text-lg-start pt-lg-5 mt-xl-4">
            <div class="lc-block mb-4">
              <div editable="rich">
                <h1 class="fw-bold display-3" style="background: #AD1457; background: radial-gradient(circle farthest-corner at center center, #AD1457 20%, #000000 91%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Get the cheapest flight with less stopovers</h1>
              </div>
            </div>

            <!-- Text block -->
            <div class="lc-block mb-5">
              <div editable="rich">
                <p class="rfs-8"> Try to create a PHP algorithm
                  that finds the lowest price, given
                  two different airport's code, assuming at most 2
                  stopovers!.
                </p>
              </div>
            </div>
            <!-- /Text block -->

            <div class="container">
              <form action="index.php" method="POST" class="flex bg-white" style="border: 1px solid #AD1457;">
                <div class="m-3">
                  <label for="departure_airport">From</label>
                  <select name="src" id="src" class="">
                    <?php
                    for ($i = 0; $i < sizeof($airports); $i++) {
                      echo "<option value=\"" . $i . "\">" . $airports[$i]->name . "</option>";
                    }
                    ?>
                  </select>
                </div>

                <div class="m-3">
                  <label for="arrival_airport">To</label>
                  <select name="dest" id="dest">
                    <?php
                    for ($i = 0; $i < sizeof($airports); $i++) {
                      echo "<option value=\"" . $i . "\">" . $airports[$i]->name . "</option>";
                    }
                    ?>
                  </select>
                </div>

                <div class="m-3">
                  <label for="stops">Stops Number</label>
                  <input type="number" name="stops" value="0" min="0" />
                </div>


                <input type="submit" name="searchFlights" value="Search" class="m-3" />
              </form>

              <h4 class="display-6 mt-3" style="color: #AD1457;">
                <?php if ($flight_price > -1) {
                  echo "Flight Price: " . $flight_price . "€";
                } else {
                  echo "No Flight Available!";
                } ?>
              </h4>
            </div>
          </div>
        </div>

      </div>
    </div>
    </div>


  </main>

  <footer>
    <!-- Remove the container if you want to extend the Footer to full width. -->
    <div class="">
      <!-- Footer -->
      <footer class="text-center text-lg-start text-white " style="background-color:#AD1457">
        <!-- Grid container -->
        <div class="container p-4 pb-0">
          <!-- Section: Links -->
          <section class="">
            <!--Grid row-->
            <div class="row">
              <!-- Grid column -->
              <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold">
                  Cheapest_Flights
                </h6>
                <p>
                  In the links all the tools, references and ideas I used to solve this problem.
                </p>
              </div>
              <!-- Grid column -->

              <hr class="w-100 clearfix d-md-none" />

              <!-- Grid column -->
              <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold">Flights</h6>
                <p>
                  <a class="text-white" href="#">Milan MXP</a>
                </p>
                <p>
                  <a class="text-white" href="#">Milan LIN</a>
                </p>
                <p>
                  <a class="text-white" href="#">Bergamo BGY</a>
                </p>
              </div>
              <!-- Grid column -->

              <hr class="w-100 clearfix d-md-none" />

              <!-- Grid column -->
              <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold">
                  Useful links
                </h6>
                <p>
                  <a class="text-white" href="https://en.wikipedia.org/wiki/Bellman%E2%80%93Ford_algorithm">Bellman-Ford Algorithm</a>
                </p>
                <p>
                  <a class="text-white" href="https://medium.com/swlh/graph-dynamic-programming-heap-cheapest-flights-within-k-stops-e622ce956479">Case Study</a>
                </p>
                <p>
                  <a class="text-white" href="https://www.youtube.com/watch?v=5eIK3zUdYmE">Other Refs</a>
                </p>
                <p>
                  <a class="text-white" href="https://en.wikipedia.org/wiki/Knapsack_problem">Similar Problem</a>
                </p>
                <p>
                  <a class="text-white" href="https://www.google.com/">Help</a>
                </p>
              </div>

              <!-- Grid column -->
              <hr class="w-100 clearfix d-md-none" />

              <!-- Grid column -->
              <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h6 class="text-uppercase mb-4 font-weight-bold">Contacts</h6>
                <p><i class="fas fa-home mr-3"></i> Milan, Italy </p>
                <p><i class="fas fa-envelope mr-3"></i> casalinidonata@gmail.com</p>
              </div>
              <!-- Grid column -->
            </div>
            <!--Grid row-->
          </section>
          <!-- Section: Links -->

          <hr class="my-3">

          <!-- Section: Copyright -->
          <section class="p-3 pt-0">
            <div class="row d-flex align-items-center">
              <!-- Grid column -->
              <div class="col-md-7 col-lg-8 text-center text-md-start">
                <!-- Copyright -->
                <div class="p-3">
                  © 2022 Copyright:
                  <a class="text-white" href="https://github.com/LaScheggia?tab=repositories">Donata Casalini</a>
                </div>
                <!-- Copyright -->
              </div>
              <!-- Grid column -->

              <!-- Grid column -->
              <div class="col-md-5 col-lg-4 ml-lg-0 text-center text-md-end">
                <!-- Facebook -->
                <a class="btn btn-outline-light btn-floating m-1" class="text-white" href="https://www.linkedin.com/in/donata-casalini/"><i class="fab fa-linkedin"></i></a>

                <!-- Twitter -->
                <a class="btn btn-outline-light btn-floating m-1" class="text-white" href="https://github.com/LaScheggia?tab=repositories"><i class="fab fa-github"></i></a>

              </div>
              <!-- Grid column -->
            </div>
          </section>
          <!-- Section: Copyright -->
        </div>
        <!-- Grid container -->
      </footer>
      <!-- Footer -->
    </div>
    <!-- End of .container -->
  </footer>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>

</body>

</html>