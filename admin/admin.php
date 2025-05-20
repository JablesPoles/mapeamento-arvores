<?php
session_start(); 

// Verifica se o administrador está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // ALTERAÇÃO: Define a mensagem de aviso antes de redirecionar
    // A variável $_SESSION['erro_login_admin'] pode ser renomeada para $_SESSION['aviso_login_necessario'] para consistência
    $_SESSION['aviso_login_necessario'] = 'Você precisa estar logado para acessar esta página.';
    header('Location: login_admin.php'); 
    exit;
}

// Caminho para conexao.php é um nível acima (../)
require __DIR__ . '/../conexao.php'; 

define('DYNAMIC_INPUT_CLASSES_PHP', 'flex-grow px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow');
define('DYNAMIC_REMOVE_BUTTON_CLASSES_PHP', 'bg-red-600 hover:bg-red-700 dark:bg-dark-remove-btn-bg dark:hover:bg-dark-remove-btn-hover-bg text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105');

$errors = [];
$input = [];
$msg = null;

function get_post_data($fields) {
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
    }
    return $data;
}

$expected_fields = [
    'nome_c', 'nat_exo', 'horario', 'localizacao', 'vegetacao', 'especie',
    'diametro_peito', 'estado_fitossanitario', 'estado_tronco', 'estado_copa',
    'tamanho_calcada', 'espaco_arvore', 'raizes', 'acessibilidade', 'curiosidade'
];

// Adicionado name="submit_arvore" ao botão de submit do formulário de árvores
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_arvore'])) {
    $input = get_post_data($expected_fields);
    $input['nome_p'] = isset($_POST['nome_p']) && is_array($_POST['nome_p'])
                        ? array_map('trim', $_POST['nome_p'])
                        : [];
    $input['nome_p'] = array_filter($input['nome_p']);

    if (empty($input['nome_c'])) { $errors['nome_c'] = 'O Nome Científico é obrigatório.'; }
    if (empty($input['especie'])) { $errors['especie'] = 'O Nome Popular Principal (Espécie) é obrigatório.'; }
    if (empty($input['nat_exo'])) { $errors['nat_exo'] = 'O campo Nativa/Exótica é obrigatório.'; }
    if (empty($input['horario'])) { $errors['horario'] = 'A Data e Hora do Registro são obrigatórias.';}
    if (empty($input['localizacao'])) { $errors['localizacao'] = 'A Localização é obrigatória.'; }
    
    if (empty($input['diametro_peito'])) { 
        $errors['diametro_peito'] = 'O Diâmetro do Peito é obrigatório.';
    } elseif (!is_numeric(str_replace(',', '.', $input['diametro_peito'])) || floatval(str_replace(',', '.', $input['diametro_peito'])) <= 0) {
        $errors['diametro_peito'] = 'O Diâmetro do Peito deve ser um número positivo.';
    }

    if (empty($input['estado_fitossanitario'])) { $errors['estado_fitossanitario'] = 'O Estado Fitossanitário é obrigatório.'; }
    if (empty($input['estado_tronco'])) { $errors['estado_tronco'] = 'O Estado do Tronco é obrigatório.'; }
    if (empty($input['estado_copa'])) { $errors['estado_copa'] = 'O Estado da Copa é obrigatório.'; }

    if (empty($input['tamanho_calcada'])) { 
        $errors['tamanho_calcada'] = 'O Tamanho da Calçada é obrigatório.';
    } elseif (!is_numeric(str_replace(',', '.', $input['tamanho_calcada'])) || floatval(str_replace(',', '.', $input['tamanho_calcada'])) < 0) {
        $errors['tamanho_calcada'] = 'O Tamanho da Calçada deve ser um número válido (0 ou maior).';
    }
    
    if (!empty($input['curiosidade']) && mb_strlen($input['curiosidade']) > 255) {
        $errors['curiosidade'] = 'A Curiosidade não pode exceder 255 caracteres.';
    }
    // Validação para nomes populares adicionais (nome_p)
    // Apenas valida se o primeiro campo de nome_p não estiver vazio, indicando que o usuário tentou adicionar.
    if (!empty($input['nome_p']) && !empty(trim($input['nome_p'][0]))) { 
        foreach ($input['nome_p'] as $idx => $np) {
            if (empty(trim($np)) && count($input['nome_p']) > 1) { // Se um campo que não o primeiro estiver vazio
                // Opcional: poderia adicionar um erro específico ou apenas ignorar campos vazios no processamento
            } elseif (!empty(trim($np)) && mb_strlen($np) > 100) { // Valida apenas se não estiver vazio
                $errors["nome_p_{$idx}"] = "O nome popular adicional '".htmlspecialchars(mb_substr($np, 0, 20))."...' excede 100 caracteres.";
            }
        }
    }


    if (empty($errors)) {
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
                ':nome_c'                  => $input['nome_c'],
                ':nat_exo'                 => $input['nat_exo'],
                ':horario'                 => $input['horario'],
                ':localizacao'             => $input['localizacao'],
                ':vegetacao'               => $input['vegetacao'] ?: null,
                ':especie'                 => $input['especie'],
                ':diametro_peito'          => floatval(str_replace(',', '.', $input['diametro_peito'])),
                ':estado_fitossanitario'   => $input['estado_fitossanitario'],
                ':estado_tronco'           => $input['estado_tronco'],
                ':estado_copa'             => $input['estado_copa'],
                ':tamanho_calcada'         => floatval(str_replace(',', '.', $input['tamanho_calcada'])),
                ':espaco_arvore'           => $input['espaco_arvore'] ?: null,
                ':raizes'                  => $input['raizes'] ?: null,
                ':acessibilidade'          => $input['acessibilidade'] ?: null,
                ':curiosidade'             => $input['curiosidade'] ?: null,
            ]);
            $arvoreId = $pdo->lastInsertId();
            
            // Processa nomes populares adicionais apenas se houver e o primeiro não estiver vazio
            if ($arvoreId && !empty($input['nome_p']) && !empty(trim($input['nome_p'][0]))) {
                $sqlInsNp = "INSERT INTO nomes_populares (nome) VALUES (:nome) ON CONFLICT (LOWER(nome)) DO NOTHING RETURNING id_nome";
                $stmtInsNp = $pdo->prepare($sqlInsNp);
                
                $sqlGetIdNp = "SELECT id_nome FROM nomes_populares WHERE LOWER(nome) = LOWER(:nome)";
                $stmtGetIdNp = $pdo->prepare($sqlGetIdNp);

                $sqlRel = "INSERT INTO nomes_populares_arvore (fk_arvore, fk_np) VALUES (:fk_arvore, :fk_np) ON CONFLICT DO NOTHING";
                $stmtRel = $pdo->prepare($sqlRel);

                foreach ($input['nome_p'] as $nomePopular) {
                    if(empty(trim($nomePopular))) continue; // Pula nomes populares adicionais vazios

                    $stmtInsNp->execute([':nome' => $nomePopular]);
                    $idNp = $stmtInsNp->fetchColumn();

                    if (!$idNp) { 
                        $stmtGetIdNp->execute([':nome' => $nomePopular]);
                        $idNp = $stmtGetIdNp->fetchColumn();
                    }
                    
                    if ($idNp) {
                        $stmtRel->execute([':fk_arvore' => $arvoreId, ':fk_np' => $idNp]);
                    }
                }
            }
            $pdo->commit();
            $msg = "Árvore cadastrada com sucesso!";
            $input = get_post_data($expected_fields);
            $input['horario'] = date('Y-m-d\TH:i');
            $input['nome_p'] = ['']; // Reseta para um campo vazio
        } catch (PDOException $e) {
            $pdo->rollBack();
            $msg = "Erro ao cadastrar árvore: Ocorreu um problema no banco de dados.";
            $errors['db_error'] = "Detalhe do erro (dev): " . $e->getMessage();
        }
    } else {
        $msg = "Foram encontrados erros no formulário. Por favor, verifique os campos destacados.";
    }
} else {
    $input = get_post_data($expected_fields);
    $input['horario'] = date('Y-m-d\TH:i');
    $input['nome_p'] = ['']; // Inicializa com um campo vazio para nomes populares
}

function render_form_field(
    string $id, string $name, string $label, string $type = 'text',
    bool $required = true, string $current_value = '', string $placeholder = '',
    array $options = [], string $error_message = ''
) {
    $label_classes = "block font-medium text-gray-700 dark:text-gray-300 mb-1";
    $input_classes_base = "w-full px-4 py-2.5 border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 transition-shadow";
    $error_border_classes = !empty($error_message)
        ? 'border-red-500 dark:border-red-400 focus:ring-red-500/50 dark:focus:ring-red-400/50 focus:border-red-500 dark:focus:border-red-400'
        : 'border-gray-300 dark:border-dark-input-border focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring';
    $final_input_classes = $input_classes_base . ' ' . $error_border_classes;
    $required_span = $required ? '<span class="text-red-500">*</span>' : '';
    $required_attr = $required ? 'required' : '';
    $placeholder_attr = $placeholder ? 'placeholder="' . htmlspecialchars($placeholder) . '"' : '';
    $value_attr = ($type !== 'textarea') ? 'value="' . htmlspecialchars($current_value) . '"' : '';

    echo "<div>";
    echo "<label class=\"{$label_classes}\" for=\"{$id}\">{$label} {$required_span}</label>";
    if ($type === 'textarea') {
        $rows = $options['rows'] ?? 4;
        $maxlength = isset($options['maxlength']) ? 'maxlength="' . $options['maxlength'] . '"' : '';
        echo "<textarea id=\"{$id}\" name=\"{$name}\" {$maxlength} rows=\"{$rows}\" class=\"{$final_input_classes}\" {$placeholder_attr} {$required_attr}>" . htmlspecialchars($current_value) . "</textarea>";
    } else {
        $inputmode = $options['inputmode'] ?? '';
        $inputmode_attr = $inputmode ? 'inputmode="' . $inputmode . '"' : '';
        echo "<input id=\"{$id}\" name=\"{$name}\" type=\"{$type}\" {$required_attr} {$value_attr} {$placeholder_attr} {$inputmode_attr} class=\"{$final_input_classes}\" />";
    }
    if (!empty($error_message)) {
        echo "<p class=\"text-xs text-red-600 dark:text-red-400 mt-1\">" . htmlspecialchars($error_message) . "</p>";
    }
    echo "</div>";
}

$_SESSION['current_page_title'] = 'Cadastro de Árvore'; // Título específico para esta página
if (file_exists(__DIR__ . '/../templates/header.php')) {
    include __DIR__ . '/../templates/header.php';
} else {
    echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Admin - Cadastro</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text'>";
}
?>

<main class="container mx-auto px-6 pt-28 pb-10">
<div class="max-w-3xl mx-auto bg-white dark:bg-dark-card p-8 rounded-2xl shadow-card">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h2 class="text-3xl font-semibold text-primary dark:text-dark-primary">Cadastrar Nova Árvore</h2>
            <a href="gerenciar_usuarios_admin.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-lg transition-colors duration-300 text-sm flex items-center gap-2 self-start sm:self-center">
                <i class="fas fa-users-cog"></i> Gerenciar Admins
            </a>
        </div>

        <?php if (isset($msg) && empty($errors['db_error']) ): ?>
            <p class="<?php echo !empty($errors) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'; ?> font-semibold mb-6 p-4 rounded-lg <?php echo !empty($errors) ? 'bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700' : 'bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700'; ?> text-center">
                <?php echo htmlspecialchars($msg); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($errors['db_error'])): // Exibe erro de banco de dados separadamente e mais proeminente ?>
            <div class="mb-6 p-4 text-sm rounded-lg bg-red-200 dark:bg-red-800/40 text-red-800 dark:text-red-300 border border-red-400 dark:border-red-600" role="alert">
                <p class="font-bold">Erro no Banco de Dados:</p>
                <p><?php echo htmlspecialchars($errors['db_error']); ?></p>
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="form-summary-errors mb-6 p-4 rounded-lg bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700" role="alert">
                <p class="font-semibold text-red-700 dark:text-red-300">Por favor, corrija os seguintes erros:</p>
                <ul class="list-disc list-inside text-red-600 dark:text-red-400 mt-2">
                    <?php foreach ($errors as $field_key => $error_text):
                        if ($field_key === 'db_error') continue; // Já exibido acima
                        if (strpos($field_key, 'nome_p_') === 0 && isset($errors['nome_p_general'])) continue;
                    ?>
                        <li><?php echo htmlspecialchars($error_text); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php" class="space-y-6" novalidate>
             <input type="hidden" name="submit_arvore" value="1"> <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <?php
                render_form_field('nome_c', 'nome_c', 'Nome Científico', 'text', true, $input['nome_c'] ?? '', 'Ex: Quercus robur L.', [], $errors['nome_c'] ?? '');
                render_form_field('especie', 'especie', 'Nome Popular Principal', 'text', true, $input['especie'] ?? '', 'Ex: Carvalho', [], $errors['especie'] ?? '');
                render_form_field('nat_exo', 'nat_exo', 'Nativa/Exótica', 'text', true, $input['nat_exo'] ?? '', 'NATIVA ou EXOTICA', [], $errors['nat_exo'] ?? '');
                render_form_field('horario', 'horario', 'Data e Hora do Registro', 'datetime-local', true, $input['horario'] ?? date('Y-m-d\TH:i'), '', [], $errors['horario'] ?? '');
                render_form_field('localizacao', 'localizacao', 'Localização', 'text', true, $input['localizacao'] ?? '', 'Ex: Praça da Matriz, em frente ao coreto', [], $errors['localizacao'] ?? '');
                render_form_field('vegetacao', 'vegetacao', 'Tipo de Vegetação', 'text', false, $input['vegetacao'] ?? '', 'Ex: Urbana, Rural, Parque', [], $errors['vegetacao'] ?? '');
                render_form_field('diametro_peito', 'diametro_peito', 'Diâmetro do Peito (cm)', 'text', true, $input['diametro_peito'] ?? '', 'Ex: 30.5', ['inputmode' => 'decimal'], $errors['diametro_peito'] ?? '');
                render_form_field('estado_fitossanitario', 'estado_fitossanitario', 'Estado Fitossanitário', 'text', true, $input['estado_fitossanitario'] ?? '', 'Ex: Saudável, Com pragas, Galhos secos', [], $errors['estado_fitossanitario'] ?? '');
                render_form_field('estado_tronco', 'estado_tronco', 'Estado do Tronco', 'text', true, $input['estado_tronco'] ?? '', 'Ex: Íntegro, Com lesões, Oco', [], $errors['estado_tronco'] ?? '');
                render_form_field('estado_copa', 'estado_copa', 'Estado da Copa', 'text', true, $input['estado_copa'] ?? '', 'Ex: Cheia, Rala, Assimétrica', [], $errors['estado_copa'] ?? '');
                render_form_field('tamanho_calcada', 'tamanho_calcada', 'Tamanho da Calçada (m)', 'text', true, $input['tamanho_calcada'] ?? '', 'Ex: 2.0', ['inputmode' => 'decimal'], $errors['tamanho_calcada'] ?? '');
                render_form_field('espaco_arvore', 'espaco_arvore', 'Espaço da Árvore', 'text', false, $input['espaco_arvore'] ?? '', 'Ex: Amplo, Restrito', [], $errors['espaco_arvore'] ?? '');
                render_form_field('raizes', 'raizes', 'Raízes (Conflitos)', 'text', false, $input['raizes'] ?? '', 'Ex: Sem conflitos, Levantando calçada', [], $errors['raizes'] ?? '');
                render_form_field('acessibilidade', 'acessibilidade', 'Acessibilidade ao Local', 'text', false, $input['acessibilidade'] ?? '', 'Ex: Boa, Difícil, Com obstáculos', [], $errors['acessibilidade'] ?? '');
                ?>
            </div>

            <div class="col-span-1 md:col-span-2">
                <?php
                render_form_field('curiosidade', 'curiosidade', 'Curiosidade (Máx. 255 caracteres)', 'textarea', false, $input['curiosidade'] ?? '', '', ['maxlength' => 255, 'rows' => 4], $errors['curiosidade'] ?? '');
                ?>
            </div>

            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Nome(s) Popular(es) Adicionais (Opcional)</label>
                <?php if(isset($errors['nome_p_general'])): ?>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1 mb-2"><?php echo htmlspecialchars($errors['nome_p_general']); ?></p>
                <?php endif; ?>

                <div id="nomes-populares-container" class="space-y-3">
                  <?php
                  $nomes_populares_render = !empty($input['nome_p']) ? $input['nome_p'] : [''];
                  if (empty($nomes_populares_render) || (count($nomes_populares_render) === 1 && empty(trim($nomes_populares_render[0])))) {
                        $nomes_populares_render = ['']; // Garante que sempre haja um campo, mesmo que vazio
                  }

                  foreach ($nomes_populares_render as $idx => $np_value):
                      $np_error_msg = $errors["nome_p_{$idx}"] ?? '';
                      $np_input_class = DYNAMIC_INPUT_CLASSES_PHP . ($np_error_msg ? ' border-red-500 dark:border-red-400' : '');
                  ?>
                      <div class="flex items-center gap-2 popular-name-group">
                          <input type="text" name="nome_p[]" placeholder="Nome popular adicional" maxlength="100"
                                  value="<?php echo htmlspecialchars($np_value); ?>"
                                  class="<?php echo $np_input_class; ?>" />
                          <?php if ($idx > 0 || (count($nomes_populares_render) > 1 && $idx === 0) ): // Mostra botão de remover se não for o primeiro E único campo ?>
                              <button type="button" class="<?php echo DYNAMIC_REMOVE_BUTTON_CLASSES_PHP; ?> remove-nome-popular-btn" aria-label="Remover nome popular">
                                  <i class="fas fa-trash-alt"></i>
                              </button>
                          <?php endif; ?>
                      </div>
                      <?php if ($np_error_msg): ?>
                          <p class="text-xs text-red-600 dark:text-red-400 -mt-2 mb-2 ml-1"><?php echo htmlspecialchars($np_error_msg); ?></p>
                      <?php endif; ?>
                  <?php endforeach; ?>
              </div>
                <button type="button" id="add-nome-popular-btn" class="mt-3 text-sm bg-accent dark:bg-dark-secondary hover:bg-secondary dark:hover:bg-dark-secondary-hover text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Adicionar Outro Nome Popular
                </button>
            </div>
            
            <button type="submit" name="submit_arvore" value="1" class="w-full bg-primary dark:bg-dark-primary hover:bg-green-800 dark:hover:bg-dark-primary-hover text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg">
                <i class="fas fa-save"></i> Cadastrar Árvore
            </button>
        </form>
    </div>
</main>

<?php 
if (file_exists(__DIR__ . '/../templates/footer.php')) {
    include __DIR__ . '/../templates/footer.php';
} else {
    echo "</body></html>";
}
unset($_SESSION['current_page_title']); 
?>
