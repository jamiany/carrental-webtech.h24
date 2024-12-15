<?php
    header('Content-type: application/json');
    
    $conn = new mysqli('db', 'root', 'example', 'autovermietung');
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Datenbankverbindung fehlgeschlagen"]));
    }

    $current_date = new DateTime('now');
    $method = $_SERVER['REQUEST_METHOD'];
    $body = file_get_contents("php://input");
    parse_str($_SERVER['QUERY_STRING'], $params);

    function actionResult($response, $status_code = 200) {
        http_response_code($status_code);
        $result = [ "request" => [ "http_method" => $GLOBALS['method'], "query" =>  $GLOBALS['params'], "body" => $GLOBALS['body'] ], "response" => $response ];
        echo json_encode($result);
    }

    function validateInput($json) {
        $error = NULL;
        if(empty($json['benutzer_name']) || strlen($json['benutzer_name']) < 3) {
            $error = ['error' => "field 'benutzer_name' not set or length is smaller than 3"];
        } elseif(empty($json['fahrzeug_id']) || !is_numeric($json['fahrzeug_id'])) {
            $error = ['error' => "field 'fahrzeug_id' not set or has invalid value (not numeric)"];
        } elseif(empty($json['dauer']) || !is_numeric($json['dauer']) || $json['dauer'] < 1 || $json['dauer'] > 12) {
            $error = ['error' => "field 'dauer' not set or has invalid value (not numeric) or is not in the range between 1 and 12"];
        } elseif(empty($json['date']) || ($input_date = strtotime($json['date'])) === false || $input_date < strtotime("now") || $input_date > strtotime("+12 months")) {
            $error = ['error' => "field 'date' not set or has invalid value (not numeric) or is not in the range between today and in 12 months"];
        }
        
        return $error;
    }

    if($method == 'GET'){
        if($params['operation'] == 'list'){
            $resultArray = array();
            $stmt = $conn->prepare("SELECT * FROM fahrzeuge");
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) { $resultArray[] = $row; }
            actionResult($resultArray);
            return;
        } elseif($params['operation'] == 'bookings'){
            if(!isset($_COOKIE['user'])){
                actionResult(["status" => "error", "message" => "Cookie wurde nicht initialisiert"]);
                return;
            }
            $resultArray = array();
            $stmt = $conn->prepare("SELECT * FROM buchungen where benutzer_id = ?");
            $stmt->bind_param("s", $_COOKIE['user']);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) { $resultArray[] = $row; }
            actionResult($resultArray);
            return;
        }
    } elseif($method == 'POST'){
        if($params['operation'] == 'book'){
            if(!isset($_COOKIE['user'])){
                actionResult(["status" => "error", "message" => "Cookie wurde nicht initialisiert"]);
                return;
            }
            $json = json_decode($body, true);
            // if (empty($json['benutzer_name']) || empty($json['fahrzeug_id']) || empty($json['dauer']) || empty($json['date'])) {
            //     actionResult(["status" => "error", "message" => "Alle Felder sind erforderlich"], 400);
            //     return;
            // }
            $validateError = validateInput($json);
            if(isset($validateError)){
                actionResult($validateError, 400);
                return;
            }

            $stmt = $conn->prepare("SELECT id FROM fahrzeuge WHERE id = ? AND verfuegbar = 1 LIMIT 1;");
            $stmt->bind_param("i", $json["fahrzeug_id"]);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $fahrzeug = $result->fetch_assoc();
                $_expire_date = new DateTime('now');
                $_expire_date->add(new DateInterval("P0Y{$json['dauer']}M0W0D"));
                $query_str = "INSERT INTO buchungen (benutzer_name, fahrzeug_id, datum, dauer, benutzer_id, created, expire_date) VALUES (?, ?, ?, ?, ?, ?, ?);";
                $stmt_insert = $conn->prepare($query_str);
                $stmt_insert->bind_param("sisiiss", $benutzer_name, $fahrzeug_id, $datum, $dauer, $benutzer_id, $created, $expire_date);
                $benutzer_name = $json['benutzer_name'];
                $fahrzeug_id = $json['fahrzeug_id'];
                $datum = $json['date'];
                $dauer = $json['dauer'];
                $benutzer_id = $_COOKIE['user'];
                $created = $current_date->format('Y-m-d');
                $expire_date = $_expire_date->format('Y-m-d');
                $insert_result = $stmt_insert->execute();
                if($insert_result){
                    actionResult(["status" => "success", "message" => "", "buchungsnummer" => $conn->insert_id]);
                    return;
                } else {
                    actionResult(["error" => "Es gab einen Fehler beim Speichern", "query" => $query_str], 500);
                    return;
                }
            } else {
                actionResult(["status" => "error", "message" => "Keine Fahrzeuge verfügbar"], 404);
                return;
            }
        }
    } elseif($method == 'PUT') {
        if($params['operation'] == 'initcookie') {
            if(!isset($_COOKIE['user'])){
                setcookie("user", rand(), time() + 3600 * 24 * 365);
            }
        }
    }

    $conn->close();
?>