<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$emailConfig = [
  'host' => 'smtp.gmail.com',
      'username' => 'seuemail@gmail.com',
      'password' => 'suasenha', // Use uma senha de aplicativo se a verificação em duas etapas estiver ativada
      'port' => 587,
      'from' => 'seuemail@gmail.com'
  ];



function sendEmail($to, $subject, $content, $config) {
  $content = str_replace("Seu código é: ", "Seu código é: <span class='code-box'>", $content);
  $content = preg_replace("/(\d{6})/", "$1</span>", $content);

    $mail = new PHPMailer(true);
  $mail->CharSet = PHPMailer::CHARSET_UTF8;

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];

        // Configurações do remetente e destinatário
        $mail->setFrom($config['from'], 'Krust Newsletter');
        $mail->addAddress($to);

        // Template HTML com estilos
        $emailHtml = "
        <html>
        <head>
    <style>
        .email-container {
            font-family: 'Arial', sans-serif;
            color: #333;
            background-color: white;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }

        .header, .footer {
            background-color: #7a5af7;
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .content {
            padding: 20px;
            text-align: left;
            line-height: 1.6;
            color: #333;
        }

        .code-box {
            display: block; 
            background-color: #7a5af7; 
            color: white; 
            padding: 10px 20px;
            margin: 20px auto; 
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            font-size: 20px;
            font-weight: bold;
            text-align: center; 
        }

    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h1>Seu Código de Verificação</h1>
        </div>
        <div class='content'>
            <p>Olá, obrigado por se inscrever. Abaixo está o seu código de verificação.</p>
            <span class='code-box'>$content</span>
            <p>Use este código para completar o seu cadastro. Se você não solicitou este código, por favor ignore este e-mail.</p>
        </div>
        <div class='footer'>
            <p>&copy; " . date("Y") . " Krust Newsletter</p>
        </div>
    </div>
</body>
        </html>";

        // Configuração da mensagem
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $emailHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Erro ao enviar email: {$mail->ErrorInfo}";
        return false;
    }
}
?>
