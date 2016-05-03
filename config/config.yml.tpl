config:
    security.enabled: true
    security.jwt_secret_key: secret
    database.driver: pdo_pgsql
    database.dbname: common
    database.host: postgres
    database.user: postgres
    database.password: postgres
    log.dir: ''
    log.file: 'php://stdout'
    log.name: APP
    api.max_pagination_limit: 50
    api.default_pagination_limit: 20

