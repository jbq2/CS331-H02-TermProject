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

$days = array("01","02","03","04","05","06","01","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$months = array("01","02","03","04","05","06","07","08","09","10","11","12");

$db = getDB();
$statement = $db->prepare("SELECT * FROM BRANCH");
try{
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $branches = $results;
}
catch(PDOException $e){
    echo "bad query (branches)";
}

$statement = $db->prepare("SELECT * FROM CAR_CLASS");
try{
   $statement->execute();
   $results = $statement->fetchAll(PDO::FETCH_ASSOC);
   $classes = $results; 
}
catch(PDOException $e){
    echo "bad query (classes)";
}
?>

<div>
    <form method="POST" onsubmit="return validate(this)">
        <!-- <label for="day">Day</label>
        <select name="day">
            <?php foreach($days as $day) : ?>
                <option value="<?php se($day) ?>"><?php se($day) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="month">Month</label>
        <select name="month">
            <?php foreach($months as $month) : ?>
                <option value="<?php se($month) ?>"><?php se($month) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="year">Year</label>
        <input type="text" name="year" /> -->
        <label for="timein">Time In (YYYY-MM-DD HH:MM)</label>
        <input type="text" name="timein" />
        <label for="timeout">Time Out (YYYY-MM-DD HH:MM)</label>
        <input type="text" name="timeout" />
        <label for="branch">Branch</label>
        <select name="branch">
            <?php foreach($branches as $branch) : ?>
                <option value="<?php se($branch["LocationID"]) ?>"> <?php se($branch["LocationID"]) ?> </option>
            <?php endforeach; ?>
        </select>
        <label for="class">Class</label>
        <select name="class">
            <?php foreach($classes as $class) : ?>
                <option value="<?php se($class["ClassID"]) ?>"><?php se($class["ClassID"]) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="license_n">Customer License Number</label>
        <input type="text" name="license_n" />
        <label for="license_s">Customer License State</label>
        <input type="text" name="license_s" />
        <label for="fname">Customer First Name</label>
        <input type="text" name="fname" />
        <labeL for="minit">Customer Middle Initial</labeL>
        <input type="text" name="minit" />
        <labeL for="lname">Customer Last Name</labeL>
        <input type="text" name="lname" />

        <input type="submit" value="File Reservation"/>
    </form>
</div>

<script>
    function validate(form){
        let year = form.year.value;
        let timein = form.timein.value;
        let timeout = form.timeout.value;
        let license = form.license_n.value;
        let isValid = true;


        if(!/[0-9]{4}/.test(year)){
            isValid = false;
        }

        if(!/[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}/.test(year)){
            isValid = false;
        }

        if(!/[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}/.test(year)){
            isValid = false;
        }
        
        if(license.length > 15){
            isValid = false;
        }

        return isValid;
    }
</script>

<?php 
//TODO make cardnum in CUSTOMER not null and remove any direct inserts to cardnum in here
$fname = se($_POST, "fname", "", false);
if(strlen($fname) > 0){
    $minit = se($_POST, "minit", "", false);
    $lname = se($_POST, "lname", "", false);
    $cardnum = "9999999999999999"; //TODO TEMPORARY
    $licensenum = se($_POST, "license_n", "", false);
    $licensestate = se($_POST, "license_s", "", false);
    
    $statement = $db->prepare("INSERT INTO CUSTOMER (LicenseNumber, LicenseState, FName, MInit, LName, CardNum)
    VALUES (:licensen, :state, :fname, :minit, :lname, :cardn)");
    try{
        $statement->execute([":licensen" => $licensenum, ":state" => $licensestate, ":fname" => $fname, ":minit" => $minit, ":lname" => $lname, ":cardn" => $cardnum]);
    
        $timein = se($_POST, "timein", "", false);
        $timein = "'" . $timein . ":00" . "'";
        $timeout = se($_POST, "timeout", "", false);
        $timeout =  "'". $timeout . ":00" . "'";
        $loc = se($_POST, "branch", "", false);
        $class = se($_POST, "class", "", false);
        
        $statement = $db->prepare("INSERT INTO RESERVATION (DateTimeIn, DateTimeOut, LocationID, ClassID, LicenseNumber)
        VALUES (TIMESTAMP( $timein), TIMESTAMP( $timeout), :loc, :class, :licensen)");
        try{
            $statement->execute([":loc" => $loc, ":class" => $class, ":licensen" => $licensenum]);
            echo "success!";
        }
        catch(PDOException $e){
            echo $e;
        }
    }
    catch(PDOException $e){
        //TODO handle entering an already existing customer (duplicate license number)
        echo $e;
    }
}
?>

<style>
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
</style>
