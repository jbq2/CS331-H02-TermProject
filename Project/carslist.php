<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<h1>Cars We Offer!</h1>

<?php 
    require_once(__DIR__ . "/../lib/functions.php");
    $db = getDB();
    $query = "SELECT Make, CAR.ModelName, CAR.ModelYear, ClassID FROM CAR_MODEL, CAR WHERE CAR.ModelName = CAR_MODEL.ModelName AND CAR.ModelYear = CAR_MODEL.ModelYear"; 
    error_log($query);
    $stmt = $db->prepare($query);
    $results = [];
    try {
        $stmt->execute();
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $results = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching cars: " . var_export($e->errorInfo, true));
    }
?>

<div class="card bg-dark center" style="width:50%">
    <div class="card-body">
        <div class="card-title">
            <div class="fw-bold fs-3">
                <?php $Cars; ?>
            </div>
        </div>
        <div class="card-text">
            <table class="table text-light center" table id="carsTable" style="width:100%">
                <thead>
                    <th onclick="sortTable(0)"> Make</th>
                    <th onclick="sortTable(1)">Model Name</th>
                    <th onclick="sortTable(2)">Year</th>
                    <th onclick="sortTable(3)">Class</th>
                </thead>
                <tbody>
                    <?php if (!$results || count($results) == 0) : ?>
                        <tr>
                            <td colspan="100%">No Cars Available</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($results as $result) : ?>
                            <tr onclick="sortTable(0)">
                                <td><?php se($result, "Make"); ?></td>
                                <td><?php se($result, "ModelName"); ?></td>
                                <td><?php se($result, "ModelYear"); ?></td>
                                <td><?php se($result, "ClassID"); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // function to sort table when title is clicked from w3 schools
    function sortTable(n) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById("carsTable");
        switching = true;
        //Set the sorting direction to ascending:
        dir = "asc"; 
        /*Make a loop that will continue until
        no switching has been done:*/
        while (switching) {
          //start by saying: no switching is done:
          switching = false;
          rows = table.rows;
          /*Loop through all table rows (except the
          first, which contains table headers):*/
          for (i = 1; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare,
            one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /*check if the two rows should switch place,
            based on the direction, asc or desc:*/
            if (dir == "asc") {
              if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                //if so, mark as a switch and break the loop:
                shouldSwitch= true;
                break;
              }
            } else if (dir == "desc") {
              if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                //if so, mark as a switch and break the loop:
                shouldSwitch = true;
                break;
              }
            }
          }
          if (shouldSwitch) {
            /*If a switch has been marked, make the switch
            and mark that a switch has been done:*/
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            //Each time a switch is done, increase this count by 1:
            switchcount ++;      
          } else {
            /*If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again.*/
            if (switchcount == 0 && dir == "asc") {
              dir = "desc";
              switching = true;
            }
          }
        }
      }
</script>

<style>
.center {
  margin-left: auto;
  margin-right: auto;
}
h1 {
  text-align: center;
}
th {
cursor: pointer;
}
</style>