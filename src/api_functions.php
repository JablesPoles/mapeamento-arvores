<?php
// src/api_functions.php

function buscarImagensPlantNet($nomeCientifico) {
    $apiUrl = "https://api.plantnet.org/v1/projects/k-world-flora/species/" .
              rawurlencode(trim($nomeCientifico)) . "?lang=pt-br&truncated=true";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // ATENÇÃO IMPORTANTE DE SEGURANÇA:
    // Em produção, CURLOPT_SSL_VERIFYPEER DEVE ser true.
    // Configurar como 'false' desabilita a verificação do certificado SSL,
    // tornando a conexão vulnerável a ataques "man-in-the-middle".
    // Certifique-se de que seu servidor PHP tenha os certificados CA raiz atualizados.
    // Se o seu servidor não conseguir verificar o certificado da PlantNet,
    // você pode precisar especificar o caminho para um bundle de certificados CA
    // usando curl_setopt($ch, CURLOPT_CAINFO, '/caminho/para/cacert.pem');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // MUITA ATENÇÃO A ESTA LINHA! Mude para true em produção.
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // O CORRETO PARA PRODUÇÃO

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout para conexão em segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);      // Timeout total da requisição em segundos

    $headers = [
        'Accept: application/json',
        'User-Agent: CatalogoArvores/1.0 (SeuAppExemplo; +http://seusite.com/contato)' // É bom ter um User-Agent específico
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // Para depuração, você pode logar o erro:
        // error_log("Erro cURL (" . curl_errno($ch) . "): " . curl_error($ch) . " para URL: " . $apiUrl);
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['images']) || !is_array($data['images'])) {
        // error_log("Erro ao decodificar JSON ou 'images' ausente/inválido para " . $nomeCientifico . ". Resposta: " . $response);
        return null;
    }

    $tiposImagens = ['fruit', 'leaf', 'bark', 'habit', 'flower'];
    $imagensEncontradas = [];

    foreach ($tiposImagens as $tipo) {
        if (!empty($data['images'][$tipo]) && is_array($data['images'][$tipo])) {
            $primeiraImagem = $data['images'][$tipo][0];
            // Operador de coalescência nula (PHP 7.0+) para simplificar
            $urlImagem = $primeiraImagem['url']['m'] ?? $primeiraImagem['url']['o'] ?? $primeiraImagem['m'] ?? null; // Ajustado conforme a estrutura da API da PlantNet (url.m ou url.o) ou 'm' direto
            if (isset($primeiraImagem['url']) && is_string($primeiraImagem['url'])) { // Fallback caso a estrutura seja ['url'] = "string"
                $urlImagem = $primeiraImagem['url'];
            }


            if ($urlImagem) {
                $imagensEncontradas[$tipo] = $urlImagem;
            }
        }
    }
    return !empty($imagensEncontradas) ? $imagensEncontradas : null;
}
?>