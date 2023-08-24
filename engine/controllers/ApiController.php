<?php
require_once "./engine/global_var.php";
class ApiController
{
    public function showUsers()
    {
        // Retrieve and respond with user data
        // echo json_encode(['users' => ['user1', 'user2']]);
        $return = ['users' => ['user1', 'user2']];
        sendResponse(responseCode("OK"), 0, "Success", $return);
    }

    public function showUser($id)
    {
        // Retrieve and respond with user data based on $userId
        // echo json_encode(['user' => ['id' => $userId, 'name' => 'John']]);
        sendResponse(responseCode("OK"), 0, "Success", $id);
    }

    public function createUser($data)
    {
        // Process the $data JSON and create a new user
        // ...
        sendResponse(responseCode("OK"), 0, "Success", $data);
    }
    public function putUser($data)
    {
        // Process the $data JSON and create a new user
        // ...
        sendResponse(responseCode("OK"), 0, "Data yang ter PUT", $data);
    }
    public function deleteUser($code)
    {
        // Process the $data JSON and create a new user
        // ...
        sendResponse(responseCode("OK"), 0, "Success", "Code yang ter delete: ".$code);
    }
}
