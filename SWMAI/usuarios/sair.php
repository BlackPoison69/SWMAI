<?php
session_start();
require_once('../usuarios/logica-autenticacao.php'); // Ajuste conforme o nome correto do arquivo.
session_destroy();
redireciona("../geral/index.php");
exit;
