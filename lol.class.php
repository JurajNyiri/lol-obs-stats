<?php

class Lol
{
    private $name = "";
    private $key = "";
    private $puuid = false;
    private $id = false;
    private $host;
    private $regionHost;
    private $downloadedMatches = 0;

    private function authorize($url)
    {
        if (strpos(substr($url, strrpos($url, '/') + 1), "?") !== false) {
            return $url . '&api_key=' . $this->key;
        } else {
            return $url . '?api_key=' . $this->key;
        }
    }
    private function get($url, $forceUpdate = false)
    {
        $cacheFile = "data/" . md5($url) . ".data";
        $returnData = new stdClass();
        if (file_exists($cacheFile) && !$forceUpdate) {
            $data = json_decode(file_get_contents($cacheFile), true);
            $returnData->data = $data;
            $returnData->lastUpdate = filemtime($cacheFile);
            return $returnData;
        }
        try {
            $data = file_get_contents($this->authorize($url), false, stream_context_create(['http' => ['ignore_errors' => true]]));
            $statusData = json_decode($data, true);
            if ((isset($statusData) && isset($statusData['status']) && isset($statusData['status']['status_code']))) {
                if ($statusData['status']['status_code'] === 429) {
                    return false;
                } else {
                    if (isset($statusData['status']['message'])) {
                        throw new Exception($statusData['status']['message']);
                    } else {
                        throw new Exception($statusData);
                    }
                }
            }

            $data = json_decode($data, true);
            file_put_contents($cacheFile, json_encode($data, true));
            $returnData->data = $data;
            $returnData->lastUpdate = time();

            return $returnData;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    function getSummonerData()
    {
        $url = $this->host . 'lol/summoner/v4/summoners/by-name/' . $this->name;
        return $this->get($url);
    }

    function getAllMatches()
    {
        $allMatches = [];
        $start = 0;
        $count = 100;
        $matches = $this->getMatches($start, $count);
        if ($matches === false) {
            return false;
        }
        while (count($matches) > 0) {
            $this->downloadedMatches += $count;
            $start += $count;
            $allMatches = array_merge($allMatches, $matches);
            $matches = $this->getMatches($start, $count);
            if ($matches === false) {
                return false;
            }
        }
        return $allMatches;
    }

    function getMatches($start = 0, $count = 100)
    {
        $url = $this->regionHost . 'lol/match/v5/matches/by-puuid/' . $this->getPuuid() . '/ids?start=' . $start . '&count=' . $count;
        $data = $this->get($url);
        if (!$data) {
            return false;
        }
        if (time() - 60 > $data->lastUpdate) {
            $data = $this->get($url, true);
            if (!$data) {
                return false;
            }
        }
        return $data->data;
    }

    function getLeagueData()
    {
        $url = $this->host . 'lol/league/v4/entries/by-summoner/' . $this->getID();
        $data = $this->get($url);
        if (!$data) {
            return false;
        }
        if (time() - 60 > $data->lastUpdate) {
            $data = $this->get($url, true);
            if (!$data) {
                return false;
            }
        }
        return $data->data;
    }

    function getMatchData($matchID)
    {
        $url = $this->regionHost . 'lol/match/v5/matches/' . $matchID;
        $data = $this->get($url);
        if (!$data) {
            return false;
        }
        return $data->data;
    }

    function getSimpleMatchData($matchID)
    {
        $returnData = new stdClass();
        $matchData = $this->getMatchData($matchID);
        if ($matchData === false) {
            return false;
        }

        foreach ($matchData['info']['participants'] as $participant) {
            if ($participant['puuid'] === $this->getPuuid()) {
                $returnData->champion = $participant['championName'];
                $returnData->score = $participant['kills'] . "/" . $participant['deaths'] . "/" . $participant['assists'];
                $returnData->won = $participant['win'];
            }
        }
        $returnData->gameType = $matchData['info']['gameType'];
        $returnData->gameMode = $matchData['info']['gameMode'];
        $returnData->gameId = $matchData['info']['gameId'];
        $returnData->gameCreation = $matchData['info']['gameCreation'];
        return $returnData;
    }

    function getPuuid()
    {
        if ($this->puuid) {
            return $this->puuid;
        }
        $data = $this->getSummonerData();
        if (!$data) {
            return false;
        }
        $this->puuid = $data->data['puuid'];
        return $this->puuid;
    }
    function getID()
    {
        if ($this->id) {
            return $this->id;
        }
        $data = $this->getSummonerData();
        if (!$data) {
            return false;
        }
        $this->id = $data->data['id'];
        return $this->id;
    }

    function __construct($name, $key, $host = 'https://euw1.api.riotgames.com/', $regionHost = 'https://europe.api.riotgames.com/')
    {
        if (!file_exists("data")) {
            mkdir("data", 0777);
        }
        $this->name = $name;
        $this->key = $key;
        $this->host = $host;
        $this->regionHost = $regionHost;
    }
}
