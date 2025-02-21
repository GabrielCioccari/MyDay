        let currentImageIndex = 0;
        const images = document.querySelectorAll('.image-container img');
        const buttons = document.querySelectorAll('.buttons button');

        // Variável de controle para saber se a troca automática deve ser ativada
        let autoChangeInterval;

        // Função para trocar a imagem
        function changeImage(index) {
            // Remove a classe 'active' de todas as imagens
            images.forEach(img => img.classList.remove('active'));
            // Adiciona a classe 'active' somente à imagem clicada
            images[index].classList.add('active');

            // Remove a classe 'active-btn' de todos os botões
            buttons.forEach(btn => btn.classList.remove('active-btn'));
            // Adiciona a classe 'active-btn' somente ao botão clicado
            buttons[index].classList.add('active-btn');

            // Atualiza o índice da imagem atual
            currentImageIndex = index;

            // Para a troca automática ao clicar no botão
            clearInterval(autoChangeInterval);
        }

        // Função para trocar a imagem automaticamente
        function autoChangeImage() {
            currentImageIndex = (currentImageIndex + 1) % images.length;
            changeImage(currentImageIndex);
        }

        // Inicia a troca automática de imagens a cada 5 segundos
        function startAutoChange() {
            autoChangeInterval = setInterval(autoChangeImage, 5000);
        }

        // Inicializa a primeira imagem e começa a troca automática
        changeImage(currentImageIndex);
        startAutoChange(); // Chama a função para iniciar o intervalo
