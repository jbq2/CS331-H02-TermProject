<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<?php

require_once(__DIR__ . "/../lib/db.php");

function se($v, $k = null, $default = "", $isEcho = true) {
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
function safer_echo($v, $k = null, $default = "", $isEcho = true){
  return se($v, $k, $default, $isEcho);
}

$db = getDB();
$statement = $db->prepare("SELECT R1.ReservationID, R1.DateTimeIn, R1.DateTimeOut, R1.LocationID, R1.ClassID, R1.LicenseNumber 
    FROM RESERVATION R1 LEFT JOIN
	(SELECT R2.ReservationID, R2.DateTimeIn, R2.DateTimeOut, R2.LocationID, R2.ClassID, R2.LicenseNumber
    FROM RESERVATION R2 WHERE R2.ReservationID IN (SELECT A.ReservationID FROM AGREEMENT A)) R3
    ON R1.ReservationID = R3.ReservationID
    WHERE R3.ReservationID IS NULL");
$reservations = [];
try{
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $reservations = $results;
}
catch(PDOException $e){
    echo "bad query (reservations, pre-delete) $e";
}

// $statement = $db->prepare("SELECT * FROM CUSTOMER WHERE CardNum IS NULL");
// $customerCardsNull = [];
// try{
//     $statement->execute();
//     $results = $statement->fetchAll(PDO::FETCH_ASSOC);
//     $customerCardsNull = $results;
// }
// catch(PDOException $e){
//     echo "bad querry (getting customers with no entered card)";
// }

$statement = $db->prepare("SELECT * FROM CAR C INNER JOIN CAR_MODEL CM ON (C.ModelName = CM.ModelName AND C.ModelYear = CM.ModelYear)
ORDER BY ClassID");
$cars = [];
try{
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $cars = $results;
}
catch(PDOException $e){
    echo "bad querry (fetching cars)";
}

$statement = $db->prepare("SELECT * FROM AGREEMENT");
$agreements = [];
try{
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $agreements = $results;
}
catch(PDOException $e){
    echo "bad querry (fetching agreements)";
}
?>

<div class="outerDiv">
    <h1>Agreements</h1>
    <div class="flex-container" >
        <div>
            <h3>Create Agreement</h3>
            <form method="POST" onsubmit="return validate(this)">
                <label for="resid">Reservation ID</label>
                <select name="resid">
                    <?php foreach($reservations as $reservation) : ?>
                        <option value="<?php se($reservation, "ReservationID") ?>"><?php se($reservation, "ReservationID") ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="rentstart">Start Date/Time (YYYY-MM-DD HH:MM)</label>
                <input type="text" name="renstart" /> 
                <label for="odomstart">Current Mileage</label>
                <input type="text" name="odomstart" />
                <label for="car">Car</label>
                <select name="car">
                    <?php foreach($cars as $car) : ?>
                        <option value="<?php se($car, "VIN") ?>"><?php echo $car["Make"] . " " . $car["ModelName"] . "; Class: " . $car["ClassID"] ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="card">Card</label>
                <input type="text" name="card" /> 
                <input type="submit" value="File Agreement" />
            </form>
        </div>

        <div>
            <h3 style="margin-left:70px">Pending Reservations</h3>
            <table>
                <tr>
                    <td>Reservation ID</td>
                    <td>Customer License</td>
                    <td>Location ID</td>
                    <td>Time In</td>
                    <td>Time Out</td>
                    <td>Class ID</td>
                </tr>
                <?php foreach($reservations as $reservation) : ?>
                    <tr>
                        <td><?php se($reservation["ReservationID"]) ?></td>
                        <td><?php se($reservation["LicenseNumber"]) ?></td>
                        <td><?php se($reservation["LocationID"]) ?></td>
                        <td><?php se($reservation["DateTimeIn"]) ?></td>
                        <td><?php se($reservation["DateTimeOut"]) ?></td>
                        <td><?php se($reservation["ClassID"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div style="margin-bottom:25px; margin-left:0px">
        <div>
            <h3 style="margin-left:50px; margin-top:25px">All Agreements</h3>
            <table style="width:90%;margin-left:0px">
                <tr>
                    <td>Contract Number</td>
                    <td>Reservation ID</td>
                    <td>Car VIN</td>
                    <td>Rental Start</td>
                    <td>Rental End</td>
                </tr>
                <?php foreach($agreements as $agreement) : ?>
                    <tr>
                        <td><?php se($agreement, "ContractNum") ?></td>
                        <td><?php se($agreement, "ReservationID") ?></td>
                        <td><?php se($agreement, "VIN") ?></td>
                        <td><?php se($agreement, "RentStart") ?></td>
                        <td><?php se($agreement, "RentEnd") ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- <div>
            <h3 style="margin-left:70px">Inactive Agreements</h3>
            table of inactive agreements (current date is past the end date)
        </div> -->
    </div>
</div>

<script>
    function validate(form){
        let rentstart = form.rentstart.value;
        let odomstart = form.odomstart.value;
        let card = form.card.value;
        let isValid = true;

        if(!/[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}/.test(rentstart)){
            isValid = false;
        }

        if(!/[0-9]+/.test(odomstart)){
            isValid = false;    
        }

        if(!/[0-9]{13,19}/.test(card)){
            isValid = false;
        }

        return isValid;
    }
</script>

<?php 

?>

<style>
    .outerDiv{
        margin-left:30px
    }
    label{
        display:block;
    }

    input{
        display:block;
        margin-bottom:15px;
    }

    select{
        margin-bottom:15px;
    }

    .flex-container{
        margin:auto;
        display:flex;
    }
    .flex-child{
        flex:1;
    }
    table{
        width:95%;
        margin-left:60px;
    }
    td{
        text-align:center;
        height:50px;
        padding-left:10px;
        padding-right:10px;
        border:1px;
    }
</style>
