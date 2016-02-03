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
    if (getWeekdayDifference($issue->getUpdated(), $now) >= 2) {
        echo "URGENTE: esta tarefa precisa ser quebrada e entregue hoje. Nenhuma tarefa pode ficar mais de 2 dias parada", "\n";
    }
    echo "\n\n";
}

$client = new \Github\Client(
    new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
);
$githubUser = getenv('GITHUB_USER');
$githubPassword = getenv('GITHUB_PASSWORD');
$githubRepo = getenv('GITHUB_REPO');
$client->authenticate($githubUser, $githubPassword, \Github\Client::AUTH_HTTP_PASSWORD);
$activity = $client->api('repo')->statistics($githubUser, $githubRepo);
echo "Commits \n\n";

foreach ($activity as $a) {
    if ($a['weeks'][71]['c'] > 0) {
        echo $a['author']['login'], " Total de commits: ", $a['total'], '. Commits na última semana (começando em '. DateTime::createFromFormat('U', $a['weeks'][71]['w'])->format('d/m/Y').' ):' .  $a['weeks'][71]['c'], "\n";
    }
}
echo "\n\nOBS: se o nome de alguém não aparece acima não significa que ela não está trabalhando e sim que está commitando em branches diferentes da master pois o Github só entrega estatísticas desta branch. A ausência de alguém na lista acima indica que a sua tarefa está demorando mais de dois dias para terminar e ser aprovada no master o que reforça a necessidade de quebrar a tarefa em pedaços menores.\n";

function getWeekdayDifference(\DateTime $startDate, \DateTime $endDate)
{
    $days = 0;

    while($startDate->diff($endDate)->days > 0) {
        $days += $startDate->format('N') < 6 ? 1 : 0;
        $startDate = $startDate->add(new \DateInterval("P1D"));
    }

    return $days;
}
