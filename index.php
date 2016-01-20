<?php
use JiraClient\JiraClient;

require_once 'vendor/autoload.php';

$user = getenv('JIRA_USER');
$password = getenv('JIRA_PASSWORD');

$api = new JiraClient('https://coderockr.atlassian.net', $user, $password);

$search = $api->issue()->search('project = Compufour AND affectedVersion is EMPTY AND fixVersion is EMPTY AND type in (Bug, Improvement, "New Feature", Sub-task) AND (labels not in (Roadmap) OR labels is EMPTY) AND status in ("In Progress", Review, Testing) ORDER BY priority ASC');
$now = new Datetime;
echo "Resumo do dia ", $now->format('d/m/Y H:i:s'), "\n\n\n";

echo $search->getTotal(), " tarefas em aberto", "\n\n";

foreach ($search->getList() as $key => $issue) {
    echo 'Código: ', $issue->getKey(), "\n";
    echo 'Descrição: ', $issue->getSummary(), "\n";
    echo 'Tipo: ', $issue->getIssueType()->getName(), "\n";
    echo 'Status:',  $issue->getStatus()->getName(), "\n";
    echo 'Responsável: ', $issue->getAssignee()->getDisplayName(), "\n";
    echo 'Prioridade:', $issue->getPriority()->getName(), "\n";
    echo 'Criado em :' , $issue->getCreated()->format('d/m/Y H:i:s'), "\n";
    echo 'Última atualização em :' , $issue->getUpdated()->format('d/m/Y H:i:s'), "\n";
    $interval = $now->diff($issue->getUpdated());
    if ((int) $interval->format("%a") >= 2) {
        echo "URGENTE: esta tarefa precisa ser quebrada e entregue hoje. Nenhuma tarefa pode ficar mais de 2 dias parada", "\n";
    }
    echo "\n\n";
}