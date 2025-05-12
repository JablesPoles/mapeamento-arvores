<?php
require 'conexao.php';
// Recebe o valor da URL e decodifica, para remover os caracteres especiais
$filtro = isset($_GET['busca']) ? urldecode($_GET['busca']) : '';
$params = [];

// Consulta SQL (sem alterações)
$sql = "SELECT * FROM arvore";
if (!empty($filtro)) {
    $sql .= " WHERE LOWER(nome_c) LIKE :filtro";
    $params[':filtro'] = '%' . strtolower($filtro) . '%';
}
$sql .= " ORDER BY horario DESC";

// Prepara e executa a consulta
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Recupera os dados
$arvores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="pt-BR">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<head>
  <meta charset="UTF-8">
  <title>Catálogo de Árvores</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
  <div class="header-title">Catálogo de Árvores</div>
  <nav class="header-nav">
    <a href="index.php" class="btn btn-home">
      <i class="fas fa-home"></i> Página Inicial
    </a>
    <a href="admin.php" class="btn btn-admin">
      <i class="fas fa-user-cog"></i> Painel Admin
    </a>
  </nav>
</header>
  <main>
    <div class="search-container">
    <form method="get" class="search-form">
      <div class="search-bar">
        <input type="text" 
               id="busca" 
               name="busca" 
               placeholder="Pesquisar árvores (ex: Tibouchina, Acacia)..." 
               value="<?php echo htmlspecialchars($filtro); ?>"
               aria-label="Buscar árvores">
        <button type="submit" class="search-button">
          <i class="fas fa-search"></i>
        </button>
        </div>
      </form>
    </div>

  <section class="tree-list" id="tree-list">

    <section class="tree-list" id="tree-list">
      <?php if (empty($arvores)): ?>
        <p>Nenhuma árvore encontrada.</p>
      <?php else: ?>
        <?php foreach ($arvores as $arvore): ?>
        <article class="tree-card">
          <div class="tree-info">
            <h3><?php echo htmlspecialchars($arvore['nome_c']); ?></h3>
            
            <div class="tree-details">
              <div class="detail-column">
                <p><strong>Nativa/Exótica:</strong> <?php echo htmlspecialchars($arvore['nat_exo']); ?></p>
                <p><strong>Espécie:</strong> <?php echo htmlspecialchars($arvore['especie']); ?></p>
                <p><strong>Localização:</strong> <?php echo htmlspecialchars($arvore['localizacao']); ?></p>
                <p><strong>Vegetação:</strong> <?php echo htmlspecialchars($arvore['vegetacao']); ?></p>
                <p><strong>Data de Cadastro:</strong> <?php echo date('d/m/Y H:i', strtotime($arvore['horario'])); ?></p>
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
              <p><strong>Curiosidade:</strong> <?php echo htmlspecialchars($arvore['curiosidade']); ?></p>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
  <footer>&copy; 2025 - Catálogo de Árvores</footer>
</body>

</html>