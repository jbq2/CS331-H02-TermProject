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

$resToCancel = se($_POST, "cancel", "", false); 
if(!empty($resToCancel)){
    $statement = $db->prepare("DELETE FROM RESERVATION WHERE ReservationID = :resID");
    try{
        $statement->execute([":resID" => $resToCancel]);
    }
    catch(PDOException $e){
        echo "Failed to delete reservation $resToCancel";
    }
}

$statement = $db->prepare("SELECT * FROM RESERVATION");
$reservations = [];
try{
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $reservations = $results;
}
catch(PDOException $e){
    echo "bad query (reservations, pre-delete)";
}
?>

<div>
    <h1>Cancel Reservation</h1>
    <table>
        <tr>
            <td>Cancel</td>
            <td>Reservation ID</td>
            <td>Customer License</td>
            <td>Location ID</td>
            <td>Time In</td>
            <td>Time Out</td>
            <td>Class ID</td>
        </tr>
        <?php foreach($reservations as $reservation) : ?>
            <tr>
                <td>
                    <form method="POST">
                        <button name="cancel" type="submit" value="<?php se($reservation, "ReservationID") ?>">X</button>
                    </form>
                </td>
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


<style>
    table{
        width:95%;
        margin-left:40px;
    }
    td{
        text-align:center;
        height:50px;
        padding-left:10px;
        padding-right:10px;
        border:1px;
    }
</style>