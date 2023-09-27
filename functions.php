<?php
// function pour se connecter à la db
function dbConnect(){

    $conn = null;

    try {
        $conn = new PDO("mysql:host=localhost;dbname=api_db", "root","");

        } catch (PDOException $e) {

            $conn = $e->getMessage();
        }

        return $conn;
}

// function pour enregistrer un utilisateur
function register($firstname,$lastname, $pseudo,$password){

    // hasher le mdp
    $passwordCrypt = password_hash($password, PASSWORD_DEFAULT);

    // connexion a la db
    $db = dbConnect();

    // préparer la requête
    $request = $db->prepare("INSERT INTO users (pseudo, firstname,lastname, password) VALUES (?,?,?,?)");

    // exécuter la requête
    try {
        $request->execute(array($pseudo, $firstname, $lastname, $passwordCrypt));
        echo json_encode([
            "status" => 201,
            "message" => "everything good"
        ]);
        
        } catch (PDOException $e) {

            echo json_encode([
                "status" => 500,
                // "message" => "internal server error"
                "message" => $e->getMessage()
            ]);
        }
}

// function pour se connecter
function login($pseudo, $password){
    // se connecter a la bd
    $db = dbConnect();

    // préparer la requete pour se connecter
    $request = $db->prepare("SELECT * FROM users WHERE pseudo = ?");

    // exécuter la requête
    try {
        $request->execute(array($pseudo));

        // récupérer la réponse de la requête
        $user = $request->fetch(PDO::FETCH_ASSOC);

        // on vérifie si l'utilisateur éxiste
        if(empty($user)){
            echo json_encode([
                "status" =>404,
                "message" => "user not found",
            ]);

            }else{
                // vérifier si le password est correct
                if(password_verify($password, $user['password'])){
                    echo json_encode([
                        "status" =>200,
                        "message" => "félicitation...",
                        "userInfo" => $user   
                        ]);

                        }else{

                            // si le password est incorrect
                            echo json_encode([
                                "status" =>401,
                                "message" => "password incorrect"

                                ]);
                            }
                    }

    } catch (PDOException $e) {

            echo json_encode([
                "status" =>500,
                "message" => $e->getMessage()

            ]);
        }
 }

// fonction pour envoyer un message
function sendMessage($expeditor,$receiver,$message){

    // se connecter a la db
    $db = dbConnect();

    // préparer la requête
    $request = $db->prepare("INSERT INTO messages (message,expeditor_id, receiver_id) VALUES (?,?,?)");

    // exécuter la requête
    try {
        $request->execute(array($message, $expeditor, $receiver));
        echo json_encode([
            "status" => 201,
            "message" => "your message is safely sent..."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "status" => 500,
            "message" => $e->getMessage()
        ]);
    }
}

// fonction pour récupérer la liste des users
function getListUser(){
    // se connecter à la db
    $db = dbConnect();

    // préparer la requête
    $request = $db->prepare("SELECT * FROM users ");

    try{
    // exécuter la requête
    $request->execute();

    // récuperer le résultat
    $listUsers = $request->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "status" => 200,
            "message" => "voici la liste des users",
            "users" => $listUsers
    ]);
    }catch(PDOException $e){
        echo json_encode([
            "status" => 500,
            "message" => $e->getMessage()
        ]);
    }
}

// fonction pour récupérer la conversation entre 2 users
function getListMessage($expeditor,$receiver) {
     // se connecter à la db
     $db = dbConnect();

     // préparer la requête
     $request = $db->prepare("SELECT * FROM messages WHERE expeditor_id = ? AND receiver_id = ? OR expeditor_id = ? AND receiver_id = ?");
 
     // exécuter la requête
     try{
        $request->execute(array($expeditor,$receiver,$receiver,$expeditor));
    
        // récuperer le résultat
        $messages = $request->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "status" => 200,
            "message" => "voici la liste de votre discution",
            "listMessage" => $messages
        ]);
    }catch(PDOException $e){
        echo json_encode([
            "status" => 500,
            "message" => $e->getMessage()
        ]);
    }
}
?>