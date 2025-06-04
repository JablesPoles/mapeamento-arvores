<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start();
}
require 'conexao.php'; 
require __DIR__ . '/src/db_functions.php';
require __DIR__ . '/src/api_functions.php';

$itensPorPagina = 10;
$pagina_atual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// $filtro_original_busca: Contém o valor exato digitado pelo usuário ou vindo da URL.
// Usado para preencher o campo de busca e para construir os links de paginação.
$filtro_original_busca = isset($_GET['busca']) ? trim(urldecode($_GET['busca'])) : '';

// Armazena o filtro original na sessão para outros usos, se necessário (ex: redirecionamento)
$_SESSION['last_search_filter'] = $filtro_original_busca;

// Lógica para determinar se a busca é por ID ou por texto
$id_filtro_db = null;          // Para o parâmetro id_filtro das funções de DB
$texto_filtro_db = $filtro_original_busca; // Para o parâmetro filtro_texto das funções de DB

if (!empty($filtro_original_busca)) {
    if (preg_match('/^#(\d+)$/', $filtro_original_busca, $matches_id)) { // Verifica se o filtro é #ID (ex: #123)
        $id_filtro_db = (int)$matches_id[1];
        $texto_filtro_db = ''; // Limpa o filtro de texto, pois a busca é por ID
    } elseif (ctype_digit($filtro_original_busca)) { // Verifica se o filtro é puramente numérico (ex: 123)
        $id_filtro_db = (int)$filtro_original_busca;
        $texto_filtro_db = ''; // Limpa o filtro de texto
    }
}

// Contar total de resultados com base nos filtros (texto e/ou ID)
$totalResultados = contarTotalArvores($pdo, $texto_filtro_db, $id_filtro_db);
$totalPaginas = $totalResultados > 0 ? ceil($totalResultados / $itensPorPagina) : 1;

// Garante que a página atual não exceda o total de páginas
$pagina_atual = min($pagina_atual, $totalPaginas); 
$offset = ($pagina_atual - 1) * $itensPorPagina;

// Buscar árvores paginadas com base nos filtros (texto e/ou ID)
$arvores = buscarArvoresPaginadas($pdo, $texto_filtro_db, $offset, $itensPorPagina, $id_filtro_db);

// Mensagens de feedback (ex: após deletar uma árvore)
$mensagem_catalogo = $_SESSION['mensagem_catalogo'] ?? null;
$tipo_mensagem_catalogo = $_SESSION['tipo_mensagem_catalogo'] ?? null;
unset($_SESSION['mensagem_catalogo'], $_SESSION['tipo_mensagem_catalogo']);

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
                    placeholder="Pesquisar por nome, #ID ou ID"
                    value="<?php echo htmlspecialchars($filtro_original_busca); // Exibe o filtro original digitado ?>"
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

    <?php if ($mensagem_catalogo): ?>
        <div class="max-w-7xl mx-auto mb-6 p-4 rounded-lg <?php echo ($tipo_mensagem_catalogo === 'success') ? 'bg-green-100 dark:bg-green-900/30 border border-green-300 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 border border-red-300 text-red-700 dark:text-red-300'; ?> text-center" role="alert">
            <?php echo htmlspecialchars($mensagem_catalogo); ?>
        </div>
    <?php endif; ?>

    <section class="mx-auto w-full max-w-7xl">
        <?php if (empty($arvores)): ?>
            <div class="bg-white dark:bg-dark-card rounded-xl shadow-card p-8 text-center">
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    <?php if (!empty($filtro_original_busca)): ?>
                        Nenhuma árvore encontrada para "<?php echo htmlspecialchars($filtro_original_busca); ?>".
                    <?php else: ?>
                        Nenhuma árvore cadastrada no momento.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="grid gap-8">
                <?php foreach ($arvores as $arvore): ?>
                    <?php
                    // Termo para buscar imagens na API e no cache
                    $termoBuscaAPI = !empty($arvore['especie']) ? $arvore['especie'] : $arvore['nome_c'];
                    $imagensArvore = buscarImagensCache($pdo, $termoBuscaAPI); 

                    if (!$imagensArvore) {
                        $imagensAPI = buscarImagensPlantNet($termoBuscaAPI);
                        if ($imagensAPI) {
                            // Tenta buscar o ID da árvore pelo nome científico/popular para associar o cache
                            $idArvoreParaCache = buscarIdArvorePorNomeCientifico($pdo, $termoBuscaAPI); 
                            if ($idArvoreParaCache) {
                                salvarImagensCache($pdo, $idArvoreParaCache, $imagensAPI);
                            }
                            $imagensArvore = $imagensAPI;
                        } else {
                            $imagensArvore = []; // Garante que $imagensArvore seja um array
                        }
                    }
                    // Passa o filtro original para o tree_card para que o botão de deletar possa usá-lo no redirecionamento
                    $filtro_para_card = $filtro_original_busca; 
                    // Define $filtro_texto explicitamente para tree_card.php, caso ele dependa desse nome de variável.
                    $filtro_texto = $filtro_original_busca;
                    include __DIR__ . '/templates/tree_card.php'; 
                    ?>
                <?php endforeach; ?>
            </div>

            <?php 
            // Condição para exibir a paginação
            if ($totalPaginas > 1): 
                // Passa as variáveis necessárias para pagination.php
                // pagination.php espera $pagina, $totalPaginas, e $filtro (com o valor original da busca)
                $pagina = $pagina_atual; // Renomeia para o nome esperado por pagination.php
                $filtro = $filtro_original_busca; // Garante que pagination.php use o filtro original para os links
            ?>
                <?php include __DIR__ . '/templates/pagination.php'; ?>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- O style.js já é carregado no header.php, não precisa aqui geralmente, mas não causa problema. -->
<!-- <script src="assets/js/style.js" defer></script> --> 
<script src="assets/js/catalogo_scripts.js" defer></script>
</body>
</html>
