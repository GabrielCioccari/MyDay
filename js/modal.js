// Seleciona os elementos de todos os modais
const modals = {
    modal: {
        openBtn: document.getElementById('openModalBtn'),
        modalElement: document.getElementById('modal'),
        closeBtn: document.getElementById('closeModalBtn'),
        overlay: document.getElementById('modalOverlay') // Overlay para o modal principal
    },
    modalpesq: {
        openBtn: document.getElementById('openModalBtnpesq'),
        modalElement: document.getElementById('modalpesq'),
        closeBtn: document.getElementById('closeModalBtnpesq'),
        overlay: document.getElementById('modalOverlay') // Overlay para o modal de pesquisa
    },
    optionsModal: { // Novo modal
        openBtn: document.getElementById('showOptionsBtn'),
        modalElement: document.getElementById('optionsModal'),
        closeBtn: null, // Não tem botão de fechar
        overlay: null // Não usa overlay
    }
};

// Função para abrir um modal
function openModal(modalElement, overlay = null, isOptionsModal = false) {
    if (isOptionsModal) {
        // Posiciona o modal abaixo do botão
        const rect = modals.optionsModal.openBtn.getBoundingClientRect();
        modalElement.style.top = `${rect.bottom}px`;
        modalElement.style.left = `${rect.left}px`;
        modalElement.style.display = 'block';
    } else {
        // Modal normal com overlay
        if (overlay) {
            overlay.style.display = 'block';
        }
        modalElement.style.display = 'flex'; // Centraliza o modal
    }
}

// Função para fechar um modal
function closeModal(modalElement, overlay = null) {
    if (overlay) {
        overlay.style.display = 'none';
    }
    modalElement.style.display = 'none';
}

// Adiciona eventos para cada modal
Object.values(modals).forEach(({ openBtn, modalElement, closeBtn, overlay }) => {
    const isOptionsModal = modalElement === modals.optionsModal.modalElement;

    // Abrir o modal
    openBtn.addEventListener('click', () => {
        if (isOptionsModal && modalElement.style.display === 'block') {
            closeModal(modalElement); // Fecha o modal se já estiver aberto
        } else {
            openModal(modalElement, overlay, isOptionsModal); // Abre o modal
        }
    });

    // Fechar o modal ao clicar no botão de fechar (se existir)
    if (closeBtn) {
        closeBtn.addEventListener('click', () => closeModal(modalElement, overlay));
    }

    // Fechar o modal ao clicar fora do conteúdo (fora da área do modal)
    window.addEventListener('click', (event) => {
        if (isOptionsModal) {
            // Fecha o modal de opções ao clicar fora dele ou do botão
            if (!openBtn.contains(event.target) && !modalElement.contains(event.target)) {
                closeModal(modalElement);
            }
        } else {
            // Fecha os outros modais ao clicar no overlay ou fora do modal
            if (event.target === modalElement || event.target === overlay) {
                closeModal(modalElement, overlay);
            }
        }
    });
});

document.getElementById('adicionar-imagem').addEventListener('click', function() {
    document.getElementById('input-imagem').click();
});

document.getElementById('input-imagem').addEventListener('change', function(event) {
    const arquivo = event.target.files[0];
    if (arquivo) {
        const leitor = new FileReader();
        leitor.onload = function(e) {
            adicionarImagem(e.target.result);
        };
        leitor.readAsDataURL(arquivo);
    }
});

function adicionarImagem(src) {
    const container = document.createElement('div');
    container.className = 'imagem'; // Para estilização futura

    const img = document.createElement('img');
    img.src = src;
    img.alt = 'Imagem carregada';
    img.style.maxWidth = '100%';
    img.style.borderRadius = '8px';

    const botaoRemover = document.createElement('button');
    botaoRemover.textContent = 'Remover';
    botaoRemover.addEventListener('click', function() {
        container.remove();
    });

    container.appendChild(img);
    container.appendChild(botaoRemover);

    document.getElementById('tarefas-container').appendChild(container);
}


