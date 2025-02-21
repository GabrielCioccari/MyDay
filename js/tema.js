

document.addEventListener("DOMContentLoaded", function () {
    const selectTema = document.getElementById("tema");
    const body = document.body;
    const header = document.querySelector("header");

    // Verificar se já existe um tema salvo no localStorage
    if (localStorage.getItem("tema") === "escuro") {
        body.classList.add("escuro");
        header.classList.add("escuro");
        selectTema.value = "escuro";
    }

    selectTema.addEventListener("change", function () {
        if (selectTema.value === "escuro") {
            body.classList.add("escuro");
            header.classList.add("escuro");
            localStorage.setItem("tema", "escuro"); // Salva no localStorage
        } else {
            body.classList.remove("escuro");
            header.classList.remove("escuro");
            localStorage.setItem("tema", "claro"); // Salva no localStorage
        }
    });
});