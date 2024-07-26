<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $preference_id = $_POST['preference_id'];
    $message_text = $_POST['message'];
    $sender_id = $_SESSION['user_id'];
    
    // Fetch the receiver's user_id based on preference_id
    $receiver_id = null;
    $sql_receiver = "SELECT user_id FROM Travel_Preferences WHERE preference_id = :preference_id";
    try {
        $stmt_receiver = $conn->prepare($sql_receiver);
        $stmt_receiver->bindParam(':preference_id', $preference_id, PDO::PARAM_INT);
        $stmt_receiver->execute();
        $result = $stmt_receiver->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $receiver_id = $result['user_id'];
        }
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }

    if ($receiver_id) {
        // Check if a conversation already exists
        $conversation_id = null;
        $sql_conversation = "SELECT conversation_id FROM Messages 
                             WHERE preference_id = :preference_id 
                             AND ((sender_id = :sender_id1 AND receiver_id = :receiver_id1) 
                             OR (sender_id = :receiver_id2 AND receiver_id = :sender_id2)) 
                             LIMIT 1";
        try {
            $stmt_conversation = $conn->prepare($sql_conversation);
            $stmt_conversation->bindParam(':preference_id', $preference_id, PDO::PARAM_INT);
            $stmt_conversation->bindParam(':sender_id1', $sender_id, PDO::PARAM_INT);
            $stmt_conversation->bindParam(':receiver_id1', $receiver_id, PDO::PARAM_INT);
            $stmt_conversation->bindParam(':sender_id2', $receiver_id, PDO::PARAM_INT);
            $stmt_conversation->bindParam(':receiver_id2', $sender_id, PDO::PARAM_INT);
            $stmt_conversation->execute();
            $conversation = $stmt_conversation->fetch(PDO::FETCH_ASSOC);
            if ($conversation) {
                $conversation_id = $conversation['conversation_id'];
            } else {
                // No existing conversation, create a new conversation_id
                $conversation_id = uniqid('conv_', true); // Generating a unique conversation ID
            }
        } catch (PDOException $e) {
            echo 'Error checking for existing conversation: ' . $e->getMessage();
            exit();
        }

        // Insert the new message with the determined conversation_id
        $sql_insert = "INSERT INTO Messages (conversation_id, sender_id, receiver_id, message_text, preference_id) 
                       VALUES (:conversation_id, :sender_id, :receiver_id, :message_text, :preference_id)";
        try {
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bindParam(':conversation_id', $conversation_id, PDO::PARAM_STR);
            $stmt_insert->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':message_text', $message_text, PDO::PARAM_STR);
            $stmt_insert->bindParam(':preference_id', $preference_id, PDO::PARAM_INT);
            $stmt_insert->execute();
            
            // Redirect with success flag
            header('Location: ../view/pages/trip_details.php?preference_id=' . $preference_id . '&message=success');
            exit();
        } catch (PDOException $e) {
            echo 'Error inserting message: ' . $e->getMessage();
            exit();
        }
    } else {
        echo 'No user found for the given preference ID.';
        exit();
    }
}
?>
