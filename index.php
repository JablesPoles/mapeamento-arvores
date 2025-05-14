<?php
// index.php – Página Inicial do Catálogo de Árvores (Fatec Itapetininga)
?>
<!DOCTYPE html>
<html lang="pt-BR" class="">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Página Inicial - Catálogo de Árvores</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
            'dark-text': '#e2e8f0',      
            'dark-primary': '#38a169',   
            'dark-primary-hover': '#2f855a',
            'dark-secondary': '#dd6b20', 
          },
          boxShadow: {
            'card': '0 2px 8px rgba(0, 0, 0, 0.08)',
            'card-hover': '0 4px 12px rgba(0, 0, 0, 0.12)'
          },
          aspectRatio: {
            '16/9': '56.25%',
          },
        }
      }
    }
  </script>
  <style>
    html { scroll-behavior: smooth; }
    body { 
        padding-top: 5rem;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    main {
        flex-grow: 1;
    }
    .hero-section-text-shadow {
        text-shadow: 1px 1px 3px rgba(0, 54, 10, 0.7);
    }
    .swiper-slide img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    .swiper-pagination-bullet-active {
      background: var(--swiper-theme-color, #2E7D32) !important;
    }
    html.dark .swiper-pagination-bullet-active {
      background: var(--swiper-theme-color-dark, #4CAF50) !important;
    }
    .swiper-button-next, .swiper-button-prev {
      color: var(--swiper-theme-color, #2E7D32) !important;
    }
    html.dark .swiper-button-next, html.dark .swiper-button-prev {
       color: var(--swiper-theme-color-dark, #4CAF50) !important;
    }
    .video-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; 
        height: 0;
        overflow: hidden;
        border-radius: 1rem; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    #theme-toggle-button svg {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
  </style>
</head>
<body class="bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text">

  <header class="fixed top-0 left-0 w-full bg-green-700 dark:bg-gray-800 text-white shadow-lg z-50 transition-colors duration-300">
    <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 h-20">
      <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold" data-aos="fade-down">Página Inicial</h1>
      <nav class="flex items-center gap-2 md:gap-4">
        <a href="index.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg bg-green-600 dark:bg-dark-primary hover:bg-green-500 dark:hover:bg-dark-primary-hover transition text-sm md:text-xl">
          <i class="fas fa-home text-lg md:text-2xl"></i>
          <span class="hidden sm:inline">Início</span>
        </a>
        <a href="catalogo.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl">
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

  <main class="bg-light-bg dark:bg-dark-bg">
    <section class="relative h-screen bg-cover bg-center flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1950&q=80');">
      <div class="absolute inset-0 bg-black bg-opacity-50 dark:bg-opacity-60"></div> 
      <div class="relative z-10 text-center text-white p-6 max-w-3xl mx-auto" data-aos="fade-up">
          <h2 class="text-5xl md:text-6xl font-extrabold mb-6 hero-section-text-shadow">Bem-vindo ao Catálogo de Árvores</h2>
          <p class="text-xl md:text-2xl mb-8 hero-section-text-shadow">
              Um projeto acadêmico da Fatec Itapetininga dedicado a documentar e compartilhar conhecimento sobre as espécies arbóreas, promovendo a educação e a preservação ambiental.
          </p>
          <a href="#sobre" class="bg-white hover:bg-gray-200 text-green-700 dark:text-dark-primary dark:hover:bg-gray-300 font-bold px-8 py-4 rounded-lg text-lg md:text-xl transition-transform transform hover:scale-105 shadow-md">
              Saiba Mais
          </a>
      </div>
    </section>

    <section id="sobre" class="py-16 md:py-24 bg-white dark:bg-dark-card">
      <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
        <div class="lg:w-1/2" data-aos="fade-right">
          <img src="https://images.pexels.com/photos/36717/amazing-animal-beautiful-beautifull.jpg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Árvore exuberante ao pôr do sol" class="rounded-2xl shadow-xl w-full h-auto object-cover" style="max-height: 500px;"/>
        </div>
        <div class="lg:w-1/2" data-aos="fade-left">
          <h3 class="text-4xl md:text-5xl font-semibold text-primary dark:text-dark-primary mb-6">Sobre o Projeto</h3>
          <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
            Este catálogo é uma iniciativa dos estudantes da Fatec Itapetininga, com o intuito de criar um repositório digital detalhado sobre as árvores presentes em nosso meio urbano e regional. 
            Cada registro inclui informações científicas, nomes populares, características distintivas, fotografias e curiosidades.
          </p>
          <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
            Nosso objetivo primordial é fomentar a conscientização sobre a importância da flora local e oferecer uma ferramenta educacional valiosa para estudantes, pesquisadores e todos os entusiastas da natureza.
          </p>
        </div>
      </div>
    </section>

    <section id="funcionalidades" class="py-16 md:py-24 bg-light-bg dark:bg-dark-bg">
      <div class="container mx-auto px-6">
        <h3 class="text-4xl md:text-5xl font-semibold text-primary dark:text-dark-primary mb-12 text-center" data-aos="fade-up">Funcionalidades Principais</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="bg-white dark:bg-dark-card p-8 rounded-xl shadow-card hover:shadow-card-hover transition-shadow duration-300" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-center bg-primary-lighter dark:bg-gray-700 w-16 h-16 rounded-full mb-6">
              <i class="fas fa-search text-3xl text-primary dark:text-dark-primary"></i>
            </div>
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white mb-3">Busca Detalhada</h4>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Encontre árvores por nome científico, popular ou características específicas.</p>
          </div>
          <div class="bg-white dark:bg-dark-card p-8 rounded-xl shadow-card hover:shadow-card-hover transition-shadow duration-300" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center justify-center bg-primary-lighter dark:bg-gray-700 w-16 h-16 rounded-full mb-6">
              <i class="fas fa-images text-3xl text-primary dark:text-dark-primary"></i>
            </div>
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white mb-3">Galeria Interativa</h4>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Explore imagens em alta resolução de folhas, flores, frutos e portes das árvores.</p>
          </div>
          <div class="bg-white dark:bg-dark-card p-8 rounded-xl shadow-card hover:shadow-card-hover transition-shadow duration-300" data-aos="fade-up" data-aos-delay="300">
            <div class="flex items-center justify-center bg-primary-lighter dark:bg-gray-700 w-16 h-16 rounded-full mb-6">
              <i class="fas fa-database text-3xl text-primary dark:text-dark-primary"></i>
            </div>
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white mb-3">Painel Administrativo</h4>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Gerencie o catálogo com facilidade, adicionando e atualizando informações.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="video" class="py-16 md:py-24 bg-white dark:bg-dark-card">
      <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
        <div class="lg:w-1/2" data-aos="fade-right">
          <div class="video-container">
            <iframe 
              src="https://www.youtube.com/embed/vRYUOiw-OHQ" 
              title="Árvores Urbanas e Arborização" 
              frameborder="0" 
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
              allowfullscreen>
            </iframe>
          </div>
        </div>
        <div class="lg:w-1/2" data-aos="fade-left">
          <h3 class="text-4xl md:text-5xl font-semibold text-primary dark:text-dark-primary mb-6">A Importância das Árvores Urbanas</h3>
          <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
            Assista a este vídeo para entender melhor os benefícios da arborização urbana, desde a melhoria da qualidade do ar até o bem-estar da população e a valorização dos espaços públicos. (Vídeo em português sobre arborização urbana: benefícios do plantio e preservação de árvores nas cidades.)
          </p>
        </div>
      </div>
    </section>

    <section id="galeria" class="py-16 md:py-24 bg-light-bg dark:bg-dark-bg">
      <div class="container mx-auto px-6" data-aos="fade-up">
        <h3 class="text-4xl md:text-5xl font-semibold text-primary dark:text-dark-primary mb-12 text-center">Galeria de Espécies</h3>
        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            <?php
            $galeria = [
              ["url" => "https://arquitetura.vivadecora.com.br/wp-content/uploads/2019/02/arboriza%C3%A7%C3%A3o-urbana-problemas-na-cal%C3%A7ada.jpg", "alt" => "Arborização urbana, problemas na calçada"],
              ["url" => "https://t.jus.com.br/nLr5tz1WI2kn8xWNPXOxMSF5zBM=/704x400/smart/assets.jus.com.br/system/file/334/de81de0d0c007cbb05b50f63dfdd0408.jpg", "alt" => "Árvores em via pública"],
              ["url" => "https://cdnm.westwing.com.br/glossary/uploads/br/2023/06/28180824/arborizac%CC%A7a%CC%83o-1.png", "alt" => "Arborização em rua residencial"],
              ["url" => "https://snoopy.archdaily.com/images/archdaily/media/images/5e38/7fe2/3312/fdd4/5200/0111/slideshow/alex-zarubi-SpNOTlrAnLA-unsplash.jpg?1580761036&format=webp&width=640&height=580", "alt" => "Parque urbano com árvores altas"],
              ["url" => "https://snoopy.archdaily.com/images/archdaily/media/images/6000/7f54/63c0/1727/af00/0269/slideshow/Lago_das_Rosas__em_Goi%C3%A2nia.jpg?1610645311&format=webp&width=640&height=580", "alt" => "Lago das Rosas em Goiânia com arborização"],
              ["url" => "https://arquitetura.vivadecora.com.br/wp-content/uploads/2019/02/arboriza%C3%A7%C3%A3o-urbana-fia%C3%A7%C3%A3o-el%C3%A9trica.jpg", "alt" => "Arborização urbana e fiação elétrica"]
            ];
            foreach ($galeria as $img): ?>
              <div class="swiper-slide h-80 md:h-96">
                <img src="<?= htmlspecialchars($img['url']) ?>" alt="<?= htmlspecialchars($img['alt']) ?>" class="rounded-xl object-cover w-full h-full shadow-lg" />
              </div>
            <?php endforeach; ?>
          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination mt-6 relative"></div>
        </div>
      </div>
    </section>

    <section id="equipe" class="py-16 md:py-24 bg-white dark:bg-dark-card">
      <div class="container mx-auto px-6" data-aos="fade-up">
        <h3 class="text-4xl md:text-5xl font-semibold text-primary dark:text-dark-primary mb-12 text-center">Nossa Equipe</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-8">
          <?php
          $nomes_membros = ['Nathanael','Poles','Camilly','Marciel','Katye','Membro 6'];
          foreach ($nomes_membros as $index => $nome): ?>
            <div class="flex flex-col items-center bg-light-bg dark:bg-dark-bg p-6 rounded-xl shadow-card transition-shadow hover:shadow-card-hover" data-aos="zoom-in" data-aos-delay="<?= $index * 100 ?>">
              <img src="https://placehold.co/400x400/E8F5E9/2E7D32?text=<?= urlencode($nome) ?>" alt="Foto de <?= htmlspecialchars($nome) ?>" class="w-32 h-32 rounded-full mb-4 object-cover shadow-md">
              <h4 class="text-xl font-semibold text-gray-800 dark:text-white"><?= htmlspecialchars($nome) ?></h4>
              <p class="text-primary-light dark:text-primary-lighter">Programador</p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section id="contato" class="py-16 md:py-24 bg-light-bg dark:bg-dark-bg">
      <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
        <div class="lg:w-1/2" data-aos="fade-right">
          <img src="https://bkpsitecpsnew.blob.core.windows.net/uploadsitecps/sites/1/2020/10/Fatec-21.jpg" alt="Fachada da Fatec Itapetininga" class="rounded-2xl shadow-xl w-full h-auto object-cover" style="max-height: 450px;"/>
        </div>
        <div class="lg:w-1/2" data-aos="fade-left">
          <h3 class="text-4xl md:text-5xl font-semibold text-primary dark:text-dark-primary mb-6">Entre em Contato</h3>
          <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
            A Fatec Itapetininga está localizada na Rua João Viêira de Camargo, 104 - Vila Barth, Itapetininga - SP, CEP 18205-340.
          </p>
          <p class="text-lg md:text-xl text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
            Telefone: (15) 3273-8011
          </p>
          <a href="https://fatecitapetininga.edu.br/contato/" target="_blank" class="inline-flex items-center gap-2 bg-primary dark:bg-dark-primary hover:bg-green-800 dark:hover:bg-dark-primary-hover text-white font-semibold px-6 py-3 rounded-lg text-lg transition-colors duration-300 shadow-md hover:shadow-lg">
            <i class="fas fa-external-link-alt"></i> Visite o Site da Fatec
          </a>
        </div>
      </div>
    </section>
  </main>

  <footer class="bg-green-700 dark:bg-gray-800 text-white text-center py-8 transition-colors duration-300">
    <p class="text-lg">© <?= date('Y') ?> Fatec Itapetininga – Projeto Catálogo de Árvores</p>
  </footer>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="style.js" defer></script>
  <script>
    // Script específico para index.php (inicialização do Swiper principal)
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof Swiper !== 'undefined' && document.querySelector('.mySwiper')) {
        const mainGallerySwiper = new Swiper('.mySwiper', {
          loop: true,
          speed: 800,
          slidesPerView: 1,
          spaceBetween: 20,
          autoplay: {
            delay: 4000,
            disableOnInteraction: false,
          },
          breakpoints: {
            768: { slidesPerView: 2, spaceBetween: 25 },
            1024: { slidesPerView: 3, spaceBetween: 30 }
          },
          pagination: { 
            el: '.swiper-pagination', 
            clickable: true,
            dynamicBullets: true,
          },
          navigation: { 
            nextEl: '.swiper-button-next', 
            prevEl: '.swiper-button-prev',
          },
          keyboard: {
            enabled: true,
          },
        });
      } else {
        console.warn('Swiper não está definido ou o container .mySwiper não foi encontrado.');
      }
    });
  </script>
</body>
</html>
