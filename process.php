<?php
function key_df($local_alice_key, $local_bob_key, $shared_key, $shared_mod) {
    $shared_alice_key = pow($shared_key, $local_alice_key) % $shared_mod;
    $shared_bob_key = pow($shared_key, $local_bob_key) % $shared_mod;

    $sim_alice_key = pow($shared_bob_key, $local_alice_key) % $shared_mod;
    $sim_bob_key = pow($shared_alice_key, $local_bob_key) % $shared_mod;

    if ($sim_alice_key == $sim_bob_key) {
        return $sim_alice_key;
    } else {
        echo "Ошибка: ключи не совпадают.\n";
        exit;
    }
}

function encrypt_decrypt($value, $key) {
    $result = '';
    $value_length = strlen($value);
    $key_length = strlen($key);

    for ($i = 0; $i < $value_length; $i++) {
        $result .= $value[$i] ^ $key[$i % $key_length];
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $startTime = microtime(true);
    $startMemory = memory_get_usage();

    $local_alice_key = intval($_GET['local_alice_key']);
    $local_bob_key = intval($_GET['local_bob_key']);
    $shared_key = intval($_GET['shared_key']);
    $shared_mod = intval($_GET['shared_mod']);
    $text = $_GET['text'];
    $action = $_GET['action'];

    $shared_secret = key_df($local_alice_key, $local_bob_key, $shared_key, $shared_mod);

    $shared_secret_key = strval($shared_secret);

    // Выполнение действия
    if ($action === 'encrypt') {
        $result_text = encrypt_decrypt($text, $shared_secret_key);
        $output = "Зашифрованный текст (hex): " . bin2hex($result_text);
    } elseif ($action === 'decrypt') {
        $decoded_text = hex2bin($text);
        $result_text = encrypt_decrypt($decoded_text, $shared_secret_key);
        $output = "Расшифрованный текст: " . $result_text;
    } else {
        $output = "Неверное действие.";
    }

    $executionTime = (microtime(true) - $startTime) * 1000;
    $memoryUsage = memory_get_usage() - $startMemory;

    // строка 1)As the world slowly came to life, a sense of tranquility filled the air, promising a beautiful day.
    //строка 2)The Allure of the Unknown: Exploring the Mysteries of the Sea
    //Beneath the shimmering surface of our oceans lies a vast and enigmatic realm, teeming with untold secrets and wonders. The deep sea, with its unfathomable depths and hidden ecosystems, has captivated the imagination of explorers, scientists, and dreamers alike.
    //Descending into the abyss, one encounters an otherworldly landscape where sunlight fades into darkness and pressure mounts relentlessly. Hydrothermal vents spew hot, mineral-rich fluids, creating vibrant oases teeming with bizarre and exotic creatures. Bioluminescent organisms illuminate the pitch-black waters, casting an ethereal glow on the darkness.
    //The deep sea is a treasure trove of undiscovered species. Giant squid, with their colossal size and piercing eyes, lurk in the depths. Anglerfish, with their bioluminescent lures, attract prey to their gaping maws. Deep-sea anglerfish, with their oversized heads and long, sharp teeth, resemble creatures from a nightmare
    echo "<h1>Результаты</h1>";
    echo "<p style='word-wrap: break-word; max-width: 500px;'>Общий секретный ключ: $shared_secret</p>";
    echo "<p style='word-wrap: break-word; max-width: 500px;'>$output</p>";
    echo "<p>Время выполнения: " . number_format($executionTime, 2) . " мс</p>";
    echo "<p>Использованная память: " . number_format($memoryUsage / 1024, 2) . " КБ</p>";
    echo "<br><a href='index.html'>Вернуться</a>";
} else {
    echo "Некорректный метод запроса.";
}
?>
