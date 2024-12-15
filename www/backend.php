<?php
    header('Content-type: application/json');
    
    $conn = new mysqli('db', 'root', 'example', 'autovermietung');
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Datenbankverbindung fehlgeschlagen"]));
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $body = file_get_contents("php://input");
    parse_str($_SERVER['QUERY_STRING'], $params);

    function actionResult($response, $status_code = 200) {
        http_response_code($status_code);
        $result = [ "request" => [ "http_method" => $GLOBALS['method'], "query" =>  $GLOBALS['params'], "body" => $GLOBALS['body'] ], "response" => $response ];
        echo json_encode($result);
    }

    if($method == 'GET'){
        if($params['operation'] == 'list'){
            $resultArray = array();
            $result = $conn->query("SELECT * FROM fahrzeuge");
            while($row = $result->fetch_assoc()) { $resultArray[] = $row; }
            actionResult($resultArray);
            return;
        } elseif($params['operation'] == 'bookings'){
            if(!isset($_COOKIE['user'])){
                die(json_encode(["status" => "error", "message" => "Cookie wurde nicht initialisiert"]));
            }
            $resultArray = array();
            $result = $conn->query("SELECT * FROM buchungen where benutzer_id = {$_COOKIE['user']}");
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
            if (empty($json['benutzer_name']) || empty($json['fahrzeug_id']) || empty($json['dauer']) || empty($json['date'])) {
                actionResult(["status" => "error", "message" => "Alle Felder sind erforderlich"], 400);
                return;
            }
            $result = $conn->query("SELECT id FROM fahrzeuge WHERE id = {$json["fahrzeug_id"]} AND verfuegbar = 1 LIMIT 1;");
            if ($result->num_rows > 0) {
                $fahrzeug = $result->fetch_assoc();
                $insert_result = $conn->query("INSERT INTO buchungen (benutzer_name, fahrzeug_id, datum, dauer, benutzer_id) VALUES ('{$json['benutzer_name']}', {$json['fahrzeug_id']}, '{$json['date']}', {$json['dauer']}, {$_COOKIE['user']});");
                if($insert_result){
                    actionResult(["status" => "success", "message" => "", "buchungsnummer" => $conn->insert_id]);
                    return;
                } else {
                    actionResult(["error" => "Es gab einen Fehler beim Speichern"], 500);
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