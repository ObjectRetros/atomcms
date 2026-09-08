# Database fixtures

The current Pest suites use MariaDB and separate disposable databases for Arcturus (`testing`) and Ada (`testing_ada`). `RefreshDatabase` drops and recreates their tables, so test credentials must be restricted to those databases. See [contribution checks](../../docs/wiki/0.-Contribution-guidelines.md#checks), `phpunit.xml`, `tests/TestCase.php` and `tests/AdaTestCase.php`.

The active emulator's test-only migration loads its base schema before the normal CMS migrations:

| Emulator | Fixture | Loader |
| --- | --- | --- |
| Arcturus | `database/migrations/sqls/default.sql` | `app/Emulator/Drivers/Arcturus/Migrations/2014_01_01_000000_core_sql_file.php` |
| Ada | `database/ada/schema.sql` | `app/Emulator/Drivers/Ada/Migrations/2013_01_01_000000_ada_schema_file.php` |

Keep fixture changes aligned with the emulator schema and run both emulator suites after shared database changes. The loaders only import their fixtures in the testing environment; production installation uses the emulator's own schema and `atom:install`.

`testing-schema.sql` in this directory is a legacy SQLite conversion retained for reference. The current test suite does not load it. It was generated from the [Arcturus base database at revision 23ae66bc](https://github.com/ObjectRetros/arcturus-ms-3-5-base-db/blob/23ae66bc2b67a3504d6e3c2e01a4e54f48b7daa4/arcturus-3-5-base-db.sql) using [mysql2sqlite](https://github.com/dumblob/mysql2sqlite); it is not the current schema source or evidence of SQLite support for the hotel.
