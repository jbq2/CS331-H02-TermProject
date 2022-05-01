<?php include_once(__DIR__ . "/../lib/navbar.php"); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">

    <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/jumbotron/">

    

    <!-- Bootstrap core CSS -->
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
  </head>
  <body>
    
<main>
  <div class="container py-4">
    <div class="p-5 mb-4 bg-light rounded-3">
      <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Rent-A-Car</h1>
        <p class="col-md-8 fs-4">Rent a car from our vast inventory for low prices at our various branches!</p>
        <button class="btn btn-primary btn-lg" href="carslist.php" type="button" onclick="window.location.href='reservation.php'">Create a Reservation</button>
      </div>
    </div>

    <div class="row align-items-md-stretch">
      <div class="col-md-6">
        <div class="h-100 p-5 text-white bg-dark rounded-3">
          <h2>View our cars!</h2>
          <p>We offer various cars at great rates:</p>
          <p>Class 1 - Sedan: $30.99 Daily/$120.99 Weekly
              <br> Class 2 - Hatchback: $18.00 Daily/$75.00 Weekly
              <br> Class 3 - SUV: $49.99 Daily/$179.99 Weekly
              <br> Class 4 - Minivan: $23.99 Daily/$99.99 Weekly
              <br> Class 5 - Truck: $24.99 Daily/$84.99 Weekly
          </p>
          <button class="btn btn-secondary btn-outline-light" type="button" onclick="window.location.href='carslist.php'">Our Cars</button>
        </div>
      </div>
      <div class="col-md-6">
        <div class="h-100 p-5 bg-light border rounded-3">
          <h2>Visit our branches!</h2>
          <p>We have multiple locations in NJ!</p>
          <p>Branch 1 - 142 Jefferson Way, Newark, NJ (862) 443-0011
              <br> Branch 2 - 142 Jefferson Way, Newark, NJ (973) 545-2899
              <br> Branch 3 - 683 Tinc Road, Glen Ridge, NJ (973) 123-1234
              <br> Branch 4 - 50 Fifth Street, Princeton, NJ (464) 332-9681
              <br> Branch 5 - 310 Violet Road, Miami, FL (234) 567-8901
          </p>
        </div>
      </div>
    </div>

    <footer class="pt-3 mt-4 text-muted border-top">
      &copy; 2021
    </footer>
  </div>
</main>


    
  </body>
</html>

<?php include_once(__DIR__ . '/styles.css'); ?>
