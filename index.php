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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Catálogo de Árvores</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    .tree-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .tree-summary {
      cursor: pointer;
    }
    
    .tree-details {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    
    .tree-details.expanded {
      max-height: 1000px;
      margin-top: 15px;
      border-top: 1px solid #eee;
      padding-top: 15px;
    }
    
    .detail-columns {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 15px;
    }
    
    .detail-column {
      flex: 1;
      min-width: 200px;
    }
    
    .expand-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }
    
    .expand-btn:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <header>
    <div class="header-title">Catálogo de Árvores</div>
    <nav class="header-nav">
      <a href="index.php" class="btn btn-home"><i class="fas fa-home"></i> Página Inicial</a>
      <a href="admin.php" class="btn btn-admin"><i class="fas fa-user-cog"></i> Painel Admin</a>
    </nav>
  </header>

  <main>
    <div class="search-container">
      <form method="get" class="search-form">
        <div class="search-bar">
          <input type="text" id="busca" name="busca" placeholder="Pesquisar por nome científico ou popular" value="<?php echo htmlspecialchars($filtro); ?>">
          <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </div>
      </form>
    </div>

    <section class="tree-list" id="tree-list">
      <?php include 'buscar_arvores.php'; ?>
    </section>
  </main>

  <footer>&copy; 2025 - Catálogo de Árvores</footer>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Função para expandir/recolher cards
      function setupExpandButtons() {
        document.querySelectorAll('.expand-btn').forEach(button => {
          button.addEventListener('click', function() {
            const details = this.closest('.tree-card').querySelector('.tree-details');
            details.classList.toggle('expanded');
            this.textContent = details.classList.contains('expanded') ? 'Recolher' : 'Expandir';
          });
        });
      }

      // Configura os botões inicialmente
      setupExpandButtons();

      // Busca dinâmica
      const form = document.querySelector(".search-form");
      const input = document.querySelector("#busca");
      const resultado = document.querySelector("#tree-list");

      form.addEventListener("submit", function(e) {
        e.preventDefault();
        fetch(`buscar_arvores.php?busca=${encodeURIComponent(input.value.trim())}`)
          .then(res => res.text())
          .then(html => {
            resultado.innerHTML = html;
            setupExpandButtons(); // Reconfigura os botões após nova busca
          });
      });

      input.addEventListener("input", function() {
        if (this.value.trim() === '') {
          fetch(`buscar_arvores.php`)
            .then(res => res.text())
            .then(html => {
              resultado.innerHTML = html;
              setupExpandButtons();
            });
        }
      });
    });
  </script>
</body>
</html>