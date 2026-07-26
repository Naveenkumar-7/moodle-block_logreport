# Log Report
------------

This plugin available as MOODLE block. 

# Description
-------------
This plugin will make available exisitng log report with easy and quick navigation, without refreshing the page including filters and pagination.

- Hourly, Daily and Monthly hits count of the application.
- Ajax based pagination and filters.
- Quick selection of the filters from dropdown using select2.

# Compatable MOODLE Versions
----------------------------
This plugin compatible with below MOODLE versions.
- v4.3
- v4.4
- v4.5
- v5.0
- v5.1
- v5.2

# Changelog
-----------
- Hit charts (hourly/daily/monthly) now use one DB-agnostic query bucketed in PHP instead of separate MySQL/Postgres SQL, so they no longer break on MariaDB, MSSQL, Oracle or SQLite.
- Fixed an undefined-variable bug in `query_db()` that skipped pagination sizing and the initials bar, and removed a duplicate log query.
- Chart tabs now use Moodle's native Bootstrap tabs (`nav-tabs` + `data-bs-toggle`) instead of jQuery UI `.tabs()`; the jQuery UI CSS dependency was dropped.
- Report table and filter dropdowns restyled to match core's `.generaltable`/`.form-select` look; the bundled DataTables stylesheet is no longer loaded.
- Pagination links now use Moodle's own pagination classes and get a pointer cursor.
- `.travis.yml` updated for current PHP (8.3/8.4) and supported MOODLE_BRANCH values, and moved to the current `moodlehq/moodle-plugin-ci` package.

# Credits
[Datatable](https://datatables.net/)
[Select2](https://select2.org/)
[Claude Code](https://claude.com/claude-code)

**Thank you**
