<?php

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        if (isset($_SESSION['user'])){
            if(isset($_GET["add"]))
                echo file_get_contents("./create-advt.html");
            else
                echo file_get_contents("./main.html");
        } else
            echo file_get_contents("./login.html");
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $db = new mysqli("localhost", "root", "", "Alex");
        if (isset($_POST['isLogin'])) {
            
            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';

            $query = "SELECT * FROM Users WHERE username = '$login'";
            $result = $db->query($query);

            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
        
                if ($user['pass'] === $password) {
                    $_SESSION['user'] = $user['username']; 
                    echo "succes";
                    exit;
                }
            }

            echo "error";
            exit;
        } else if(isset($_POST["isExit"])) {
            session_unset();
            session_destroy();
            echo "succes";
            exit;
        } else if(isset($_POST["isGetCategories"]) && isset($_SESSION['user'])) {
            $cats = $db->query("SELECT * FROM Categories;")->fetch_all();
            echo json_encode($cats, JSON_UNESCAPED_UNICODE);
            exit;
        } else if(isset($_POST["isAddAdvt"]) && isset($_SESSION["user"] )) {
            if(isset($_FILES["image"]) &&
               isset($_POST["caption"]) &&
               isset($_POST["cost"]) &&
               isset($_POST["category"]) &&
               isset($_POST["description"])){
                $uploadDir = 'uploads/'; // Папка для сохранения файлов
            
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = uniqid() . basename($_FILES['image']['name']);
                $uploadFilePath = $uploadDir .  $fileName;

                if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                    
                    $username = $_SESSION['user'];
                    $query = "SELECT * FROM Users WHERE username = '$username'";
                    $user = $db->query($query)->fetch_assoc();
                    
                    $user_id = $user["id"];
                    $caption = $_POST["caption"];
                    $cost = $_POST["cost"];
                    $category = $_POST["category"];
                    $description = $_POST["description"];

                    $category = $db->query("SELECT id FROM Categories WHERE cat_name = '$category'")->fetch_assoc()["id"];
                    
                    $query = "INSERT INTO advts (user_id, photo_name, ad_name, ad_desc, ad_cost, ad_category) VALUES ($user_id, '$fileName', '$caption', '$description', $cost, $category);";
                    $db->query($query);
                    echo "succes";
                } else {
                    echo "error";
                }
            }    
        } else if(isset($_POST["isGetAdvt"])) {
            $advts = NULL;
            if(isset($_POST["cat"])){
                $cat_id = $db->query("SELECT * FROM Categories WHERE cat_name='".$_POST['cat']."';" )->fetch_assoc()["id"];
                $advts = $db->query("SELECT * FROM advts WHERE ad_category=$cat_id;");
            } else 
                $advts = $db->query("SELECT * FROM advts;");
            echo json_encode($advts->fetch_all(), JSON_UNESCAPED_UNICODE);
        }
    }


?>
