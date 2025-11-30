<?php
/**
 * @var $object  // Объект плагина (так как резолвер привязан к нему)
 * @var $options // Массив опций из setup.options.php
 */

if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            
            $modx->log(modX::LOG_LEVEL_INFO, 'Настройка событий плагина...');

            // 1. Удаляем старые привязки (чтобы не дублировать при обновлении)
            // $object - это сам объект modPlugin
            $pluginId = $object->get('id');
            $modx->removeCollection('modPluginEvent', ['pluginid' => $pluginId]);

            // 2. Получаем выбранные пользователем события
            if (!empty($options['events'])) {
                foreach ($options['events'] as $eventName) {
                    $event = $modx->newObject('modPluginEvent');
                    $event->set('event', $eventName);
                    $event->set('pluginid', $pluginId);
                    $event->set('priority', 0);
                    $event->set('propertyset', 0);
                    $event->save();
                    
                    $modx->log(modX::LOG_LEVEL_INFO, "Подключено событие: {$eventName}");
                }
            }
            break;
    }
}
return true;
