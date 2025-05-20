<?php
// src/db_functions.php

/**
 * Retorna o total de árvores cadastradas, com opção de filtro por nome científico ou nome popular.
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
        // Aplica filtro por nome científico ou nome popular, case-insensitive
        $sqlContagem .= " WHERE (LOWER(arvore.nome_c) LIKE :filtro OR LOWER(np.NOME) LIKE :filtro)";
        $params[':filtro'] = '%' . strtolower($filtro) . '%';
    }

    $stmtContagem = $pdo->prepare($sqlContagem);
    $stmtContagem->execute($params);
    return (int) $stmtContagem->fetchColumn();
}

/**
 * Retorna uma lista paginada de árvores, incluindo nomes populares concatenados, com filtro opcional.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $filtro Termo de busca (opcional).
 * @param int $offset Posição inicial da página.
 * @param int $itensPorPagina Quantidade de itens por página.
 * @return array Lista de árvores.
 */
function buscarArvoresPaginadas(PDO $pdo, string $filtro = '', int $offset = 0, int $itensPorPagina = 10): array {
    $sql = "SELECT arvore.*, STRING_AGG(DISTINCT np.NOME, ', ' ORDER BY np.NOME) AS nomes_populares
                  FROM arvore
                  LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
                  LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";
    $paramsParaQuery = [];

    if (!empty($filtro)) {
        // Subconsulta para encontrar IDs de árvores que correspondem ao filtro
        // Isso é necessário por causa do GROUP BY na query principal
        $sql .= " WHERE arvore.id IN (
                    SELECT a.id FROM arvore a
                    LEFT JOIN NOMES_POPULARES_ARVORE npa_sub ON a.id = npa_sub.FK_ARVORE
                    LEFT JOIN NOMES_POPULARES np_sub ON npa_sub.FK_NP = np_sub.ID_NOME
                    WHERE (LOWER(a.nome_c) LIKE :filtro OR LOWER(np_sub.NOME) LIKE :filtro)
                    GROUP BY a.id
                  )";
        $paramsParaQuery[':filtro'] = '%' . strtolower($filtro) . '%';
    }

    $sql .= " GROUP BY arvore.id ORDER BY arvore.horario DESC, arvore.nome_c ASC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    if (!empty($filtro)) {
        $stmt->bindValue(':filtro', $paramsParaQuery[':filtro'], PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Busca links de imagens em cache para uma espécie (usando nome_c da tabela arvore),
 * filtrando categorias específicas.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $nomeCientificoCompleto Nome científico da espécie (com autor, da coluna arvore.nome_c).
 * @return array|null Array associativo com categorias e links das imagens, ou null se não houver.
 */
function buscarImagensCache(PDO $pdo, string $nomeCientificoCompleto): ?array {
    $sql = "
        SELECT
            L.LINK,
            C.NOME_CATEGORIA
        FROM
            LINKS L
        JOIN
            CATEGORIA_LINKS CL ON L.ID_LINKS = CL.FK_LINKS
        JOIN
            CATEGORIA C ON C.ID_CATEGORIA = CL.FK_CATEGORIA
        JOIN
            ARVORE A ON A.ID = CL.FK_ARVORE
        WHERE
            LOWER(A.nome_c) = LOWER(:nome_cientifico_completo) 
            AND C.NOME_CATEGORIA IN ('fruit', 'leaf', 'bark', 'habit', 'flower') 
    ";
    // Nota: A API PlantNet usa 'fruit', 'leaf', etc.
    // Se você tem 'imagem_fruto', 'imagem_folha' no seu banco para o cache da PlantNet,
    // ajuste o IN(...) para ('fruit', 'leaf', 'bark', 'habit', 'flower')
    // e mapeie de volta para 'imagem_fruto' etc., ao retornar, se necessário.
    // Ou, melhor ainda, salve no cache com os nomes da API PlantNet ('fruit', 'leaf', etc.)

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_cientifico_completo', $nomeCientificoCompleto);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $imagensCache = [];
    foreach ($resultados as $resultado) {
        // Mapeia os nomes de categoria da API PlantNet para as chaves desejadas
        $tipoImagem = strtolower($resultado['nome_categoria']); // ex: 'leaf', 'flower'
        $imagensCache[$tipoImagem] = $resultado['link'];
    }
    return !empty($imagensCache) ? $imagensCache : null;
}


/**
 * Obtém o ID da árvore a partir do nome científico completo (com autor).
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $nomeCientificoCompleto Nome científico da espécie com autor (da coluna arvore.nome_c).
 * @return int|null ID da árvore ou null se não encontrada.
 */
function buscarIdArvorePorNomeCientificoCompleto(PDO $pdo, string $nomeCientificoCompleto): ?int {
    // Busca case-insensitive
    $stmt = $pdo->prepare("SELECT id FROM arvore WHERE LOWER(nome_c) = LOWER(:nome_c) LIMIT 1");
    $stmt->execute([':nome_c' => $nomeCientificoCompleto]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado ? (int)$resultado['id'] : null;
}

/**
 * Salva links de imagens no cache, associando-os à árvore e categoria correspondente.
 * As categorias devem ser as usadas pela API PlantNet ('fruit', 'leaf', etc.).
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param int $idArvore ID da árvore.
 * @param array $linksPorCategoria Array associativo com categorias (fruit, leaf, bark, habit, flower) e URLs.
 */
function salvarImagensCache(PDO $pdo, int $idArvore, array $linksPorCategoria) {
    // Busca IDs das categorias da API PlantNet
    $categoriasPlantNet = ['fruit', 'leaf', 'bark', 'habit', 'flower'];
    $placeholders = implode(',', array_fill(0, count($categoriasPlantNet), '?'));
    $sqlCat = "SELECT ID_CATEGORIA, NOME_CATEGORIA FROM CATEGORIA WHERE LOWER(NOME_CATEGORIA) IN ($placeholders)";
    $stmtCat = $pdo->prepare($sqlCat);
    $stmtCat->execute(array_map('strtolower', $categoriasPlantNet));
    $mapaCategoriasDB = $stmtCat->fetchAll(PDO::FETCH_KEY_PAIR); // NOME_CATEGORIA => ID_CATEGORIA

    foreach ($linksPorCategoria as $categoriaNomeApi => $url) {
        $categoriaNomeApiLower = strtolower($categoriaNomeApi);
        if (!isset($mapaCategoriasDB[$categoriaNomeApiLower])) {
            error_log("Categoria da API PlantNet '$categoriaNomeApiLower' não encontrada na tabela CATEGORIA do banco.");
            continue; // Pula se a categoria da API não estiver no nosso banco
        }
        $categoriaIdDB = $mapaCategoriasDB[$categoriaNomeApiLower];

        // Insere o link se não existir, e obtém o ID
        $stmtLink = $pdo->prepare("INSERT INTO LINKS (LINK) VALUES (:url) ON CONFLICT (LINK) DO NOTHING RETURNING ID_LINKS");
        $stmtLink->execute([':url' => $url]);
        $idLink = $stmtLink->fetchColumn();

        if (!$idLink) { // Se ON CONFLICT ocorreu, o link já existia, então buscamos o ID
            $stmtGetLink = $pdo->prepare("SELECT ID_LINKS FROM LINKS WHERE LINK = :url");
            $stmtGetLink->execute([':url' => $url]);
            $idLink = $stmtGetLink->fetchColumn();
        }

        if ($idLink && $categoriaIdDB) {
            // Insere a associação, tratando conflitos (se a árvore já tem uma imagem para essa categoria)
            $stmtRel = $pdo->prepare("
                INSERT INTO CATEGORIA_LINKS (FK_ARVORE, FK_CATEGORIA, FK_LINKS) 
                VALUES (:fk_arvore, :fk_categoria, :fk_links)
                ON CONFLICT (FK_ARVORE, FK_CATEGORIA) DO UPDATE SET FK_LINKS = EXCLUDED.FK_LINKS
            ");
            $stmtRel->execute([
                ':fk_arvore' => $idArvore,
                ':fk_categoria' => $categoriaIdDB,
                ':fk_links' => $idLink
            ]);
        }
    }
}

// ========================================================================
// FUNÇÕES PARA GERENCIAMENTO DE ADMINISTRADORES
// ========================================================================

/**
 * Busca um administrador pelo nome de usuário (case-insensitive).
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $usuario Nome de usuário do administrador.
 * @return array|false Retorna os dados do administrador ou false se não encontrado.
 */
function buscarAdminPorUsuario(PDO $pdo, string $usuario) {
    $sql = "SELECT id, usuario, nome_completo, senha FROM administradores WHERE LOWER(usuario) = LOWER(:usuario)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Busca um administrador pelo ID.
 * @param PDO $pdo Instância da conexão PDO.
 * @param int $id ID do administrador.
 * @return array|false Retorna os dados do administrador ou false se não encontrado.
 */
function buscarAdminPorId(PDO $pdo, int $id) {
    $sql = "SELECT id, usuario, nome_completo FROM administradores WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Lista todos os usuários administradores.
 * @param PDO $pdo Instância da conexão PDO.
 * @return array Lista de administradores.
 */
function listarAdmins(PDO $pdo): array {
    $sql = "SELECT id, usuario, nome_completo, data_criacao FROM administradores ORDER BY nome_completo ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Cria um novo usuário administrador.
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $usuario Nome de usuário.
 * @param string $senhaHash Senha já processada com password_hash().
 * @param string $nomeCompleto Nome completo do administrador.
 * @return bool True se bem-sucedido, false caso contrário.
 */
function criarAdmin(PDO $pdo, string $usuario, string $senhaHash, string $nomeCompleto): bool {
    try {
        $sql = "INSERT INTO administradores (usuario, senha, nome_completo) VALUES (:usuario, :senha, :nome_completo)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':nome_completo', $nomeCompleto);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erro ao criar admin: " . $e->getMessage());
        return false;
    }
}

/**
 * Atualiza os dados de um usuário administrador.
 * @param PDO $pdo Instância da conexão PDO.
 * @param int $id ID do administrador a ser atualizado.
 * @param string $usuario Novo nome de usuário.
 * @param string $nomeCompleto Novo nome completo.
 * @param string|null $novaSenhaHash Nova senha hasheada (opcional, se for alterar a senha).
 * @return bool True se bem-sucedido, false caso contrário.
 */
function atualizarAdmin(PDO $pdo, int $id, string $usuario, string $nomeCompleto, ?string $novaSenhaHash = null): bool {
    try {
        if ($novaSenhaHash !== null) {
            $sql = "UPDATE administradores SET usuario = :usuario, nome_completo = :nome_completo, senha = :senha WHERE id = :id";
        } else {
            $sql = "UPDATE administradores SET usuario = :usuario, nome_completo = :nome_completo WHERE id = :id";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':nome_completo', $nomeCompleto);
        if ($novaSenhaHash !== null) {
            $stmt->bindParam(':senha', $novaSenhaHash);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erro ao atualizar admin: " . $e->getMessage());
        return false;
    }
}

/**
 * Exclui um usuário administrador.
 * @param PDO $pdo Instância da conexão PDO.
 * @param int $id ID do administrador a ser excluído.
 * @return bool True se bem-sucedido, false caso contrário.
 */
function excluirAdmin(PDO $pdo, int $id): bool {
    try {
        $sql = "DELETE FROM administradores WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Erro ao excluir admin: " . $e->getMessage());
        return false;
    }
}
// ========================================================================
// FIM DAS FUNÇÕES PARA GERENCIAMENTO DE ADMINISTRADORES
// ========================================================================
?>
