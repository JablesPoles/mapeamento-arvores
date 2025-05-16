<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    $pageTitle = '';

    switch ($currentPage) {
        case 'index.php':
            $pageTitle = 'Página Inicial';
            break;
        case 'admin.php':
            $pageTitle = 'Painel Administrativo';
            break;
        case 'catalogo.php':
            $pageTitle = 'Catálogo';
            break;
        default:
            $pageTitle = 'Catálogo de Árvores'; // Título padrão
            break;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/custom_styles.css">
    <link rel="stylesheet" href="assets/css/index_styles.css" />

    <script>
        // Configuração do Tailwind CSS (pode ser movida para tailwind.config.js se você usar o CLI do Tailwind)
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { /* ...suas cores ... */
                        primary: '#2E7D32', 'primary-light': '#4CAF50', 'primary-lighter': '#E8F5E9',
                        secondary: '#FFA000', accent: '#00796B', 'card-border': '#4CAF50',
                        'card-hover': 'rgba(46, 125, 50, 0.05)', 'light-bg': '#f7faf7',
                        'dark-bg': '#1a202c', 'dark-card': '#2d3748', 'dark-card-header': '#1f2937',
                        'dark-text': '#e2e8f0', 'dark-primary': '#38a169', 'dark-primary-hover': '#2f855a',
                        'dark-secondary': '#dd6b20', 'dark-border': '#4a5568',
                    },
                    boxShadow: { /* ... seus boxShadow ... */
                        'card': '0 2px 8px rgba(0, 0, 0, 0.08)', 'card-hover': '0 4px 12px rgba(0, 0, 0, 0.12)'
                    },
                    maxHeight: { /* ... seus maxHeight ... */
                        '0': '0', '9999px': '9999px'
                    },
                    transitionProperty: { /* ... seus transitionProperty ... */
                        'height': 'height', 'max-height': 'max-height',
                    }
                }
            }
        }
    </script>
    </head>
<body class="bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text">

<header class="fixed top-0 left-0 w-full bg-green-700 dark:bg-gray-800 text-white shadow-lg z-50 transition-colors duration-300">
    <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 h-20">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold" data-aos="fade-down" data-aos-delay="100"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <nav class="flex items-center gap-2 md:gap-4">
            <a href="index.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl <?php if ($currentPage === 'index.php') echo 'bg-green-600 dark:bg-dark-primary'; ?>">
                <i class="fas fa-home text-lg md:text-2xl"></i>
                <span class="hidden sm:inline">Início</span>
            </a>
            <a href="catalogo.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl <?php if ($currentPage === 'catalogo.php') echo 'bg-green-600 dark:bg-dark-primary'; ?>">
                <i class="fas fa-leaf text-lg md:text-2xl"></i>
                <span class="hidden sm:inline">Catálogo</span>
            </a>
            <a href="admin.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl <?php if ($currentPage === 'admin.php') echo 'bg-green-600 dark:bg-dark-primary'; ?>">
                <i class="fas fa-user-cog text-lg md:text-2xl"></i>
                <span class="hidden sm:inline">Admin</span>
            </a>
            <button id="theme-toggle-button" type="button" class="text-white hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 dark:focus:ring-gray-600 rounded-lg text-sm p-2.5 transition-colors duration-300">
                <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>
        </nav>
    </div>
</header>