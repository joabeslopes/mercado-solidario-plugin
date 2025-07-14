#!/bin/bash
# Script para facilitar troca das urls do wordpress no banco de dados
# Exemplo: ./mariadb_url_change.sh http://oldurl http://newurl
# Necessario criar o arquivo ~/.my.cnf com conteudo similar ao abaixo entre as aspas
: '
[client]
host=localhost
user=seu_usuario
password=sua_senha
database=seu_database

[mysql]
pager=/usr/bin/less
'

old_url=$1
new_url=$2

command="
    UPDATE wp_options SET option_value = replace(option_value, '$old_url', '$new_url') WHERE option_name = 'home' OR option_name = 'siteurl';

    UPDATE wp_posts SET guid = replace(guid, '$old_url', '$new_url');

    UPDATE wp_posts SET post_content = replace(post_content, '$old_url', '$new_url');

    UPDATE wp_postmeta SET meta_value = replace(meta_value, '$old_url', '$new_url');
"

mariadb -e "$command"