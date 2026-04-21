<?php
// API simples para buscar versículo do dia da abibliadigital.com.br
header('Content-Type: application/json; charset=utf-8');

$url = 'https://www.abibliadigital.com.br/api/verses/nvi/random';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    // 'Authorization: Token <SUA_CHAVE_AQUI>' // Se necessário, adicione sua chave
]);
$result = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200 && $result) {
    echo $result;
} else {
    echo json_encode([
        'ok' => false,
        'msg' => 'Não foi possível obter o versículo do dia.'
    ]);
}
