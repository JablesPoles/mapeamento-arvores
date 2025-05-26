<?php
require __DIR__ . '/../conexao.php';
$csvFile = __DIR__ . '/data/dados_arvore.csv';

if (!file_exists($csvFile)) {
    die("Arquivo CSV não encontrado em: $csvFile");
}

try {
    $count = 0;
    $startTime = microtime(true);
    echo "Iniciando importação...\n";

    $handle = fopen($csvFile, 'r');
    if ($handle === false) {
        throw new Exception("Não foi possível abrir o arquivo CSV");
    }

    fgetcsv($handle);

    $sql = "INSERT INTO arvore (
        NOME_C, NAT_EXO, HORARIO, LOCALIZACAO, VEGETACAO, ESPECIE, 
        DIAMETRO_PEITO, ESTADO_FITOSSANITARIO, ESTADO_TRONCO, ESTADO_COPA, 
        TAMANHO_CALCADA, ESPACO_ARVORE, RAIZES, ACESSIBILIDADE, CURIOSIDADE
    ) VALUES (
        :nome_c, :nat_exo, :horario, :localizacao, :vegetacao, :especie, 
        :diametro_peito, :estado_fitossanitario, :estado_tronco, :estado_copa, 
        :tamanho_calcada, :espaco_arvore, :raizes, :acessibilidade, :curiosidade
    )";

    $stmt = $pdo->prepare($sql);
    $pdo->beginTransaction();
    $batchSize = 100;

    while (($data = fgetcsv($handle, 0, ",")) !== false) {
        if (empty($data[0]) || count($data) < 15) {
            continue;
        }

        $horario = DateTime::createFromFormat('n/j/Y H:i:s', trim($data[0]));
        if ($horario === false) {
            echo "Aviso: Formato de data inválido na linha " . ($count + 1) . ": " . $data[0] . "\n";
            continue;
        }

        $params = [
            ':nome_c' => trim($data[3]),
            ':nat_exo' => (strtoupper(trim($data[5])) === 'NATIVA') ? 'NATIVA' : 'EXOTICA',
            ':horario' => $horario->format('Y-m-d H:i:s'),
            ':localizacao' => trim($data[1]),
            ':vegetacao' => trim($data[2]),
            ':especie' => trim($data[4]),
            ':diametro_peito' => trim($data[7]),
            ':estado_fitossanitario' => trim($data[8]),
            ':estado_tronco' => trim($data[9]),
            ':estado_copa' => trim($data[10]),
            ':tamanho_calcada' => trim($data[12]),
            ':espaco_arvore' => trim($data[13]),
            ':raizes' => trim($data[14]),
            ':acessibilidade' => 'ADEQUADO',
            ':curiosidade' => trim($data[6])
        ];
        $stmt->execute($params);
        $count++;
        if ($count % $batchSize === 0) {
            $pdo->commit();
            $pdo->beginTransaction();
            echo "Processadas $count linhas...\n";
        }
    }

    $pdo->commit();
    fclose($handle);
    $executionTime = round(microtime(true) - $startTime, 2);
    echo "\nImportação concluída com sucesso!\n";
    echo "Total de registros inseridos: $count\n";
    echo "Tempo de execução: $executionTime segundos\n";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("\nErro na importação: " . $e->getMessage() . "\nLinha: " . ($count + 1));
} catch (Exception $e) {
    die("\nErro: " . $e->getMessage());
}