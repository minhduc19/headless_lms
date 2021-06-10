# necampus

### Yêu cầu

- PHP 7.1
- MariaDB/Mysql >= 5.6

### Cài đặt
- Import database from `src/app/Modal/Schema/necampus.sql`
- Config webserver
    + webroot là `src/app/webroot`
    + Index: index.php

```
bash ci_cd/initial_setup.sh
```

## Deployment

Set các biến môi trường variables.example.rb
