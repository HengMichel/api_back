<?php 
header(("Access-Control-Allow-Origin: *"));
// inclure function.php
require_once "functions.php";

// "REQUEST_METHOD" est une constante
if($_SERVER["REQUEST_METHOD"] == "GET"){

    $url = $_SERVER["REQUEST_URI"];
    $url = trim($url, "\/");

    // explode() permet d'avoir un affichage sous forme de tableau
    $url = explode("/",$url);
    $action = $url[1];

    if($action == "getuserlist"){
        getListUser();
    }
    else if($action == "getListMessage"){
        getListMessage($url[2],$url[3]);
    }else{
        echo json_encode([
            "status" =>404,
            "message" => "not found"
        ]);
    }

 }else{

    // ce que l'utilisateur envoi via un formulaire on le récupère dans la variable $data
    $data = json_decode(file_get_contents("php://input"), true);

    if($data['action'] == "login"){

        // appel de la fonction login
        login($data['pseudo'], $data['password']);

        }else if($data['action'] == "register"){

            // on fait appel à la function register pour enregistrer le user
            register($data['firstname'],$data['lastname'],$data['pseudo'],$data['password']);
            
            }else if($data['action'] == "send message"){
            
                // appel de la fonction sendMessage
                sendMessage($data['expeditor'],$data['receiver'],$data['message']);

                }else{
            
                    echo json_encode([
                        "status" => 404,
                        "message" => "service not found"
                    ]);
                }

}

?>