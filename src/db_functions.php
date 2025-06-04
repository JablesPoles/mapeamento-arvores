<?php
// UPDATED: Function signature to include $id_filtro
function contarTotalArvores(PDO $pdo, string $filtro_texto = '', ?int $id_filtro = null): int {
    $sqlContagemBase = "SELECT COUNT(DISTINCT arvore.id) AS total FROM arvore";
    $joins = " LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
               LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";
    $whereClause = "";
    $params = [];

    if ($id_filtro !== null && $id_filtro > 0) {
        // Se busca por ID, ignora o filtro de texto
        $whereClause = " WHERE arvore.id = :id_filtro";
        $params[':id_filtro'] = $id_filtro;
        $sqlContagem = $sqlContagemBase . $whereClause; // Joins não são necessários para contar por ID único
    } elseif (!empty($filtro_texto)) {
        $whereClause = " WHERE (LOWER(arvore.nome_c) LIKE :filtro_texto OR LOWER(np.NOME) LIKE :filtro_texto)";
        $params[':filtro_texto'] = '%' . strtolower($filtro_texto) . '%';
        $sqlContagem = $sqlContagemBase . $joins . $whereClause; // Joins são necessários para busca por nome
    } else {
        $sqlContagem = $sqlContagemBase; // Sem filtro, conta todas
    }
    
    $stmtContagem = $pdo->prepare($sqlContagem);
    $stmtContagem->execute($params);
    return (int) $stmtContagem->fetchColumn();
}

// UPDATED: Function signature to include $id_filtro
function buscarArvoresPaginadas(PDO $pdo, string $filtro_texto = '', int $offset = 0, int $itensPorPagina = 10, ?int $id_filtro = null): array {
    $sqlBase = "SELECT arvore.*, STRING_AGG(DISTINCT np.NOME, ', ' ORDER BY np.NOME) AS nomes_populares 
                FROM arvore";
    $joins = " LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
               LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";
    $whereClause = "";
    $paramsParaQuery = [];

    if ($id_filtro !== null && $id_filtro > 0) {
        // Se busca por ID, ignora o filtro de texto
        $whereClause = " WHERE arvore.id = :id_filtro";
        $paramsParaQuery[':id_filtro'] = $id_filtro;
        $sql = $sqlBase . $joins . $whereClause; // Mantém joins para buscar nomes populares
    } elseif (!empty($filtro_texto)) {
        $whereClause = " WHERE (LOWER(arvore.nome_c) LIKE :filtro_texto OR LOWER(np.NOME) LIKE :filtro_texto)";
        $paramsParaQuery[':filtro_texto'] = '%' . strtolower($filtro_texto) . '%';
        $sql = $sqlBase . $joins . $whereClause;
    } else {
        $sql = $sqlBase . $joins; // Sem filtro, busca todas
    }

    $sql .= " GROUP BY arvore.id ORDER BY arvore.horario DESC LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    foreach ($paramsParaQuery as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- Funções restantes permanecem as mesmas da versão anterior ---
function buscarImagensCache($pdo, $nomeCientificoOuPopular) {
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
            (A.ESPECIE = :termo_busca OR A.NOME_C = :termo_busca) 
            AND C.NOME_CATEGORIA IN ('imagem_fruto', 'imagem_folha', 'imagem_casca', 'imagem_habito', 'imagem_flor')
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':termo_busca', $nomeCientificoOuPopular);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $imagensCache = [];
    foreach ($resultados as $resultado) {
        $tipoImagem = str_replace('imagem_', '', $resultado['nome_categoria']);
        $imagensCache[$tipoImagem] = $resultado['link'];
    }
    return !empty($imagensCache) ? $imagensCache : null;
}

function buscarIdArvorePorNomeCientifico(PDO $pdo, string $nomeCientificoOuPopular): ?int {
    $stmt = $pdo->prepare("SELECT id FROM arvore WHERE especie = :termo_busca OR nome_c = :termo_busca LIMIT 1");
    $stmt->execute([':termo_busca' => $nomeCientificoOuPopular]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado ? (int)$resultado['id'] : null;
}


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
        $stmt = $pdo->prepare("SELECT id_links FROM LINKS WHERE LINK = :url");
        $stmt->execute([':url' => $url]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($link && isset($link['id_links'])) {
            $idLink = $link['id_links'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO LINKS (LINK) VALUES (:url) RETURNING id_links"); 
            $stmt->execute([':url' => $url]);
            $idLink = $stmt->fetchColumn();
        }

        if ($idLink) { 
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
}

function buscarAdminPorUsuario(PDO $pdo, string $usuario) {
    $sql = "SELECT id, usuario, nome_completo, senha FROM administradores WHERE LOWER(usuario) = LOWER(:usuario)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function buscarAdminPorId(PDO $pdo, int $id) {
    $sql = "SELECT id, usuario, nome_completo FROM administradores WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function listarAdmins(PDO $pdo): array {
    $sql = "SELECT id, usuario, nome_completo, data_criacao FROM administradores ORDER BY nome_completo ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

function buscarArvorePorId(PDO $pdo, int $id) {
    $stmt = $pdo->prepare("SELECT * FROM arvore WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function buscarNomesPopularesPorArvoreId(PDO $pdo, int $id_arvore): array {
    $stmt = $pdo->prepare("
        SELECT np.nome FROM nomes_populares np
        JOIN nomes_populares_arvore npa ON np.id_nome = npa.fk_np
        WHERE npa.fk_arvore = :id_arvore
        ORDER BY np.nome
    ");
    $stmt->execute([':id_arvore' => $id_arvore]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function atualizarArvore(PDO $pdo, int $id_arvore, array $dados): bool {
    $sql = "UPDATE arvore SET
                nome_c = :nome_c, nat_exo = :nat_exo, horario = :horario, localizacao = :localizacao, 
                vegetacao = :vegetacao, especie = :especie, diametro_peito = :diametro_peito, 
                estado_fitossanitario = :estado_fitossanitario, estado_tronco = :estado_tronco,
                estado_copa = :estado_copa, tamanho_calcada = :tamanho_calcada, espaco_arvore = :espaco_arvore, 
                raizes = :raizes, acessibilidade = :acessibilidade, curiosidade = :curiosidade,
                latitude = :latitude, longitude = :longitude 
            WHERE id = :id_arvore";
    
    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':nome_c' => $dados['nome_c'],
        ':nat_exo' => $dados['nat_exo'],
        ':horario' => $dados['horario'],
        ':localizacao' => $dados['localizacao'],
        ':vegetacao' => $dados['vegetacao'] ?: null,
        ':especie' => $dados['especie'],
        ':diametro_peito' => $dados['diametro_peito'],
        ':estado_fitossanitario' => $dados['estado_fitossanitario'],
        ':estado_tronco' => $dados['estado_tronco'],
        ':estado_copa' => $dados['estado_copa'],
        ':tamanho_calcada' => $dados['tamanho_calcada'],
        ':espaco_arvore' => $dados['espaco_arvore'] ?: null,
        ':raizes' => $dados['raizes'] ?: null,
        ':acessibilidade' => $dados['acessibilidade'] ?: null,
        ':curiosidade' => $dados['curiosidade'] ?: null,
        ':latitude' => !empty($dados['latitude']) ? $dados['latitude'] : null,      
        ':longitude' => !empty($dados['longitude']) ? $dados['longitude'] : null,   
        ':id_arvore' => $id_arvore
    ]);
}

function atualizarNomesPopulares(PDO $pdo, int $id_arvore, array $nomes_populares) {
    $stmt_delete = $pdo->prepare("DELETE FROM nomes_populares_arvore WHERE fk_arvore = :id_arvore");
    $stmt_delete->execute([':id_arvore' => $id_arvore]);
    if (empty($nomes_populares)) {
        return; 
    }

    $sqlInsNp = "INSERT INTO nomes_populares (nome) VALUES (:nome) ON CONFLICT (LOWER(nome)) DO NOTHING RETURNING id_nome";
    $stmtInsNp = $pdo->prepare($sqlInsNp);
    $sqlGetIdNp = "SELECT id_nome FROM nomes_populares WHERE LOWER(nome) = LOWER(:nome)";
    $stmtGetIdNp = $pdo->prepare($sqlGetIdNp);
    $sqlRel = "INSERT INTO nomes_populares_arvore (fk_arvore, fk_np) VALUES (:fk_arvore, :fk_np) ON CONFLICT DO NOTHING";
    $stmtRel = $pdo->prepare($sqlRel);

    foreach ($nomes_populares as $nome) {
        if (empty(trim($nome))) continue;

        $stmtInsNp->execute([':nome' => $nome]);
        $idNp = $stmtInsNp->fetchColumn();

        if (!$idNp) {
            $stmtGetIdNp->execute([':nome' => $nome]);
            $idNp = $stmtGetIdNp->fetchColumn();
        }

        if ($idNp) {
            $stmtRel->execute([':fk_arvore' => $id_arvore, ':fk_np' => $idNp]);
        }
    }
}

function deletarArvore(PDO $pdo, int $id_arvore): bool {
    try {
        $pdo->beginTransaction();
        $stmt_links = $pdo->prepare("DELETE FROM categoria_links WHERE fk_arvore = :id_arvore");
        $stmt_links->execute([':id_arvore' => $id_arvore]);

        $stmt1 = $pdo->prepare("DELETE FROM nomes_populares_arvore WHERE fk_arvore = :id_arvore");
        $stmt1->execute([':id_arvore' => $id_arvore]);
        
        $stmt2 = $pdo->prepare("DELETE FROM arvore WHERE id = :id_arvore");
        $stmt2->execute([':id_arvore' => $id_arvore]);

        $pdo->commit();
        return true;

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erro ao deletar árvore: " . $e->getMessage());
        return false;
    }
}
?>
