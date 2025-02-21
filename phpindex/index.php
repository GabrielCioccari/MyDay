<!DOCTYPE html>
<html lang="en">
<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat+Underline:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../img/LogoMyDay.png">
    <title>MyDay</title>
</head>
<body>
<main id="inicio">
    <header>
        <nav>
            <div class="logo">
                <a href=""><img src="../img/logoMyDayEscrito.png" alt=""></a>
            </div>
            <ul>
                <a href="login.php" class="entrar">Entrar</a>
                <a href="cadastro.php" class="cadastrarPreto">Comece a usar o MyDay</a>
            </ul>
        </nav>
    </header>

    <div class="center">
        <video class="background-video-main" autoplay muted loop>
            <source src="../img/bg.mp4" type="video/mp4">
            Seu navegador não suporta vídeos.
        </video>    

        <div class="introimg">
            <img src="../img/imagem-inicio.png" alt="">
        </div>

        <div class="introtext">
            <h1>Organizando Conquistando Objetivos</h1>
            <p>O MyDay veio para ajudar você a conquistar todas suas metas</p>
            <a href="cadastro.php" class="cadastrarAmarelo">Comece a usar o MyDay</a>
        </div>
    </div>
    
</main>

    <section class="funcionalidades" id='funcionalidades'>
        <div class="contfunc">
            <div class="functext">
                <h2><span>Explore</span> Tudo o Que Podemos Fazer por Você</h2>
                <p>Tudo que você precisa em um sé lugar para mudar de vida.</p>
            </div>
            <div class="funcimg">
                <img src="../img/imagem-funcionalidades.png" alt="">
            </div>
        </div>

        <div class="image-container">
            <img src="../img/imagem-exemplo.png" alt="Imagem 1" class="active">
            <img src="../img/imagem-exemplo.png" alt="Imagem 2">
            <img src="../img/imagem-exemplo.png" alt="Imagem 3">
        </div>

        <div class="buttons">
            <button onclick="changeImage(0)" class="active-btn">Imagem 1</button>
            <button onclick="changeImage(1)">Imagem 2</button>
            <button onclick="changeImage(2)">Imagem 3</button>
        </div>

        <script src="../jsIndex/funcionalidades.js"></script>
    </section>

    <section class="beneficios" id='beneficios'>
        <div class="contbene">
            <!-- Texto à esquerda -->
            <div class="benetext">
                <h2><span>Benefícios</span> ao usar o Myday</h2>
                <p>Organize-se, alcance seus objetivos e viva o seu melhor dia, todos os dias.</p>
            </div>

            <!-- Carrossel à direita -->
            <div class="carousel-container">
                <div id="slide">
                    <div class="item">
                        <h2>Planejamento Simplificado</h2>
                        <p>Crie rotinas diárias personalizadas em minutos.</p>
                    </div>
                    <div class="item">
                        <h2>Gestão de Tempo Eficiente</h2>
                        <p>Priorize tarefas e maximize seu dia.</p>
                    </div>
                    <div class="item">
                        <h2>Criação de Hábitos Positivos</h2>
                        <p>Alcance seus objetivos com pequenas ações consistentes.</p>
                    </div>
                    <div class="item">
                        <h2>Flexibilidade Total</h2>
                        <p>Adapte sua rotina conforme suas necessidades mudam.</p>
                    </div>
                    <div class="item">
                        <h2>Interface Intuitiva</h2>
                        <p>Ferramentas fáceis de usar que tornam a organização prazerosa.</p>
                    </div>
                    <div class="item">
                        <h2>Suporte à Motivação</h2>
                        <p>Receba lembretes e insights para continuar no caminho certo.</p>
                    </div>
                </div>
                <div class="buttons">
                    <button id="prev"><i class="fa-solid fa-arrow-left"></i></button>
                    <button id="next"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>
        </div>
        <script src="../jsIndex/carrousel.js"></script>
    </section>

    <section class="historia" id='historia'>
        <div class="conthist">
            <div class="histtext">
                <h2> Organizando Minha Vida, <span>inspirando</span> a Sua</h2>
                <p>Este projeto nasceu da minha necessidade de organizar a vida e encontrar 
                    equilíbrio. Hoje em dia o maior problema nao e nao saber o que fazer mas sim a preguica de fazer o que tem que ser feito. 
                    Com a vontade de resover esse problema o MyDay foi criado</p>
                <a href="" class="instagram">Siga o MyDay no Instagram</a>
            </div>
            <div class="histimg">
                <img src="../img/imagem-historia.png" alt="">
            </div>
        </div>
    </section>

    <section class="peres" id='faqs'>
        <div class="email">
            <h2>Perguntas e Respostas</h2>
            <p>Deixe uma mensagem de feedback ou caso tenha ficado com alguma dúvida</p>

            <div class="formcont">
            <form id="contact-form" action="contato_email.php" method="POST">
                <div id="form-fields">
                    <input type="email" name="email" placeholder="E-mail" required><br><br>
                    <textarea id="mensagem" name="mensagem" rows="4" cols="50" placeholder="Mensagem" required></textarea><br><br>
                    <button type="submit" class="formbtn">Enviar</button>
                </div>

                <!-- Spinner -->
                <div id="loading-spinner" style="display:none; margin-top: 10px;">
                    <div class="spinner"></div>
                </div>
            </form>

                    <!-- Mensagem de sucesso -->
                    <div id="success-message" style="display:none;">
                            <p>Obrigado! Seu email foi enviado com sucesso!</p>
                        </div>
                        </div>
                        <script src="../jsIndex/formulario.js"></script>
                    </div>

                <!-- parte dos faqs -->
                
                <div class="faqs">
                
                <div class="faq-item">
                    <h2 class="faq-question">Por que é importante ter uma rotina?</h2>
                    <p class="faq-answer" style="display: none;">Ter uma rotina ajuda a organizar o seu dia, aumentar a produtividade e reduzir o estresse. Quando você segue uma rotina, seu cérebro se adapta e se torna mais eficiente na realização de tarefas diárias.</p>
                </div>
                <div class="separator"></div>

                <div class="faq-item">
                    <h2 class="faq-question">Como posso começar a criar uma rotina?</h2>
                    <p class="faq-answer" style="display: none;">Comece identificando suas prioridades diárias. Defina horários para acordar, trabalhar, fazer pausas e se exercitar. Utilize um planner ou aplicativo para ajudar a visualizar sua rotina.</p>
                </div>
                <div class="separator"></div>

                <div class="faq-item">
                    <h2 class="faq-question">O que devo incluir na minha rotina diária?</h2>
                    <p class="faq-answer" style="display: none;">Inclua atividades essenciais, como trabalho, exercícios, refeições e momentos de lazer. Também é importante reservar tempo para o autocuidado e relaxamento.</p>
                </div>
                <div class="separator"></div>

                <div class="faq-item">
                    <h2 class="faq-question">Como posso me manter motivado a seguir minha rotina?</h2>
                    <p class="faq-answer" style="display: none;">Estabeleça metas realistas e recompense-se ao alcançá-las. Mantenha uma lista de tarefas visível e lembre-se dos benefícios que uma rotina pode trazer para sua vida.</p>
                </div>
                <div class="separator"></div>

                <div class="faq-item">
                    <h2 class="faq-question">O que fazer se minha rotina não estiver funcionando?</h2>
                    <p class="faq-answer" style="display: none;">É normal que rotinas precisem de ajustes. Reavalie suas atividades e horários, e não tenha medo de modificar sua rotina até encontrar o que funciona melhor para você.</p>
                </div>
                <div class="separator"></div>
                </div>
            </div>

        <script src="../jsIndex/faqs.js"></script>
    </section>

    <footer>
        <video class="background-video-footer" autoplay muted loop>
            <source src="../img/bg.mp4" type="video/mp4">
            Seu navegador não suporta vídeos.
        </video>
        <div class="fotcont">
                <div class="fotlogo">
                    <div class="logo">
                        <a href=""><img src="../img/logoMyDayEscrito.png" alt=""></a>
                    </div>
                    <p>Tudo que você precisa em um só lugar para se tornar alguém com mais resultados no que quer que seja.</p>
                </div>

                <div class="fotnav">
                    <div class="navegar">
                        <ul>
                            <h3>Navegar</h3>
                            <li><a href="#inicio">Inicio</a></li>
                            <li><a href="#funcionalidades">Funcionalidades</a></li>
                            <li><a href="#beneficios">Beneficios</a></li>
                            <li><a href="#historia">História</a></li>
                            <li><a href="#faqs">Faqs</a></li>
                        </ul>
                    </div>
                    <div class="acessar">
                        <ul>
                            <h3>Acessar</h3> 
                            <li><a href="login.php">Login</a></li>
                            <li><a href="cadastro.php">Cadastrar-se</a></li>
                        </ul>  
                    </div>
                </div>
            </div>            
    </footer>


    
</body>
</html>