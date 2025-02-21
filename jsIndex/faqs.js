
document.addEventListener("DOMContentLoaded", function() {
    const questions = document.querySelectorAll(".faq-question");
    
    questions.forEach(question => {
        question.addEventListener("click", function() {
            const answer = this.nextElementSibling; // Seleciona a resposta relacionada

            // Fecha todas as respostas
            document.querySelectorAll(".faq-answer").forEach(item => {
                if (item !== answer) {
                    item.style.display = "none"; // Oculta outras respostas
                }
            });

            // Alterna a visibilidade da resposta clicada
            if (answer.style.display === "block") {
                answer.style.display = "none"; // Se já está aberta, fecha
            } else {
                answer.style.display = "block"; // Se está fechada, abre
            }
        });
    });
});
