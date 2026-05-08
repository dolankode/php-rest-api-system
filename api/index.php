<?php

header("Content-Type: application/json");

include_once "../config/database.php";
include_once "../models/User.php";

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$stmt = $user->getUsers();
$num = $stmt->rowCount();

if ($num > 0) {
    $users_arr = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $user_item = [
            "id" => $id,
            "name" => $name,
            "email" => $email
        ];

        array_push($users_arr, $user_item);
    }

    echo json_encode($users_arr);
} else {
    echo json_encode(["message" => "No users found"]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"));

    $query = "INSERT INTO users (name, email) VALUES (:name, :email)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":email", $data->email);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User created"]);
    } else {
        echo json_encode(["message" => "Failed"]);
    }
}