<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - CBT</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($error)): ?>
        <div style="color:red"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="post" action="/auth?action=process">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
