document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("contact-form");
    const formFields = document.getElementById("form-fields");
    const spinner = document.getElementById("loading-spinner");
    const successMessage = document.getElementById("success-message");

    form.addEventListener("submit", function (e) {
        e.preventDefault(); // Impede o envio padrão do formulário

        // Ocultar os campos do formulário e mostrar o spinner
        formFields.style.display = "none";
        spinner.style.display = "block";

        // Pega os dados do formulário
        const formData = new FormData(this);

        // Envia o formulário via AJAX (fetch)
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.ok ? response : Promise.reject(response))
        .then(() => {
            // Ocultar o spinner e exibir mensagem de sucesso
            spinner.style.display = "none";
            successMessage.style.display = "block";
        })
        .catch(error => {
            console.error('Erro no envio do formulário:', error);
            alert('Houve um erro ao enviar o formulário. Tente novamente mais tarde.');

            // Oculta o spinner em caso de erro
            spinner.style.display = "none";

            // Mostrar os campos do formulário novamente
            formFields.style.display = "block";
        });
    });
});
