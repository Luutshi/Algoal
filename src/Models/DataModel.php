<?php

namespace Mvc\Models;

use Config\Model;
use PDO;

class DataModel extends Model
{
    public function findLeagueByID(int $leagueID)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `league` WHERE `id` = :id');
        $statement->execute([
            'id' => $leagueID
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function matchesFromDate($date)
    {
        $statement = $this->pdo->prepare('SELECT match.id, match.date, league.name, league.country, league.flag, home_team.name AS home_team_name, home_team.logo AS home_team_logo , away_team.name AS away_team_name, away_team.logo AS away_team_logo FROM `match`
                                                LEFT JOIN league ON match.league_id = league.id
                                                LEFT JOIN team home_team ON match.home_id = home_team.id
                                                LEFT JOIN team away_team ON match.away_id = away_team.id
                                                WHERE Date(match.date) = :date
                                                ORDER BY league.country, league.name, match.date, home_team_name');
        $statement->execute([
            'date' => $date
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}