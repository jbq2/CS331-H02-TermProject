<?php
require_once(__DIR__ . "/db.php");
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

function carCount($model, $year) {
    $db = getDB();
    $query = "SELECT MODELNAME, MODELYEAR, COUNT(*) as 'Total' FROM CAR WHERE MODELNAME = :modelname AND MODELYEAR = :modelyear GROUP BY :modelname, :modelyear"; 
    error_log($query);
    $stmt = $db->prepare($query);
    $results = [];
    try {
        $stmt->execute([
            ":modelname" => $model, 
            ":modelyear" => $year
        ]);
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $results = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching cars: " . var_export($e->errorInfo, true));
    }
    return $results[0]["Total"];
}


?>