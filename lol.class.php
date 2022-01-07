<?php

class Lol
{
    private $name = "";
    private $key = "";
    private $puuid = false;
    private $id = false;
    private $host;
    private $regionHost;

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
            $data = file_get_contents($url);
            if ($data === false) {
                return false;
            }
            $data = json_decode($data, true);
            file_put_contents($cacheFile, json_encode($data, true));
            $returnData->data = $data;
            $returnData->lastUpdate = time();

            return $returnData;
        } catch (Exception $e) {
            return false;
        }
    }

    function getSummonerData()
    {
        $url = $this->host . 'lol/summoner/v4/summoners/by-name/' . $this->name . '?api_key=' . $this->key;
        return $this->get($url);
    }

    function getAllMatches()
    {
        $allMatches = [];
        $start = 0;
        $count = 100;
        $matches = $this->getMatches($start, $count);
        while (count($matches) > 0) {
            $start += $count;
            $allMatches = array_merge($allMatches, $matches);
            $matches = $this->getMatches($start, $count);
        }
        return $allMatches;
    }

    function getMatches($start = 0, $count = 100)
    {
        $url = $this->regionHost . 'lol/match/v5/matches/by-puuid/' . $this->getPuuid() . '/ids?start=' . $start . '&count=' . $count . '&api_key=' . $this->key;
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
        $url = $this->host . 'lol/league/v4/entries/by-summoner/' . $this->getID() . '?api_key=' . $this->key;
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
        $url = $this->regionHost . 'lol/match/v5/matches/' . $matchID . '?api_key=' . $this->key;
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

        foreach ($matchData['info']['participants'] as $participant) {
            if ($participant['puuid'] === $this->getPuuid()) {
                $returnData->champion = $participant['championName'];
                $returnData->score = $participant['kills'] . "/" . $participant['deaths'] . "/" . $participant['assists'];
                $returnData->won = $participant['win'];
            }
        }
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
        $this->name = $name;
        $this->key = $key;
        $this->host = $host;
        $this->regionHost = $regionHost;
    }
}
