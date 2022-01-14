<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Willkommen :: Pfarrplaner</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="/landing/assets/img/favicon.png" rel="icon">
    <link href="/landing/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="/landing/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="/landing/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/landing/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/landing/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="/landing/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="/landing/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="/landing/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="/landing/assets/css/style.css" rel="stylesheet">


    <!-- favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/img/favicons/site.webmanifest">
    <link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/img/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- =======================================================
    * Template Name: Bootslander - v4.7.0
    * Template URL: https://bootstrapmade.com/bootslander-free-bootstrap-landing-page-template/
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>

<body>

<!-- ======= Header ======= -->
<header id="header" class="fixed-top d-flex align-items-center header-transparent">
    <div class="container d-flex align-items-center justify-content-between">

        <div class="logo">
            <h1><a href="/">
                    <img src="/img/logo/pfarrplaner.svg"/>
                    <span>Pfarrplaner</span></a></h1>
            <!-- Uncomment below if you prefer to use an image logo -->
            <!-- <a href="index.html"><img src="/landing/assets/img/logo.png" alt="" class="img-fluid"></a>-->
        </div>

        <nav id="navbar" class="navbar">
            <ul>
                <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                <li><a class="nav-link scrollto" href="#about">Über</a></li>
                <li><a class="nav-link scrollto" href="#features">Features</a></li>
                <li><a class="nav-link scrollto" href="#gallery">Tutorials</a></li>
                <li><a class="nav-link scrollto" href="#contact">Kontakt</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

    </div>
</header><!-- End Header -->

<!-- ======= Hero Section ======= -->
<section id="hero">

    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-7 pt-5 pt-lg-0 order-2 order-lg-1 d-flex align-items-center">
                <div data-aos="zoom-out">
                    <h1>Kirche gemeinsam planen mit dem <span>Pfarrplaner</span></h1>
                    <h2>Tools, die deine Kirchengemeinde braucht</h2>
                    <form method="POST" action="{{ route('login') }}">
                        <div class="login-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    @input(['name' => 'email', 'label' => '', 'placeholder' => 'deine@email.de', 'value' => old('email'), 'type' => 'email', 'id' => 'email', 'autofocus' => true])
                                </div>
                                <div class="col-md-6">
                                    @input(['name' => 'password', 'label' => '', 'placeholder' => 'Dein Passwort', 'type' => 'password'])
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-lg-start mt-3">
                            <button type="submit" class="btn-get-started">
                                Anmelden
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="300">
                <img src="/landing/assets/img/screenshot1.png" class="img-fluid animated  rounded rounded-lg gallery-lightbox" alt="">
            </div>
        </div>
    </div>

    <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
         viewBox="0 24 150 28 " preserveAspectRatio="none">
        <defs>
            <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
        </defs>
        <g class="wave1">
            <use xlink:href="#wave-path" x="50" y="3" fill="rgba(255,255,255, .1)">
        </g>
        <g class="wave2">
            <use xlink:href="#wave-path" x="50" y="0" fill="rgba(255,255,255, .2)">
        </g>
        <g class="wave3">
            <use xlink:href="#wave-path" x="50" y="9" fill="#fff">
        </g>
    </svg>

</section><!-- End Hero -->

<main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
        <div class="container-fluid">

            <div class="row">
                <div class="col-xl-5 col-lg-6 "
                     data-aos="fade-right">
                    <img src="/landing/assets/img/screenshot2.png" class="img-fluid pt-md-5 rounded rounded-lg  gallery-lightbox"/>
                </div>

                <div
                    class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5"
                    data-aos="fade-left">
                    <h3>Digitales Gemeindemanagement, das funktioniert</h3>
                    <p>Aus der Praxis, für die Praxis: Der Pfarrplaner ist das fehlende Bindeglied zwischen
                        verschiedenen vorhandenen Tools in der Verwaltung einer
                        Kirchengemeinde der Evangelischen Landeskirche in Württemberg.</p>

                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
                        <div class="icon"><i class="fa fa-calendar"></i></div>
                        <h4 class="title"><a href="">Gottesdienstplanung über Gemeindegrenzen hinweg</a></h4>
                        <p class="description">Gegenseitige Vertretung, Zusammenarbeit im Distrikt, Urlaube,
                            Vakaturen,... Mit dem Pfarrplaner behältst du mehr als eine Gemeinde im Blick.</p>
                    </div>

                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="200">
                        <div class="icon"><i class="fa fa-cross"></i></div>
                        <h4 class="title"><a href="">Kasualien perfekt im Griff</a></h4>
                        <p class="description">Vom Erstgespräch am Telefon bis zum Eintrag im Kirchenbuch -- mit dem
                            Pfarrplaner behältst du den Überblick über alle anstehenden Kasualien.</p>
                    </div>

                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
                        <div class="icon"><i class="fa fa-th-list"></i></div>
                        <h4 class="title"><a href="">Liturgie auf Mausklick</a></h4>
                        <p class="description">Glocken, Orgelspiel, Psalm &amp; Predigt... Der Pfarrplaner hilft dir,
                            deine Liturgien und Abläufe zu planen und mit den richtigen Leuten im richtigen Format zu
                            teilen.</p>
                    </div>

                </div>
            </div>

        </div>
    </section><!-- End About Section -->

    <!-- ======= Features Section ======= -->
    <section id="features" class="features">
        <div class="container">

            <div class="section-title" data-aos="fade-up">
                <h2>Features</h2>
                <p>Ein kompletter Werkzeugkasten</p>
            </div>

            <div class="row" data-aos="fade-left">
                <div class="col-lg-3 col-md-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="50">
                        <i class="fa fa-calendar" style="color: #ffbb2c;"></i>
                        <h3>Dienstplan</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4 mt-md-0">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
                        <i class="fa fa-globe" style="color: #5578ff;"></i>
                        <h3>Urlaubsplan</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4 mt-md-0">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="150">
                        <i class="fa fa-th-list" style="color: #e80368;"></i>
                        <h3>Liturgieeditor</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4 mt-lg-0">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="200">
                        <i class="fa fa-file-code" style="color: #e361ff;"></i>
                        <h3>Integration in die eigene Homepage</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="250">
                        <i class="fa fa-microphone-alt" style="color: #47aeff;"></i>
                        <h3>Predigteditor</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
                        <i class="fa fa-calendar-alt" style="color: #ffa76e;"></i>
                        <h3>Synchronisierung mit Outlook</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="350">
                        <i class="fa fa-ticket-alt" style="color: #11dbcf;"></i>
                        <h3>Anmeldesystem mit Sitzplan</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="400">
                        <i class="fa fa-water" style="color: #4233ff;"></i>
                        <h3>Taufen und Taufanfragen</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="450">
                        <i class="fa fa-cross" style="color: #b2904f;"></i>
                        <h3>Beerdigungen</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="500">
                        <i class="fa fa-ring" style="color: #b20969;"></i>
                        <h3>Trauungen</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="550">
                        <i class="fa fa-users" style="color: #ff5828;"></i>
                        <h3>Detaillierte Benutzerverwaltung</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="600">
                        <i class="fa fa-print" style="color: #29cc61;"></i>
                        <h3>Viele praktische Ausgabeformate</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="600">
                        <i class="fab fa-youtube" style="color: red;"></i>
                        <h3>Integration mit YouTube-Livestreaming</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="600">
                        <img src="/img/external/konfiapp.png" style="max-height: 32px; margin-right: 5px;"/>
                        <h3>Integration mit der <a href="https://konfiapp.de/" target="_blank">KonfiApp</a></h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="600">
                        <img src="/img/external/communiapp.png" style="max-height: 32px; margin-right: 20px;"/>
                        <h3>Integration mit der <a href="https://www.communiapp.de/" target="_blank">CommuniApp</a></h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mt-4">
                    <div class="icon-box" data-aos="zoom-in" data-aos-delay="600">
                        <i class="fa fa-code" style="color: darkgray;"></i>
                        <h3>Ständige Weiterentwicklung</h3>
                    </div>
                </div>
            </div>

        </div>
    </section><!-- End Features Section -->

    <!-- ======= Counts Section ======= -->
    <section id="counts" class="counts">
        <div class="container">

            <div class="row" data-aos="fade-up">

                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="fa fa-church"></i>
                        <span data-purecounter-start="0" data-purecounter-end="{{ $count['cities'] }}" data-purecounter-duration="1"
                              class="purecounter"></span>
                        <p>Kirchengemeinden</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-5 mt-md-0">
                    <div class="count-box">
                        <i class="fa fa-users"></i>
                        <span data-purecounter-start="0" data-purecounter-end="{{ $count['users'] }}" data-purecounter-duration="1"
                              class="purecounter"></span>
                        <p>Benutzer</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
                    <div class="count-box">
                        <i class="fa fa-calendar-day"></i>
                        <span data-purecounter-start="0" data-purecounter-end="{{ $count['services'] }}" data-purecounter-duration="1"
                              class="purecounter"></span>
                        <p>Gottesdienste</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
                    <div class="count-box">
                        <i class="fa fa-laptop-code"></i>
                        <span>{{ $version }}</span>
                        <p>Aktuelle Version</p>
                    </div>
                </div>

            </div>

        </div>
    </section><!-- End Counts Section -->

    <!-- ======= Details Section ======= -->
    <section id="details" class="details">
        <div class="container">

            <div class="row content">
                <div class="col-md-4" data-aos="fade-right">
                    <img src="/landing/assets/img/screenshot3.png" class="img-fluid pt-md-5 rounded rounded-lg gallery-lightbox" alt="">
                </div>
                <div class="col-md-8 pt-5" data-aos="fade-up">
                    <h3>Alle Daten zum Gottesdienst an einem Ort</h3>
                    <p>Nie wieder 30 verschiedene Listen führen. Alles, was mit Gottesdiensten zu tun hat, ist im Pfarrplaner an einem Ort vereint.</p>
                    <ul>
                        <li><i class="bi bi-check"></i> Unterschiedliche Lese- und Schreibberechtigungen pro Nutzer</li>
                        <li><i class="bi bi-check"></i> Automatische Änderungsnachrichten an alle Beteiligten</li>
                        <li><i class="bi bi-check"></i> Synchronisation mit dem Outlookkalender für @elkw.de-Benutzer</li>
                        <li><i class="bi bi-check"></i> Anmeldungen und Kasualien zum Gottesdienst verwalten</li>
                        <li><i class="bi bi-check"></i> Dateien und Kommentare anhängen</li>
                        <li><i class="bi bi-check"></i> Integrationen mit KonfiApp und CommuniApp</li>
                        <li><i class="bi bi-check"></i> Automatische Freischaltung von YouTube-Livestreams</li>
                    </ul>
                </div>
            </div>



            <div class="row content">
                <div class="col-md-4 order-1 order-md-2" data-aos="fade-left">
                    <img src="/landing/assets/img/screenshot4.png" class="img-fluid pt-md-5 rounded rounded-lg gallery-lightbox" alt="">
                </div>
                <div class="col-md-8 pt-5 order-2 order-md-1" data-aos="fade-up">
                    <h3>Werkzeuge für Liturgie und Predigt</h3>
                    <p class="fst-italic">
                        Der Pfarrplaner kennt den liturgischen Kalender und bietet die nötigen Informationen und Tools,
                        um schnell die passende Liturgie und Predigt zusammenzustellen.
                    </p>
                    <ul>
                        <li><i class="bi bi-check"></i> Informationen zu Liturgie, Kirchenjahr und Perikopenplan</li>
                        <li><i class="bi bi-check"></i> Liturgieimport aus Vorlagen</li>
                        <li><i class="bi bi-check"></i> Drag-and-Drop-Oberfläche zur Ablaufplanung</li>
                        <li><i class="bi bi-check"></i> Automatische Zeitschätzung für Ablaufelemente</li>
                        <li><i class="bi bi-check"></i> Export der fertigen Liturgie in viele Formate (Ablaufplan,
                            Volltext, Liedblatt, Powerpointpräsentation, ...)</li>
                        <li><i class="bi bi-check"></i> API für die Ausgabe von Predigten auf externen Seiten</li>
                        <li><i class="bi bi-check"></i> Viele fertige Textbausteine von anderen Benutzern</li>
                    </ul>
                </div>
            </div>

            <div class="row content">
                <div class="col-md-4" data-aos="fade-right">
                    <img src="/landing/assets/img/screenshot5.png" class="img-fluid pt-md-5 rounded rounded-lg gallery-lightbox" alt="">
                </div>
                <div class="col-md-8 pt-4" data-aos="fade-up">
                    <h3>Sicheres Hosting nach deutschen Standards</h3>
                    <p class="fst-italic">
                        Der Pfarrplaner wird auf einem Server des
                        <a href="https://www.kirchenbezirk-balingen.de" target="_blank">Evangelischen Kirchenbezirks Balingen</a>
                        in einem deutschen Rechenzentrum gehostet.
                    </p>
                    <ul>
                        <li><i class="bi bi-check"></i> Sicheres Hosting in einem deutschen Rechenzentrum mit höchten Sicherheitsstandards.</li>
                        <li><i class="bi bi-check"></i> Verschlüsselte Speicherung und Übertragung von personenbezogenen Daten.</li>
                        <li><i class="bi bi-check"></i> Tägliche, mehrfach redundante Backups</li>
                        <li><i class="bi bi-check"></i> Kostenlose Nutzung für Kirchengemeinden des Kirchenbezirks Balingen</li>
                    </ul>
                    <p>
                        Kirchengemeinden außerhalb des Kirchenbezirks Balingen können bei Interesse gerne Kontakt mit uns aufnehmen und
                        gegen eine geringe Kostenbeteiligung den Pfarrplaner nutzen.
                    </p>
                </div>
            </div>


            <div class="row content">
                <div class="col-md-4 order-1 order-md-2" data-aos="fade-left">
                    <img src="/landing/assets/img/github1.png" class="img-fluid pt-md-5 rounded rounded-lg gallery-lightbox" alt="">
                </div>
                <div class="col-md-8 pt-5 order-2 order-md-1" data-aos="fade-up">
                    <h3>Aus der Praxis für die Praxis</h3>
                    <p class="fst-italic">
                        Der Pfarrplaner wird basierend auf den Erfahrungen des realen Arbeitsalltags in den beteiligten
                        Kirchengemeinden ständig weiterentwickelt. Verbesserungsvorschläge sind immer willkommen.
                    </p>
                    <p>
                        Damit auch andere von der geleisteten Arbeit profitieren können, ist der Pfarrplaner Open Source.
                        Der Quellcode des kompletten Projekts
                        <a href="https://github.com/pfarrplaner/pfarrplaner" target="_blank">steht auf GitHub zur Verfügung.</a>
                    </p>
                    <ul>
                        <li><i class="bi bi-check"></i> Ständige Verbesserungen</li>
                        <li><i class="bi bi-check"></i> Kurze Entwicklungszyklen</li>
                        <li><i class="bi bi-check"></i> Beteiligungsmöglichkeiten durch Open Source</li>
                        <li><i class="bi bi-check"></i> Erfahrungen aus dem realen Arbeitsumfeld</li>
                    </ul>
                </div>
            </div>

        </div>
    </section><!-- End Details Section -->

    <!-- ======= Gallery Section ======= -->
    <section id="gallery" class="gallery">
        <div class="container">

            <div class="section-title" data-aos="fade-up">
                <h2>Tutorials</h2>
                <p>Video-Tutorials auf <a href="https://www.youtube.com/channel/UCLRinVsLiYMJtN56bynCPEw"
                                          target="_blank">YouTube</a>
            </div>

            <div class="row no-gutters" data-aos="fade-left">


                @foreach($videos as $videoTitle => $videoUrl)
                    <div class="col-lg-3 col-md-4">
                        <div class="gallery-item" data-aos="zoom-in" data-aos-delay="100">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item" src="{{ $videoUrl }}"></iframe>
                            </div>
                            <b>{{ $videoTitle }}</b>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section><!-- End Gallery Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
        <div class="container">

            <div class="section-title" data-aos="fade-up">
                <h2>Interessiert?</h2>
                <p>Nimm Kontakt mit uns auf</p>
            </div>

            <div class="row">

                <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
                    <div class="info">
                        <div class="address">
                            <i class="bi bi-geo-alt"></i>
                            <h4>Entwickler:</h4>
                            <p>Pfarrer Christoph Fischer<br/>
                                für den <a href="https://www.kirchenbezirk-balingen.de" target="_blank">Evangelischen
                                    Kirchenbezirk Balingen</a>
                                <br/>Liegnitzer Str. 38<br/>72461 Albstadt</p>
                        </div>

                        <div class="email">
                            <i class="bi bi-envelope"></i>
                            <h4>Email:</h4>
                            <p>christoph.fischer@elkw.de</p>
                        </div>

                        <div class="phone">
                            <i class="bi bi-phone"></i>
                            <h4>Telefon:</h4>
                            <p>07432 3762</p>
                        </div>

                    </div>

                </div>

                <div class="col-lg-8 mt-5 mt-lg-0" data-aos="fade-left" data-aos-delay="200">

                    <form action="/kontaktformular" method="post" role="form" class="php-email-form" id="contactForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" name="name" class="form-control" id="name" placeholder="Dein Name"
                                       required>
                            </div>
                            <div class="col-md-6 form-group mt-3 mt-md-0">
                                <input type="email" class="form-control" name="email" id="email"
                                       placeholder="Deine E-Mailadresse" required>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Betreff"
                                   required>
                        </div>
                        <div class="form-group mt-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Nachricht"
                                      required></textarea>
                        </div>
                        <div class="my-3">
                            <div class="loading">Lade...</div>
                            <div class="error-message"></div>
                            <div class="sent-message">Deine Nachricht wurde gesendet. Danke!</div>
                        </div>
                        <div class="text-center">
                            <button type="submit">Nachricht senden</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </section><!-- End Contact Section -->

</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer">
    <div class="container">
        <div class="copyright">
            Copyright &copy; <strong><span>Pfarrplaner</span></strong>. All Rights Reserved.
        </div>

    </div>
</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
        class="bi bi-arrow-up-short"></i></a>
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="/landing/assets/vendor/purecounter/purecounter.js"></script>
<script src="/landing/assets/vendor/aos/aos.js"></script>
<script src="/landing/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/landing/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/landing/assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="/landing/assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="/landing/assets/js/main.js"></script>

</body>
</html>
