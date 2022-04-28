<?php 
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

$days = array("01",
"02",
"03",
"04",
"05",
"06",
"01",
"08",
"09",
"10",
"11",
"12",
"13",
"14",
"15",
"16",
"17",
"01",
"18",
"19",
"20",
"21",
"22",
"23",
"24",
"25",
"26",
"27",
"28",
"29",
"30",
"31");

$months = array("01",
"02",
"03",
"04",
"05",
"06",
"07",
"08",
"09",
"10",
"11",
"12",);
?>

<div>
    <form method="POST">
        <label for="day">Day</label>
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
        <label for="timein">Time In</label>
        <input type="text" name="timein" />
        <label for="timeout">Time Out</label>
        <input type="text" name="timeout" />
        <label for="branch">Branch</label>
        <select name="branch">
            <!-- fill with options -->
        </select>
        <label for="class">Class</label>
        <select name="class">
            <!-- fill with options -->
        </select>
        <label for="license"></label>
        <input type="text" name="license" />

        <input type="submit" value="File Reservation"/>
    </form>
</div>