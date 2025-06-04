<?php
session_start();
require __DIR__ . '/../conexao.php'; 
require __DIR__ . '/../src/db_functions.php'; 

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    $_SESSION['aviso_login_necessario'] = 'Você precisa estar logado para acessar o gerenciamento de usuários.';
    header('Location: login_admin.php'); 
    exit;
}

$admins = listarAdmins($pdo);
$mensagem = $_SESSION['mensagem_admin_usuarios'] ?? null;
$tipo_mensagem = $_SESSION['tipo_mensagem_admin_usuarios'] ?? null;
unset($_SESSION['mensagem_admin_usuarios'], $_SESSION['tipo_mensagem_admin_usuarios']);

$edit_admin_id = null;
$edit_usuario = '';
$edit_nome_completo = '';
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $admin_para_editar = buscarAdminPorId($pdo, (int)$_GET['id']);
    if ($admin_para_editar) {
        $edit_admin_id = $admin_para_editar['id'];
        $edit_usuario = $admin_para_editar['usuario'];
        $edit_nome_completo = $admin_para_editar['nome_completo'];
    } else {
        $_SESSION['mensagem_admin_usuarios'] = 'Administrador não encontrado para edição.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
        header('Location: gerenciar_usuarios_admin.php'); 
        exit;
    }
}

$_SESSION['current_page_title'] = 'Gerenciar Administradores'; 
if (file_exists(__DIR__ . '/../templates/header.php')) {
    include __DIR__ . '/../templates/header.php';
} else {
    echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Gerenciar Administradores</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text'>";
}
?>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-10 flex-grow">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-semibold text-primary dark:text-dark-primary">
                Gerenciar Usuários Administradores
            </h2>
            <a href="admin.php" 
               class="inline-flex items-center gap-2 text-sm text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 px-4 py-2 rounded-lg transition-colors duration-200 shadow hover:shadow-md">
                <i class="fas fa-plus-circle"></i> Cadastrar Árvore
            </a>
        </div>


        <?php if ($mensagem): ?>
            <div class="mb-6 p-4 text-sm rounded-lg <?php echo $tipo_mensagem === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-700' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-300 dark:border-red-700'; ?>" role="alert">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-2xl shadow-card mb-10">
            <h3 class="text-xl font-semibold mb-6 text-gray-700 dark:text-gray-200">
                <?php echo $edit_admin_id ? 'Editar Administrador' : 'Adicionar Novo Administrador'; ?>
            </h3>
            <form action="processa_usuario_admin.php" method="POST" class="space-y-6">
                <?php if ($edit_admin_id): ?>
                    <input type="hidden" name="admin_id" value="<?php echo $edit_admin_id; ?>">
                <?php endif; ?>
                <input type="hidden" name="action" value="<?php echo $edit_admin_id ? 'update' : 'create'; ?>">

                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuário:</label>
                    <input type="text" name="usuario" id="usuario" value="<?php echo htmlspecialchars($edit_usuario); ?>" required 
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow">
                </div>
                <div>
                    <label for="nome_completo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo:</label>
                    <input type="text" name="nome_completo" id="nome_completo" value="<?php echo htmlspecialchars($edit_nome_completo); ?>" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow">
                </div>
                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha <?php echo $edit_admin_id ? '(Deixe em branco para não alterar)' : ''; ?>:</label>
                    <input type="password" name="senha" id="senha" <?php echo !$edit_admin_id ? 'required' : ''; ?>
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow">
                </div>
                <div>
                    <label for="confirmar_senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Senha:</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha" <?php echo !$edit_admin_id ? 'required' : ''; ?>
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow">
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <?php if ($edit_admin_id): ?>
                         <a href="gerenciar_usuarios_admin.php" class="px-5 py-2.5 border border-gray-300 dark:border-dark-border rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm hover:shadow">Cancelar Edição</a>
                    <?php endif; ?>
                    <button type="submit" class="px-5 py-2.5 bg-primary dark:bg-dark-primary hover:bg-green-800 dark:hover:bg-dark-primary-hover text-white font-semibold rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105 shadow-md hover:shadow-lg">
                        <i class="fas <?php echo $edit_admin_id ? 'fa-save' : 'fa-plus-circle'; ?> mr-2"></i>
                        <?php echo $edit_admin_id ? 'Salvar Alterações' : 'Adicionar Administrador'; ?>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-dark-card p-6 sm:p-8 rounded-2xl shadow-card">
            <h3 class="text-xl font-semibold mb-6 text-gray-700 dark:text-gray-200">Administradores Cadastrados</h3>
            <?php if (empty($admins)): ?>
                <p class="text-gray-500 dark:text-gray-400">Nenhum administrador cadastrado.</p>
            <?php else: ?>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nome Completo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data Criação</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-dark-card divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($admins as $admin_item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo $admin_item['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo htmlspecialchars($admin_item['usuario']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"><?php echo htmlspecialchars($admin_item['nome_completo']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?php echo date('d/m/Y H:i', strtotime($admin_item['data_criacao'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="gerenciar_usuarios_admin.php?action=edit&id=<?php echo $admin_item['id']; ?>#form-edit" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <?php if ($admin_item['id'] !== $_SESSION['admin_id'] && strtolower($admin_item['usuario']) !== 'admin'): ?>
                                            <a href="processa_usuario_admin.php?action=delete&id=<?php echo $admin_item['id']; ?>" 
                                               onclick="return confirm('Tem certeza que deseja excluir este administrador? Esta ação não pode ser desfeita.');" 
                                               class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Excluir">
                                               <i class="fas fa-trash"></i> Excluir
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-10 text-center">
             <a href="admin.php" 
                class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-dark-primary hover:underline bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm hover:shadow">
                <i class="fas fa-arrow-left"></i> Voltar para Cadastro de Árvores
            </a>
        </div>
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
