#!/bin/bash
# Instalação INICIAL — roda uma vez só, na primeira vez que o projeto vai
# pro ar. Depois disso, use deploy/deploy.sh para atualizações.
#
# Uso: bash deploy/install.sh
# (rode este script já DENTRO da pasta que você acabou de clonar do git)

set -e

if [ ! -f .env ]; then
    echo "==> Criando .env a partir do exemplo..."
    cp deploy/.env.production.example .env
    echo "    ATENÇÃO: edite o .env agora com as credenciais do banco,"
    echo "    Mercado Pago e Instagram antes de continuar."
    read -p "    Pressione ENTER quando tiver terminado de editar o .env..."
fi

echo "==> Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader

echo "==> Gerando APP_KEY..."
php artisan key:generate

echo "==> Ajustando permissões..."
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Rodando migrations..."
php artisan migrate --force

echo "==> Criando usuário admin padrão..."
php artisan db:seed --class=AdminSeeder

echo "==> Criando link de storage público..."
php artisan storage:link

echo "==> Gerando caches de produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "==> Instalação concluída!"
echo "    Login admin: admin@sorteiosaas.com / troque-esta-senha"
echo "    TROQUE ESSA SENHA AGORA."
