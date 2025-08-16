<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: normal;
        }

        .content {
            padding: 30px;
            color: #333;
        }

        .success {
            border: 1px solid #007bff;
            background-color: #fcfcfc;
            color: #007bff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #e9ecef;
        }

        .info-box h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 18px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }

        .info-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
            color: #495057;
        }

        .value {
            color: #2c3e50;
        }

        .footer {
            background-color: #007bff;
            padding: 20px 30px;
            text-align: center;
            color: white;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .header, .content, .footer {
                padding: 20px;
            }

            .label {
                width: 100%;
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmation d'inscription</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $inscription->nom }} {{ $inscription->prenom }}</strong>,</p>

            <div class="success">
                Votre inscription a été enregistrée avec succès.
            </div>

            <div class="info-box">
                <h3>Vos informations de connexion</h3>
                <ul class="info-list">
                    <li>
                        <span class="label">Nom d'utilisateur :</span>
                        <span class="value">{{ $inscription->username }}</span>
                    </li>
                    <li>
                        <span class="label">Mot de passe :</span>
                        <span class="value">{{ $password }}</span>
                    </li>
                    <li>
                        <span class="label">Téléphone :</span>
                        <span class="value">{{ $inscription->numero_telephone }}</span>
                    </li>
                </ul>
            </div>

            <p>Cordialement,<br>
            <strong>L'équipe</strong></p>
        </div>

        <div class="footer">
            Cet email a été envoyé automatiquement.
        </div>
    </div>
</body>
</html>
