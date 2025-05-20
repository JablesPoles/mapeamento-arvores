<?php
// index.php – Página Inicial do Catálogo de Árvores (Fatec Itapetininga)
  include 'templates/header.php';
?>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                <?php
                $membros = [
                    ['nome' => 'Nathanael Netto'],
                    ['nome' => 'Camilly Santos'],
                    ['nome' => 'Matheus Poles'],
                    ['nome' => 'Otavio Augusto'],
                    ['nome' => 'Enzo Padilha'],
                    ['nome' => 'Kayke Yuji'],
                    ['nome' => 'Marciel Silva'],
                ];
                $github_url = "https://github.com/JablesPoles/mapeamento-arvores";
                $cargo_padrao = "Programador(a)";

                foreach ($membros as $index => $membro): ?>
                    <div class="flex flex-col items-center bg-light-bg dark:bg-dark-bg p-4 rounded-xl shadow-card transition-all duration-300 hover:shadow-xl hover:scale-105 max-w-xs mx-auto w-full" data-aos="zoom-in" data-aos-delay="<?= $index * 100 ?>">
                        <img src="https://placehold.co/112x112/E8F5E9/2E7D32?text=<?= urlencode(substr($membro['nome'], 0, 1)) ?>" alt="Foto de <?= htmlspecialchars($membro['nome']) ?>" class="w-28 h-28 rounded-full mb-3 object-cover shadow-md">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white text-center"><?= htmlspecialchars($membro['nome']) ?></h4>
                        <p class="text-sm text-primary-light dark:text-primary-lighter text-center"><?= htmlspecialchars($cargo_padrao) ?></p>
                    </div>
                <?php endforeach; ?>

                <a href="<?= htmlspecialchars($github_url) ?>" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center justify-center bg-light-bg dark:bg-dark-bg p-4 rounded-xl shadow-card transition-all duration-300 hover:shadow-xl hover:scale-105 max-w-xs mx-auto w-full" data-aos="zoom-in" data-aos-delay="<?= count($membros) * 100 ?>">
                    <div class="w-28 h-28 rounded-full mb-3 flex items-center justify-center bg-gray-200 dark:bg-gray-700 shadow-md">
                        <i class="fab fa-github text-5xl text-gray-800 dark:text-white"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white text-center">Nosso Projeto</h4>
                    <p class="text-sm text-primary-light dark:text-primary-lighter text-center">Veja no GitHub</p>
                </a>
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
    <?php include 'templates/footer.php'; ?>
</main>

 <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
 <script src="assets/js/style.js" defer></script>
 <script src="assets/js/index.js" defer></script>
</body>
</html>