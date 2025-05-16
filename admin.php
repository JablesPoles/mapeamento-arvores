<?php
require 'conexao.php';

// Constantes para classes CSS usadas dinamicamente (para consistência com JS)
define('DYNAMIC_INPUT_CLASSES_PHP', 'flex-grow px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow');
define('DYNAMIC_REMOVE_BUTTON_CLASSES_PHP', 'bg-red-600 hover:bg-red-700 dark:bg-dark-remove-btn-bg dark:hover:bg-dark-remove-btn-hover-bg text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105');

$errors = []; // Array para armazenar mensagens de erro de validação
$input = [];   // Array para armazenar os dados de entrada do usuário
$msg = null;    // Mensagem de sucesso ou erro geral

// Função para obter dados do POST de forma segura e popular o array $input
function get_post_data($fields) {
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
    }
    return $data;
}

// Lista de campos esperados do formulário (exceto arrays como nome_p)
$expected_fields = [
    'nome_c', 'nat_exo', 'horario', 'localizacao', 'vegetacao', 'especie',
    'diametro_peito', 'estado_fitossanitario', 'estado_tronco', 'estado_copa',
    'tamanho_calcada', 'espaco_arvore', 'raizes', 'acessibilidade', 'curiosidade'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Popula $input com os dados do POST
    $input = get_post_data($expected_fields);
    // Trata o campo 'nome_p' que é um array
    $input['nome_p'] = isset($_POST['nome_p']) && is_array($_POST['nome_p'])
                        ? array_map('trim', $_POST['nome_p'])
                        : [];
    $input['nome_p'] = array_filter($input['nome_p']); // Remove nomes populares em branco

    // --- VALIDAÇÃO NO LADO DO SERVIDOR ---
    if (empty($input['nome_c'])) { $errors['nome_c'] = 'O Nome Científico é obrigatório.'; }
    if (empty($input['nat_exo'])) { $errors['nat_exo'] = 'O campo Nativa/Exótica é obrigatório.'; }
    if (empty($input['horario'])) {
        $errors['horario'] = 'A Data e Hora do Registro são obrigatórias.';
    } // Poderia adicionar validação de formato de data aqui
    if (empty($input['localizacao'])) { $errors['localizacao'] = 'A Localização é obrigatória.'; }
    if (empty($input['especie'])) { $errors['especie'] = 'A Espécie é obrigatória.'; }

    if (empty($input['diametro_peito'])) {
        $errors['diametro_peito'] = 'O Diâmetro do Peito é obrigatório.';
    } elseif (!is_numeric($input['diametro_peito']) || floatval($input['diametro_peito']) <= 0) {
        $errors['diametro_peito'] = 'O Diâmetro do Peito deve ser um número positivo.';
    }

    if (empty($input['estado_fitossanitario'])) { $errors['estado_fitossanitario'] = 'O Estado Fitossanitário é obrigatório.'; }
    if (empty($input['estado_tronco'])) { $errors['estado_tronco'] = 'O Estado do Tronco é obrigatório.'; }
    if (empty($input['estado_copa'])) { $errors['estado_copa'] = 'O Estado da Copa é obrigatório.'; }

    if (empty($input['tamanho_calcada'])) {
        $errors['tamanho_calcada'] = 'O Tamanho da Calçada é obrigatório.';
    } elseif (!is_numeric($input['tamanho_calcada']) || floatval($input['tamanho_calcada']) < 0) {
        $errors['tamanho_calcada'] = 'O Tamanho da Calçada deve ser um número válido (0 ou maior).';
    }

    // Validação para 'curiosidade' (opcional, mas com limite de tamanho)
    if (!empty($input['curiosidade']) && mb_strlen($input['curiosidade']) > 255) {
        $errors['curiosidade'] = 'A Curiosidade não pode exceder 255 caracteres.';
    }

    // Validação para 'nome_p' (pelo menos um é obrigatório)
    if (empty($input['nome_p'])) {
        $errors['nome_p_general'] = 'Pelo menos um Nome Popular é obrigatório.';
    } else {
        foreach ($input['nome_p'] as $idx => $np) {
            if (mb_strlen($np) > 100) {
                $errors["nome_p_{$idx}"] = "O nome popular '".htmlspecialchars(mb_substr($np, 0, 20))."...' excede 100 caracteres.";
            }
        }
    }
    // Adicione mais validações conforme necessário para os campos restantes...


    if (empty($errors)) { // Se não houver erros de validação
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
                ':diametro_peito'          => floatval($input['diametro_peito']),
                ':estado_fitossanitario'   => $input['estado_fitossanitario'],
                ':estado_tronco'           => $input['estado_tronco'],
                ':estado_copa'             => $input['estado_copa'],
                ':tamanho_calcada'         => floatval($input['tamanho_calcada']),
                ':espaco_arvore'           => $input['espaco_arvore'] ?: null,
                ':raizes'                  => $input['raizes'] ?: null,
                ':acessibilidade'          => $input['acessibilidade'] ?: null,
                ':curiosidade'             => $input['curiosidade'] ?: null,
            ]);

            $arvoreId = $pdo->lastInsertId();

            if ($arvoreId && !empty($input['nome_p'])) {
                $sqlCheckNp = "SELECT id_nome FROM nomes_populares WHERE LOWER(nome) = LOWER(:nome)";
                $stmtCheckNp = $pdo->prepare($sqlCheckNp);

                $sqlInsNp = "INSERT INTO nomes_populares (nome) VALUES (:nome)";
                $stmtInsNp = $pdo->prepare($sqlInsNp);

                $sqlRel = "INSERT INTO nomes_populares_arvore (fk_arvore, fk_np) VALUES (:fk_arvore, :fk_np)";
                $stmtRel = $pdo->prepare($sqlRel);

                foreach ($input['nome_p'] as $nomePopular) {
                    $stmtCheckNp->execute([':nome' => $nomePopular]);
                    $rowNp = $stmtCheckNp->fetch(PDO::FETCH_ASSOC);
                    $idNp = $rowNp ? $rowNp['id_nome'] : null;

                    if (!$idNp) {
                        $stmtInsNp->execute([':nome' => $nomePopular]);
                        $idNp = $pdo->lastInsertId();
                    }

                    if ($idNp) {
                        $stmtRel->execute([':fk_arvore' => $arvoreId, ':fk_np' => $idNp]);
                    }
                }
            }
            $pdo->commit();
            $msg = "Árvore cadastrada com sucesso!";
            // Limpa os campos do formulário após o sucesso
            $input = get_post_data($expected_fields);
            $input['horario'] = date('Y-m-d\TH:i');
            $input['nome_p'] = [''];
        } catch (PDOException $e) {
            $pdo->rollBack();
            $msg = "Erro ao cadastrar árvore: Ocorreu um problema no banco de dados.";
            $errors['db_error'] = "Detalhe do erro (dev): " . $e->getMessage();
        }
    } else {
        // Erros de validação encontrados
        $msg = "Foram encontrados erros no formulário. Por favor, verifique os campos destacados.";
    }
} else {
    // Se não for POST, inicializa $input para o formulário em branco
    $input = get_post_data($expected_fields);
    $input['horario'] = date('Y-m-d\TH:i');
    $input['nome_p'] = [''];
}


// Função render_form_field refatorada para incluir valores e erros
function render_form_field(
    $id, $name, $label, $type = 'text', $required = true,
    $current_value = '', // Valor atual do campo
    $placeholder = '', $options = [],
    $error_message = '' // Mensagem de erro específica para este campo
) {
    $label_classes = "block font-medium text-gray-700 dark:text-gray-300 mb-1";
    $input_classes_base = "w-full px-4 py-2.5 border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 transition-shadow";

    // Adiciona classes de erro se houver mensagem de erro
    $error_border_classes = $error_message
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

    // Exibe a mensagem de erro para este campo, se houver
    if ($error_message) {
        echo "<p class=\"text-xs text-red-600 dark:text-red-400 mt-1\">" . htmlspecialchars($error_message) . "</p>";
    }
    echo "</div>";
}

include __DIR__ . '/templates/header.php';
?>

<main class="container mx-auto px-6 pt-28 pb-10">
<script src="assets/js/style.js" defer></script>
    <div class="max-w-3xl mx-auto bg-white dark:bg-dark-card p-8 rounded-2xl shadow-card">
        <h2 class="text-3xl font-semibold mb-8 text-primary dark:text-dark-primary text-center md:text-left">Cadastrar Nova Árvore</h2>

        <?php // Exibir mensagem geral (sucesso ou falha na validação)
        if (isset($msg) && empty($errors['db_error']) ): // Não mostrar se for erro de DB específico
        ?>
            <p class="<?php echo !empty($errors) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'; ?> font-semibold mb-6 p-4 rounded-lg <?php echo !empty($errors) ? 'bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700' : 'bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700'; ?> text-center">
                <?php echo htmlspecialchars($msg); ?>
            </p>
        <?php endif; ?>

        <?php // Sumário de erros de validação
        if (!empty($errors) && empty($errors['db_error'])): // Não mostrar sumário se o erro principal for de DB
        ?>
            <div class="form-summary-errors mb-6" role="alert">
                <p class="font-semibold">Por favor, corrija os seguintes erros:</p>
                <ul>
                    <?php foreach ($errors as $field_key => $error_text):
                        // Não listar erros de nome popular individualmente aqui se já houver um geral
                        if (strpos($field_key, 'nome_p_') === 0 && isset($errors['nome_p_general'])) continue;
                    ?>
                        <li><?php echo htmlspecialchars($error_text); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6" novalidate>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <?php
                // Usar $input para repopular e $errors para mensagens de erro
                render_form_field('nome_c', 'nome_c', 'Nome Científico', 'text', true, $input['nome_c'] ?? '', '', [], $errors['nome_c'] ?? '');
                render_form_field('nat_exo', 'nat_exo', 'Nativa/Exótica', 'text', true, $input['nat_exo'] ?? '', '', [], $errors['nat_exo'] ?? '');
                render_form_field('horario', 'horario', 'Data e Hora do Registro', 'datetime-local', true, $input['horario'] ?? date('Y-m-d\TH:i'), '', [], $errors['horario'] ?? '');
                render_form_field('localizacao', 'localizacao', 'Localização', 'text', true, $input['localizacao'] ?? '', '', [], $errors['localizacao'] ?? '');
                render_form_field('vegetacao', 'vegetacao', 'Tipo de Vegetação', 'text', false, $input['vegetacao'] ?? '', '', [], $errors['vegetacao'] ?? ''); // Exemplo não obrigatório
                render_form_field('especie', 'especie', 'Espécie', 'text', true, $input['especie'] ?? '', '', [], $errors['especie'] ?? '');
                render_form_field('diametro_peito', 'diametro_peito', 'Diâmetro do Peito (cm)', 'text', true, $input['diametro_peito'] ?? '', 'Ex: 30.5', ['inputmode' => 'decimal'], $errors['diametro_peito'] ?? '');
                render_form_field('estado_fitossanitario', 'estado_fitossanitario', 'Estado Fitossanitário', 'text', true, $input['estado_fitossanitario'] ?? '', '', [], $errors['estado_fitossanitario'] ?? '');
                render_form_field('estado_tronco', 'estado_tronco', 'Estado do Tronco', 'text', true, $input['estado_tronco'] ?? '', '', [], $errors['estado_tronco'] ?? '');
                render_form_field('estado_copa', 'estado_copa', 'Estado da Copa', 'text', true, $input['estado_copa'] ?? '', '', [], $errors['estado_copa'] ?? '');
                render_form_field('tamanho_calcada', 'tamanho_calcada', 'Tamanho da Calçada (m)', 'text', true, $input['tamanho_calcada'] ?? '', 'Ex: 2.0', ['inputmode' => 'decimal'], $errors['tamanho_calcada'] ?? '');
                render_form_field('espaco_arvore', 'espaco_arvore', 'Espaço da Árvore', 'text', false, $input['espaco_arvore'] ?? '', '', [], $errors['espaco_arvore'] ?? '');
                render_form_field('raizes', 'raizes', 'Raízes', 'text', false, $input['raizes'] ?? '', '', [], $errors['raizes'] ?? '');
                render_form_field('acessibilidade', 'acessibilidade', 'Acessibilidade', 'text', false, $input['acessibilidade'] ?? '', '', [], $errors['acessibilidade'] ?? '');
                ?>
            </div>

            <div class="col-span-1 md:col-span-2">
                <?php
                render_form_field('curiosidade', 'curiosidade', 'Curiosidade (Máx. 255 caracteres)', 'textarea', false, $input['curiosidade'] ?? '', '', ['maxlength' => 255, 'rows' => 4], $errors['curiosidade'] ?? '');
                ?>
            </div>

            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Nome(s) Popular(es) <span class="text-red-500">*</span> (Pelo menos um)</label>
                <?php if(isset($errors['nome_p_general'])): ?>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1 mb-2"><?php echo htmlspecialchars($errors['nome_p_general']); ?></p>
                <?php endif; ?>

                <div id="nomes-populares-container" class="space-y-3">
                  <?php
                  $nomes_populares_render = !empty($input['nome_p']) ? $input['nome_p'] : [''];
                  if (empty($nomes_populares_render)) $nomes_populares_render = [''];

                  foreach ($nomes_populares_render as $idx => $np_value):
                      $np_error_msg = $errors["nome_p_{$idx}"] ?? '';
                      $np_input_class = DYNAMIC_INPUT_CLASSES_PHP . ($np_error_msg ? ' border-red-500 dark:border-red-400' : '');
                  ?>
                      <div class="flex items-start gap-2 popular-name-group">
                        <input type="text" name="nome_p[]" placeholder="Nome popular" maxlength="100"
                                      value="<?php echo htmlspecialchars($np_value); ?>"
                                      class="<?php echo $np_input_class; ?>"
                                      <?php if ($idx === 0 && empty($errors['nome_p_general']) && empty($np_error_msg) ) echo 'required'; ?> />
                              <?php if ($np_error_msg): ?>
                                  <p class="text-xs text-red-600 dark:text-red-400 mt-1"><?php echo htmlspecialchars($np_error_msg); ?></p>
                              <?php endif; ?>
                          </div>
                          <?php if (count($nomes_populares_render) > 1): ?>
                              <button type="button" class="<?php echo DYNAMIC_REMOVE_BUTTON_CLASSES_PHP; ?> remove-nome-popular-btn" aria-label="Remover nome popular">
                                  <i class="fas fa-trash-alt"></i>
                              </button>
                          <?php endif; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
                <button type="button" id="add-nome-popular-btn" class="mt-3 text-sm bg-accent dark:bg-dark-secondary hover:bg-secondary dark:hover:bg-dark-secondary-hover text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Adicionar Outro Nome Popular
                </button>
                <button type="submit" class="w-full bg-primary dark:bg-dark-primary hover:bg-green-800 dark:hover:bg-dark-primary-hover text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg">
                <i class="fas fa-save"></i> Cadastrar Árvore
                </button>
              </div>

            
        </form>
    </div>
</main>

<?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>