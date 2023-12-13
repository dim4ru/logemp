<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
</head>
<body>
<form action="register_handler.php" method="post">
    <div>
        <label for="username">Логин:</label>
        <input type="text" id="username" name="username" maxlength="10">
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" maxlength="10">
    </div>
    <div>
        <label for="name">ФИО:</label>
        <input type="text" id="name" name="name" maxlength="100">
    </div>
    <div>
        <label for="tel">Номер телефона:</label>
        <input type="tel" id="tel" name="tel" placeholder="Без +7 (напр.9991234566)" maxlength="10">
    </div>
    <button type="submit">Зарегистрироваться</button>
    <a href="login.php">Авторизоваться</a>
</form>

</body>
</html>