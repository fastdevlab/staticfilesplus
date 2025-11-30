StaticFilesPlus
------------------------------------------
Version: 1.0.0-pl
Author:  FastDevLab <mail@fastdevlab.com>
License: MIT
Repo:    https://github.com/fastdevlab/staticfilesplus

DESCRIPTION / ОПИСАНИЕ
------------------------------------------
EN: StaticFilesPlus is a MODX 3 plugin that automatically creates static 
files for Elements (Chunks, Snippets, Templates, Plugins) organized by 
categories with transliteration support for Russian category names.

RU: StaticFilesPlus — это плагин для MODX 3, который автоматически 
создаёт статические файлы для элементов (чанки, сниппеты, шаблоны, 
плагины), организованные по категориям с поддержкой транслитерации 
русских названий категорий.

FEATURES / ВОЗМОЖНОСТИ
------------------------------------------
EN:
- Automatic file creation on element save
- Mirrors your Category structure as folders
- Transliterates Russian category names (e.g. "Новости" → "novosti")
- Respects 'pdotools_elements_path' system setting
- Allows selecting which Element types to track during installation
- Uses MODX File System media source

RU:
- Автоматическое создание файлов при сохранении элемента
- Воспроизводит структуру категорий в виде папок
- Транслитерирует русские названия (например, "Новости" → "novosti")
- Использует системную настройку 'pdotools_elements_path'
- Позволяет выбрать типы элементов при установке
- Использует медиа-источник File System

REQUIREMENTS / ТРЕБОВАНИЯ
------------------------------------------
- MODX Revolution 3.0+
- PHP 8.0+

INSTALLATION / УСТАНОВКА
------------------------------------------
EN:
1. Install via Package Management
2. Select which events (Elements) you want to track in setup options
3. (Optional) Configure 'pdotools_elements_path' system setting

RU:
1. Установите через Менеджер пакетов
2. Выберите события (типы элементов) в окне настройки установки
3. (Опционально) Настройте системную настройку 'pdotools_elements_path'

USAGE / ИСПОЛЬЗОВАНИЕ
------------------------------------------
EN: Just create or save any Chunk, Snippet, Template, or Plugin in the 
MODX Manager. A corresponding file will be created automatically in your 
/core/elements/ directory with proper category structure.

RU: Просто создайте или сохраните любой чанк, сниппет, шаблон или плагин 
в админке MODX. Соответствующий файл будет автоматически создан в папке 
/core/elements/ с правильной структурой категорий.

EXAMPLE STRUCTURE / ПРИМЕР СТРУКТУРЫ
------------------------------------------
core/elements/
├── chunks/
│   ├── header/
│   │   └── mainMenu.tpl
│   └── dizayn_sayta/
│       └── colors.tpl
├── snippets/
│   └── helpers/
│       └── getPrice.php

SUPPORT / ПОДДЕРЖКА
------------------------------------------
GitHub Issues: https://github.com/fastdevlab/staticfilesplus/issues
MODX Forum: https://community.modx.com/
Русскоязычное сообщество MODX: https://modx.pro/
