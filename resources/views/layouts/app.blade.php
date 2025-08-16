<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dydy solutions') }} - Service WHOIS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

     <!-- Inclusion des feuilles de style Bootstrap et personnalisées -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
       <!-- Font Awesome pour les icônes -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top">
        <div class="container">
            <button class="navbar-toggler" type="button" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
                <i class="fas fa-times d-none"></i>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}">
               <i class="fas fa-home me-1" style="color: #eef1f3;"></i> DDO
            </a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('whois.index') ? 'active' : '' }}" href="{{ route('whois.index') }}">
                            <i class="fas fa-search me-1"></i>WHOIS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ip.index') ? 'active' : '' }}" href="{{ route('ip.index') }}">
                            <i class="fas fa-network-wired me-1"></i> Local
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('nmap.index') ? 'active' : '' }}" href="{{ route('nmap.index') }}">
                            <i class="fas fa-shield-alt me-1"></i> Nmap
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('proxy.detector') ? 'active' : '' }}" href="{{ route('proxy.detector') }}">
                            <i class="fas fa-user-secret me-1"></i>Scan-Proxy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#helpModal">
                            <i class="fas fa-question-circle me-1"></i> Aide
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-md-0">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link d-flex align-items-center">
                                <span class="avatar-circle me-2">
                                    {{ strtoupper(substr(Auth::user()->mail, 0, 1)) }}
                                </span>
                                {{ Auth::user()->mail }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="nav-link connexion-button" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"> DECONNEXION</i>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link inscription-button {{ request()->routeIs('register.index') ? 'active' : '' }}" href="{{ route('register.index') }}">
                                <i class="fas fa-user-plus me-1"></i> Inscription
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link connexion-button" href="{{ route('login') }}" role="button">
                                <i class="fas fa-user-plus me-1"></i> Connexion
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal d'aide -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel">Guide d'utilisation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6><i class="fas fa-search text-primary"></i> Service WHOIS</h6>
                    <p>Le service WHOIS vous permet d'obtenir des informations détaillées sur un nom de domaine, notamment son propriétaire, sa date de création et d'expiration.</p>

                    <h6><i class="fas fa-network-wired text-primary"></i> Mon IP</h6>
                    <p>Cette fonction vous permet de connaître votre adresse IP publique ainsi que d'autres informations sur votre connexion Internet.</p>

                    <h6><i class="fas fa-shield-alt text-primary"></i> Nmap</h6>
                    <p>L'outil Nmap vous permet d'analyser les ports ouverts et les services en cours d'exécution sur une adresse IP ou un nom de domaine spécifique.</p>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> Pour toute question supplémentaire, n'hésitez pas à nous contacter.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

      <br>
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

      <br>
    <footer>
        <div class="container">
            <div class="footer-content">

                <div class="footer-links">
                     <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> Accueil</a>
                     <a href="{{ route('whois.index') }}"><i class="fas fa-search me-1"></i> Service WHOIS</a>
                     <a href="{{ route('ip.index') }}"><i class="fas fa-network-wired me-1"></i>Local</a>
                    <a href="{{ route('nmap.index') }}"><i class="fas fa-shield-alt me-1"></i> Nmap</a>
                    <a href="{{ route('proxy.detector') }}"><i class="fas fa-user-secret me-1"></i> Détecteur Proxy</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="fas fa-envelope me-1"></i> Contact</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#legalModal"><i class="fas fa-gavel me-1"></i> Mentions légales</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal"><i class="fas fa-lock me-1"></i> Confidentialité</a>
                </div>
                <div class="social-icons">
                    <a href="https://web.facebook.com/?_rdc=1&_rdr#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://api.whatsapp.com/send/?phone=28679234436&text&type=phone_number&app_absent=0" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.tiktok.com/" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>

                <p class="copyright">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Dydy solutions') }}. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

    <!-- Modal Contact -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel"><i class="fas fa-envelope text-primary"></i> Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6>Pour nous contacter :</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-envelope-square text-primary me-2"></i>Email : contact@dydy-solutions.com</li>
                            <li><i class="fas fa-phone text-primary me-2"></i>Téléphone : +22 12 34 56 78</li>
                            <li><i class="fas fa-clock text-primary me-2"></i>Horaires : Lun-Ven, 9h-18h</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Mentions Légales -->
    <div class="modal fade" id="legalModal" tabindex="-1" aria-labelledby="legalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="legalModalLabel"><i class="fas fa-gavel text-primary"></i> Mentions Légales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Informations légales</h6>
                    <p>Ce site est édité par Dydy solutions.</p>
                    <p>Siège social : Burkina Faso<br>
                    Directeur de la publication : Dydy solutions</p>

                    <h6>Hébergement</h6>
                    <p>Ce site est hébergé par Dydy solutions<br>

                    <h6>Propriété intellectuelle</h6>
                    <p>L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confidentialité -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel"><i class="fas fa-lock text-primary"></i> Politique de Confidentialité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Collecte des informations</h6>
                    <p>Nous collectons les informations suivantes lors de l'utilisation de nos services :</p>
                    <ul>
                        <li>Les adresses IP et noms de domaine analysés via nos outils</li>
                        <li>Les informations de navigation anonymes</li>
                    </ul>

                    <h6>Utilisation des données</h6>
                    <p>Les données collectées sont utilisées uniquement dans le cadre des services proposés :</p>
                    <ul>
                        <li>Analyse WHOIS</li>
                        <li>Détection d'IP</li>
                        <li>Analyse Nmap</li>
                    </ul>

                    <h6>Protection des données</h6>
                    <p>Nous nous engageons à protéger vos données personnelles et à ne pas les partager avec des tiers.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var menuItems = document.querySelectorAll('.navbar-nav .nav-link');
            var navbarCollapse = document.getElementById('navbarSupportedContent');
            var navbarToggler = document.querySelector('.navbar-toggler');
            var togglerIcon = navbarToggler.querySelector('.navbar-toggler-icon');
            var closeIcon = navbarToggler.querySelector('.fa-times');

            function toggleMenu() {
                navbarCollapse.classList.toggle('show');
                togglerIcon.classList.toggle('d-none');
                closeIcon.classList.toggle('d-none');
            }

            function closeMenu() {
                if (navbarCollapse.classList.contains('show')) {
                    toggleMenu();
                }
            }

            navbarToggler.addEventListener('click', toggleMenu);

            menuItems.forEach(function (item) {
                item.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        closeMenu();
                    }
                });
            });

            document.addEventListener('click', function (event) {
                var isClickInside = navbarCollapse.contains(event.target);
                var isClickOnToggler = navbarToggler.contains(event.target);

                if (!isClickInside && !isClickOnToggler) {
                    closeMenu();
                }
            });
        });
    </script>
    <style>
        .navbar-nav {
            justify-content: space-between !important;
        }
        .navbar-nav > li {
            margin: 5px 0 !important; /* Adjust margin for vertical stacking */
        }
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem !important;
        }
        .avatar-circle {
            display: inline-flex; /* Use flexbox for centering */
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
            width: 30px; /* Diameter of the circle */
            height: 30px; /* Diameter of the circle */
            border-radius: 50%; /* Makes it a circle */
            background-color: #007bff; /* Blue background */
            color: white; /* White text */
            font-weight: bold;
            font-size: 1rem; /* Adjust size as needed */
            text-transform: uppercase; /* Ensure first letter is uppercase */
        }
    </style>
</body>
</html>
