<?php
// Простой тест CSRF защиты
session_start();

// Подключаем CSRF класс
require_once 'src/Core/CSRF.php';

echo "<h1>Тест CSRF защиты</h1>";

// Тест 1: Генерация токена
echo "<h2>Тест 1: Генерация токена</h2>";
$token1 = CSRF::generateToken();
$token2 = CSRF::generateToken();
echo "Токен 1: " . substr($token1, 0, 20) . "...<br>";
echo "Токен 2: " . substr($token2, 0, 20) . "...<br>";
echo "Токены одинаковые: " . ($token1 === $token2 ? "ДА" : "НЕТ") . "<br>";

// Тест 2: Проверка токена
echo "<h2>Тест 2: Проверка токена</h2>";
$isValid = CSRF::verifyToken($token1);
echo "Токен валидный: " . ($isValid ? "ДА" : "НЕТ") . "<br>";

// Тест 3: Проверка неверного токена
echo "<h2>Тест 3: Проверка неверного токена</h2>";
$isValid = CSRF::verifyToken("неверный_токен");
echo "Неверный токен валидный: " . ($isValid ? "ДА" : "НЕТ") . "<br>";

// Тест 4: HTML поле
echo "<h2>Тест 4: HTML поле</h2>";
echo CSRF::getTokenField();

// Тест 5: Обновление токена
echo "<h2>Тест 5: Обновление токена</h2>";
$oldToken = CSRF::generateToken();
$newToken = CSRF::refreshToken();
echo "Старый токен: " . substr($oldToken, 0, 20) . "...<br>";
echo "Новый токен: " . substr($newToken, 0, 20) . "...<br>";
echo "Токены разные: " . ($oldToken !== $newToken ? "ДА" : "НЕТ") . "<br>";

echo "<h2>CSRF защита успешно работает!</h2>";
?> 