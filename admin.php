<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexao.php'; 

$msg = null; 

// Função auxiliar para renderizar campos do formulário
function render_form_field($id, $name, $label, $type = 'text', $required = true, $value = '', $placeholder = '', $options = []) {
    $label_classes = "block font-medium text-gray-700 dark:text-gray-300 mb-1";
    // As classes de input são aplicadas diretamente no HTML ou via JavaScript para campos dinâmicos
    $input_classes_base = "w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow";
    
    $required_span = $required ? '<span class="text-red-500">*</span>' : '';
    $required_attr = $required ? 'required' : '';
    $placeholder_attr = $placeholder ? 'placeholder="' . htmlspecialchars($placeholder) . '"' : '';
    $value_attr = $value ? 'value="' . htmlspecialchars($value) . '"' : '';

    echo "<div>";
    echo "<label class=\"{$label_classes}\" for=\"{$id}\">{$label} {$required_span}</label>";

    if ($type === 'textarea') {
        $rows = isset($options['rows']) ? $options['rows'] : 4;
        $maxlength = isset($options['maxlength']) ? 'maxlength="' . $options['maxlength'] . '"' : '';
        echo "<textarea id=\"{$id}\" name=\"{$name}\" {$maxlength} rows=\"{$rows}\" class=\"{$input_classes_base}\" {$placeholder_attr} {$required_attr}>" . htmlspecialchars($value) . "</textarea>";
    } else {
        $inputmode = isset($options['inputmode']) ? 'inputmode="' . $options['inputmode'] . '"' : '';
        echo "<input id=\"{$id}\" name=\"{$name}\" type=\"{$type}\" {$required_attr} {$value_attr} {$placeholder_attr} {$inputmode} class=\"{$input_classes_base}\" />";
    }
    echo "</div>";
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $pdo->beginTransaction();

        $sql_arvore = "INSERT INTO arvore (
            nome_c, nat_exo, horario, localizacao, vegetacao, especie,
            diametro_peito, estado_fitossanitario, estado_tronco,
            estado_copa, tamanho_calcada, espaco_arvore, raizes,
            acessibilidade, curiosidade
        ) VALUES (
            :nome_c, :nat_exo, :horario, :localizacao, :vegetacao, :especie,
            :diametro_peito, :estado_fitossanitario, :estado_tronco, :estado_copa, 
            :tamanho_calcada, :espaco_arvore, :raizes, :acessibilidade, :curiosidade
        )";

        $stmt_arvore = $pdo->prepare($sql_arvore);
        $stmt_arvore->execute([
            ':nome_c'                 => $_POST['nome_c'],
            ':nat_exo'                => $_POST['nat_exo'],
            ':horario'                => $_POST['horario'],
            ':localizacao'            => $_POST['localizacao'],
            ':vegetacao'              => $_POST['vegetacao'],
            ':especie'                => $_POST['especie'],
            ':diametro_peito'         => $_POST['diametro_peito'],
            ':estado_fitossanitario'  => $_POST['estado_fitossanitario'],
            ':estado_tronco'          => $_POST['estado_tronco'],
            ':estado_copa'            => $_POST['estado_copa'],
            ':tamanho_calcada'        => $_POST['tamanho_calcada'],
            ':espaco_arvore'          => $_POST['espaco_arvore'],
            ':raizes'                 => $_POST['raizes'],
            ':acessibilidade'         => $_POST['acessibilidade'],
            ':curiosidade'            => $_POST['curiosidade'],
        ]);

        $arvoreId = $pdo->lastInsertId();

        if (!empty($_POST['nome_p']) && is_array($_POST['nome_p'])) {
            foreach ($_POST['nome_p'] as $nomePopular) {
                $nomePopular = trim($nomePopular);
                if ($nomePopular === '') {
                    continue;
                }

                $sqlCheckNp = "SELECT id_nome FROM nomes_populares WHERE LOWER(nome) = LOWER(:nome)";
                $stmtCheckNp = $pdo->prepare($sqlCheckNp);
                $stmtCheckNp->execute([':nome' => $nomePopular]);
                $rowNp = $stmtCheckNp->fetch(PDO::FETCH_ASSOC);

                $idNp = null;
                if ($rowNp) {
                    $idNp = $rowNp['id_nome'];
                } else {
                    $sqlInsNp = "INSERT INTO nomes_populares (nome) VALUES (:nome)";
                    $stmtInsNp = $pdo->prepare($sqlInsNp);
                    $stmtInsNp->execute([':nome' => $nomePopular]);
                    $idNp = $pdo->lastInsertId();
                }

                if ($idNp) {
                    $sqlRel = "INSERT INTO nomes_populares_arvore (fk_arvore, fk_np) VALUES (:fk_arvore, :fk_np)";
                    $stmtRel = $pdo->prepare($sqlRel);
                    $stmtRel->execute([':fk_arvore' => $arvoreId, ':fk_np' => $idNp]);
                }
            }
        }
        $pdo->commit();
        $msg = "Árvore cadastrada com sucesso!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $msg = "Erro ao cadastrar árvore: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Catálogo de Árvores</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <script>
    // Configuração do Tailwind CSS
    tailwind.config = {
      darkMode: 'class', 
      theme: {
        extend: {
          colors: {
            primary: '#2E7D32',
            'primary-light': '#4CAF50',
            'primary-lighter': '#E8F5E9',
            secondary: '#FFA000',
            accent: '#00796B', 
            'dark-accent': '#004d40', 
            'card-border': '#4CAF50',
            'card-hover': 'rgba(46, 125, 50, 0.05)',
            'light-bg': '#f7faf7',
            'dark-bg': '#1a202c',
            'dark-card': '#2d3748',
            'dark-text': '#e2e8f0',
            'dark-primary': '#38a169',
            'dark-primary-hover': '#2f855a',
            'dark-secondary': '#dd6b20', 
            'dark-secondary-hover': '#c55a1b',
            'dark-border': '#4a5568',
            'dark-input-bg': '#2d3748', 
            'dark-input-border': '#4a5568', 
            'dark-input-focus-ring': '#38a169', 
            'dark-remove-btn-bg': '#c53030', 
            'dark-remove-btn-hover-bg': '#9b2c2c', 
          },
          boxShadow: {
            'card': '0 2px 8px rgba(0, 0, 0, 0.08)',
            'card-hover': '0 4px 12px rgba(0, 0, 0, 0.12)'
          }
        }
      }
    }
  </script>
  <style>
    html, body {
        height: 100%;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    body {
        padding-top: 5rem; 
        display: flex;
        flex-direction: column;
    }
    main {
        flex-grow: 1;
    }
    #theme-toggle-button svg {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    html.dark input[type="datetime-local"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
  </style>
</head>
<body class="bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text">

  <header class="fixed top-0 left-0 w-full bg-green-700 dark:bg-gray-800 text-white shadow-lg z-50 transition-colors duration-300">
    <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 h-20">
      <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold" data-aos="fade-down">Administrador</h1>
      <nav class="flex items-center gap-2 md:gap-4">
        <a href="index.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl">
          <i class="fas fa-home text-lg md:text-2xl"></i>
          <span class="hidden sm:inline">Início</span>
        </a>
        <a href="catalogo.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg hover:bg-green-600 dark:hover:bg-gray-700 transition text-sm md:text-xl">
          <i class="fas fa-leaf text-lg md:text-2xl"></i>
          <span class="hidden sm:inline">Catálogo</span>
        </a>
        <a href="admin.php" class="flex items-center gap-2 px-3 py-2 md:px-4 rounded-lg bg-green-600 dark:bg-dark-primary hover:bg-green-500 dark:hover:bg-dark-primary-hover transition text-sm md:text-xl">
          <i class="fas fa-user-cog text-lg md:text-2xl"></i>
          <span class="hidden sm:inline">Admin</span>
        </a>
        <button id="theme-toggle-button" type="button" class="text-white hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 dark:focus:ring-gray-600 rounded-lg text-sm p-2.5 transition-colors duration-300">
          <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
          <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </button>
      </nav>
    </div>
  </header>

  <main class="container mx-auto px-6 pt-28 pb-10">
    <div class="max-w-3xl mx-auto bg-white dark:bg-dark-card p-8 rounded-2xl shadow-card">
      <h2 class="text-3xl font-semibold mb-8 text-primary dark:text-dark-primary text-center md:text-left">Cadastrar Nova Árvore</h2>

      <?php if (isset($msg)): ?>
        <p class="<?php echo strpos($msg, 'Erro') !== false ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'; ?> font-semibold mb-6 p-4 rounded-lg <?php echo strpos($msg, 'Erro') !== false ? 'bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700' : 'bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700'; ?> text-center">
            <?php echo htmlspecialchars($msg); ?>
        </p>
      <?php endif; ?>

      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
          <?php 
            render_form_field('nome_c', 'nome_c', 'Nome Científico');
            render_form_field('nat_exo', 'nat_exo', 'Nativa/Exótica');
            render_form_field('horario', 'horario', 'Data e Hora do Registro', 'datetime-local', true, date('Y-m-d\TH:i'));
            render_form_field('localizacao', 'localizacao', 'Localização');
            render_form_field('vegetacao', 'vegetacao', 'Tipo de Vegetação');
            render_form_field('especie', 'especie', 'Espécie');
            render_form_field('diametro_peito', 'diametro_peito', 'Diâmetro do Peito (cm)', 'text', true, '', 'Ex: 30.5', ['inputmode' => 'decimal']);
            render_form_field('estado_fitossanitario', 'estado_fitossanitario', 'Estado Fitossanitário');
            render_form_field('estado_tronco', 'estado_tronco', 'Estado do Tronco');
            render_form_field('estado_copa', 'estado_copa', 'Estado da Copa');
            render_form_field('tamanho_calcada', 'tamanho_calcada', 'Tamanho da Calçada (m)', 'text', true, '', 'Ex: 2.0', ['inputmode' => 'decimal']);
            render_form_field('espaco_arvore', 'espaco_arvore', 'Espaço da Árvore');
            render_form_field('raizes', 'raizes', 'Raízes');
            render_form_field('acessibilidade', 'acessibilidade', 'Acessibilidade');
          ?>
        </div>

        <div class="col-span-1 md:col-span-2">
          <?php 
            render_form_field('curiosidade', 'curiosidade', 'Curiosidade (Máx. 255 caracteres)', 'textarea', false, '', '', ['maxlength' => 255, 'rows' => 4]);
          ?>
        </div>
        
        <div>
          <label class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Nome(s) Popular(es) <span class="text-red-500">*</span> (Pelo menos um)</label>
          <div id="nomes-populares-container" class="space-y-3">
            <div class="flex items-center gap-2">
                <input type="text" name="nome_p[]" placeholder="Nome popular" maxlength="100" required class="flex-grow px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow" />
            </div>
          </div>
          <button type="button" onclick="adicionarCampoNomePopular()" class="mt-3 text-sm bg-accent dark:bg-dark-secondary hover:bg-secondary dark:hover:bg-dark-secondary-hover text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Adicionar Outro Nome Popular
          </button>
        </div>

        <button type="submit" class="w-full bg-primary dark:bg-dark-primary hover:bg-green-800 dark:hover:bg-dark-primary-hover text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg">
          <i class="fas fa-save"></i> Cadastrar Árvore
        </button>
      </form>
    </div>
  </main>

  <footer class="bg-gradient-to-r from-green-700 to-green-600 dark:from-gray-800 dark:to-gray-700 text-white text-center py-6 shadow-inner mt-auto transition-colors duration-300">
    <div class="container mx-auto px-4">
      © <?php echo date('Y'); ?> - Catálogo de Árvores | Todos os direitos reservados
    </div>
  </footer>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="style.js" defer></script>
</body>
</html>