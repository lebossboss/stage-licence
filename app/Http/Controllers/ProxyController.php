<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProxyController extends Controller
{
    public function index()
    {
        return view('proxy.index');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'target' => 'required|string|max:255',
        ]);

        $target = $this->sanitizeTarget($request->input('target'));

        if (!$this->isValidTarget($target)) {
            return back()->withErrors(['target' => 'Cible invalide. Veuillez entrer un domaine ou une URL valide.']);
        }

        $cacheKey = 'scan_' . md5($target);
        $results = Cache::remember($cacheKey, 300, function () use ($target) {
            return $this->performScan($target);
        });

        return view('proxy.results', ['results' => $results]);
    }

    private function sanitizeTarget($target)
    {
        $target = trim($target);
        $target = preg_replace('/^https?:\/\//', '', $target);
        $target = preg_replace('/\/.*$/', '', $target);
        return $target;
    }

    private function isValidTarget($target)
    {
        return filter_var($target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) ||
               filter_var($target, FILTER_VALIDATE_IP) ||
               filter_var("http://{$target}", FILTER_VALIDATE_URL);
    }

    private function performScan($target)
    {
        set_time_limit(120); // Augmenter le temps d'exécution

        $results = [
            'target' => $target,
            'timestamp' => now()->toDateTimeString(),
            'ip' => $this->getIpAddress($target),
            'waf' => $this->detectWAF($target),
            'ports' => $this->scanPorts($target),
            'headers' => $this->analyzeHeaders($target),
            'technologies' => $this->detectTechnologies($target),
            'ssl_info' => $this->getSSLInfo($target),
            'dns_info' => $this->getDNSInfo($target),
            'whois' => $this->getWhoisInfo($target),
            'subdomains' => $this->findSubdomains($target),
            'cms' => $this->detectCMS($target),
            'security_headers' => $this->checkSecurityHeaders($target),
            'server_info' => $this->getServerInfo($target),
            'robots_txt' => $this->checkRobotsTxt($target),
            'sitemap' => $this->checkSitemap($target)
        ];

        return $results;
    }

    private function getIpAddress($target)
    {
        try {
            // Essayer plusieurs méthodes de résolution DNS
            $ip = gethostbyname($target);
            if ($ip !== $target && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }

            // Méthode alternative avec dns_get_record
            $records = dns_get_record($target, DNS_A);
            if (!empty($records) && isset($records[0]['ip'])) {
                return $records[0]['ip'];
            }

            // Méthode avec curl pour forcer la résolution
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://{$target}");
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            if (isset($info['primary_ip']) && filter_var($info['primary_ip'], FILTER_VALIDATE_IP)) {
                return $info['primary_ip'];
            }

        } catch (\Exception $e) {
            \Log::error("Erreur résolution IP pour {$target}: " . $e->getMessage());
        }

        return 'N/A - Résolution échouée';
    }

    private function detectWAF($target)
    {
        $detectedWAFs = [];

        // Test 1: Headers normaux
        $headerWAF = $this->detectWAFFromHeaders($target);
        if ($headerWAF !== 'Non détecté' && $headerWAF !== 'Erreur de connexion') {
            $detectedWAFs[] = $headerWAF;
        }

        // Test 2: Payloads malveillants
        $maliciousWAF = $this->detectWAFFromMaliciousRequests($target);
        if ($maliciousWAF !== 'Non détecté' && $maliciousWAF !== 'Erreur de connexion') {
            $detectedWAFs[] = $maliciousWAF;
        }

        // Test 3: Fingerprinting avancé
        $fingerprintWAF = $this->detectWAFFingerprinting($target);
        if ($fingerprintWAF !== 'Non détecté' && $fingerprintWAF !== 'Erreur de connexion') {
            $detectedWAFs[] = $fingerprintWAF;
        }

        // Try wafw00f if available and no specific WAF detected yet
        if (empty($detectedWAFs)) {
            try {
                $process = new Process(['wafw00f', '-a', $target]);
                $process->setTimeout(30);
                $process->run();

                if ($process->isSuccessful()) {
                    $output = $process->getOutput();
                    if (preg_match('/is behind a (.*?) WAF/i', $output, $matches)) {
                        $detectedWAFs[] = trim($matches[1]) . ' (via wafw00f)';
                    } elseif (strpos($output, 'No WAF detected') !== false) {
                        // No specific WAF detected by wafw00f, but it ran successfully
                    }
                } else {
                    \Log::error("wafw00f failed for {$target}: " . $process->getErrorOutput());
                }
            } catch (ProcessFailedException $e) {
                \Log::warning("wafw00f command not found or failed to execute: " . $e->getMessage());
            } catch (\Exception $e) {
                \Log::error("Unexpected error with wafw00f for {$target}: " . $e->getMessage());
            }
        }


        $uniqueWAFs = array_unique($detectedWAFs);

        if (!empty($uniqueWAFs)) {
            return implode(', ', $uniqueWAFs);
        }

        // If no specific WAF detected, but there were connection errors, report that
        if ($headerWAF === 'Erreur de connexion' || $maliciousWAF === 'Erreur de connexion' || $fingerprintWAF === 'Erreur de connexion') {
            return 'Erreur de connexion lors de la détection WAF';
        }

        // If no specific WAF detected, but some generic WAF behavior was observed
        if ($headerWAF === 'WAF générique détecté' || $maliciousWAF === 'WAF générique détecté') {
            return 'Possible WAF détecté (générique)';
        }

        return 'Non détecté';
    }

    private function detectWAFFromHeaders($target)
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'curl/7.68.0'
        ];

        foreach ($userAgents as $ua) {
            try {
                // Test HTTPS
                $response = Http::timeout(15)->withHeaders([
                    'User-Agent' => $ua,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ])->get("https://{$target}");

                return $this->analyzeWAFHeaders($response->headers());

            } catch (\Exception $e) {
                // Test HTTP en fallback
                try {
                    $response = Http::timeout(15)->withHeaders([
                        'User-Agent' => $ua,
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                    ])->get("http://{$target}");

                    return $this->analyzeWAFHeaders($response->headers());

                } catch (\Exception $e2) {
                    continue;
                }
            }
        }

        return 'Erreur de connexion';
    }

    private function analyzeWAFHeaders($headers)
    {
        $wafSignatures = [
            'Cloudflare' => [
                'cf-ray', 'cf-cache-status', 'cloudflare-nginx', '__cfduid',
                'cf-request-id', 'cf-visitor', 'cloudflare'
            ],
            'Akamai' => [
                'akamai-grn', 'x-akamai-transformed', 'akamai-x-cache',
                'x-akamai-request-id', 'akamai-x-get-request-id'
            ],
            'Incapsula/Imperva' => [
                'x-iinfo', 'x-cdn', 'incap_ses', 'visid_incap', 'imperva'
            ],
            'Sucuri' => [
                'x-sucuri-cache', 'x-sucuri-cloud', 'sucuri', 'x-sucuri-id'
            ],
            'Barracuda' => [
                'x-barracuda-log-id', 'barra', 'barracuda'
            ],
            'ModSecurity' => [
                'mod_security', 'modsecurity'
            ],
            'F5 BIG-IP' => [
                'f5-ltm-pool', 'bigipserver', 'x-waf-rule', 'f5-'
            ],
            'AWS WAF/CloudFront' => [
                'x-amz-cf-id', 'x-amz-request-id', 'awselb', 'x-amzn-requestid',
                'x-amz-cf-pop', 'x-cache'
            ],
            'Azure WAF' => [
                'x-ms-request-id', 'x-azure-ref', 'x-ms-'
            ],
            'Fortinet FortiWeb' => [
                'fortigate', 'x-fortinet-id', 'fortinet'
            ],
            'Radware' => [
                'x-appwall-status', 'radware'
            ],
            'StackPath' => [
                'x-sp-cache-status', 'stackpath'
            ],
            'KeyCDN' => [
                'keycdn-cache', 'x-keycdn'
            ]
        ];

        foreach ($wafSignatures as $wafName => $signatures) {
            foreach ($signatures as $signature) {
                foreach ($headers as $headerName => $headerValues) {
                    $headerName = strtolower($headerName);
                    $values = is_array($headerValues) ? $headerValues : [$headerValues];

                    if (stripos($headerName, $signature) !== false) {
                        return $wafName;
                    }

                    foreach ($values as $value) {
                        if (stripos($value, $signature) !== false) {
                            return $wafName;
                        }
                    }
                }
            }
        }

        return 'Non détecté';
    }

    private function detectWAFFromMaliciousRequests($target)
    {
        $payloads = [
            '?test=<script>alert(1)</script>',
            '?id=1\' OR 1=1--',
            '?file=../../../etc/passwd',
            '?cmd=cat /etc/passwd',
            '/?test=<img src=x onerror=alert(1)>',
            '/?union+select+*+from+users'
        ];

        foreach ($payloads as $payload) {
            try {
                $response = Http::timeout(10)->get("https://{$target}{$payload}");
                $waf = $this->analyzeWAFResponse($response->body(), $response->status());
                if ($waf !== 'Non détecté') {
                    return $waf;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return 'Non détecté';
    }

    private function analyzeWAFResponse($content, $statusCode)
    {
        $wafMessages = [
            'Cloudflare' => [
                'attention required', 'cloudflare', 'cf-browser-verification',
                'checking your browser', 'ddos protection by cloudflare'
            ],
            'Incapsula' => [
                'incapsula', 'request unsuccessful', 'incapsula incident id'
            ],
            'Sucuri' => [
                'sucuri', 'access denied', 'blocked by sucuri'
            ],
            'ModSecurity' => [
                'mod_security', 'not acceptable', 'modsecurity'
            ],
            'F5 BIG-IP' => [
                'the requested url was rejected', 'support id', 'f5 big-ip'
            ],
            'Barracuda' => [
                'barracuda', 'you have been blocked', 'web application firewall'
            ],
            'Fortinet' => [
                'fortigate', 'blocked by fortigate', 'fortinet'
            ],
            'AWS WAF' => [
                'request blocked', 'aws waf', 'blocked by aws'
            ]
        ];

        if (in_array($statusCode, [403, 406, 429, 503])) {
            foreach ($wafMessages as $wafName => $messages) {
                foreach ($messages as $message) {
                    if (stripos($content, $message) !== false) {
                        return $wafName;
                    }
                }
            }

            if (stripos($content, 'blocked') !== false ||
                stripos($content, 'forbidden') !== false ||
                stripos($content, 'access denied') !== false ||
                stripos($content, 'security') !== false) {
                return 'WAF générique détecté';
            }
        }

        return 'Non détecté';
    }

    private function detectWAFFingerprinting($target)
    {
        try {
            // Test avec différentes méthodes HTTP
            $methods = ['GET', 'POST', 'PUT', 'DELETE'];

            foreach ($methods as $method) {
                try {
                    $response = Http::timeout(5)->withHeaders([
                        'X-Forwarded-For' => '127.0.0.1',
                        'X-Real-IP' => '127.0.0.1'
                    ])->send($method, "https://{$target}");

                    $waf = $this->analyzeWAFHeaders($response->headers());
                    if ($waf !== 'Non détecté') {
                        return $waf;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Continue
        }

        return 'Non détecté';
    }

    private function scanPorts($target)
    {
        $commonPorts = [21, 22, 23, 25, 53, 80, 110, 143, 443, 993, 995, 8080, 8443, 3389, 5432, 3306];
        $openPorts = [];

        foreach ($commonPorts as $port) {
            $connection = @fsockopen($target, $port, $errno, $errstr, 2);
            if ($connection) {
                $openPorts[] = [
                    'port' => $port,
                    'state' => 'open',
                    'service' => $this->getServiceName($port)
                ];
                fclose($connection);
            }
        }

        return $openPorts;
    }

    private function getServiceName($port)
    {
        $services = [
            21 => 'FTP',
            22 => 'SSH',
            23 => 'Telnet',
            25 => 'SMTP',
            53 => 'DNS',
            80 => 'HTTP',
            110 => 'POP3',
            143 => 'IMAP',
            443 => 'HTTPS',
            993 => 'IMAPS',
            995 => 'POP3S',
            3306 => 'MySQL',
            3389 => 'RDP',
            5432 => 'PostgreSQL',
            8080 => 'HTTP Alt',
            8443 => 'HTTPS Alt'
        ];

        return $services[$port] ?? 'Unknown';
    }

    private function analyzeHeaders($target)
    {
        try {
            $response = Http::timeout(15)->get("https://{$target}");
            return $response->headers();
        } catch (\Exception $e) {
            try {
                $response = Http::timeout(15)->get("http://{$target}");
                return $response->headers();
            } catch (\Exception $e2) {
                return ['error' => 'Impossible de récupérer les headers'];
            }
        }
    }

    private function detectTechnologies($target)
    {
        try {
            $response = Http::timeout(15)->get("https://{$target}");
            $content = $response->body();
            $headers = $response->headers();

            $technologies = [];

            // Détection serveur
            if (isset($headers['server'])) {
                $server = is_array($headers['server']) ? $headers['server'][0] : $headers['server'];
                if (stripos($server, 'apache') !== false) $technologies[] = 'Apache';
                if (stripos($server, 'nginx') !== false) $technologies[] = 'Nginx';
                if (stripos($server, 'iis') !== false) $technologies[] = 'Microsoft IIS';
                if (stripos($server, 'cloudflare') !== false) $technologies[] = 'Cloudflare';
            }

            // Détection X-Powered-By
            if (isset($headers['x-powered-by'])) {
                $powered = is_array($headers['x-powered-by']) ? $headers['x-powered-by'][0] : $headers['x-powered-by'];
                $technologies[] = "Powered by: {$powered}";
            }

            // Détection dans le contenu
            $contentDetection = [
                'WordPress' => ['wp-content', 'wp-includes', 'wp-admin', '/wp-json/'],
                'Joomla' => ['joomla', '/media/jui/', 'Joomla!'],
                'Drupal' => ['drupal', 'sites/default/files', 'Drupal.settings'],
                'Magento' => ['magento', '/skin/frontend/', 'var/view_preprocessed'],
                'Shopify' => ['shopify', 'cdn.shopify.com'],
                'jQuery' => ['jquery'],
                'React' => ['react', '_react'],
                'Vue.js' => ['vue.js', '__vue__'],
                'Angular' => ['angular', 'ng-app'],
                'Bootstrap' => ['bootstrap']
            ];

            foreach ($contentDetection as $tech => $patterns) {
                foreach ($patterns as $pattern) {
                    if (stripos($content, $pattern) !== false) {
                        $technologies[] = $tech;
                        break;
                    }
                }
            }

            return array_unique($technologies);
        } catch (\Exception $e) {
            return ['error' => 'Détection échouée'];
        }
    }

    private function getSSLInfo($target)
    {
        try {
            $context = stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false
                ]
            ]);

            $socket = stream_socket_client("ssl://{$target}:443", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);

            if ($socket) {
                $params = stream_context_get_params($socket);
                $cert = $params['options']['ssl']['peer_certificate'];
                $certData = openssl_x509_parse($cert);

                fclose($socket);

                return [
                    'valid' => true,
                    'issuer' => $certData['issuer']['CN'] ?? 'N/A',
                    'subject' => $certData['subject']['CN'] ?? 'N/A',
                    'valid_from' => date('Y-m-d H:i:s', $certData['validFrom_time_t']),
                    'valid_to' => date('Y-m-d H:i:s', $certData['validTo_time_t']),
                    'days_until_expiry' => ceil(($certData['validTo_time_t'] - time()) / 86400),
                    'signature_algorithm' => $certData['signatureTypeSN'] ?? 'N/A'
                ];
            }
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'SSL non disponible ou erreur de connexion'
            ];
        }

        return ['valid' => false, 'error' => 'Connexion SSL échouée'];
    }

    private function getDNSInfo($target)
    {
        try {
            return [
                'A' => dns_get_record($target, DNS_A) ?: [],
                'AAAA' => dns_get_record($target, DNS_AAAA) ?: [],
                'MX' => dns_get_record($target, DNS_MX) ?: [],
                'NS' => dns_get_record($target, DNS_NS) ?: [],
                'TXT' => dns_get_record($target, DNS_TXT) ?: []
            ];
        } catch (\Exception $e) {
            return ['error' => 'Requêtes DNS échouées'];
        }
    }

    private function getWhoisInfo($target)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.whois.com/whois/{$target}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Scanner/1.0)');

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $result) {
                return 'Whois disponible (détails via API externe)';
            }
        } catch (\Exception $e) {
            // Continue
        }

        return 'Whois non disponible';
    }

    private function findSubdomains($target)
    {
        $commonSubdomains = [
            'www', 'mail', 'ftp', 'admin', 'test', 'dev', 'staging',
            'api', 'blog', 'shop', 'store', 'support', 'help',
            'news', 'mobile', 'm', 'secure', 'ssl', 'vpn'
        ];
        $foundSubdomains = [];

        foreach ($commonSubdomains as $subdomain) {
            $fullDomain = "{$subdomain}.{$target}";
            $ip = gethostbyname($fullDomain);

            if ($ip !== $fullDomain && filter_var($ip, FILTER_VALIDATE_IP)) {
                $foundSubdomains[] = [
                    'subdomain' => $fullDomain,
                    'ip' => $ip
                ];
            }
        }

        return $foundSubdomains;
    }

    private function detectCMS($target)
    {
        try {
            $response = Http::timeout(15)->get("https://{$target}");
            $content = $response->body();
            $headers = $response->headers();

            $cmsSignatures = [
                'WordPress' => [
                    'content' => ['wp-content', 'wp-includes', 'wp-admin', '/wp-json/'],
                    'headers' => ['x-powered-by' => 'wordpress']
                ],
                'Joomla' => [
                    'content' => ['/media/jui/', 'Joomla!', '/administrator/'],
                    'headers' => []
                ],
                'Drupal' => [
                    'content' => ['sites/default/files', 'Drupal.settings', '/core/'],
                    'headers' => ['x-drupal-cache' => '']
                ],
                'Magento' => [
                    'content' => ['magento', '/skin/frontend/', 'var/view_preprocessed'],
                    'headers' => []
                ]
            ];

            foreach ($cmsSignatures as $cms => $signatures) {
                // Check content
                foreach ($signatures['content'] as $pattern) {
                    if (stripos($content, $pattern) !== false) {
                        return $cms;
                    }
                }

                // Check headers
                foreach ($signatures['headers'] as $header => $value) {
                    if (isset($headers[$header])) {
                        return $cms;
                    }
                }
            }

            return 'CMS non détecté';
        } catch (\Exception $e) {
            return 'Erreur de détection CMS';
        }
    }

    private function checkSecurityHeaders($target)
    {
        try {
            $response = Http::timeout(15)->get("https://{$target}");
            $headers = $response->headers();

            $securityHeaders = [
                'Content-Security-Policy' => isset($headers['content-security-policy']),
                'X-Frame-Options' => isset($headers['x-frame-options']),
                'X-XSS-Protection' => isset($headers['x-xss-protection']),
                'X-Content-Type-Options' => isset($headers['x-content-type-options']),
                'Strict-Transport-Security' => isset($headers['strict-transport-security']),
                'Referrer-Policy' => isset($headers['referrer-policy']),
                'Permissions-Policy' => isset($headers['permissions-policy']),
                'X-Permitted-Cross-Domain-Policies' => isset($headers['x-permitted-cross-domain-policies'])
            ];

            // Calculer le score de sécurité
            $score = array_sum($securityHeaders);
            $total = count($securityHeaders);

            $securityHeaders['security_score'] = round(($score / $total) * 100, 1);

            return $securityHeaders;
        } catch (\Exception $e) {
            return ['error' => 'Vérification des headers de sécurité échouée'];
        }
    }

    private function getServerInfo($target)
    {
        try {
            $response = Http::timeout(15)->get("https://{$target}");
            $headers = $response->headers();

            return [
                'server' => $headers['server'] ?? 'Non spécifié',
                'powered_by' => $headers['x-powered-by'] ?? 'Non spécifié',
                'response_time' => $response->transferStats->getTransferTime() ?? 'N/A',
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return ['error' => 'Informations serveur indisponibles'];
        }
    }

    private function checkRobotsTxt($target)
    {
        try {
            $response = Http::timeout(10)->get("https://{$target}/robots.txt");
            if ($response->status() === 200) {
                return 'Disponible';
            }
        } catch (\Exception $e) {
            // Continue
        }

        return 'Non trouvé';
    }

    private function checkSitemap($target)
    {
        $sitemapUrls = [
            "/sitemap.xml",
            "/sitemap_index.xml",
            "/sitemap.txt"
        ];

        foreach ($sitemapUrls as $url) {
            try {
                $response = Http::timeout(5)->get("https://{$target}{$url}");
                if ($response->status() === 200) {
                    return "Trouvé: {$url}";
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return 'Non trouvé';
    }
}
