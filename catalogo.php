<?php
require 'conexao.php'; // Garante que $pdo está disponível
require __DIR__ . '/src/db_functions.php';
require __DIR__ . '/src/api_functions.php';

$itensPorPagina = 10;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1; // Garante que a página seja no mínimo 1
$filtro = isset($_GET['busca']) ? trim(urldecode($_GET['busca'])) : '';

$totalResultados = contarTotalArvores($pdo, $filtro);
$totalPaginas = ($totalResultados > 0) ? ceil($totalResultados / $itensPorPagina) : 1; // Evita divisão por zero
$pagina = min($pagina, $totalPaginas); // Corrige a página se for maior que o total
$offset = ($pagina - 1) * $itensPorPagina;

$arvores = buscarArvoresPaginadas($pdo, $filtro, $offset, $itensPorPagina);

// Inclui o cabeçalho HTML
include __DIR__ . '/templates/header.php';
?>

<main class="flex-grow pt-28 pb-8 mx-4 lg:mx-8 px-4 lg:px-0">
    <div class="flex justify-center mb-10">
        <form method="get" action="catalogo.php" class="w-full max-w-3xl">
            <div class="flex bg-white dark:bg-dark-card rounded-xl border-2 border-gray-200 dark:border-dark-border hover:border-green-400 dark:hover:border-primary-light transition-colors duration-300 shadow-sm hover:shadow-md overflow-hidden">
                <input
                    type="text"
                    id="busca"
                    name="busca"
                    placeholder="Pesquisar por nome científico ou popular"
                    value="<?php echo htmlspecialchars($filtro); ?>"
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
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    <?php if (!empty($filtro)): ?>
                        Nenhuma árvore encontrada com o termo "<?php echo htmlspecialchars($filtro); ?>".
                    <?php else: ?>
                        Nenhuma árvore cadastrada no momento.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="grid gap-8">
                <?php foreach ($arvores as $arvore): ?>
                    <?php
                    // A busca de imagens é feita aqui para passar para o template do card
                    $imagensArvore = buscarImagensPlantNet($arvore['especie'] ?: $arvore['nome_c']); // Tenta espécie, senão nome científico
                    include __DIR__ . '/templates/tree_card.php'; // Passa $arvore e $imagensArvore
                    ?>
                <?php endforeach; ?>
            </div>
            <?php if ($totalPaginas > 1): ?>
                <?php include __DIR__ . '/templates/pagination.php'; // Passa $pagina, $totalPaginas, $filtro ?>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php
// Inclui o rodapé HTML
include __DIR__ . '/templates/footer.php';
?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="assets/js/style.js" defer></script> <script src="assets/js/catalogo_scripts.js" defer></script> </body>
</html>