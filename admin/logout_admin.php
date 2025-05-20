<?php
session_start();

unset($_SESSION['admin_logado']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_usuario']);
unset($_SESSION['admin_nome']);

// Redirecionar para a página de login dentro da pasta admin
header('Location: login_admin.php'); 
exit;
?>
