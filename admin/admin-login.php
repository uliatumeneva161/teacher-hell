<?php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require('../db/db.php');
    
    $login = $_POST['login'];
    $pass = $_POST['pass'];

    $stmt = $conn->prepare("SELECT login, pass FROM admins WHERE login = ?");
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        if (password_verify($pass, $user['pass'])) {
            $_SESSION['login'] = $login;
            $_SESSION['pass'] = $pass;
            header('Location: admin.html');
            exit();
        } else {
            $error = 'Неверный пароль';
        }
    } else {
        $error = 'Пользователь не найден';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход администратора - Управление школой</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #89a373;
            --primary-dark: #728674;
            --secondary-color: #a984c1;
            --danger-color: #d37e85;
            --success-color: #99b29b;
            --warning-color: #f8961e;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-image: linear-gradient(135deg, var(--secondary-color), var(--success-color));
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 50px;
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .logo-text h1 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .logo-text p {
            color: var(--gray-color);
            font-size: 14px;
        }

        h2 {
            font-size: 22px;
            color: var(--dark-color);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .login-form {
            text-align: left;
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(137, 163, 115, 0.2);
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(137, 163, 115, 0.3);
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .login-footer {
            margin-top: 25px;
            color: var(--gray-color);
            font-size: 14px;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="logo-text">
                <h1>Администратор</h1>
                <p>Вход в панель управления</p>
            </div>
        </div>

        <h2>Вход для администратора</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="login"><i class="fas fa-user"></i> Логин</label>
                <input type="text" id="login" name="login" placeholder="Введите логин" required>
            </div>
            <div class="form-group">
                <label for="pass"><i class="fas fa-lock"></i> Пароль</label>
                <input type="password" id="pass" name="pass" placeholder="Введите пароль" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Войти
            </button>
        </form>

        <div class="login-footer">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> На главную</a>
        </div>
    </div>
</body>
</html>