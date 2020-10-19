## Summary
This module provides a method for migrating the users and taxonomy terms from
an existing Drupal 8+ site to a fresh installation of a site with the same configuration (i.e., content types, taxonomy, etc.). This is
useful for contexts where you want to drop all node data but retain user information
in order to do a reimport of node data, or a migration of that data to a different
data structure.

### Notes
- It is the responsibility of the user to migrate all site configuration. The recommended way of achieving this is through configuration management.
- As a true installation + migration, artifacts such as database logs, State API data, or environment-specific configuration will not be reflected. Only that which is specified to be migrated will be migrated.

## Setup
1. Register a new database connection that you can use as the source. The following example shows a setup using lando, but the principle is the same for all local development:

In `.lando.yml`:

```yml
services:
  source_database:
    type: mysql
```

In `settings.php`:
```php
$databases['migrate']['default'] = [
  'database' => $lando_info['source_database']['creds']['database'],
  'username' => $lando_info['source_database']['creds']['user'],
  'password' => $lando_info['source_database']['creds']['password'],
  'host' => $lando_info['source_database']['internal_connection']['host'],
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
```

2. Import the source database 

```
lando db-import source.sql.gz --host=source_database
```

3. Install the site afresh using configuration IDs

```
lando drush si -y
lando drush config-get "system.site" uuid
lando drush config-set "system.site" uuid <value>
lando drush config-get "language.entity.en" uuid
lando drush config-set "language.entity.en" uuid <value>
lando drush cim -y
```

4. Run the migration

```
lando drush en corpus_migrate -y && lando drush mim
```

## References/adaptations
- https://drupal.org/project/migrate_drupal_d8
- https://thinktandem.io/blog/2020/03/12/migrating-a-drupal-8-multisite-to-a-standalone-drupal-8-site/
