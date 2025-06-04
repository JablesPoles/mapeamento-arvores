<?php
if (!isset($pagina) || !isset($totalPaginas) || !isset($filtro) || $totalPaginas <= 1) {
    return;
}

$numLinksAdjacentes = 2;
$queryStringParams = array_filter(['busca' => $filtro]);
?>
<nav class="mt-10 flex justify-center" aria-label="Paginação">
    <ul class="inline-flex items-center space-x-1 bg-white dark:bg-dark-card rounded-lg p-2 shadow">
        <?php if ($pagina > 1): ?>
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $pagina - 1])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm"
                   aria-label="Página anterior">
                    &laquo;
                </a>
            </li>
        <?php endif; ?>

        <?php if ($pagina > ($numLinksAdjacentes + 1)): ?>
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => 1])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm">1</a>
            </li>
            <?php if ($pagina > ($numLinksAdjacentes + 2)): ?>
                <li><span class="px-3 py-1 text-gray-500 dark:text-gray-400">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php 
        for ($i = max(1, $pagina - $numLinksAdjacentes); $i <= min($totalPaginas, $pagina + $numLinksAdjacentes); $i++): 
        ?>
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $i])); ?>"
                   class="px-3 py-1 rounded-md text-sm
                       <?php echo $i === $pagina
                                ? 'bg-green-600 text-white font-semibold pointer-events-none'
                                : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200'; ?>"
                   <?php if ($i === $pagina): ?>aria-current="page"<?php endif; ?>>
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <?php if ($pagina < ($totalPaginas - $numLinksAdjacentes)): ?>
            <?php if ($pagina < ($totalPaginas - $numLinksAdjacentes - 1)): ?>
                <li><span class="px-3 py-1 text-gray-500 dark:text-gray-400">...</span></li>
            <?php endif; ?>
            <li>
                <a href="?<?php echo http_build_query(array_merge($queryStringParams, ['pagina' => $totalPaginas])); ?>"
                   class="px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm"><?php echo $totalPaginas; ?></a>
            </li>
        <?php endif; ?>

        <?php if ($pagina < $totalPaginas): ?>
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