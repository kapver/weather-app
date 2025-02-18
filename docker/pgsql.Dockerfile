FROM postgres:17

RUN apt-get update && apt-get install -y \
    postgis \
    postgresql-17-postgis-3 \
    && rm -rf /var/lib/apt/lists/*

RUN echo "CREATE EXTENSION IF NOT EXISTS postgis;" > /docker-entrypoint-initdb.d/init-postgis.sql