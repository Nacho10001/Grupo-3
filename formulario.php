<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $atualizacoes = isset($_POST['atualizacoes']);

    if (empty($name) || empty($email) || !$atualizacoes) {
        echo "Por favor, rellena todos los campos obligatorios.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido.";
        exit;
    }

    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO formulario (name, email) VALUES (:name, :email)");
        $stmt->execute([':name' => $name, ':email' => $email]);

        // Exibe mensagem com contagem regressiva
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Enviado</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        #countdown { font-size: 24px; color: #E38417; }
    </style>
</head>
<body>
    <p>Dados enviados com sucesso!</p>
    <p>Você será redirecionado em <span id="countdown">3</span> segundos...</p>
    <script>
        let timeLeft = 3;
        const countdownElement = document.getElementById("countdown");
        const timer = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = "https://padariafamiliasantos.onrender.com/";
            }
        }, 1000);
    </script>
</body>
</html>';
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == '23505') {
            echo "Esta dirección de correo electrónico ya está registrada.";
        } else {
            echo "Error al conectar o insertar datos: " . $e->getMessage();
        }
        exit;
    }
} else {
    echo "Acesso inválido.";
    exit;
}
?>