<?php
if (!isset($arvore)) { return; }

$idArvore = htmlspecialchars($arvore['id']);
$nomeCientificoArvore = htmlspecialchars($arvore['nome_c']);
$especieArvore = htmlspecialchars($arvore['especie']);
?>

<article class="tree-card bg-white dark:bg-dark-card rounded-xl shadow-md dark:shadow-lg overflow-hidden transition-all duration-300 hover:shadow-lg border-l-8 border-green-600 dark:border-dark-primary">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-green-50 dark:bg-dark-card-header p-6 gap-4 border-b border-green-100 dark:border-dark-border">
        <span class="text-sm text-gray-500 dark:text-gray-400 font-medium order-2 md:order-1">
            <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($arvore['horario']))); ?>
        </span>
        <h3 class="text-2xl md:text-3xl font-bold text-green-700 dark:text-dark-primary text-center md:text-left flex-grow px-0 md:px-4 order-1 md:order-2">
            <?php echo $nomeCientificoArvore; ?>
        </h3>
        <button
            class="expand-btn bg-yellow-600 dark:bg-dark-secondary hover:bg-yellow-500 dark:hover:bg-orange-600 text-white px-5 py-3 rounded-lg transition-all duration-300 min-w-[120px] font-medium transform hover:scale-105 order-3"
            aria-expanded="false"
            aria-controls="details-<?php echo $idArvore; ?>"
            aria-label="Expandir detalhes da árvore <?php echo $nomeCientificoArvore; ?>">
            Expandir
        </button>
    </div>

    <div id="details-<?php echo $idArvore; ?>" class="tree-details max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-gray-50 dark:bg-gray-700">
        <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white/90 dark:bg-dark-card/80 p-6 rounded-lg border border-gray-100 dark:border-dark-border shadow-sm">
                <h4 class="text-xl font-semibold text-green-700 dark:text-dark-primary mb-4 pb-2 border-b border-green-200 dark:border-dark-border">Informações Básicas</h4>
                <div class="space-y-3 text-base">
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Nativa/Exótica:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo htmlspecialchars($arvore['nat_exo']); ?></span></p>
                    <p><strong class="font-semibold text-green-700 dark:text-green-400">Espécie:</strong> <span class="text-gray-700 dark:text-gray-300 ml-1"><?php echo $especieArvore; ?></span></p>
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
                <div class="swiper gallery-swiper-<?php echo $idArvore; ?>">
                    <div class="swiper-wrapper">
                        <?php if ($imagensArvore): ?>
                            <?php foreach ($imagensArvore as $tipo => $url): ?>
                                <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                                    <img src="<?php echo htmlspecialchars($url); ?>"
                                         alt="<?php echo htmlspecialchars(ucfirst($tipo) . ' de ' . $nomeCientificoArvore); ?>"
                                         onerror="this.src='https://placehold.co/600x300/E8F5E9/2E7D32?text=Imagem+Indispon%C3%ADvel'; this.alt='Imagem indisponível'">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white p-2 text-center text-sm">
                                        <?php echo htmlspecialchars(ucfirst($tipo)); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                                <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Folha+de+<?php echo urlencode($nomeCientificoArvore); ?>"
                                     alt="Folha de <?php echo $nomeCientificoArvore; ?>">
                                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white p-2 text-center text-sm">Folha</div>
                            </div>
                            <div class="swiper-slide bg-gray-100 dark:bg-gray-600">
                                <img src="https://placehold.co/600x300/E8F5E9/2E7D32?text=Flor+de+<?php echo urlencode($nomeCientificoArvore); ?>"
                                     alt="Flor de <?php echo $nomeCientificoArvore; ?>">
                                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white p-2 text-center text-sm">Flor</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</article>