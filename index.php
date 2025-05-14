<?php
require 'conexao.php';

// Script de busca
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

$sql .= " GROUP BY arvore.id, arvore.nome_c, arvore.nat_exo, arvore.horario, arvore.localizacao, 
          arvore.vegetacao, arvore.especie, arvore.diametro_peito, arvore.estado_fitossanitario, 
          arvore.estado_tronco, arvore.estado_copa, arvore.tamanho_calcada, arvore.espaco_arvore, 
          arvore.raizes, arvore.acessibilidade, arvore.curiosidade
          ORDER BY arvore.horario DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arvores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Catálogo de Árvores</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Adicione as dependências do Swiper -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <style>
    /* Estilos personalizados para o carrossel */
    .swiper {
      width: 100%;
      height: 300px;
      border-radius: 0.5rem;
      margin-top: 1rem;
      background-color: #f7faf7;
    }
    .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }
    .swiper-button-next, .swiper-button-prev {
      color: #4CAF50;
      padding: 0 15px;
    }
    .swiper-pagination-bullet-active {
      background: #4CAF50;
    }
  </style>
  <script>
    tailwind.config = {
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
            'light-bg': '#f7faf7' 
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light-bg font-sans text-gray-800 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-green-700 text-white px-6 py-4 flex justify-between items-center shadow-lg">
    <h1 class="text-2xl font-bold">Catálogo de Árvores</h1>
    <nav class="flex gap-4">
      <a href="index.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-500 px-4 py-2 rounded-lg text-white transition-all duration-300 transform hover:scale-105">
        <i class="fas fa-home"></i> Página Inicial
      </a>
      <a href="admin.php" class="flex items-center gap-2 bg-green-600 hover:bg-green-500 px-4 py-2 rounded-lg text-white transition-all duration-300 transform hover:scale-105">
        <i class="fas fa-user-cog"></i> Painel Admin
      </a>
    </nav>
  </header>

  <!-- Main Content -->
  <main class="flex-grow my-8 mx-4 lg:mx-8 px-4 lg:px-0">
    <!-- Search Form -->
    <div class="flex justify-center mb-10">
      <form method="get" class="w-full max-w-3xl">
        <div class="flex bg-white rounded-xl border-2 border-gray-200 hover:border-green-400 transition-colors duration-300 shadow-sm hover:shadow-md overflow-hidden">
          <input
            type="text"
            id="busca"
            name="busca"
            placeholder="Pesquisar por nome científico ou popular"
            value="<?php echo isset($filtro) ? htmlspecialchars($filtro) : ''; ?>"
            class="flex-grow px-5 py-3 text-gray-700 focus:outline-none text-lg border-none focus:ring-0"
          >
          <button 
            type="submit" 
            class="bg-green-600 text-white px-6 py-3 hover:bg-green-500 transition-all duration-300 flex items-center justify-center"
          >
            <i class="fas fa-search text-xl"></i>
            <span class="sr-only">Pesquisar</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Tree List -->
    <section class="mx-auto w-full max-w-7xl">
      <?php if (empty($arvores)): ?>
        <div class="bg-white rounded-xl shadow-card p-8 text-center">
          <p class="text-xl text-gray-600">Nenhuma árvore encontrada.</p>
        </div>
      <?php else: ?>
        <div class="grid gap-8">
          <?php foreach ($arvores as $arvore): ?>
            <article class="tree-card bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-lg border-l-8 border-green-600">
              <!-- Card Header -->
              <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-green-50 p-6 gap-4 border-b border-green-100">
                <span class="text-sm text-gray-500 font-medium">
                  <?php echo date('d/m/Y H:i', strtotime($arvore['horario'])); ?>
                </span>
                <h3 class="text-2xl md:text-3xl font-bold text-green-700 text-center flex-grow px-4">
                  <?php echo htmlspecialchars($arvore['nome_c']); ?>
                </h3>
                <button class="expand-btn bg-yellow-600 hover:bg-yellow-500 text-white px-5 py-3 rounded-lg transition-all duration-300 min-w-[120px] font-medium transform hover:scale-105">
                  Expandir
                </button>
              </div>

              <!-- Tree Details -->
              <div class="tree-details max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-gray-50">
                <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <!-- Coluna 1 -->
                  <div class="bg-white/90 p-6 rounded-lg border border-gray-100 shadow-sm">
                    <h4 class="text-xl font-semibold text-green-700 mb-4 pb-2 border-b border-green-200">Informações Básicas</h4>
                    <div class="space-y-4">
                      <p>
                        <span class="font-bold text-green-700">Nativa/Exótica:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['nat_exo']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Espécie:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['especie']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Localização:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['localizacao']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Vegetação:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['vegetacao']); ?></span>
                      </p>
                      <?php if (!empty($arvore['nomes_populares'])): ?>
                        <p>
                          <span class="font-bold text-green-700">Nomes populares:</span>
                          <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['nomes_populares']); ?></span>
                        </p>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Coluna 2 -->
                  <div class="bg-green-50/30 p-6 rounded-lg border border-green-100">
                    <h4 class="text-xl font-semibold text-green-700 mb-4 pb-2 border-b border-green-200">Características Físicas</h4>
                    <div class="space-y-4">
                      <p>
                        <span class="font-bold text-green-700">Diâmetro do Peito:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['diametro_peito']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Estado Fitossanitário:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['estado_fitossanitario']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Estado do Tronco:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['estado_tronco']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Estado da Copa:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['estado_copa']); ?></span>
                      </p>
                    </div>
                  </div>

                  <!-- Coluna 3 -->
                  <div class="bg-green-50/30 p-6 rounded-lg border border-green-100">
                    <h4 class="text-xl font-semibold text-green-700 mb-4 pb-2 border-b border-green-200">Ambiente</h4>
                    <div class="space-y-4">
                      <p>
                        <span class="font-bold text-green-700">Tamanho da Calçada:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['tamanho_calcada']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Espaço para Árvore:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['espaco_arvore']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Raízes:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['raizes']); ?></span>
                      </p>
                      <p>
                        <span class="font-bold text-green-700">Acessibilidade:</span>
                        <span class="text-gray-700 ml-2"><?php echo htmlspecialchars($arvore['acessibilidade']); ?></span>
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Curiosidade e Galeria -->
                <div class="px-8 pb-8">
                  <div class="bg-yellow-50/50 border-l-4 border-yellow-500 p-6 rounded-r-lg mb-6">
                    <h4 class="text-xl font-semibold text-yellow-700 mb-3">Curiosidade</h4>
                    <p class="text-gray-700 text-lg">
                      <?php echo !empty($arvore['curiosidade']) ? htmlspecialchars($arvore['curiosidade']) : 'Nenhuma curiosidade disponível.'; ?>
                    </p>
                  </div>
                  
                  <!-- Galeria - Carrossel -->
                  <div class="mt-6">
                    <h4 class="text-xl font-semibold text-green-700 mb-4">Galeria</h4>
                    <div class="swiper gallery-swiper-<?= $arvore['id'] ?>">
                      <div class="swiper-wrapper">
                        <!-- Slide Folha -->
                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                          <div class="text-center p-4">
                            <div class="text-gray-500 mb-2">[Folha]</div>
                            <div class="text-sm text-gray-400">Imagem da folha da <?= htmlspecialchars($arvore['nome_c']) ?></div>
                          </div>
                        </div>
                        <!-- Slide Flor -->
                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                          <div class="text-center p-4">
                            <div class="text-gray-500 mb-2">[Flor]</div>
                            <div class="text-sm text-gray-400">Imagem da flor da <?= htmlspecialchars($arvore['nome_c']) ?></div>
                          </div>
                        </div>
                        <!-- Slide Fruta -->
                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                          <div class="text-center p-4">
                            <div class="text-gray-500 mb-2">[Fruta]</div>
                            <div class="text-sm text-gray-400">Imagem da fruta da <?= htmlspecialchars($arvore['nome_c']) ?></div>
                          </div>
                        </div>
                        <!-- Slide Copa -->
                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                          <div class="text-center p-4">
                            <div class="text-gray-500 mb-2">[Copa]</div>
                            <div class="text-sm text-gray-400">Imagem da copa da <?= htmlspecialchars($arvore['nome_c']) ?></div>
                          </div>
                        </div>
                        <!-- Slide Casco -->
                        <div class="swiper-slide bg-gray-100 flex items-center justify-center">
                          <div class="text-center p-4">
                            <div class="text-gray-500 mb-2">[Casco]</div>
                            <div class="text-sm text-gray-400">Imagem do casco da <?= htmlspecialchars($arvore['nome_c']) ?></div>
                          </div>
                        </div>
                      </div>
                      <!-- Navegação -->
                      <div class="swiper-button-next"></div>
                      <div class="swiper-button-prev"></div>
                      <!-- Paginação -->
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

  <!-- Footer -->
  <footer class="bg-gradient-to-r from-green-700 to-green-600 text-white text-center py-6 shadow-inner mt-12">
    <div class="container mx-auto px-4">
      &copy; <?php echo date('Y'); ?> - Catálogo de Árvores | Todos os direitos reservados
    </div>
  </footer>

<!-- JavaScript -->
<script>
    // Função para inicializar os carrosseis
    function initCarrosseis() {
      document.querySelectorAll('[class*="gallery-swiper-"]').forEach(swiperEl => {
        new Swiper(swiperEl, {
          loop: true,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
        });
      });
    }

    // Função para os botões de expansão
    function initExpandButtons() {
      document.querySelectorAll('.expand-btn').forEach(button => {
        button.addEventListener('click', function() {
          const details = this.closest('article').querySelector('.tree-details');
          const isExpanded = details.classList.contains('max-h-[9999px]');
          
          // Fecha outros cards
          document.querySelectorAll('.tree-details').forEach(d => {
            if (d !== details) {
              d.classList.add('max-h-0', 'overflow-hidden');
              d.classList.remove('max-h-[9999px]');
              d.closest('article').querySelector('.expand-btn').textContent = 'Expandir';
            }
          });

          // Alterna estado atual
          if (isExpanded) {
            details.classList.remove('max-h-[9999px]');
            details.classList.add('max-h-0');
            this.textContent = 'Expandir';
          } else {
            details.classList.remove('max-h-0');
            details.classList.add('max-h-[9999px]');
            this.textContent = 'Recolher';
            
            // Re-inicializa os carrosseis quando expandido
            setTimeout(initCarrosseis, 50);
          }
        });
      });
    }

    // Inicializa tudo quando a página carrega
    document.addEventListener('DOMContentLoaded', () => {
      initExpandButtons();
      initCarrosseis();
    });
</script>
</body>
</html>