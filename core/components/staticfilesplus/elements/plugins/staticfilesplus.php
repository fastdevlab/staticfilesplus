<?php
/**
 * StaticFilesPlus
 *
 * Автоматически делает элементы статическими и сохраняет их в файлы
 * по пути pdotools_elements_path + тип + категории с транслитерацией.
 *
 * ВАЖНО: привязать к событиям:
 * OnBeforeChunkFormSave, OnBeforeTempFormSave, OnBeforeSnipFormSave, OnBeforePluginFormSave
 *
 * Работает в MODX 3, PHP 8+
 */

$eventName = $modx->event->name;

$events = [
    'OnBeforeChunkFormSave'  => ['type' => 'chunks',    'extension' => 'tpl'],
    'OnBeforeTempFormSave'   => ['type' => 'templates', 'extension' => 'tpl'],
    'OnBeforeSnipFormSave'   => ['type' => 'snippets',  'extension' => 'php'],
    'OnBeforePluginFormSave' => ['type' => 'plugins',   'extension' => 'php'],
];

if (!isset($events[$eventName])) {
    return;
}

$config = $events[$eventName];

// Получаем объект элемента в зависимости от события
$element = null;
switch ($eventName) {
    case 'OnBeforeChunkFormSave':
        $element = isset($chunk) ? $chunk : null;
        break;
    case 'OnBeforeTempFormSave':
        $element = isset($template) ? $template : null;
        break;
    case 'OnBeforeSnipFormSave':
        $element = isset($snippet) ? $snippet : null;
        break;
    case 'OnBeforePluginFormSave':
        $element = isset($plugin) ? $plugin : null;
        break;
}

if (!$element) {
    return;
}

// ---------------------------------------------------------------------
// 1. Определяем базовый путь для записи файла (pdotools_elements_path)
// ---------------------------------------------------------------------

$basePath = $modx->getOption('pdotools_elements_path', null, '');

if (empty($basePath)) {
    // По умолчанию пишем в core/elements/
    $basePath = MODX_CORE_PATH . 'elements/';
} else {
    // Если путь относительный — делаем его абсолютным от корня сайта
    if (strpos($basePath, '/') !== 0 && strpos($basePath, ':') === false) {
        $basePath = MODX_BASE_PATH . $basePath;
    }
}

$basePath = rtrim($basePath, '/') . '/';
$typePath = $basePath . $config['type'] . '/';

// ---------------------------------------------------------------------
// 2. Строим путь категорий с использованием filterPathSegment()
// ---------------------------------------------------------------------

$categoryPath = '';
$categoryId   = (int) $element->get('category');

if ($categoryId > 0) {
    $path     = [];
    $category = $modx->getObject('modCategory', $categoryId);

    if ($category) {
        $maxDepth = 10;
        $depth    = 0;

        while ($category && $depth < $maxDepth) {
            $categoryName = (string) $category->get('category');

            if (!empty($categoryName)) {
                // Очистка/транслитерация имени папки
                $categoryName = $modx->filterPathSegment($categoryName);
                // Пробелы и дефисы → подчёркивания
                $categoryName = str_replace([' ', '-'], '_', $categoryName);
                // В нижний регистр
                $categoryName = strtolower($categoryName);

                if (!empty($categoryName)) {
                    array_unshift($path, $categoryName);
                }
            }

            $parentId = (int) $category->get('parent');
            if ($parentId > 0) {
                $category = $modx->getObject('modCategory', $parentId);
            } else {
                break;
            }

            $depth++;
        }
    }

    if (!empty($path)) {
        $categoryPath = implode('/', $path) . '/';
    }
}

// Полный путь к каталогу, где будет лежать файл
$fullPath = $typePath . $categoryPath;

// ---------------------------------------------------------------------
// 3. Создаём директорию при необходимости
// ---------------------------------------------------------------------

if (!file_exists($fullPath)) {
    if (!mkdir($fullPath, 0755, true) && !is_dir($fullPath)) {
        return;
    }
}

// ---------------------------------------------------------------------
// 4. Формируем имя файла и пишем содержимое
// ---------------------------------------------------------------------

$elementName = $element->get('name');
if (empty($elementName)) {
    return;
}

$fileName = $elementName . '.' . $config['extension'];
$filePath = $fullPath . $fileName;

// Берём содержимое элемента
$content = (string) $element->get('content');

// Для PHP-файлов добавляем открывающий тег при отсутствии
if ($config['extension'] === 'php' && strpos($content, '<?php') !== 0) {
    $content = "<?php\n" . $content;
}

// Пишем файл на диск
if (file_put_contents($filePath, $content) === false) {
    return;
}

// ---------------------------------------------------------------------
// 5. Привязываем элемент к статическому файлу через File System-источник
// ---------------------------------------------------------------------

// Абсолютный путь → относительный от корня сайта
$absolutePath      = $filePath;
$relativeForSource = ltrim(str_replace(MODX_BASE_PATH, '', $absolutePath), '/');

$mediaSourceId = 1;

// На всякий случай убеждаемся, что это File System
$fsSource = $modx->getObject('sources.modMediaSource', $mediaSourceId);
if (!$fsSource || $fsSource->get('class_key') !== 'sources.modFileMediaSource') {
    // Если указанный ID не FileSystem — пробуем найти любой FileSystem
    $fsSource = $modx->getObject('sources.modMediaSource', [
        'class_key' => 'sources.modFileMediaSource',
    ]);
    if ($fsSource) {
        $mediaSourceId = (int) $fsSource->get('id');
    }
}

// Отмечаем элемент как статический и прописываем путь
$element->set('static', 1);
$element->set('source', $mediaSourceId);
$element->set('static_file', $relativeForSource);

// На OnBefore*FormSave можно не вызывать save(),
// процессор всё равно сохранит объект, но лишний save() не критичен.
$element->save();
