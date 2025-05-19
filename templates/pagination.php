<?php
// Validação inicial para evitar execução se dados essenciais não estiverem definidos ou se houver apenas uma página
if (!isset($pagina) || !isset($totalPaginas) || !isset($filtro) || $totalPaginas <= 1) {
    return;
}

$numLinksAdjacentes = 2; // Quantidade de links exibidos antes e depois da página atual
$queryStringParams = array_filter(['busca' => $filtro]); // Parâmetros de consulta, exclui filtro vazio
?>
<nav class="mt-10 flex justify-center" aria-label="Paginação">
    <ul class="inline-flex items-center space-x-1 bg-white dark:bg-dark-card rounded-lg p-2 shadow">
        <?php if ($pagina > 1): ?>
            <!-- Link para página anterior -->
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $pagina - 1])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm"
                   aria-label="Página anterior">
                    &laquo;
                </a>
            </li>
        <?php endif; ?>

        <?php if ($pagina > ($numLinksAdjacentes + 1)): ?>
            <!-- Link para primeira página -->
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => 1])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm">1</a>
            </li>
            <?php if ($pagina > ($numLinksAdjacentes + 2)): ?>
                <!-- Indicação de salto de páginas -->
                <li><span class="px-3 py-1 text-gray-500 dark:text-gray-400">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php 
        // Links das páginas próximas à página atual
        for ($i = max(1, $pagina - $numLinksAdjacentes); $i <= min($totalPaginas, $pagina + $numLinksAdjacentes); $i++): 
        ?>
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $i])); ?>"
                   class="px-3 py-1 rounded-md text-sm
                     <?php echo $i === $pagina
                         ? 'bg-green-600 text-white font-semibold pointer-events-none' // Página atual destacada
                         : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200'; ?>"
                   <?php if ($i === $pagina): ?>aria-current="page"<?php endif; ?>>
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($pagina < ($totalPaginas - $numLinksAdjacentes)): ?>
            <?php if ($pagina < ($totalPaginas - $numLinksAdjacentes - 1)): ?>
                <!-- Indicação de salto de páginas -->
                <li><span class="px-3 py-1 text-gray-500 dark:text-gray-400">...</span></li>
            <?php endif; ?>
            <!-- Link para última página -->
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $totalPaginas])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm"><?php echo $totalPaginas; ?></a>
            </li>
        <?php endif; ?>

        <?php if ($pagina < $totalPaginas): ?>
            <!-- Link para próxima página -->
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $pagina + 1])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm"
                   aria-label="Próxima página">
                    &raquo;
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
