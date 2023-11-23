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

        // Template HTML com estilos inline
  $emailHtml = "
  <html>
<head>
<meta charset='UTF-8'>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            background-color: #f4f4f4; 
            margin: 0; 
            padding: 0; 
        }
        .container { 
            background-color: white; 
            margin: 20px auto; 
            width: 90%; 
            max-width: 600px; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .header { 
            background-color: #7a5af7; 
            color: white; 
            padding: 20px; 
            text-align: center; 
            border-top-left-radius: 10px; 
            border-top-right-radius: 10px; 
        }
        .content { 
            padding: 20px; 
            text-align: justify; /* Texto justificado */
            line-height: 1.6; 
            color: #333; 
            white-space: pre-wrap; 
        }
        .content h1, .content h2, .content h3 { 
            color: #333; 
            margin-top: 20px; 
        }
        .content p, .content ul, .content ol { 
            margin-bottom: 15px; 
        }
        .footer { 
            background-color: #7a5af7; 
            color: white; 
            padding: 10px; 
            text-align: center; 
            font-size: 14px; 
            border-bottom-left-radius: 10px; 
            border-bottom-right-radius: 10px; 
        }
        a { 
            color: #7a5af7; 
            text-decoration: none; 
        }
        a:hover { 
            text-decoration: underline; 
        }
        ul, ol { 
            padding-left: 20px; 
        }
        li { 
            margin-bottom: 10px; 
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Newsletter</h1>
        </div>
        <div class='content'>
            {$content}
        </div>
        <div class='footer'>
            <p>&copy; " . date("Y") . " Krust Newsletter</p>
        </div>
    </div>
</body>
</html>
";


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
