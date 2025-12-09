<?php
function generar_token($email, $ttl = 3600){
    $secret = 'cL_s3cr3t_2025';
    $exp = time() + $ttl;
    $data = $email . '|' . $exp;
    $sig = hash_hmac('sha256', $data, $secret);
    $token = rtrim(strtr(base64_encode($data . '|' . $sig), '+/', '-_'), '=');
    return $token;
}

function validar_token($token){
    $secret = 'cL_s3cr3t_2025';
    $decoded = base64_decode(strtr($token, '-_', '+/'));
    if(!$decoded) return false;
    $parts = explode('|', $decoded);
    if(count($parts) !== 3) return false;
    list($email, $exp, $sig) = $parts;
    if(time() > intval($exp)) return false;
    $check = hash_hmac('sha256', $email . '|' . $exp, $secret);
    if(!hash_equals($check, $sig)) return false;
    return ['email' => $email, 'exp' => $exp];
}
?>