<?php
    header('Content-type: application/json');
    
    $conn = new mysqli('db', 'root', 'example', 'autovermietung');
    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Datenbankverbindung fehlgeschlagen"]));
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $body = file_get_contents("php://input");
    parse_str($_SERVER['QUERY_STRING'], $params);

    if($method == 'GET'){
        if($params['operation'] == 'list'){
            $resultArray = array();
            $result = $conn->query("SELECT * FROM fahrzeuge");
            while($row = $result->fetch_assoc()) { $resultArray[] = $row; }
            echo json_encode($resultArray);
            return;
        } elseif($params['operation'] == 'bookings'){
            $resultArray = array();
            $result = $conn->query("SELECT * FROM fahrzeuge");
            while($row = $result->fetch_assoc()) { $resultArray[] = $row; }
            echo json_encode($resultArray);
            return;
        }
    } elseif($method == 'POST'){
        if($params['operation'] == 'book'){
            if(!isset($_COOKIE['user'])){
                die(json_encode(["status" => "error", "message" => "Cookie wurde nicht initialisiert"]));
            }
            $json = json_decode($body, true);
            if (empty($json['benutzer_name']) || empty($json['fahrzeug_id']) || empty($json['dauer']) || empty($json['date'])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Alle Felder sind erforderlich"]);
                exit;
            }
            $result = $conn->query("SELECT id FROM fahrzeuge WHERE id = {$json["fahrzeug_id"]} AND verfuegbar = 1 LIMIT 1;");
            if ($result->num_rows > 0) {
                $fahrzeug = $result->fetch_assoc();
                $insert_result = $conn->query("INSERT INTO buchungen (benutzer_name, fahrzeug_id, datum, dauer, benutzer_id) VALUES ('{$json['benutzer_name']}', {$json['fahrzeug_id']}, '{$json['date']}', {$json['dauer']}, {$_COOKIE['user']});");
                if($insert_result){
                    echo json_encode(["status" => "success", "message" => "Buchung erfolgreich", "buchungsnummer" => $conn->insert_id]);
                } else {
                    http_response_code(500);
                    echo "Es gab einen Fehler beim Speichern";
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Keine Fahrzeuge verfügbar"]);
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