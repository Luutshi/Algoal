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
}