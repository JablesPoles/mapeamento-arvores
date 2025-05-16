<?php
// src/db_functions.php

/**
 * Conta o número total de árvores, opcionalmente aplicando um filtro.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $filtro Termo de busca (opcional).
 * @return int Total de árvores encontradas.
 */
function contarTotalArvores(PDO $pdo, string $filtro = ''): int {
    $sqlContagem = "SELECT COUNT(DISTINCT arvore.id) AS total
                    FROM arvore
                    LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
                    LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";
    $params = [];
    if (!empty($filtro)) {
        $sqlContagem .= " WHERE (LOWER(arvore.nome_c) LIKE :filtro OR LOWER(np.NOME) LIKE :filtro)";
        $params[':filtro'] = '%' . strtolower($filtro) . '%';
    }

    $stmtContagem = $pdo->prepare($sqlContagem);
    $stmtContagem->execute($params);
    return (int) $stmtContagem->fetchColumn();
}

/**
 * Busca árvores com paginação e filtro.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $filtro Termo de busca (opcional).
 * @param int $offset Ponto de início para a busca (paginação).
 * @param int $itensPorPagina Número de itens por página.
 * @return array Lista de árvores.
 */
function buscarArvoresPaginadas(PDO $pdo, string $filtro = '', int $offset = 0, int $itensPorPagina = 10): array {
    $sql = "SELECT arvore.*, STRING_AGG(np.NOME, ', ' ORDER BY np.NOME) AS nomes_populares
            FROM arvore
            LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
            LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";
    $paramsParaQuery = [];

    if (!empty($filtro)) {
        $sql .= " WHERE (LOWER(arvore.nome_c) LIKE :filtro OR LOWER(np.NOME) LIKE :filtro)";
        $paramsParaQuery[':filtro'] = '%' . strtolower($filtro) . '%';
    }

    $sql .= " GROUP BY arvore.id ORDER BY arvore.horario DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    if (!empty($filtro)) {
        $stmt->bindValue(':filtro', $paramsParaQuery[':filtro'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>