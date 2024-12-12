<?php
    header('Content-type: application/json');
    $method = $_SERVER['REQUEST_METHOD'];
    $body = file_get_contents("php://input");
    parse_str($_SERVER['QUERY_STRING'], $params);

    if($method == 'GET'){
        if($params['operation'] == 'list'){
            $person = [
                [ 
                    'name' => 'Volkswagen',
                    'modell' => 'Kombi'
                ],
                [ 
                    'name' => 'Skoda',
                    'modell' => 'Octavia'
                ]
            ];

            $personJSON = json_encode($person);
            echo($personJSON);
            return;
        }
    }
?>