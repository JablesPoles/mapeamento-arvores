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

<head>
  <meta charset="UTF-8">
  <title>Catálogo de Árvores</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>Catálogo de Árvores</header>
  <main>
    <aside class="sidebar">
      <form method="get" class="search-container">
        <label for="busca">Pesquisar por nome</label>
        <input type="text" id="busca" name="busca" placeholder="Digite para buscar..."
          value="<?php echo htmlspecialchars($filtro); ?>" />

        <button type="submit">Buscar</button>
      </form>
    </aside>

    <section class="tree-list" id="tree-list">
      <?php if (empty($arvores)): ?>
      <p>Nenhuma árvore encontrada.</p>
      <?php else: ?>
      <?php foreach ($arvores as $arvore): ?>
      <article class="tree-card">
        <div class="tree-info">
          <h3>
            <?php echo $arvore['nome_c']; ?>
          </h3>
          <p><strong>Espécie:</strong>
            <?php echo $arvore['especie']; ?>
          </p>
          <p><strong>Localização:</strong>
            <?php echo $arvore['localizacao']; ?>
          </p>
          <p><strong>Vegetação:</strong>
            <?php echo $arvore['vegetacao']; ?>
          </p>
          <p><strong>Estado da Copa:</strong>
            <?php echo $arvore['estado_copa']; ?>
          </p>
          <p><strong>Curiosidade:</strong>
            <?php echo $arvore['curiosidade']; ?>
          </p>
        </div>
      </article>
      <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
  <footer>&copy; 2025 - Catálogo de Árvores</footer>
</body>

</html>