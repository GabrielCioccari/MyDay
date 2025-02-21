document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('tarefas-container');

    // Ativa a funcionalidade de arrastar e soltar
    container.querySelectorAll('.tarefa, .texto, .titulo, .pagina-filha, .imagem').forEach(item => {
        item.setAttribute('draggable', true);
        item.addEventListener('dragstart', dragStart);
        item.addEventListener('dragover', dragOver);
        item.addEventListener('drop', drop);
        item.addEventListener('dragend', dragEnd);
    });

    let draggedItem = null;

    function dragStart(e) {
        draggedItem = this;
        setTimeout(() => {
            this.style.opacity = '0.5';
        }, 0);
    }

    function dragOver(e) {
        e.preventDefault();
        const afterElement = getDragAfterElement(container, e.clientY);
        if (afterElement == null) {
            container.appendChild(draggedItem);
        } else {
            container.insertBefore(draggedItem, afterElement);
        }
    }

    function drop(e) {
        e.preventDefault();
    }

    function dragEnd() {
        this.style.opacity = '1';
        salvarOrdem();
    }

    // Função para identificar a posição correta para inserir o item arrastado
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.tarefa, .texto, .titulo, .pagina-filha, .imagem:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Envia a nova ordem para o backend via AJAX
    function salvarOrdem() {
        const ordem = Array.from(container.children).map((item, index) => ({
            id: item.dataset.id,
            tipo: item.classList.contains('tarefa') ? 'tarefa' :
                item.classList.contains('texto') ? 'texto' :
                item.classList.contains('titulo') ? 'titulo' :
                item.classList.contains('pagina-filha') ? 'pagina_filha' : 
                'imagem',
            posicao: index + 1
        }));

        fetch('salvar_ordem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(ordem)
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Ordem salva com sucesso');
            } else {
                console.error('Erro ao salvar ordem');
            }
        }).catch(error => console.error('Erro:', error));
    }
});
