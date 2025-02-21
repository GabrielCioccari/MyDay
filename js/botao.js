// Seleciona todos os links e botões que serão usados na navegação
const navItems = document.querySelectorAll('.selecionado');

// Função para adicionar e remover a classe 'selected'
navItems.forEach(item => {
    item.addEventListener('click', () => {
        // Remove a classe 'selected' de todos os itens
        navItems.forEach(nav => nav.classList.remove('selected'));

        // Adiciona a classe 'selected' ao item clicado
        item.classList.add('selected');
    });
});

// Mantém o item ativo com base na URL atual (opcional)
window.addEventListener('DOMContentLoaded', () => {
    navItems.forEach(item => {
        if (item.href === window.location.href) {
            item.classList.add('selected');
        }
    });
});

function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto'; // Redefine a altura para o padrão
    textarea.style.height = (textarea.scrollHeight) + 'px'; // Ajusta a altura com base no conteúdo
}

// Aplicar a função a todos os textareas existentes
document.querySelectorAll('textarea[name="titulo_texto[]"]').forEach(textarea => {
    textarea.addEventListener('input', function() {
        autoResizeTextarea(this);
    });

    // Ajustar a altura inicialmente (caso já haja conteúdo)
    autoResizeTextarea(textarea);
});


// Aplicar a função a novos textareas adicionados dinamicamente
document.getElementById('adicionar-texto').addEventListener('click', function() {
    // Aguardar um pequeno intervalo para garantir que o novo textarea seja adicionado ao DOM
    setTimeout(() => {
        const novoTextarea = document.querySelector('.texto:last-child textarea[name="titulo_texto[]"]');
        if (novoTextarea) {
            novoTextarea.addEventListener('input', function() {
                autoResizeTextarea(this);
            });

            // Ajustar a altura inicialmente (caso já haja conteúdo)
            autoResizeTextarea(novoTextarea);
        }
    }, 100); // 100ms de atraso para garantir que o DOM seja atualizado
});


function toggleFilhas(paginaId) {
    // Verifica se o botão de toggle pertence a uma página pai, filha ou neta
    const filhas = document.getElementById('filhas_pagina_' + paginaId);
    const btnToggle = document.querySelector(`#pagina_${paginaId} .btn-toggle`);

    // Alterna a exibição das páginas filhas ou netas
    if (filhas.style.display === 'none' || filhas.style.display === '') {
        filhas.style.display = 'block';
        btnToggle.textContent = '^'; // Muda a seta para cima
    } else {
        filhas.style.display = 'none';
        btnToggle.textContent = '˅'; // Muda a seta para baixo
    }
}


