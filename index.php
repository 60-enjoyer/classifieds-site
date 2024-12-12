<?php

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        if (isset($_SESSION['user_id'])){
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
        
            // Проверка входных данных
            if (empty($login) || empty($password)) {
                echo json_encode(['error' => 'Логін та пароль не можуть бути пустими']);
                exit;
            }
        
            // Подготовленный запрос для предотвращения SQL-инъекций
            $stmt = $db->prepare("SELECT id, pass FROM Users WHERE username = ?");
            if (!$stmt) {
                echo json_encode(['error' => 'Помилка створення запиту до БД']);
                exit;
            }
        
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
        
                // Сравнение пароля
                if (password_verify($password, $user['pass'])) {
                    $_SESSION['user_id'] = $user['id']; // Сохраняем user_id в сессии
                    echo json_encode(['status' => 'success', 'id' => $user['id']]);
                    exit;
                }
            }
        
            echo json_encode(['error' => 'Невірний логін або пароль']);
            exit;
        } else if (isset($_POST['isRegister'])) {
            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';
        
            // Проверка входных данных
            if (empty($login) || empty($password)) {
                echo json_encode(['error' => 'Логін та пароль не можуть бути пустими']);
                exit;
            }
        
            // Хеширование пароля
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
            // Подготовленный запрос для вставки данных
            $stmt = $db->prepare("INSERT INTO Users (username, pass) VALUES (?, ?)");
            if (!$stmt) {
                echo json_encode(['error' => 'Ошибка подготовки запроса']);
                exit;
            }
        
            $stmt->bind_param("ss", $login, $hashedPassword);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id; // Сохраняем ID нового пользователя в сессии
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['error' => 'Ошибка регистрации']);
            }
        
            exit;
        } else if(isset($_POST["isExit"])) {
            session_unset();
            session_destroy();
            echo "succes";
            exit;
        } else if(isset($_POST["isGetCategories"]) && isset($_SESSION['user_id'])) {
            $cats = $db->query("SELECT * FROM Categories;")->fetch_all();
            echo json_encode($cats, JSON_UNESCAPED_UNICODE);
            exit;
        } else if (isset($_POST["isAddAdvt"]) && isset($_SESSION["user_id"])) {
            if (isset($_FILES["image"]) && isset($_POST["caption"]) && isset($_POST["cost"]) && isset($_POST["category"]) && isset($_POST["description"])) {
        
                // Экранирование данных перед записью в базу
                $caption = mysqli_real_escape_string($db, $_POST["caption"]);
                $cost = floatval($_POST["cost"]); // Убедитесь, что цена — это число
                $category = mysqli_real_escape_string($db, $_POST["category"]);
                $description = mysqli_real_escape_string($db, $_POST["description"]);
        
                $uploadDir = 'uploads/'; // Папка для сохранения файлов
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = uniqid() . basename($_FILES['image']['name']);
                $uploadFilePath = $uploadDir . $fileName;
        
                // Проверка существования категории
                $query = "SELECT id FROM Categories WHERE cat_name = '$category'";
                $categoryResult = $db->query($query);
        
                if ($categoryResult && $categoryResult->num_rows > 0) {
                    $category_id = $categoryResult->fetch_assoc()["id"];
                } else {
                    // Если категории нет, создаём её
                    $insertCategoryQuery = "INSERT INTO Categories (cat_name) VALUES ('$category')";
                    if ($db->query($insertCategoryQuery)) {
                        $category_id = $db->insert_id; // Получаем ID только что добавленной категории
                    } else {
                        echo "Ошибка при добавлении категории.";
                        exit;
                    }
                }
        
                $user_id = $_SESSION["user_id"];
        
                // Теперь можно безопасно переместить файл
                if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                    // Вставка записи в базу данных
                    $insertQuery = "INSERT INTO advts (user_id, photo_name, ad_name, ad_desc, ad_cost, ad_category) VALUES ('$user_id', '$fileName', '$caption', '$description', '$cost', '$category_id')";
                    if ($db->query($insertQuery)) {
                        echo "success";
                    } else {
                        // В случае ошибки записи в базу данных удаляем файл
                        unlink($uploadFilePath);
                        echo "Ошибка при записи в базу данных.";
                    }
                } else {
                    echo "Ошибка при загрузке файла.";
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
