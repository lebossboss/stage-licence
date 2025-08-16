<?php

namespace App\Services;

use Exception;

class WhoisService
{
    public function getWhoisInfo(string $query): array
    {
        if (filter_var($query, FILTER_VALIDATE_IP)) {
            return $this->getIpInfo($query);
        } else {
            // Convertir le nom de domaine en IP
            $ip = gethostbyname($query);

            // Si la conversion a échoué, gethostbyname retourne la chaîne d'origine
            if ($ip === $query) {
                return ['Signal' => "Impossible d'obtenir des informations sur ce site web, essayez un Scan-proxy."];
            }

            // Récupérer les informations de l'IP
            $ipInfo = $this->getIpInfo($ip);

            // Ajouter le nom de domaine original aux informations
            $ipInfo['domain_name'] = $query;
            $ipInfo['resolved_ip'] = $ip;

            return $ipInfo;
        }
    }

    public function getIpInfo(string $ip): array
    {
        try {
            // Configuration du contexte pour éviter les erreurs SSL
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                    ]
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);

            // Utilisation de ip-api.com qui fournit plus d'informations
            $url = "http://ip-api.com/json/{$ip}?fields=status,message,continent,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,isp,org,as,asname,reverse,mobile,proxy,hosting,query";
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                throw new Exception("Impossible de récupérer les informations pour cette IP");
            }

            $data = json_decode($response, true);

            if (!$data || $data['status'] === 'fail') {
                throw new Exception($data['message'] ?? "Erreur lors de la récupération des informations");
            }

            return $data;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Les méthodes getDomainInfo et getDomainInfoFallback ont été supprimées car nous utilisons maintenant
    // la résolution DNS + IP lookup à la place
}
