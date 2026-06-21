<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Система подтверждения квалификации</title>
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
            background-image: linear-gradient(var(--secondary-color), var(--success-color));
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
            width: clamp(300px, 80%, 700px);
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

        .description {
            color: var(--gray-color);
            margin-bottom: 40px;
            font-size: 16px;
            line-height: 1.5;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 18px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: white;
        }

        .button i {
            font-size: 20px;
        }

        .button-admin {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .button-admin:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(137, 163, 115, 0.3);
        }

        .button-teacher {
            background: linear-gradient(135deg, var(--secondary-color), #b893c9);
        }

        .button-teacher:hover {
            background: linear-gradient(135deg, #b893c9, var(--secondary-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(169, 132, 193, 0.3);
        }

        .footer-note {
            margin-top: 30px;
            color: var(--gray-color);
            font-size: 14px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 40px 30px;
            }
            
            h2 {
                font-size: 20px;
            }
            
            .button {
                padding: 16px 25px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .logo {
                flex-direction: column;
                text-align: center;
            }
            
            .button {
                padding: 14px 20px;
            }
        }
        @media (max-width: 376px){
            .login-container{
                min-width: 300px;
            }
        }        
    </style>
</head>
<body>
    <div class="login-container">
       
        <h2>Выберите роль для входа</h2>
        <p class="description">
            Пожалуйста, выберите соответствующую роль для доступа к функционалу системы
        </p>

        <div class="button-container">
            <a href="admin/admin-login.php" class="button button-admin">
                <i class="fas fa-user-shield"></i>
                Вход администратора
            </a>
            
            <a href="teacher/teach-login.php" class="button button-teacher">
                <i class="fas fa-chalkboard-teacher"></i>
                Вход учителя
            </a>
        </div>

        <p class="footer-note">
            Для доступа к системе требуется авторизация
        </p>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


</body>
</html>

