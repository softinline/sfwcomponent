# SfwComponent - Skill / Reference

## Overview
SfwComponent is a JSON-driven UI/rendering layer used to build backoffice/panel screens (index lists, forms, detail views) in Laravel apps.

## Key concepts
- **Defines (JSON):** screen definitions live in `app/Defines/.../*.json` (project-side) or package defines. A screen typically has `type=screen`, `layout`, `url`, and `components`.
- **SfwConfig:** loads and queries JSON definitions (`load`, `getConfig`, `getParam`, `getById`, etc.).
- **SfwComponent:** renders component trees and handles form `submit`, `delete`, etc.
- **Components:** `div`, `card`-like structures via div classes, `function` (calls controller static method), `datatable`, `form`, `tabs/tab`, inputs (`text`, `textarea`, `number`, `email`, `password`, `select`, `checkbox`, `date`, etc.), `button`.

## Translation system
- By default components translate labels/titles using `trans($translationFile.$title)` with `$translationFile = 'messages.'` (i.e. keys from `resources/lang/{locale}/messages.php` or JSON translations), unless overridden by `config.translationFile` in the screen.
- **Field labels:** `component.title` is translated as `messages.{title}`.
- **Button labels:** `component.title` is translated the same way (e.g. `accept`, `cancel`, `back`, `add`).
- **Help/placeholders/messages:** follow the same `translationFile` prefix where applicable.
- When adding new screens/buttons/fields, ensure the corresponding keys exist in the app's language files (`resources/lang/es/messages.php`, `en/messages.php`, etc.) or use existing keys.

## Common patterns
- **Index screen:** breadcrumb (`type:function`, controller static `breadcrumbIndex`) + card + datatable + action buttons (`link:` actions).
- **Add/Edit forms:** breadcrumb (`breadcrumbAdd`/`breadcrumbEdit`) + `form` with id/action + tabs/fields + submit/cancel buttons (`js:sfwcomponent.submit('formId')`, `link:...`).
- **Read-only detail:** set item on component and use inputs with `"disabled": true` (and `textarea` with `rows` as needed).
- **Delete (datatable actions):** use `sfwcomponent-delete` with `sfwcomponent-data-id`, `sfwcomponent-data-url`, `sfwcomponent-data-datatable` attributes; datatable `url` must match the screen/base endpoint for ajax operations.
- **URLs in actions/forms:** `{param}` placeholders (e.g. `{id}`, `{userId}`) can be replaced from route params via `SfwUtils::replaceUrlParams`.

## Layout notes (Bootstrap 5 apps)
- Prefer `container py-4`, cards for content, and `d-flex justify-content-end gap-2` for action button groups.
- Form controls should have vertical spacing (e.g. `mb-3`) to separate rows clearly. If a theme removed `.form-group` margins (Bootstrap 5), ensure components include margin utilities/classes.