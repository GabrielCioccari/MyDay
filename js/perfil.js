// Seletores e funções relacionadas à foto de perfil
var fotoPerfil = document.getElementById('fotoPerfilAtual');
var letraPerfil = document.getElementById('letraPerfil');
var inputFotoPerfil = document.getElementById('foto_perfil'); // Input de arquivo

// Atribui o evento de clique para abrir o explorador de arquivos
function abrirExplorador() {
    if (inputFotoPerfil) {
        inputFotoPerfil.click(); // Simula o clique no input de arquivo
    }
}

// Verifica se a imagem de perfil existe e atribui o evento de clique
if (fotoPerfil) {
    fotoPerfil.addEventListener('click', abrirExplorador);
}

// Verifica se o placeholder da letra do perfil existe e atribui o evento de clique
if (letraPerfil) {
    letraPerfil.addEventListener('click', abrirExplorador);
}

// Função de pré-visualização da imagem
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('fotoPerfilAtual');
            if (img) {
                img.src = e.target.result; // Atualiza a imagem de perfil
            }
        };
        reader.readAsDataURL(file);
    }
}

const perfilBtn = document.getElementById('perfilBtn');
            const aparenciaBtn = document.getElementById('aparenciaBtn');
            const perfilSection = document.getElementById('perfilSection');
            const aparenciaSection = document.getElementById('aparenciaSection');

            // Função para exibir a seção de perfil
            perfilBtn.addEventListener('click', () => {
                perfilSection.style.display = 'block';
                aparenciaSection.style.display = 'none';
            });

            // Função para exibir a seção de aparência
            aparenciaBtn.addEventListener('click', () => {
                perfilSection.style.display = 'none';
                aparenciaSection.style.display = 'block';
            });

            // Iniciar com a seção de perfil
            perfilSection.style.display = 'block';
            aparenciaSection.style.display = 'none';