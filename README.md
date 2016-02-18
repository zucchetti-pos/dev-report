# jira-report
Script para enviar relatórios das tarefas do Jira

É preciso exportar as variáveis com as credenciais

    export JIRA_USER=user
    export JIRA_PASSWORD=password
    export GITHUB_USER=user
    export GITHUB_PASSWORD=password
    export GITHUB_REPO=repo

E executar
    
    php index.php

Para enviar por e-mail basta executar

    php index.php | mail -s "Report `date`" email@domain.com