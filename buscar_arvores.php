<?php
require 'conexao.php';

$filtro = isset($_GET['busca']) ? urldecode($_GET['busca']) : '';
$params = [];

$sql = "SELECT arvore.*, STRING_AGG(np.NOME, ', ' ORDER BY np.NOME) AS nomes_populares
        FROM arvore
        LEFT JOIN NOMES_POPULARES_ARVORE npa ON arvore.id = npa.FK_ARVORE
        LEFT JOIN NOMES_POPULARES np ON npa.FK_NP = np.ID_NOME";

if (!empty($filtro)) {
    $sql .= " WHERE (LOWER(arvore.nome_c) LIKE :filtro OR LOWER(np.NOME) LIKE :filtro)";
    $params[':filtro'] = '%' . strtolower($filtro) . '%';
}

$sql .= " GROUP BY arvore.id, arvore.nome_c, arvore.nat_exo, arvore.horario, arvore.localizacao, 
          arvore.vegetacao, arvore.especie, arvore.diametro_peito, arvore.estado_fitossanitario, 
          arvore.estado_tronco, arvore.estado_copa, arvore.tamanho_calcada, arvore.espaco_arvore, 
          arvore.raizes, arvore.acessibilidade, arvore.curiosidade
          ORDER BY arvore.horario DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$arvores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (empty($arvores)): ?>
  <p>Nenhuma árvore encontrada.</p>
<?php else: ?>
  <?php foreach ($arvores as $arvore): ?>
    <article class="tree-card">
      <div class="tree-summary">
        <div class="tree-summary-time">
          <?php echo date('d/m/Y H:i', strtotime($arvore['horario'])); ?>
        </div>
        <h3 class="tree-summary-title"><?php echo htmlspecialchars($arvore['nome_c']); ?></h3>
        <?php if (!empty($arvore['nomes_populares'])): ?>
          <p class="tree-popular-names">Nomes populares: <?php echo htmlspecialchars($arvore['nomes_populares']); ?></p>
        <?php endif; ?>
        <button class="expand-btn">Expandir</button>
      </div>

      <div class="tree-details">
        <div class="detail-columns">
          <div class="detail-column">
            <p><strong>Nativa/Exótica:</strong> <?php echo htmlspecialchars($arvore['nat_exo']); ?></p>
            <p><strong>Espécie:</strong> <?php echo htmlspecialchars($arvore['especie']); ?></p>
            <p><strong>Localização:</strong> <?php echo htmlspecialchars($arvore['localizacao']); ?></p>
            <p><strong>Vegetação:</strong> <?php echo htmlspecialchars($arvore['vegetacao']); ?></p>
          </div>
          <div class="detail-column">
            <p><strong>Diâmetro do Peito:</strong> <?php echo htmlspecialchars($arvore['diametro_peito']); ?></p>
            <p><strong>Estado Fitossanitário:</strong> <?php echo htmlspecialchars($arvore['estado_fitossanitario']); ?></p>
            <p><strong>Estado do Tronco:</strong> <?php echo htmlspecialchars($arvore['estado_tronco']); ?></p>
            <p><strong>Estado da Copa:</strong> <?php echo htmlspecialchars($arvore['estado_copa']); ?></p>
          </div>
          <div class="detail-column">
            <p><strong>Tamanho da Calçada:</strong> <?php echo htmlspecialchars($arvore['tamanho_calcada']); ?></p>
            <p><strong>Espaço para Árvore:</strong> <?php echo htmlspecialchars($arvore['espaco_arvore']); ?></p>
            <p><strong>Raízes:</strong> <?php echo htmlspecialchars($arvore['raizes']); ?></p>
            <p><strong>Acessibilidade:</strong> <?php echo htmlspecialchars($arvore['acessibilidade']); ?></p>
          </div>
        </div>

        <div class="tree-curiosity">
          <?php if (!empty($arvore['curiosidade'])): ?>
            <p><strong>Curiosidade:</strong> <?php echo htmlspecialchars($arvore['curiosidade']); ?></p>
          <?php else: ?>
            <p><strong>Curiosidade:</strong> Nenhuma curiosidade disponível.</p>
          <?php endif; ?>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
<?php endif; ?>