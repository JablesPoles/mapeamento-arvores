<?php
require 'conexao.php'; 

$filtro = isset($_GET['busca']) ? urldecode($_GET['busca']) : '';
$params = [];

$sql = "SELECT arvore.*, STRING_AGG(np.NOME, ', ' ORDER BY np.NOME) AS nomes_populares
        FROM arvore
        LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
        LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";

if (!empty($filtro)) {
    $sql .= " WHERE (LOWER(arvore.nome_c) LIKE :filtro OR LOWER(np.NOME) LIKE :filtro)";
    $params[':filtro'] = '%' . strtolower($filtro) . '%';
}

// Se o ID da árvore é a chave primária e única para agrupar, esta simplificação é válida.
// Caso contrário, você precisará listar todas as colunas não agregadas da tabela 'arvore'.
$sql .= " GROUP BY arvore.id ORDER BY arvore.horario DESC"; 


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arvores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR" class="">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo de Árvores</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script>
    // Configuração do Tailwind CSS
    tailwind.config = {
      darkMode: 'class', 
      theme: {
        extend: {
          colors: {
            primary: '#2E7D32',
            'primary-light': '#4CAF50',
            'primary-lighter': '#E8F5E9',
            secondary: '#FFA000',
            accent: '#00796B',
            'card-border': '#4CAF50',
            'card-hover': 'rgba(46, 125, 50, 0.05)',
            'light-bg': '#f7faf7',
            'dark-bg': '#1a202c',        
            'dark-card': '#2d3748',      
            'dark-card-header': '#1f2937',
            'dark-text': '#e2e8f0',      
            'dark-primary': '#38a169',   
            'dark-primary-hover': '#2f855a',
            'dark-secondary': '#dd6b20', 
            'dark-border': '#4a5568',    
          },
          boxShadow: {
            'card': '0 2px 8px rgba(0, 0, 0, 0.08)',
            'card-hover': '0 4px 12px rgba(0, 0, 0, 0.12)'
          },
          maxHeight: {
            '0': '0',
            '9999px': '9999px' 
          },
          transitionProperty: {
            'height': 'height',
            'max-height': 'max-height',
          }
        }
      }
    }
  </script>
  <style>
    .swiper {
      width: 100%;
      height: 300px; 
      border-radius: 0.5rem; 
      margin-top: 1rem; 
      background-color: #f7faf7; 
    }
    html.dark .swiper {
        background-color: #2d3748; 
    }
    .swiper-slide {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      text-align: center;
    }
    .swiper-slide img { 
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover; 
        border-radius: 0.5rem;
    }
    .swiper-button-next, .swiper-button-prev {
      color: #4CAF50; 
      padding: 0 15px; 
    }
    html.dark .swiper-button-next, html.dark .swiper-button-prev {
      color: #FFA000; 
    }
    .swiper-pagination-bullet-active {
      background: #4CAF50; 
    }
     html.dark .swiper-pagination-bullet-active {
      background: #FFA000; 
    }
    html, body {
        height: 100%;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    body {
        padding-top: 5rem; 
        display: flex;
        flex-direction: column;
    }
    main {
        flex-grow: 1;
    }
    #theme-toggle-button svg {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
  </style>
</head>
<body class="bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text">

<header class="fixed top-0 left-0 w-full bg-green-700 dark:bg-gray-800 text-white shadow-lg z-50 transition-colors duration-300">
  <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 h-20">
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold" data-aos="fade-down">Catálogo</h1>
    <nav class="flex items-center gap-2 md:gap-4">
      <a href="index.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl">
        <i class="fas fa-home text-lg md:text-2xl"></i>
        <span class="hidden sm:inline">Início</span>
      </a>
      <a href="catalogo.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg bg-green-600 dark:bg-dark-primary hover:bg-green-500 dark:hover:bg-dark-primary-hover transition text-sm md:text-xl">
        <i class="fas fa-leaf text-lg md:text-2xl"></i>
        <span class="hidden sm:inline">Catálogo</span>
      </a>
      <a href="admin.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl">
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

<main class="flex-grow pt-28 pb-8 mx-4 lg:mx-8 px-4 lg:px-0">
  <div class="flex justify-center mb-10">
    <form method="get" action="catalogo.php" class="w-full max-w-3xl">
      <div class="flex bg-white dark:bg-dark-card rounded-xl border-2 border-gray-200 dark:border-dark-border hover:border-green-400 dark:hover:border-primary-light transition-colors duration-300 shadow-sm hover:shadow-md overflow-hidden">
        <input
          type="text"
          id="busca"
          name="busca"
          placeholder="Pesquisar por nome científico ou popular"
          value="<?php echo isset($filtro) ? htmlspecialchars($filtro) : ''; ?>"
          class="flex-grow px-5 py-3 text-gray-700 dark:text-dark-text bg-transparent focus:outline-none text-lg border-none focus:ring-0"
        >
        <button 
          type="submit" 
          class="bg-green-600 dark:bg-dark-primary text-white px-6 py-3 hover:bg-green-500 dark:hover:bg-dark-primary-hover transition-all duration-300 flex items-center justify-center"
          aria-label="Pesquisar"
        >
          <i class="fas fa-search text-xl"></i>
          <span class="sr-only">Pesquisar</span>
        </button>
      </div>
    </form>
  </div>

  <section class="mx-auto w-full max-w-7xl">
    <?php if (empty($arvores)): ?>
      <div class="bg-white dark:bg-dark-card rounded-xl shadow-card p-8 text-center">
        <p class="text-xl text-gray-600 dark:text-gray-400">Nenhuma árvore encontrada com o termo "<?php echo htmlspecialchars($filtro); ?>".</p>
      </div>
    <?php else: ?>
      <div class="grid gap-8">
        <?php foreach ($arvores as $arvore): ?>
          <article class="tree-card bg-white dark:bg-dark-card rounded-xl shadow-md dark:shadow-lg overflow-hidden transition-all duration-300 hover:shadow-lg border-l-8 border-green-600 dark:border-dark-primary">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-green-50 dark:bg-dark-card-header p-6 gap-4 border-b border-green-100 dark:border-dark-border">
              <span class="text-sm text-gray-500 dark:text-gray-400 font-medium order-2 md:order-1">
                <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($arvore['horario']))); ?>
              </span>
              <h3 class="text-2xl md:text-3xl font-bold text-green-700 dark:text-dark-primary text-center md:text-left flex-grow px-0 md:px-4 order-1 md:order-2">
                <?php echo htmlspecialchars($arvore['nome_c']); ?>
              </h3>
              <button class="expand-btn bg-yellow-600 dark:bg-dark-secondary hover:bg-yellow-500 dark:hover:bg-orange-600 text-white px-5 py-3 rounded-lg transition-all duration-300 min-w-[120px] font-medium transform hover:scale-105 order-3">
                Expandir
              </button>
            </div>

            <div class="tree-details max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-gray-50 dark:bg-gray-700"> 
              <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-white/90 dark:bg-dark-card/80 p-6 rounded-lg border border-gray-100 dark:border-dark-border shadow-sm">
                  <h4 class="text-xl font-semibold text-green-700 dark:text-dark-primary mb-4 pb-2 border-b border-green-200 dark:border-dark-border">Informações Básicas</h4>
                  <div class="space-y-3 text-base">
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Nativa/Exótica:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['nat_exo']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Espécie:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['especie']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Localização:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['localizacao']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Vegetação:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['vegetacao']); ?></span></p>
                    <?php if (!empty($arvore['nomes_populares'])): ?>
                      <p><strong class="font-semibold text-green-700 dark:text-green-400">Nomes populares:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['nomes_populares']); ?></span></p>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="bg-green-50/30 dark:bg-gray-700/50 p-6 rounded-lg border border-green-100 dark:border-dark-border">
                  <h4 class="text-xl font-semibold text-green-700 dark:text-dark-primary mb-4 pb-2 border-b border-green-200 dark:border-dark-border">Características Físicas</h4>
                  <div class="space-y-3 text-base">
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Diâmetro do Peito:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['diametro_peito']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Estado Fitossanitário:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['estado_fitossanitario']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Estado do Tronco:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['estado_tronco']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Estado da Copa:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['estado_copa']); ?></span></p>
                  </div>
                </div>

                <div class="bg-green-50/30 dark:bg-gray-700/50 p-6 rounded-lg border border-green-100 dark:border-dark-border">
                  <h4 class="text-xl font-semibold text-green-700 dark:text-dark-primary mb-4 pb-2 border-b border-green-200 dark:border-dark-border">Ambiente</h4>
                  <div class="space-y-3 text-base">
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Tamanho da Calçada:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['tamanho_calcada']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Espaço para Árvore:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['espaco_arvore']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Raízes:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['raizes']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Acessibilidade:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['acessibilidade']); ?></span></p>
                  </div>
                </div>
              </div>

              <div class="px-8 pb-8">
                <div class="bg-yellow-50/50 dark:bg-yellow-700/30 border-l-4 border-yellow-500 dark:border-yellow-400 p-6 rounded-r-lg my-6">
                  <h4 class="text-xl font-semibold text-yellow-700 dark:text-yellow-300 mb-3">Curiosidade</h4>
                  <p class="text-gray-700 dark:text-gray-300 text-lg">
                    <?php echo !empty($arvore['curiosidade']) ? nl2br(htmlspecialchars($arvore['curiosidade'])) : 'Nenhuma curiosidade disponível.'; ?>
                  </p>
                </div>
                
                <div class="mt-6">
                  <h4 class="text-xl font-semibold text-green-700 dark:text-dark-primary mb-4">Galeria</h4>
                  <div class="swiper gallery-swiper-<?php echo $arvore['id']; ?>">
                    <div class="swiper-wrapper">
                      <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                        <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Folha+de+<?php echo urlencode(htmlspecialchars($arvore['nome_c'])); ?>" alt="Folha da <?php echo htmlspecialchars($arvore['nome_c']); ?>" onerror="this.src='https://placehold.co/600x300/E8F5E9/2E7D32?text=Imagem+Indisponível'; this.alt='Imagem da folha indisponível'">
                      </div>
                      <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                         <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Flor+de+<?php echo urlencode(htmlspecialchars($arvore['nome_c'])); ?>" alt="Flor da <?php echo htmlspecialchars($arvore['nome_c']); ?>" onerror="this.src='https://placehold.co/600x300/E8F5E9/2E7D32?text=Imagem+Indisponível'; this.alt='Imagem da flor indisponível'">
                      </div>
                      <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                         <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Fruta+de+<?php echo urlencode(htmlspecialchars($arvore['nome_c'])); ?>" alt="Fruta da <?php echo htmlspecialchars($arvore['nome_c']); ?>" onerror="this.src='https://placehold.co/600x300/E8F5E9/2E7D32?text=Imagem+Indisponível'; this.alt='Imagem da fruta indisponível'">
                      </div>
                       <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                         <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Copa+de+<?php echo urlencode(htmlspecialchars($arvore['nome_c'])); ?>" alt="Copa da <?php echo htmlspecialchars($arvore['nome_c']); ?>" onerror="this.src='https://placehold.co/600x300/E8F5E9/2E7D32?text=Imagem+Indisponível'; this.alt='Imagem da copa indisponível'">
                      </div>
                       <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                         <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Casco+de+<?php echo urlencode(htmlspecialchars($arvore['nome_c'])); ?>" alt="Casco da <?php echo htmlspecialchars($arvore['nome_c']); ?>" onerror="this.src='https://placehold.co/600x300/E8F5E9/2E7D32?text=Imagem+Indisponível'; this.alt='Imagem do casco indisponível'">
                      </div>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                  </div>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<footer class="bg-gradient-to-r from-green-700 to-green-600 dark:from-gray-800 dark:to-gray-700 text-white text-center py-6 shadow-inner mt-auto transition-colors duration-300">
  <div class="container mx-auto px-4">
    © <?php echo date('Y'); ?> - Catálogo de Árvores | Todos os direitos reservados
  </div>
</footer>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="style.js" defer></script>
<script>
// Script específico para catalogo.php (inicialização de Swipers e botões de expandir)
document.addEventListener('DOMContentLoaded', () => {
  // Inicializa os carrosseis Swiper para cada card de árvore
  const swiperContainers = document.querySelectorAll('[class*="gallery-swiper-"]');
  swiperContainers.forEach(container => {
    new Swiper(container, {
      loop: true,
      slidesPerView: 1, 
      spaceBetween: 20, 
      pagination: {
        el: container.querySelector('.swiper-pagination'), 
        clickable: true,
      },
      navigation: {
        nextEl: container.querySelector('.swiper-button-next'), 
        prevEl: container.querySelector('.swiper-button-prev'), 
      },
      breakpoints: {
        640: { slidesPerView: 2, spaceBetween: 20 },
        1024: { slidesPerView: 3, spaceBetween: 30 }
      }
    });
  });

  // Inicializa botões de expandir/recolher para detalhes da árvore
  const expandButtons = document.querySelectorAll('.expand-btn');
  expandButtons.forEach(button => {
    button.addEventListener('click', function() {
      const article = this.closest('article.tree-card');
      if (!article) return; 
      const details = article.querySelector('.tree-details');
      if (!details) return; 
      const isExpanded = details.classList.contains('max-h-[9999px]');

      if (!isExpanded) {
        document.querySelectorAll('.tree-card').forEach(otherArticle => {
          const otherDetails = otherArticle.querySelector('.tree-details');
          const otherButton = otherArticle.querySelector('.expand-btn');
          if (otherDetails && otherDetails !== details && otherDetails.classList.contains('max-h-[9999px]')) {
            otherDetails.classList.remove('max-h-[9999px]');
            otherDetails.classList.add('max-h-0');
            if (otherButton) otherButton.textContent = 'Expandir';
          }
        });
      }

      if (isExpanded) {
        details.classList.remove('max-h-[9999px]');
        details.classList.add('max-h-0');
        this.textContent = 'Expandir';
      } else {
        details.classList.remove('max-h-0');
        details.classList.add('max-h-[9999px]');
        this.textContent = 'Recolher';
        
        const swiperInstance = details.querySelector('[class*="gallery-swiper-"]');
        if (swiperInstance && swiperInstance.swiper) {
          swiperInstance.swiper.update(); 
        }
      }
    });
  });
});
</script>
</body>
</html>