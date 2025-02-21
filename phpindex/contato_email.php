<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $email = $_POST['email'];
    $mensagem = $_POST['mensagem'];

    // Cria uma nova instância do PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP do Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'mydaycompanybr@gmail.com'; // Seu e-mail
        $mail->Password = 'w d t f m o r s t c x k k x w h'; // Sua senha do Gmail
        $mail->SMTPSecure = 'tls'; // Criptografia
        $mail->Port = 587; // Porta para TLS

        // Configurações do e-mail
        $mail->setFrom('mydaycompanybr@gmail.com', 'MyDay');
        $mail->addAddress('mydaycompanybr@gmail.com', 'My Day Company'); // Destinatário

        // Conteúdo do e-mail principal
        $mail->isHTML(true);
        $mail->Subject = 'Novo Contato!';
        $mail->Body = "Email: $email <br> Mensagem: $mensagem";
        $mail->AltBody = "Email: $email \n Mensagem: $mensagem";

        // Envia o e-mail para o administrador
        $mail->send();

        // Envia e-mail de agradecimento para o usuário
        $mail->clearAddresses(); // Limpa os destinatários
        $mail->addAddress($email); // Adiciona o e-mail do usuário
        $mail->Subject = 'Agradecemos pelo seu feedback!'; // Assunto do e-mail de agradecimento
        $mail->Body = "
            <!DOCTYPE html>
            <html lang='pt-br'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    body {
                        font-family: 'Poppins', sans-serif;
                        background-color: #f9f9f9;
                        color: #333;
                        padding: 20px;
                        line-height: 1.6;
                    }
            
                    .container {
                        background-color: #fff;
                        padding: 20px;
                        border-radius: 5px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    }
            
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
            
                    .footer {
                        text-align: center;
                        margin-top: 20px;
                        font-size: 0.9em;
                        color: #777;
                    }
            
                    .btn {
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #007bff; /* Cor de fundo do botão */
                        color: white; /* Cor do texto do botão */
                        text-decoration: none;
                        border-radius: 25px;
                        transition: background-color 0.3s ease;
                    }
            
                    .btn:hover {
                        opacity: 0.7; 
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Obrigado por entrar em contato!</h2>
                    </div>
                    <p>Olá! 😊</p>
                    <p>Agradecemos pelo seu feedback! Sua mensagem foi recebida e nossa equipe entrará em contato em breve.</p>
                    <p>Atenciosamente,<br>A equipe do MyDay 💙</p>
                    <div class='footer'>
                        <p>Você está recebendo este e-mail porque sua conta foi criada em nosso site.</p>
                    </div>
                </div>
            </body>
            </html>
            ";

        // Envia o e-mail de agradecimento
        $mail->send();

        echo 'Mensagem enviada com sucesso!';
    } catch (Exception $e) {
        echo "Mensagem não enviada. Erro: {$mail->ErrorInfo}";
    }
} else {
    echo 'Método de requisição inválido.';
}
