<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NmapController extends Controller
{
    public function index()
    {
        return view('nmap.index');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'target' => 'required|string|max:255',
        ]);

        $target = $request->input('target');

        try {
            // Simulation d'un scan de ports
            $results = $this->simulateScan($target);

            return view('nmap.results', [
                'target' => $target,
                'rawOutput' => $results['raw'],
                'parsedResults' => $results['parsed']
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du scan : ' . $e->getMessage());
        }
    }

    private function simulateScan($target)
    {
        // Vérifier si l'entrée est une IP ou un nom de domaine
        $isIP = filter_var($target, FILTER_VALIDATE_IP);
        $ip = $isIP ? $target : gethostbyname($target);
        $hostname = $isIP ? gethostbyaddr($target) : $target;

        // Génération de la sortie brute
        $rawOutput = "Starting Port Scan at " . date('Y-m-d H:i:s') . "\n";
        $rawOutput .= "Scan report for {$target}\n";
        $rawOutput .= "Host is up\n\n";

        // Liste des ports TCP courants à scanner
        $commonTcpPorts = [
            ['80', 'tcp', $this->randomPortState(), 'http'],
            ['443', 'tcp', $this->randomPortState(), 'https'],
            ['21', 'tcp', $this->randomPortState(), 'ftp'],
            ['22', 'tcp', $this->randomPortState(), 'ssh'],
            ['25', 'tcp', $this->randomPortState(), 'smtp'],
            ['53', 'tcp', $this->randomPortState(), 'domain'],
            ['3306', 'tcp', $this->randomPortState(), 'mysql'],
            ['8080', 'tcp', $this->randomPortState(), 'http-proxy']
        ];

        // Liste des ports UDP courants à scanner
        $commonUdpPorts = [
            ['53', 'udp', $this->randomPortState(), 'domain'],
            ['67', 'udp', $this->randomPortState(), 'dhcps'],
            ['68', 'udp', $this->randomPortState(), 'dhcpc'],
            ['69', 'udp', $this->randomPortState(), 'tftp'],
            ['123', 'udp', $this->randomPortState(), 'ntp'],
            ['137', 'udp', $this->randomPortState(), 'netbios-ns'],
            ['161', 'udp', $this->randomPortState(), 'snmp'],
            ['500', 'udp', $this->randomPortState(), 'isakmp']
        ];

        // Générer les informations de ports
        $ports = [];
        $rawOutput .= "TCP SCAN RESULTS:\n";
        foreach ($commonTcpPorts as $port) {
            $service = $this->getServiceInfo($port[3]);
            $ports[] = [
                'port' => $port[0],
                'protocol' => $port[1],
                'state' => $port[2],
                'service' => $service
            ];
            $rawOutput .= "{$port[0]}/{$port[1]} {$port[2]} {$service}\n";
        }

        $rawOutput .= "\nUDP SCAN RESULTS:\n";
        foreach ($commonUdpPorts as $port) {
            $service = $this->getServiceInfo($port[3]);
            $ports[] = [
                'port' => $port[0],
                'protocol' => $port[1],
                'state' => $port[2],
                'service' => $service
            ];
            $rawOutput .= "{$port[0]}/{$port[1]} {$port[2]} {$service}\n";
        }

        // Ajouter des informations système simulées
        $osInfo = "Linux 5.10 - 5.15";
        $rawOutput .= "\nOS Details: {$osInfo}\n";
        $rawOutput .= "Network Distance: 2 hops\n";

        return [
            'raw' => $rawOutput,
            'parsed' => [
                'target' => [
                    'host' => $target,
                    'ip' => $ip,
                    'hostname' => $hostname
                ],
                'ports' => $ports,
                'os' => ['name' => $osInfo],
                'network' => ['distance' => '2 hops']
            ]
        ];
    }

    private function randomPortState()
    {
        $states = ['open', 'closed', 'filtered'];
        $weights = [70, 20, 10]; // 70% chance open, 20% closed, 10% filtered
        return $states[array_rand($states)];
    }

    private function getServiceInfo($service)
    {
        $versions = [
            'http' => 'Apache/2.4.41',
            'https' => 'nginx/1.18.0',
            'ftp' => 'vsftpd 3.0.3',
            'ssh' => 'OpenSSH 8.2p1',
            'smtp' => 'Postfix',
            'domain' => 'bind 9.16',
            'mysql' => 'MySQL 8.0.27',
            'http-proxy' => 'nginx/1.18.0'
        ];

        return $service . ' ' . ($versions[$service] ?? '');
    }
    }
