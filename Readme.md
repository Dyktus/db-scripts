# Database renaming

Because mysql doesn't have `RENAME DATABASE` query
you need to move each table from source database separately.

You can try to use `rename_database.php`
like this

`DATABASE_HOSTNAME=localhost DATABASE_USER=root DATABASE_PASSWORD="" php rename_database.php sourceDatabase finalDatabase`

* `sourceDatabase` - Name of the database which you want to rename
* `finalDatabase` - New name of the database.

*BE AWARE THAT IF `finalDatabase` EXISTS IT WILL BE REMOVED*

It simply querying database to show ALL TABLES and execute `RENAME TABLE` for each table.