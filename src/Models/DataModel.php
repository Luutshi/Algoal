<?php

namespace Mvc\Models;

use Config\Model;
use PDO;

class DataModel extends Model
{
    public function getLeagueDataByID(int $id, int $season)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api-football-v1.p.rapidapi.com/v3/standings?season=$season&league=$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: api-football-v1.p.rapidapi.com",
                "X-RapidAPI-Key: 12cde18a4cmsh2616eecb288b74ep17b1a5jsnc458b60c52fa"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function insertLeague(array $league)
    {
        $statement = $this->pdo->prepare('INSERT INTO `league`(`id`, `name`, `country`, `logo`, `flag`, `season`) VALUES (:id, :name, :country, :logo, :flag, :season)');
        $statement->execute([
            'id' => $league['id'],
            'name' => $league['name'],
            'country' => $league['country'],
            'logo' => $league['logo'],
            'flag' => $league['flag'],
            'season' => $league['season']
        ]);
    }

    public function updateLeague(array $league)
    {
        $statement = $this->pdo->prepare('UPDATE `league` SET `name` = :name, `country` = :country, `logo` = :logo, `flag` = :flag, `season` = :season WHERE `id` = :id');
        $statement->execute([
            'id' => $league['id'],
            'name' => $league['name'],
            'country' => $league['country'],
            'logo' => $league['logo'],
            'flag' => $league['flag'],
            'season' => $league['season']
        ]);
    }

    public function findLeagueByID(array $league)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `league` WHERE `id` = :id');
        $statement->execute([
            'id' => $league['id']
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function insertTeam(array $team)
    {
        $statement = $this->pdo->prepare('INSERT INTO `team` (`id`, `name`, `logo`) VALUES (:id, :name, :logo)');
        $statement->execute([
            'id' => $team['team']['id'],
            'name' => $team['team']['name'],
            'logo' => $team['team']['logo'],
        ]);
    }

    public function updateTeam(array $team)
    {
        $statement = $this->pdo->prepare('UPDATE `team` SET `name` = :name, `logo` = :logo WHERE `id` = :id');
        $statement->execute([
            'id' => $team['team']['id'],
            'name' => $team['team']['name'],
            'logo' => $team['team']['logo'],
        ]);
    }

    public function findTeamByID($teamID)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `team` WHERE `id` = :id');
        $statement->execute([
            'id' => $teamID
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function insertTeamRank(array $team, array $league)
    {
        $statement = $this->pdo->prepare('INSERT INTO `table` (`rank`, `team_id`, `points`, `goalsDiff`, `league_id`, `form`, `season`) VALUES (:rank, :team_id, :points, :goalsDiff, :league_id, :form, :season)');
        $statement->execute([
            'rank' => $team['rank'],
            'team_id' => $team['team']['id'],
            'points' => $team['points'],
            'goalsDiff' => $team['goalsDiff'],
            'league_id' => $league['id'],
            'form' => $team['form'],
            'season' => $league['season']
        ]);
    }

    public function updateTeamRank(array $team, array $league)
    {
        $statement = $this->pdo->prepare('UPDATE `table` SET `rank` = :rank, `points` = :points, `goalsDiff` = :goalsDiff, `league_id` = :league_id, `form` = :form, `season` = :season WHERE `team_id` = :team_id');
        $statement->execute([
            'team_id' => $team['team']['id'],
            'rank' => $team['rank'],
            'points' => $team['points'],
            'goalsDiff' => $team['goalsDiff'],
            'league_id' => $league['id'],
            'form' => $team['form'],
            'season' => $league['season']
        ]);
    }

    public function findTeamRankByTeamID(array $team, array $league)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `table` WHERE `team_id` = :team_id AND `season` = :season');
        $statement->execute([
            'team_id' => $team['team']['id'],
            'season' => $league['season']
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function insertTeamData(array $team, string $teamPosition, array $league)
    {
        $statement = $this->pdo->prepare('INSERT INTO `data` (`league_id`, `team_id`, `played`, `win`, `draw`, `lose`, `goalsFor`, `goalsAgainst`, `teamPosition`, `season`) VALUES (:league_id, :team_id, :played, :win, :draw, :lose, :goalsFor, :goalsAgainst, :teamPosition, :season)');
        $statement->execute([
            'league_id' => $league['id'],
            'team_id' => $team['team']['id'],
            'played' => $team[$teamPosition]['played'],
            'win' => $team[$teamPosition]['win'],
            'draw' => $team[$teamPosition]['draw'],
            'lose' => $team[$teamPosition]['lose'],
            'goalsFor' => $team[$teamPosition]['goals']['for'],
            'goalsAgainst' => $team[$teamPosition]['goals']['against'],
            'teamPosition' => $teamPosition,
            'season' => $league['season']
        ]);
    }

    public function updateTeamData(array $team, string $teamPosition, array $league)
    {
        $statement = $this->pdo->prepare('UPDATE `data` SET `played` = :played,`win` = :win,`draw` = :draw,`lose` = :lose,`goalsFor` = :goalsFor,`goalsAgainst` = :goalsAgainst WHERE league_id = :league_id AND team_id = :team_id AND teamPosition = :teamPosition AND season = :season');
        $statement->execute([
            'league_id' => $league['id'],
            'team_id' => $team['team']['id'],
            'played' => $team[$teamPosition]['played'],
            'win' => $team[$teamPosition]['win'],
            'draw' => $team[$teamPosition]['draw'],
            'lose' => $team[$teamPosition]['lose'],
            'goalsFor' => $team[$teamPosition]['goals']['for'],
            'goalsAgainst' => $team[$teamPosition]['goals']['against'],
            'teamPosition' => $teamPosition,
            'season' => $league['season']
        ]);
    }

    public function findTeamDataByID(array $team, string $teamPosition, array $league)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `data` WHERE `team_id` = :team_id AND `teamPosition` = :teamPosition AND `season` = :season');
        $statement->execute([
            'team_id' => $team['team']['id'],
            'teamPosition' => $teamPosition,
            'season' => $league['season']
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findMatch($leagueID, $teams, $date)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `match` WHERE `league_id` = :league_id AND `home_id` = :home_id AND `away_id` = :away_id AND `date` = :date');
        $statement->execute([
            'league_id' => $leagueID,
            'home_id' => $teams['home']['id'],
            'away_id' => $teams['away']['id'],
            'date' => $date
        ]);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function insertMatch($id, $leagueID, $teams, $date, $season)
    {
        $statement = $this->pdo->prepare('INSERT INTO `match` (`id`, `league_id`, `home_id`, `away_id`, `date`, `season`) VALUES (:id, :league_id, :home_id, :away_id, :date, :season)');
        $statement->execute([
            'id' => $id,
            'league_id' => $leagueID,
            'home_id' => $teams['home']['id'],
            'away_id' => $teams['away']['id'],
            'date' => $date,
            'season' => $season
        ]);
    }

    public function leaguesFromDate($date)
    {
        $statement = $this->pdo->prepare('SELECT DISTINCT `league_id`, name, country, logo, flag FROM `match` INNER JOIN `league` ON league_id = league.id WHERE DATE(`date`) = :date ORDER BY country, league.name');
        $statement->execute([
            'date' => $date
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function matchesFromDate($date)
    {
        $statement = $this->pdo->prepare('SELECT match.id, match.date, league.name, league.country, league.flag, home_team.name AS home_team_name, home_team.logo AS home_team_logo , away_team.name AS away_team_name, away_team.logo AS away_team_logo FROM `match`
                                                LEFT JOIN league ON match.league_id = league.id
                                                LEFT JOIN team home_team ON match.home_id = home_team.id
                                                LEFT JOIN team away_team ON match.away_id = away_team.id
                                                WHERE Date(match.date) = :date
                                                ORDER BY league.country, league.name');
        $statement->execute([
            'date' => $date
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}