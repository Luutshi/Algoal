<?php

namespace Mvc\Models;

use Config\Model;
use PDO;

class ApiModel extends Model
{
    public function eachLeagues()
    {
        $statement = $this->pdo->prepare('SELECT * FROM `league` ORDER BY `country` ASC, `name` ASC');
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function leagueTeams(int $leagueID, int $season)
    {
        $statement = $this->pdo->prepare('SELECT rank, team.name, team_id, points, goalsDiff, form, team.logo FROM `table` INNER JOIN team WHERE `league_id` = :league_id AND table.team_id = team.id AND season = :season ORDER BY team.name ASC');
        $statement->execute([
            'league_id' => $leagueID,
            'season' => $season
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function leagueTables(int $leagueID, string $homeAway)
    {
        $statement = $this->pdo->prepare('SELECT id, name, logo, played, win, draw, lose, goalsFor, goalsAgainst FROM `team` INNER JOIN `data` ON team.id = `team_id` WHERE `league_id` = :league_id AND `teamPosition` = :teamPosition');
        $statement->execute([
            'league_id' => $leagueID,
            'teamPosition' => $homeAway
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function teamData(int $leagueID, int $teamID, string $homeAway)
    {
        $statement = $this->pdo->prepare('SELECT id, rank, name, logo, played, goalsFor, goalsAgainst  FROM `team` INNER JOIN `table` ON table.team_id = id INNER JOIN `data` ON data.team_id = id WHERE table.league_id = :league_id AND id = :team_id AND data.teamPosition = :teamPosition');
        $statement->execute([
            'league_id' => $leagueID,
            'team_id' => $teamID,
            'teamPosition' => $homeAway
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function fixtureData(int $fixtureID)
    {
        $statement = $this->pdo->prepare('SELECT *  FROM `match` WHERE id = :fixtureID');
        $statement->execute([
            'fixtureID' => $fixtureID,
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

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