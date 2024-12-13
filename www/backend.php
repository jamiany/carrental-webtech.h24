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
            while($row = $result->fetch_assoc()) { 
                $resultArray[] = $row;
            }
            
            echo json_encode($resultArray);
            return;
        }
    } else if($method == 'POST'){
        // if($params['operation'] == 'book'){
        //     $json = json_decode($body)
        //     if (empty($json['benutzer_name']) || empty($json['fahrzeug_id']) || empty($json['dauer']) || empty($json['date'])) {
        //         echo json_encode(["status" => "error", "message" => "Alle Felder sind erforderlich"]);
        //         exit;
        //     }

        //     $result = $conn->query("SELECT id FROM fahrzeuge WHERE id = '$json['fahrzeug_id']' AND verfuegbar = 1 LIMIT 1");
        //     if ($result->num_rows > 0) {
        //         $fahrzeug = $result->fetch_assoc();
        //         $conn->query("INSERT INTO buchungen (benutzer_name, fahrzeug_id, datum, dauer) VALUES ({$json['benutzer_name']}, {$fahrzeug['id']}, {$json['datum']}, {$json['dauer']})");
        //         echo json_encode(["status" => "success", "message" => "Buchung erfolgreich", "buchungsnummer" => $conn->insert_id]);
        //     } else {
        //         echo json_encode(["status" => "error", "message" => "Keine Fahrzeuge verfügbar"]);
        //     }
        // }
    }

    $conn->close();
?>