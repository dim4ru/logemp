<!DOCTYPE html>
<html>
<head>
    <title>Авторизация</title>
</head>
<body>
<a href="../stuff/stuff_login.php" style="position: absolute; top: 10px; right: 10px;">Авторизация сотрудника</a>

<form action="client_login_handler.php" method="post">
    <div>
        <label for="tel">Номер телефона:</label>
        <input type="tel" id="tel" name="tel" placeholder="Без +7 (напр.9991234566)" maxlength="10">
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" maxlength="10">
    </div>
    <button type="submit">Войти</button>
    <a href="client_register.php">Регистрация</a>
</form>

</body>
</html>