<?php
// Подключаем конфиг и MODX
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);

// Если класс не найден в глобальной области, используем полный путь MODX 3
if (!class_exists('modPackageBuilder')) {
    /** @var \MODX\Revolution\Transport\modPackageBuilder $builder */
    $builder = new \MODX\Revolution\Transport\modPackageBuilder($modx);
} else {
    $builder = new modPackageBuilder($modx);
}

// Имя пакета
$builder->createPackage('StaticFilesPlus', '1.0.0', 'pl');
$builder->registerNamespace('staticfilesplus', false, true, '{core_path}components/staticfilesplus/');

// 1. Создаем объект Плагина
$plugin = $modx->newObject('modPlugin');
$plugin->set('id', 1);
$plugin->set('name', 'StaticFilesPlus');
$plugin->set('description', 'Automatically creates static files for Elements organized by categories.');
$plugin->set('plugincode', file_get_contents(dirname(dirname(__FILE__)) . '/core/components/staticfilesplus/elements/plugins/staticfilesplus.php'));
$plugin->set('category', 0);

// ВАЖНО: Мы НЕ добавляем $plugin->addMany($events) здесь, 
// так как события будут добавлены резолвером динамически!

// 2. Создаем Vehicle для Плагина
$attr = [
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => false, // false, так как события мы не тянем
];
$vehicle = $builder->createVehicle($plugin, $attr);

// 3. Прикрепляем файлы (код плагина, лицензии)
$vehicle->resolve('file', [
    'source' => dirname(dirname(__FILE__)) . '/core/components/staticfilesplus/',
    'target' => "return MODX_CORE_PATH . 'components/';",
]);

// 4. Прикрепляем наш PHP Резолвер (который обработает чекбоксы)
$vehicle->resolve('php', [
    'source' => dirname(__FILE__) . '/resolvers/resolve.setup.php',
]);

$builder->putVehicle($vehicle);

// 5. Добавляем Setup Options (форма с чекбоксами)
$builder->setPackageAttributes([
    'license' => file_get_contents(dirname(dirname(__FILE__)) . '/core/components/staticfilesplus/docs/license.txt'),
    'readme' => file_get_contents(dirname(dirname(__FILE__)) . '/core/components/staticfilesplus/docs/readme.txt'),
    'changelog' => file_get_contents(dirname(dirname(__FILE__)) . '/core/components/staticfilesplus/docs/changelog.txt'),
    'setup-options' => [
        'source' => dirname(__FILE__) . '/setup.options.php',
    ],
]);

$builder->pack();
exit();
