<?php
session_start();
require __DIR__ . '/../conexao.php'; 
require __DIR__ . '/../src/db_functions.php'; 

$erro_login = '';
$aviso_necessidade_login = '';

// Verifica se há uma mensagem de necessidade de login vinda de outra página
if (isset($_SESSION['aviso_login_necessario'])) {
    $aviso_necessidade_login = $_SESSION['aviso_login_necessario'];
    unset($_SESSION['aviso_login_necessario']); // Limpa a mensagem após exibir
}

if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: admin.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_form = trim($_POST['usuario'] ?? '');
    $senha_form = trim($_POST['senha'] ?? ''); 

    if (empty($usuario_form) || empty($senha_form)) {
        $erro_login = 'Por favor, preencha usuário e senha.';
    } else {
        $admin = buscarAdminPorUsuario($pdo, $usuario_form);
        
        if ($admin && password_verify($senha_form, $admin['senha'])) {
            $_SESSION['admin_logado'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_usuario'] = $admin['usuario'];
            $_SESSION['admin_nome'] = $admin['nome_completo'];
            header('Location: admin.php'); 
            exit;
        } else {
            $erro_login = 'Usuário ou senha inválidos.';
        }
    }
}

$_SESSION['current_page_title'] = 'Login Administrativo'; // Para o header.php
if (file_exists(__DIR__ . '/../templates/header.php')) {
    include __DIR__ . '/../templates/header.php';
} else {
    echo "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Login Admin</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-light-bg dark:bg-dark-bg text-gray-800 dark:text-dark-text'>";
}
?>

<main class="container mx-auto px-6 pt-28 pb-10 flex-grow flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-dark-card p-8 rounded-2xl shadow-xl">
            <h2 class="text-3xl font-semibold mb-8 text-primary dark:text-dark-primary text-center">Login Administrativo</h2>

            <?php if (!empty($aviso_necessidade_login)): ?>
                <div class="mb-4 p-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-900/30 dark:text-yellow-300 text-center" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($aviso_necessidade_login); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro_login)): ?>
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900/30 dark:text-red-400 text-center" role="alert">
                    <?php echo htmlspecialchars($erro_login); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login_admin.php" class="space-y-6">
                <div>
                    <label for="usuario" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Usuário</label>
                    <input type="text" name="usuario" id="usuario" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow"
                           value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>">
                </div>
                <div>
                    <label for="senha" class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Senha</label>
                    <input type="password" name="senha" id="senha" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow">
                </div>
                <div>
                    <button type="submit"
                            class="w-full bg-primary dark:bg-dark-primary hover:bg-green-800 dark:hover:bg-dark-primary-hover text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </div>
            </form>
            <div class="mt-8 text-center">
                <a href="../index.php" 
                   class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-dark-primary hover:underline bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left"></i> Voltar para a Página Inicial
                </a>
            </div>
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
