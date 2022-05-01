<?php
require_once(__DIR__ . "/../lib/db.php");
$db = getDB();
if(isset($_POST["ModelName"]) && isset($_POST["ModelYear"]) && isset($_POST["Make"]) && isset($_POST["VIN"]) && isset($_POST["LocationID"]) && isset($_POST["ClassID"])){ 
    $data = $_POST;

    // echo 'got to 1';
    $query = "INSERT INTO CAR_MODEL VALUES(:ModelName, :ModelYear, :Make)";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([
        ":ModelName" => $data['ModelName'], 
        ":ModelYear" => $data['ModelYear'], 
        ":Make" => $data['Make']
    ]);
    // echo 'Successfully added car1';
    } catch (PDOException $e) {
        echo 'Error adding car';
    }

    // echo 'got to 2';
    $query = "INSERT INTO CAR VALUES(:VIN, :LocationID, :ModelName, :ModelYear, :ClassID)";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([
        ":VIN" => $data['VIN'], 
        ":LocationID" => $data['LocationID'],
        ":ModelName" => $data['ModelName'],
        ":ModelYear" => $data['ModelYear'], 
        ":ClassID" => $data['ClassID']
    ]);
    echo 'Successfully added car!';
    } catch (PDOException $e) {
        echo 'Error adding car2';
    }
}   
// elseif (isset($_POST)) {
//     echo "Please fill out all fields!";
//     // var_dump($_POST);
// }

$stmt = $db->prepare("SELECT * FROM BRANCH");
$branches = [];
try{
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $branches = $results;
}
catch(PDOException $e){
    echo "bad query (branches)";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<h1>Add Car Model</h1>
<form form name="addCar" onsubmit="return validateCar()" method="POST">
  <div class="row mb-3">
    <label for="Model" class="col-sm-2 col-form-label">Model</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="Model" name="ModelName" required>
    </div>
  </div>
  <!-- <div class="mb-3">
    <label for="Year" class="form-label">Model Year</label>
    <input type="text" class="form-control" id="Year" name="ModelYear">
  </div> -->
  <div class="row mb-3">
    <label for="ClassID" class="col-sm-2 col-form-label">Year</label>
    <div class="col-sm-9">
        <select class="custom-select custom-select-sm" id="Year" name="ModelYear" required>
            <?php for($i = 1970; $i <= 2022; $i++) :?>
            <option value= <?php echo $i ?>><?php echo $i ?></option>
            <?php endfor; ?>
        </select>  
    </div>
  </div>
  <div class="row mb-3">
    <label for="Make" class="col-sm-2 col-form-label">Make</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="Make" name="Make" required>
    </div>
  </div>
  <div class="row mb-3">
    <label for="vin" class="col-sm-2 col-form-label">VIN</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="vin" name="VIN" required>
    </div>
  </div>
  <div class="row mb-3">
    <label for="LocationID" class="col-sm-2 col-form-label">Location</label>
    <!-- <div class="col-sm-9">
        <input type="text" class="form-control" id="LocationID" name="LocationID">
    </div> -->
    <div class="col-sm-9">
        <select class="custom-select custom-select-sm" id="LocationID" name="LocationID" required>
            <?php foreach($branches as $branch) :?>
            <option value=<?php echo $branch["LocationID"] ?>> <?php echo $branch["Address"] ?> </option>
            <?php endforeach; ?>
        </select>  
    </div>
  </div>
  <div class="row mb-3">
    <label for="ClassID" class="col-sm-2 col-form-label">Class</label>
    <div class="col-sm-9">
        <select class="custom-select custom-select-sm" id="ClassID" name="ClassID">
            <option value="1">Sedan</option>
            <option value="2">Hatchback</option>
            <option value="3">SUV</option>
            <option value="4">Minivan</option>
            <option value="5">Truck</option>
        </select>  
    </div>
  </div>
  <button type="reset" class="btn btn-primary">Reset</button>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script>
    function validateCar(){
        let model = document.forms["addCar"]["ModelName"].value;
        let make = document.forms["addCar"]["Make"].value;
        let vin = document.forms["addCar"]["VIN"].value;
        let isValid = true;

        if(model ""){
            alert("Model must be filled out");
            isValid = false;
        }

        if(make ""){
            alert("Make must be filled out");
            isValid = false;
        }

        if(vin ""){
            alert("VIN must be filled out");
            isValid = false;
        }

        return isValid;
    }
</script>

<style>
.center {
  margin-left: auto;
  margin-right: auto;
}
form { 
    margin: 0 auto; 
    width:600px;
}
h1 {
    text-align: center;
}

* {
 font-size: 100%;
 font-family: Source Sans Pro;
}
</style>