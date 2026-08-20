#!/bin/bash
# Deploy de ATUALIZAÇÃO — roda dentro da pasta do projeto já clonada no servidor.
# Uso: bash deploy/deploy.sh

set -e

echo "==> Colocando o site em modo de manutenção..."
php artisan down || true

echo "==> Baixando última versão do git..."
git pull origin main

echo "==> Instalando dependências PHP (produção)..."
composer install --no-dev --optimize-autoloader

# Só precisa disso se você alterar o visual (CSS/JS) — os assets já vêm
# commitados no repositório, então normalmente pode pular estas duas linhas.
if [ "$1" == "--assets" ]; then
    echo "==> Instalando e buildando assets front-end..."
    npm ci
    npm run build
fi

echo "==> Ajustando permissões..."
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Rodando migrations..."
php artisan migrate --force

echo "==> Limpando e recriando caches de produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Reiniciando fila (se estiver rodando via Supervisor)..."
php artisan queue:restart

echo "==> Tirando o site do modo de manutenção..."
php artisan up

echo "==> Deploy concluído."
