<?php
/**
 * @var modX $modx
 * @var array $options
 */
$output = '';

// 1. Проверка требований (PHP 8.0+)
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    $error = '<h1>Ошибка!</h1><p>Для работы пакета требуется <b>PHP 8.0+</b>. Ваша версия: ' . PHP_VERSION . '.</p>';
    // В MODX 3 можно вернуть массив с ошибкой, но для совместимости вернем HTML
    return $error; 
}

// 2. Вывод опций (Чекбоксы событий)
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $output .= '<h3>Выберите события для отслеживания:</h3>
        <p>Плагин будет создавать файлы только для выбранных элементов.</p>
        <div style="margin-top: 10px;">
            <label><input type="checkbox" name="events[]" value="OnBeforeChunkFormSave" checked> Чанки (OnBeforeChunkFormSave)</label><br>
            <label><input type="checkbox" name="events[]" value="OnBeforeTempFormSave" checked> Шаблоны (OnBeforeTempFormSave)</label><br>
            <label><input type="checkbox" name="events[]" value="OnBeforeSnipFormSave" checked> Сниппеты (OnBeforeSnipFormSave)</label><br>
            <label><input type="checkbox" name="events[]" value="OnBeforePluginFormSave" checked> Плагины (OnBeforePluginFormSave)</label><br>
        </div>';
        break;
}

return $output;
