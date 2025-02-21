            function enviarEmail() {
                // Mostra o spinner de carregamento
                document.getElementById("loading-spinner").style.display = "block";
                
                // Simula o envio do formulário
                setTimeout(function() {
                    // Esconde o spinner
                    document.getElementById("loading-spinner").style.display = "none";
                    // Mostra a mensagem de sucesso
                    document.getElementById("success-message").style.display = "block";
                    // Limpa o formulário
                    document.getElementById("contact-form").reset();
                }, 2000); // Simula um delay de 2 segundos para o envio
                
                return false; // Impede o envio real do formulário (remover quando estiver usando PHP)
            }

            document.getElementById('contact-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita o envio padrão do formulário

    // Exibe o spinner de carregamento
    document.getElementById('loading-spinner').style.display = 'block';

    // Pega os dados do formulário
    const formData = new FormData(this);

    // Envia o formulário via AJAX (fetch)
    fetch(this.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.ok ? response : Promise.reject(response))
    .then(() => {
      // Oculta o spinner e o formulário
      document.getElementById('loading-spinner').style.display = 'none';
      document.getElementById('contact-form').style.display = 'none';

      // Exibe a mensagem de sucesso
      document.getElementById('success-message').style.display = 'block';
    })
    .catch(error => {
      console.error('Erro no envio do formulário:', error);
      alert('Houve um erro ao enviar o formulário. Tente novamente mais tarde.');

      // Oculta o spinner em caso de erro
      document.getElementById('loading-spinner').style.display = 'none';
    });
});