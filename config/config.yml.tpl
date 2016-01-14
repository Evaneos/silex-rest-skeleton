config:
  debug: true
  security:
    active: true
    jwt_secret_key: secret
  database:
    driver:   pdo_pgsql
    dbname:   common
    host:     postgres
    user:     postgres
    password: postgres
  log:
    file: php://stdout
    name: APP
  api:
    cache_dir:                cache/serializer/
    max_pagination_limit:     50
    default_pagination_limit: 20