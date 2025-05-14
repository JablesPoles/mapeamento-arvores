// style.js - Funções JavaScript Globais e Inicializadores

// Constantes para classes CSS reutilizáveis (usadas em admin.php para campos dinâmicos)
const DYNAMIC_INPUT_CLASSES = 'flex-grow px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow';
const DYNAMIC_REMOVE_BUTTON_CLASSES = 'bg-red-600 hover:bg-red-700 dark:bg-dark-remove-btn-bg dark:hover:bg-dark-remove-btn-hover-bg text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105';

/**
 * Lógica para alternância de tema (Light/Dark Mode)
 * Esta função será chamada no evento DOMContentLoaded.
 */
function initializeThemeToggle() {
    const themeToggleButton = document.getElementById('theme-toggle-button');
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    // Função para aplicar o tema e atualizar o ícone do botão
    function applyThemePreference(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
            if (themeToggleLightIcon) themeToggleLightIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add('hidden');
            if (themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
        }
    }

    // Verifica a preferência do usuário no localStorage ou a preferência do sistema
    // e aplica o tema na carga inicial da página.
    function initializeCurrentTheme() {
        const userPreference = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (userPreference === 'dark' || (!userPreference && systemPrefersDark)) {
            applyThemePreference('dark');
        } else {
            applyThemePreference('light'); // Padrão para modo claro
        }
    }
    
    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', () => {
            const isDarkMode = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
            applyThemePreference(isDarkMode ? 'dark' : 'light');
        });
    }

    initializeCurrentTheme(); // Aplica o tema na carga da página
}

/**
 * Função para adicionar campos dinâmicos de nome popular (usada em admin.php).
 * Esta função deve ser chamada por um evento onclick no botão correspondente em admin.php.
 * Exemplo de uso no HTML: <button type="button" onclick="adicionarCampoNomePopular()">...</button>
 */
function adicionarCampoNomePopular() {
  const container = document.getElementById('nomes-populares-container');
  if (!container) {
    // Adiciona um aviso no console se o container não for encontrado.
    // Isso ajuda na depuração caso o ID do container mude ou não exista na página.
    console.warn("Container 'nomes-populares-container' não encontrado para adicionar campo dinâmico.");
    return;
  }

  const wrapper = document.createElement('div');
  // Adiciona classes para layout flexível e espaçamento.
  wrapper.className = 'flex items-center gap-2 mt-2'; 

  const input = document.createElement('input');
  input.type = 'text';
  input.name = 'nome_p[]'; // Importante para o PHP receber como array
  input.placeholder = 'Outro nome popular';
  input.maxLength = 100;
  // Não definimos 'required' para campos adicionais, pois o primeiro já é.
  input.className = DYNAMIC_INPUT_CLASSES; // Utiliza a constante de classes definida acima

  const removeButton = document.createElement('button');
  removeButton.type = 'button';
  removeButton.innerHTML = '<i class="fas fa-trash-alt"></i>'; // Ícone de lixeira
  removeButton.className = DYNAMIC_REMOVE_BUTTON_CLASSES; // Utiliza a constante de classes definida acima
  removeButton.setAttribute('aria-label', 'Remover nome popular'); // Para acessibilidade
  removeButton.onclick = function() {
    // Remove o 'wrapper' (que contém o input e o botão de remover) do container.
    container.removeChild(wrapper);
  };

  wrapper.appendChild(input);
  wrapper.appendChild(removeButton);
  container.appendChild(wrapper);
}

// Inicializações globais que devem ocorrer em todas as páginas após o DOM carregar.
// O evento DOMContentLoaded garante que o HTML foi completamente carregado e parseado
// antes que esses scripts tentem interagir com os elementos do DOM.
document.addEventListener('DOMContentLoaded', () => {
    initializeThemeToggle(); // Inicializa a lógica do tema em todas as páginas.

    // Inicializa AOS (Animate On Scroll) se a biblioteca AOS estiver definida.
    // Isso evita erros caso a biblioteca AOS não seja carregada em alguma página.
    if (typeof AOS !== 'undefined') {
        AOS.init({ 
            duration: 800, // Duração da animação em milissegundos
            easing: 'ease-in-out', // Tipo de easing para a animação
            once: true // Define se a animação deve ocorrer apenas uma vez
        });
    } else {
        // Adiciona um aviso no console se AOS não estiver carregado.
        console.warn('AOS (Animate On Scroll) não está definido. Verifique se a biblioteca foi carregada corretamente.');
    }
});
