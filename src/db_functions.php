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
    $sql = "SELECT arvore.*, STRING_AGG(np.NOME, ', ' ORDER BY np.NOME) AS nomes_populares
                  FROM arvore
                  LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
                  LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";
    $paramsParaQuery = [];

    if (!empty($filtro)) {
        // Aplica filtro case-insensitive por nome científico ou nome popular
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

/**
 * Busca links de imagens em cache para uma espécie, filtrando categorias específicas.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $nomeCientifico Nome científico da espécie.
 * @return array|null Array associativo com categorias e links das imagens, ou null se não houver.
 */
function buscarImagensCache($pdo, $nomeCientifico) {
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
            A.ESPECIE = :nome_cientifico
            AND C.NOME_CATEGORIA IN ('imagem_fruto', 'imagem_folha', 'imagem_casca', 'imagem_habito', 'imagem_flor')
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_cientifico', $nomeCientifico);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $imagensCache = [];
    foreach ($resultados as $resultado) {
        // Remove prefixo 'imagem_' para usar como chave no array
        $tipoImagem = str_replace('imagem_', '', $resultado['nome_categoria']);
        $imagensCache[$tipoImagem] = $resultado['link'];
    }
    return !empty($imagensCache) ? $imagensCache : null;
}

/**
 * Obtém o ID da árvore a partir do nome científico.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param string $nomeCientifico Nome científico da espécie.
 * @return int|null ID da árvore ou null se não encontrada.
 */
function buscarIdArvorePorNomeCientifico(PDO $pdo, string $nomeCientifico): ?int {
    $stmt = $pdo->prepare("SELECT id FROM arvore WHERE especie = :especie LIMIT 1");
    $stmt->execute([':especie' => $nomeCientifico]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado ? (int)$resultado['id'] : null;
}

/**
 * Salva links de imagens no cache, associando-os à árvore e categoria correspondente.
 *
 * @param PDO $pdo Instância da conexão PDO.
 * @param int $idArvore ID da árvore.
 * @param array $linksPorCategoria Array associativo com categorias (fruit, leaf, bark, habit, flower) e URLs.
 */
function salvarImagensCache($pdo, $idArvore, $linksPorCategoria) {
    $mapaCategorias = [
        'fruit'  => 1,
        'leaf'   => 2,
        'bark'   => 3,
        'habit'  => 4,
        'flower' => 5,
    ];

    foreach ($linksPorCategoria as $categoriaNome => $url) {
        if (!isset($mapaCategorias[$categoriaNome])) {
            continue;
        }

        $categoriaId = $mapaCategorias[$categoriaNome];

        // Verifica existência do link para evitar duplicação
        $stmt = $pdo->prepare("SELECT id_links FROM LINKS WHERE LINK = :url");
        $stmt->execute([':url' => $url]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($link && isset($link['id_links'])) {
            $idLink = $link['id_links'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO LINKS (LINK) VALUES (:url)");
            $stmt->execute([':url' => $url]);
            $idLink = $pdo->lastInsertId();
        }

        // Verifica e insere a associação entre árvore, categoria e link, se não existir
        $stmt = $pdo->prepare("SELECT 1 FROM categoria_links 
            WHERE fk_arvore = :arvore AND fk_categoria = :categoria AND fk_links = :link");
        $stmt->execute([
            ':arvore' => $idArvore,
            ':categoria' => $categoriaId,
            ':link' => $idLink
        ]);

        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO categoria_links (fk_arvore, fk_categoria, fk_links) 
                VALUES (:arvore, :categoria, :link)");
            $stmt->execute([
                ':arvore' => $idArvore,
                ':categoria' => $categoriaId,
                ':link' => $idLink
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
