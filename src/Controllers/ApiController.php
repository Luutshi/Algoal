<?php

namespace Mvc\Controllers;

use Mvc\Models\ApiModel;

class ApiController
{
    private ApiModel $apiModel;

    public function __construct()
    {
        $this->apiModel = new ApiModel();
    }

    public function matches($date)
    {
        date_default_timezone_set('Europe/Paris');

        $matches = $this->apiModel->matchesFromDate($date);

        $data = [];

        foreach ($matches as $match) {
            $data[$match['country'].' - '.$match['name']][] = $match;
        }

        http_response_code(201);
        echo json_encode([
            'status' => 201,
            'data' => $data,
        ], true);
    }

    public function predict(int $fixtureID)
    {
        $match = $this->apiModel->fixtureData($fixtureID);
        $league = $this->apiModel->findLeagueByID($match['league_id']);
        $homeTable = $this->apiModel->leagueTables($match['league_id'], 'home');
        $stats['totalPlayed'] = $stats['homeGoalsFor'] = $stats['homeGoalsAgainst'] = 0;

        foreach ($homeTable as $team)
        {
            $stats['totalPlayed'] += $team['played'];
            $stats['homeGoalsFor'] += $team['goalsFor'];
            $stats['homeGoalsAgainst'] += $team['goalsAgainst'];
        }

        $stats['homeGoalsForAverage'] = $stats['homeGoalsFor'] / $stats['totalPlayed'];
        $stats['homeGoalsAgainstAverage'] = $stats['homeGoalsAgainst'] / $stats['totalPlayed'];

        $homeTeam = $this->apiModel->teamData($match['league_id'], $match['home_id'], 'home');
        $awayTeam = $this->apiModel->teamData($match['league_id'], $match['away_id'], 'away');

        $stats['homeGoalsPredict'] = (($homeTeam['goalsFor']/$homeTeam['played'])/($stats['homeGoalsForAverage']))*(($awayTeam['goalsAgainst']/$awayTeam['played'])/$stats['homeGoalsForAverage'])*$stats['homeGoalsForAverage'];
        $stats['awayGoalsPredict'] = (($awayTeam['goalsFor']/$awayTeam['played'])/($stats['homeGoalsAgainstAverage']))*(($homeTeam['goalsAgainst']/$homeTeam['played'])/$stats['homeGoalsAgainstAverage'])*$stats['homeGoalsAgainstAverage'];

        function factorielle($number)
        {
            if ($number === 0) {
                return 1;
            }
            return $number*factorielle($number-1);
        }

        $result['winner']['home'] = $result['winner']['draw'] = $result['winner']['away'] = $result['btts']['yes'] = $result['btts']['no'] = 0;

        for ($i = 0; $i <= 10; $i++) {
            $home = ($stats['homeGoalsPredict']**$i*2.71828**(-$stats['homeGoalsPredict']))/factorielle($i);
            for ($j = 0; $j <= 10; $j++) {
                $away = ($stats['awayGoalsPredict']**$j*2.71828**(-$stats['awayGoalsPredict']))/factorielle($j);

                // WINNER RESULT
                // Home
                if ($i > $j) {
                    $result['winner']['home'] += $home * $away;
                }
                // Draw
                elseif ($i === $j) {
                    $result['winner']['draw'] += $home * $away;
                }
                // Away
                else {
                    $result['winner']['away'] += $home * $away;
                }

                // BTTS RESULT
                // Yes
                if ($i > 0 && $j > 0) {
                    $result['btts']['yes'] += $home * $away;
                }
                // No
                else {
                    $result['btts']['no'] += $home * $away;
                }
            }
        }

        foreach($result as $key => $value) {
            $total[$key] = 0;
            foreach($result[$key] as $secondKey => $secondValue) {
                $result[$key][$secondKey] = round($secondValue * 100, 0);
                $total[$key] += $result[$key][$secondKey];
            }
        }

        if ($total['winner'] > 100) {
            $result['winner']['home'] -= 0.3;
            $result['winner']['draw'] -= 0.4;
            $result['winner']['away'] -= 0.3;
        } elseif ($total['winner'] < 100) {
            $result['winner']['home'] += 0.3;
            $result['winner']['draw'] += 0.4;
            $result['winner']['away'] += 0.3;
        }

        if ($total['btts'] > 100) {
            $result['btts']['yes'] -= 0.5;
            $result['btts']['no'] -= 0.5;
        } elseif ($total['btts'] < 100) {
            $result['btts']['yes'] += 0.5;
            $result['btts']['no'] += 0.5;
        }

        // Winner betting tips
        if (($result['winner']['home'] + $result['winner']['draw']) > 70) {
            $bettingTips['winner'] = $homeTeam['name'].' gagne ou match nul';
        } elseif (($result['winner']['away'] + $result['winner']['draw']) > 70) {
            $bettingTips['winner'] = $awayTeam['name'].' gagne ou match nul';
        } elseif ($result['winner']['home'] > 70) {
            $bettingTips['winner'] = $homeTeam['name'].' gagne ou match nul';
        } elseif ($result['winner']['away'] > 70) {
            $bettingTips['winner'] = $awayTeam['name'].' gagne ou match nul';
        } else {
            $bettingTips['winner'] = 'Pas de conseil';
        }

        // BTTS betting tips
        if ($result['btts']['yes'] > 70) {
            $bettingTips['btts'] = 'Les deux équipes marquent';
        } elseif ($result['btts']['no'] > 70) {
            $bettingTips['btts'] = 'Les deux équipes ne marquent pas';
        } else {
            $bettingTips['btts'] = 'Pas de conseil';
        }

        unset(
            $league['id'], $league['season'],
            $homeTeam['id'], $homeTeam['rank'], $homeTeam['played'], $homeTeam['goalsFor'], $homeTeam['goalsAgainst'],
            $awayTeam['id'], $awayTeam['rank'], $awayTeam['played'], $awayTeam['goalsFor'], $awayTeam['goalsAgainst']);


        $data['result'] = $result;
        $data['teams']['home'] = $homeTeam;
        $data['teams']['away'] = $awayTeam;
        $data['league'] = $league;
        $data['bettingTips'] = $bettingTips;

        echo(json_encode($data, true));
    }
}
