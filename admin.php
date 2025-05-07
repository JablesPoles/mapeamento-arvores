<?php 
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "INSERT INTO arvore (
        nome_c, nat_exo, horario, localizacao, vegetacao, especie,
        diametro_peito, estado_fitossanitario, estado_tronco,
        estado_copa, tamanho_calcada, espaco_arvore, raizes,
        acessibilidade, curiosidade
    ) VALUES (
        :nome, :nat_exo, NOW(), :local, :vegetacao, :especie,
        :diametro, :fitossanitario, :tronco, :copa, :calcada,
        :espaco, :raizes, :acessibilidade, :curiosidade
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $_POST['nome_c'],
        ':nat_exo' => $_POST['nat_exo'],
        ':local' => $_POST['localizacao'],
        ':vegetacao' => $_POST['vegetacao'],
        ':especie' => $_POST['especie'],
        ':diametro' => $_POST['diametro_peito'],
        ':fitossanitario' => $_POST['estado_fitossanitario'],
        ':tronco' => $_POST['estado_tronco'],
        ':copa' => $_POST['estado_copa'],
        ':calcada' => $_POST['tamanho_calcada'],
        ':espaco' => $_POST['espaco_arvore'],
        ':raizes' => $_POST['raizes'],
        ':acessibilidade' => $_POST['acessibilidade'],
        ':curiosidade' => $_POST['curiosidade'],
    ]);

    $msg = "Árvore cadastrada com sucesso.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administração - Cadastro de Árvores</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <header>
    Painel Administrativo
  </header>
  <main class="admin">
    <div class="form-container">
      <h2>Cadastrar Nova Árvore</h2>
      <?php if (isset($msg)): ?>
      <p style="color: green; font-weight: bold; text-align: center;">
        <?= htmlspecialchars($msg) ?>
      </p>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label for="nome_c">Nome científico</label>
          <input type="text" id="nome_c" name="nome_c" required />
        </div>
        <div class="form-group">
          <label for="nat_exo">Nativa/Exótica</label>
          <input type="text" id="nat_exo" name="nat_exo" maxlength="8" required />
        </div>
        <div class="form-group">
          <label for="localizacao">Localização</label>
          <input type="text" id="localizacao" name="localizacao" required />
        </div>
        <!-- Adicione todos os outros campos necessários -->
        <div class="form-group">
          <label for="curiosidade">Curiosidade</label>
          <textarea id="curiosidade" name="curiosidade"></textarea>
        </div>
        <button type="submit">Cadastrar</button>
      </form>
    </div>
  </main>
  <footer>
    &copy; 2025 - Cadastro de Árvores
  </footer>
</body>
</html>