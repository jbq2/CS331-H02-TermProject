<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<?php 
include_once(__DIR__ . "/../lib/navbar.php");
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

if(isset($_POST["rentend"]) && isset($_POST["odomend"]) && isset($_GET["contnum"])){
    $contnum = $_GET["contnum"];
    $rentend = se($_POST, "rentend", "", false);
    $rentend = "'" . $rentend . ":00" . "'";
    $odomend = se($_POST, "odomend", "", false);

    $statement = $db->prepare("UPDATE AGREEMENT
    SET RentEnd = TIMESTAMP( $rentend), OdomEnd = :odomend
    WHERE ContractNum = :contnum");
    try{
        $statement->execute([":odomend" => $odomend, ":contnum" => $contnum]);
        echo "Successfully updated agreement with contract number $contnum";
    }
    catch(PDOException $e){
        echo "bad query (updating agreement) $e";
    }
}

$chosenAgreement = -1;
if(isset($_GET["contnum"])){
    $contnum = $_GET["contnum"];
    $statement = $db->prepare("SELECT * FROM AGREEMENT WHERE ContractNum = :contnum");
    try{
        $statement->execute([":contnum" => $contnum]);
        $results = $statement->fetch(PDO::FETCH_ASSOC);
        $chosenAgreement = $results;
    }
    catch(PDOException $e){
        "bad query (fetching chosen agreement) $e";
    }
}

$statement = $db->prepare("SELECT * FROM AGREEMENT WHERE RentEND IS NULL AND OdomEnd IS NULL");
$agreements = [];
try{
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $agreements = $results;
}
catch(PDOException $e){
    echo "bad query (fetching incomplete agreements) $e";
}
?>

<div class="outerDiv">
    <h1>Edit Agreements</h1>
    <div class="flex-container">
        <div>
            <!-- form to fill out -->
            <form method="GET">
                <label for="contnum">Contract Number</label>
                <select style="display:inline" name="contnum">
                    <option></option>
                    <?php foreach($agreements as $agreement) : ?>
                        <option value="<?php se($agreement, "ContractNum") ?>"><?php se($agreement, "ContractNum") ?></option>
                    <?php endforeach; ?>
                </select>
                <input style="display:inline" type="submit" value="Select" />
            </form>
            <form method="POST" onsubmit="return validate(this)">
                <label for="rentstart">Rental Start</label>
                <input type="text" name="rentstart" value="<?php ($chosenAgreement != -1) ? se($chosenAgreement, "RentStart") : '' ?>" disabled />
                <label for="rentend">Rental End (YYYY-MM-DD HH:MM)</label>
                <input type="text" name="rentend" />
                <label for="odomstart">Odometer Start Reading</label>
                <input type="text" name="odomstart" value="<?php ($chosenAgreement != -1) ? se($chosenAgreement, "OdomStart") : '' ?>" disabled />
                <label for="odomend">Odometer End Reading</label>
                <input type="text" name="odomend" />
                <label for="resid">Reservation ID</label>
                <input type="text" name="resid" value="<?php ($chosenAgreement != -1) ? se($chosenAgreement, "ReservationID") : '' ?>" disabled />
                <label for="vin">VIN</label>
                <input type="text" name="vin" value="<?php ($chosenAgreement != -1) ? se($chosenAgreement, "VIN") : '' ?>" disabled />
                <input type="submit" value="Confirm" />
            </form>
        </div>

        <div>
            <h3 style="margin-left:70px; margin-top:25px">Active Agreements</h3>
            <table>
                <tr>
                    <td>Contract Number</td>
                    <td>Reservation ID</td>
                    <td>Car VIN</td>
                    <td>Rental Start</td>
                    <td>Odometer Start</td>
                </tr>
                <?php foreach($agreements as $agreement) : ?>
                    <td><?php se($agreement, "ContractNum") ?></td>
                    <td><?php se($agreement, "ReservationID") ?></td>
                    <td><?php se($agreement, "VIN") ?></td>
                    <td><?php se($agreement, "RentStart") ?></td>
                    <td><?php se($agreement, "OdomStart") ?></td>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<script>
    function validate(form){
        let rentend = form.rentend.value;
        let odomend = form.odomend.value;
        let isValid = true;

        if(!/[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}/.test(rentend)){
            isValid = false;
        }

        if(!/[0-9]+/.test(odomend)){
            isValid = true;
        }

        return isValid;
    }
</script>

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