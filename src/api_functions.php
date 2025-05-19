<?php
// src/api_functions.php

/**
 * Consulta a API PlantNet para obter imagens da árvore pelo nome científico.
 *
 * @param string $nomeCientifico Nome científico da árvore.
 * @return array|null Array associativo com URLs das imagens por tipo ou null se não encontrar.
 */
function buscarImagensPlantNet($nomeCientifico) {
    $apiUrl = "https://api.plantnet.org/v1/projects/k-world-flora/species/" .
              rawurlencode(trim($nomeCientifico)) . "?lang=pt-br&truncated=true";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Em ambiente de produção, a verificação SSL deve estar habilitada para garantir segurança.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Alterar para true em produção.

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $headers = [
        'Accept: application/json',
        'User-Agent: CatalogoArvores/1.0 (SeuAppExemplo; +http://seusite.com/contato)'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['images']) || !is_array($data['images'])) {
        return null;
    }

    $tiposImagens = ['fruit', 'leaf', 'bark', 'habit', 'flower'];
    $imagensEncontradas = [];

    foreach ($tiposImagens as $tipo) {
        if (!empty($data['images'][$tipo]) && is_array($data['images'][$tipo])) {
            $primeiraImagem = $data['images'][$tipo][0];

            $urlImagem = $primeiraImagem['url']['m'] ?? $primeiraImagem['url']['o'] ?? $primeiraImagem['m'] ?? null;
            if (isset($primeiraImagem['url']) && is_string($primeiraImagem['url'])) {
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
