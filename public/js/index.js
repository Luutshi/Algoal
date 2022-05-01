window.addEventListener('DOMContentLoaded', () => {
    let date = new Date();
    let dateSelector = document.querySelector('#matchesDate');
    document.querySelector('#matchesDate').value = date.getFullYear().toString() + '-' + (date.getMonth() + 1).toString().padStart(2, 0) + '-' + date.getDate().toString().padStart(2, 0);

    let leaguesDiv = document.querySelector('div.leagues');

    function eachMatches(date) {
        let eachLeagues = document.querySelectorAll('div.leagues > div')

        if (eachLeagues) {
            eachLeagues.forEach((league) => {
                league.remove();
            })
        }


        if (date) {
            fetch(`/matches/${date}`)
            .then((response) => response.json())
            .then((res) => {
                let leagues = res.data;

                for (const key in leagues) {
                    let newLeague = document.createElement('div');

                    // League Header
                    let leagueHeader = document.createElement('div');
                    leagueHeader.className = "league-header"

                    let countryFlag = document.createElement('img');
                    countryFlag.className = "flag"
                    countryFlag.src = leagues[key][0]['flag'];

                    let leagueName = document.createElement('p');
                    let leagueNameContent = document.createTextNode(`${leagues[key][0]['country']} - ${leagues[key][0]['name']}`);
                    leagueName.appendChild(leagueNameContent);

                    leagueHeader.appendChild(countryFlag);
                    leagueHeader.appendChild(leagueName);
                    newLeague.append(leagueHeader)


                    // League Matches
                    let leagueMatches = document.createElement('div');
                    leagueMatches.className = "league-matches"

                    leagues[key].forEach((match) => {
                        let newMatch = document.createElement('div');

                        // Home
                        let home = document.createElement('div');
                        home.className = "home";

                        let homeName = document.createElement('p');
                        let homeNameContent = document.createTextNode(match['home_team_name']);
                        homeName.appendChild(homeNameContent);

                        let homeLogo = document.createElement('img');
                        homeLogo.src = match['home_team_logo'];

                        home.appendChild(homeName);
                        home.appendChild(homeLogo);
                        newMatch.appendChild(home);

                        // Hour
                        let hour = document.createElement('p');
                        let hourRegex = new RegExp(/\d{2}:\d{2}:\d{2}/);
                        let hourContent = document.createTextNode(`${match['date'].match(hourRegex)}`);
                        hour.appendChild(hourContent);
                        newMatch.appendChild(hour);

                        // Away
                        let away = document.createElement('div');
                        away.className = "away";

                        let awayName = document.createElement('p');
                        let awayNameContent = document.createTextNode(match['away_team_name']);
                        awayName.appendChild(awayNameContent);

                        let awayLogo = document.createElement('img');
                        awayLogo.src = match['away_team_logo'];

                        away.appendChild(awayLogo);
                        away.appendChild(awayName);
                        newMatch.appendChild(away);

                        newMatch.addEventListener('click', () => window.location.href=`./predict/${match['id']}`)

                        leagueMatches.appendChild(newMatch);
                    })

                    newLeague.appendChild(leagueMatches);

                    leaguesDiv.appendChild(newLeague);
                }
            })
        }
    }
    eachMatches(dateSelector.value);

    dateSelector.addEventListener('input', () => {
        eachMatches(dateSelector.value);
    })
})