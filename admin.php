<?php 
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "INSERT INTO arvore (
        nome_c, nat_exo, horario, localizacao, vegetacao, especie,
        diametro_peito, estado_fitossanitario, estado_tronco,
        estado_copa, tamanho_calcada, espaco_arvore, raizes,
        acessibilidade, curiosidade
    ) VALUES (
        :nome, :nat_exo, :horario, :local, :vegetacao, :especie,
        :diametro, :fitossanitario, :tronco, :copa, :calcada,
        :espaco, :raizes, :acessibilidade, :curiosidade
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $_POST['nome_c'],
        ':nat_exo' => $_POST['nat_exo'],
        ':horario' => $_POST['horario'],
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
  <title>Cadastro de Árvores</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .form-container {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
      background-color: var(--card-bg);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: var(--shadow-md);
    }

    .form-container h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 1.5rem;
      font-size: 2rem;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      font-weight: 600;
      color: var(--text-dark);
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 0.8rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
      margin-top: 0.5rem;
      transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border: 1px solid var(--primary);
      background-color: var(--primary-lighter);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }

    .add-btn {
      display: inline-block;
      padding: 8px 16px;
      background-color: var(--primary);
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.3s;
    }

    .add-btn:hover {
      background-color: var(--primary-light);
    }

    .btn-submit {
      background-color: var(--secondary);
      padding: 10px 20px;
      color: var(--text-light);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 20px;
    }

    .btn-submit:hover {
      background-color: var(--primary-light);
      box-shadow: var(--shadow-md);
    }

    .btn-submit:focus {
      outline: none;
    }

    @media (max-width: 768px) {
      .form-container {
        padding: 1.5rem;
      }

      .form-group input,
      .form-group textarea {
        font-size: 0.95rem;
      }
      
      .add-btn, .btn-submit {
        width: 100%;
      }
    }

    

  </style>

  
</head>
<body>
  <header>  
    <div class="header-title">Painel Administrativo</div>
  <nav class="header-nav">
    <a href="index.php" class="btn btn-home">
      <i class="fas fa-home"></i> Página Inicial
    </a>
  </nav>
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
        <div class="detail-column">
          <div class="form-group">
            <label for="nome_c">Nome científico</label>
            <input type="text" id="nome_c" name="nome_c" required />
          </div>
          <div class="form-group">
            <label for="nat_exo">Nativa/Exótica</label>
            <input type="text" id="nat_exo" name="nat_exo" required />
          </div>
          <div class="form-group">
            <label for="horario">Data e Hora</label>
            <input type="datetime-local" id="horario" name="horario" required />
          </div>
          <div class="form-group">
            <label for="localizacao">Localização</label>
            <input type="text" id="localizacao" name="localizacao" required />
          </div>
          <div class="form-group">
            <label for="vegetacao">Tipo de vegetação</label>
            <input type="text" id="vegetacao" name="vegetacao" required />
          </div>
          <div class="form-group">
            <label for="especie">Espécie</label>
            <input type="text" id="especie" name="especie" required />
          </div>
          <div class="form-group">
            <label for="diametro_peito">Diâmetro do peito</label>
            <input type="text" id="diametro_peito" name="diametro_peito" required />
          </div>
          <div class="form-group">
            <label for="estado_fitossanitario">Estado fitossanitário</label>
            <input type="text" id="estado_fitossanitario" name="estado_fitossanitario" required />
          </div>
          <div class="form-group">
            <label for="estado_tronco">Estado do tronco</label>
            <input type="text" id="estado_tronco" name="estado_tronco" required />
          </div>
          <div class="form-group">
            <label for="estado_copa">Estado da copa</label>
            <input type="text" id="estado_copa" name="estado_copa" required />
          </div>
          <div class="form-group">
            <label for="tamanho_calcada">Tamanho da calçada</label>
            <input type="text" id="tamanho_calcada" name="tamanho_calcada" required />
          </div>
          <div class="form-group">
            <label for="espaco_arvore">Espaço da árvore</label>
            <input type="text" id="espaco_arvore" name="espaco_arvore" required />
          </div>
          <div class="form-group">
            <label for="raizes">Raízes</label>
            <input type="text" id="raizes" name="raizes" required />
          </div>
          <div class="form-group">
            <label for="acessibilidade">Acessibilidade</label>
            <input type="text" id="acessibilidade" name="acessibilidade" required />
          </div>
          <div class="form-group">
            <label for="curiosidade">Curiosidade</label>
            <textarea id="curiosidade" name="curiosidade" maxlength="100"></textarea>
          </div>
        </div>

        <div class="detail-column">
          <div class="form-group">
            <label>Nome(s) Popular(es)</label>
            <div id="nomes-populares-container">
              <input type="text" name="nome_p[]" placeholder="Nome popular 1" />
            </div>
            <button type="button" class="add-btn" onclick="adicionarCampo()">+ Adicionar Nome</button>
          </div>
        </div>

        <button type="submit" class="btn-submit">Cadastrar</button>
      </form>
    </div>
  </main>
  <footer>&copy; 2025 - Cadastro de Árvores</footer>

  <script>
    function adicionarCampo() {
      const container = document.getElementById('nomes-populares-container');
      const input = document.createElement('input');
      input.type = 'text';
      input.name = 'nome_p[]';
      input.placeholder = 'Outro nome popular';
      container.appendChild(input);
    }
  </script>
</body>
</html>
