# ZIP-выгрузка вложений комментариев

Этот модуль из коммерческого Laravel-проекта собирает вложения ветки комментариев в ZIP. Участник проекта может запросить весь набор файлов или выбрать только медиа либо остальные вложения. API возвращает готовый архив или сообщает, что сборка ещё идёт.

Стек включает PHP 8.2, Laravel 9.52.20, MySQL 8.0, JWT, вложения Orchid и очередь Laravel на базе MySQL. HTTP-приложение и воркер используют общий локальный диск. В исходном проекте настроен также Redis. Выделенный сценарий и оригинальный тест продолжения сборки используют database queue.

## Локальный запуск

Откройте корень репозитория в PowerShell. Для запуска нужны Docker и Docker Compose. В Bash замените первую команду на `cp .env.example .env`.

```powershell
Copy-Item .env.example .env
docker compose up -d mysql
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan jwt:secret --force
docker compose run --rm app php artisan migrate --seed
docker compose run --rm app php artisan storage:link
docker compose up -d app worker
docker compose exec app php artisan portfolio:seed-archive
```

Последняя команда создаёт синтетический комментарий с вложением и выводит его ID и JWT участника проекта. Подставьте их в запрос.

```powershell
curl.exe -H "Accept: application/json" -H "Authorization: Bearer <token>" "http://127.0.0.1:8000/api/v1/comment/<id>/file/zip?only=files"
```

## API

| Метод и путь | Поведение |
|---|---|
| `GET /api/v1/comment/{id}/file/zip` | Требует JWT и право просмотра комментария. Без параметра `only` собирает все вложения. Значение `media` выбирает поддерживаемые изображения и видео, `files` — остальные файлы. |

При `filling_status=filled` ответ содержит URL, имя, расширение и размер архива. При `filling_status=filling` повторите тот же запрос, пока воркер продолжает сборку. HTTP 401 означает отсутствие аутентификации, 403 — отказ в доступе, 422 — отсутствие файлов для выбранного фильтра.

Команда `php artisan comments:delete-old-attachment-archives` очищает архивы, которые в последний раз выдавались более недели назад. Она включена в планировщик на 02:00 и доступна для ручного запуска. Демо рассчитано на localhost и синтетические данные. Ограничения авторизации, публичных URL и восстановления после сбоя описаны в [ревью кода](docs/code-review.md).

## Тесты

```powershell
docker compose run --rm -e DB_DATABASE=komm_portfolio_test app php vendor/bin/phpunit
```

При первом создании тома MySQL Compose подготавливает отдельную тестовую схему. Исходные тесты используют `RefreshDatabase`. Фактические результаты приведены в [отчёте о проверке](docs/verification.md).

## Что посмотреть в коде

1. Сервис вычисляет хеш фильтра и контрольную сумму набора вложений, переиспользует подходящий ZIP и запускает первый этап сборки в HTTP-запросе. [AttachmentArchiveService](app/Services/Comments/AttachmentArchiveService.php)
2. Job сохраняет ID ожидающих вложений, выбирает их порциями и ставит продолжение в очередь, когда исчерпан бюджет времени. [FillCommentAttachmentArchiveJob](app/Jobs/FillCommentAttachmentArchiveJob.php)
3. Модель обходит дерево комментариев и фильтрует вложения по MIME-типу. Комментарии и архивы используют один полиморфный тип связи. [Comment](app/Models/Comment.php) · [Attachable](app/Traits/Attachable.php)
4. Политика проверяет членство в проекте и право просмотра приватного комментария. Form Request проверяет доступ и значение фильтра. [CommentPolicy](app/Policies/CommentPolicy.php) · [GetFilesZipCommentRequest](app/Http/Requests/GetFilesZipCommentRequest.php)
5. Исходный тест запускает продолжение сборки через очередь MySQL и проверяет имена обоих файлов в ZIP. [FillCommentAttachmentArchiveJobTest](tests/Feature/Jobs/FillCommentAttachmentArchiveJobTest.php)

[Анализ проекта](docs/project-analysis.md) · [Отчёт о выделении](docs/extraction-report.md) · [Ревью кода](docs/code-review.md)
