<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Credenciales de Acceso - Perseo Software</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #edf2f7;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-spacing: 0;
        }

        td {
            border-collapse: collapse;
            mso-line-height-rule: exactly;
            vertical-align: top;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            display: block;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            text-align: center;
            padding: 25px;
            color: #ffffff;
        }

        .header img {
            width: 180px;
            height: auto;
            margin: 0 auto;
        }

        .content {
            background-color: #f9fafb;
        }

        .greeting {
            padding: 25px 20px 20px 20px;
            text-align: center;
            background: #ffffff;
        }

        .greeting h1 {
            color: #1d6ea9;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .greeting p {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        .credentials-section {
            padding: 20px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        .credentials-title {
            text-align: center;
            color: #2d3748;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Mejora para iconos usando símbolos Unicode más compatibles */
        .icon-lock::before {
            content: "🔐";
            font-size: 16px;
            margin-right: 5px;
        }

        .icon-user::before {
            content: "👤";
            font-size: 16px;
            color: #1d6ea9;
            margin-right: 8px;
        }

        .icon-key::before {
            content: "🔑";
            font-size: 16px;
            color: #1d6ea9;
            margin-right: 8px;
        }

        .icon-web::before {
            content: "🌐";
            font-size: 16px;
            color: #1d6ea9;
            margin-right: 8px;
        }

        .icon-attachment::before {
            content: "📎";
            font-size: 14px;
            margin-right: 5px;
        }

        .credentials-grid {
            display: block;
            max-width: 450px;
            margin: 0 auto;
            width: 100%;
        }

        .credential-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #1d6ea9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .credential-card:last-child {
            margin-bottom: 0;
        }

        /* Mejorado: Usar tabla para mejor compatibilidad en emails */
        .credential-card table {
            width: 100%;
        }

        .credential-icon-cell {
            width: 40px;
            vertical-align: middle;
            text-align: center;
        }

        .credential-icon {
            font-size: 20px;
            color: #1d6ea9;
            line-height: 1;
            display: inline-block;
            width: 30px;
            height: 30px;
            text-align: center;
            vertical-align: middle;
        }

        .credential-info-cell {
            vertical-align: middle;
            padding-left: 10px;
        }

        .credential-label {
            font-size: 11px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credential-value {
            font-size: 14px;
            font-weight: bold;
            color: #2d3748;
            font-family: monospace;
            background: rgba(29, 110, 169, 0.1);
            padding: 8px 10px;
            border-radius: 6px;
            word-break: break-all;
            display: block;
            width: 100%;
            box-sizing: border-box;
        }

        .info-section {
            padding: 20px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        .section-title {
            color: #1d6ea9;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .benefits-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .benefit-item {
            flex: 1 1 auto;
            min-width: 160px;
            max-width: 250px;
            background: #f7fafc;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .benefit-link {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .benefit-link:hover {
            background: linear-gradient(135deg, #1d6ea9 0%, #2b77c0 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(29, 110, 169, 0.3);
            border-color: #1d6ea9;
        }

        .benefit-link:hover .benefit-title,
        .benefit-link:hover .benefit-description {
            color: #ffffff;
        }

        /* Mejorado: Iconos de beneficios centrados usando tabla */
        .benefit-content {
            display: block;
            margin: 0 auto;
            width: 100%;
        }

        .benefit-icon {
            font-size: 24px;
            margin-bottom: 8px;
            display: block;
            line-height: 1;
            text-align: center;
        }

        .benefit-title {
            color: #2d3748;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
            text-align: center;
        }

        .benefit-description {
            color: #4a5568;
            font-size: 12px;
            line-height: 1.4;
            text-align: center;
        }

        .links-section {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 20px;
            border-radius: 12px;
            margin: 15px 0;
            border: 1px solid #e2e8f0;
        }

        .links-title {
            color: #2d3748;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        .links-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .link-button {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #1d6ea9 0%, #2b77c0 100%);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(29, 110, 169, 0.3);
            transition: all 0.3s ease;
        }

        .link-button:hover {
            background: linear-gradient(135deg, #1a5490 0%, #2465a8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(29, 110, 169, 0.4);
        }

        .additional-services {
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 15px 0;
        }

        .services-title {
            color: #2d3748;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        .services-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .service-item {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border: 2px solid #1d6ea9;
            color: #1d6ea9;
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(29, 110, 169, 0.1);
        }

        .service-item:hover {
            background: linear-gradient(135deg, #1d6ea9 0%, #2b77c0 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(29, 110, 169, 0.3);
        }

        .footer-note {
            padding: 15px 20px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .note-text {
            color: #4a5568;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .footer {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            text-align: center;
            padding: 20px;
            color: #e2e8f0;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 6px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .social-links a:hover {
            transform: scale(1.1);
        }

        .social-links img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .footer-text {
            font-size: 11px;
            line-height: 1.4;
            margin: 3px 0;
            opacity: 0.9;
        }

        /* Media Queries para móviles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            .header {
                padding: 20px 15px !important;
            }

            .header img {
                width: 140px !important;
            }

            .greeting {
                padding: 20px 15px 15px 15px !important;
            }

            .greeting h1 {
                font-size: 18px !important;
            }

            .credentials-section {
                padding: 15px !important;
            }

            .credentials-grid {
                max-width: 100% !important;
                margin: 0 auto !important;
            }

            .credential-card {
                margin-bottom: 10px !important;
                padding: 12px !important;
            }

            .credential-value {
                font-size: 13px !important;
                padding: 6px 8px !important;
            }

            .info-section {
                padding: 15px !important;
            }

            .benefits-list {
                display: block !important;
                width: 100% !important;
            }

            .benefit-item {
                display: block !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                margin-bottom: 15px !important;
                flex: none !important;
            }

            .benefit-link {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .benefit-content {
                display: block !important;
                width: 100% !important;
            }

            .benefit-link:hover {
                transform: none !important;
            }

            .links-section {
                margin: 10px 0 !important;
                padding: 15px !important;
            }

            .links-grid {
                flex-direction: column !important;
                align-items: center !important;
            }

            .services-list {
                justify-content: center !important;
            }

            .footer-note {
                padding: 12px 15px !important;
            }

            .footer {
                padding: 15px !important;
            }

            .credentials-title,
            .section-title {
                flex-direction: column !important;
                gap: 4px !important;
            }

            .note-text {
                flex-direction: column !important;
                gap: 4px !important;
            }
        }

        /* Media query adicional para pantallas muy pequeñas */
        @media only screen and (max-width: 480px) {
            .credentials-grid {
                padding: 0 5px;
            }

            .credential-card table {
                width: 100%;
            }

            .credential-icon-cell {
                width: 35px;
            }

            .credential-icon {
                font-size: 18px;
            }

            .benefit-item {
                padding: 12px !important;
                margin-bottom: 12px !important;
            }

            .benefit-icon {
                font-size: 20px !important;
            }

            .benefit-title {
                font-size: 12px !important;
            }

            .benefit-description {
                font-size: 11px !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #edf2f7;">

<div class="email-container">
    <!-- Header -->
    <div class="header">
        @if($array['producto'] == 'facturito')
            <img src="{{ $message->embed(public_path() . '/assets/media/logoFacturito.png') }}" alt="Facturito">
        @else
            <img src="{{ $message->embed(public_path() . '/assets/media/logo.png') }}" alt="Perseo Software">
        @endif
    </div>

    <div class="content">
        <!-- Greeting -->
        <div class="greeting">
            @if(isset($array['tipo_credenciales']) && $array['tipo_credenciales'] === 'simples')
                {{-- CREDENCIALES SIMPLES --}}
                <h1>Hola {{ $array['cliente'] }}</h1>
                <p>Aquí tienes tus credenciales de acceso al sistema:</p>
            @else
                {{-- CREDENCIALES COMPLETAS --}}
                <h1>¡Bienvenido {{ $array['cliente'] }}!</h1>
                @if($array['tipo_producto']==9)
                    <p>¡Gracias por registrarte en el <strong>periodo de prueba de Perseo Lite</strong>! Tienes acceso completo para descubrir cómo
                        nuestro sistema puede transformar tu negocio. Ahorra tiempo, aumenta tu productividad y automatiza tus procesos desde el
                        primer día. ¡Aprovecha al máximo estos días de demo!</p>
                @else
                    <p>Gracias por adquirir nuestro sistema. Ahorra tiempo y aumenta tu productividad desde el primer día.</p>
                @endif
            @endif
        </div>

        <!-- Credentials -->
        <div class="credentials-section">
            <div class="credentials-title">
                Credenciales de Acceso
            </div>
            <div class="credentials-grid">
                <div class="credential-card">
                    <table>
                        <tr>
                            <td class="credential-icon-cell">
                                <div class="credential-icon">👤</div>
                            </td>
                            <td class="credential-info-cell">
                                <div class="credential-label">Usuario</div>
                                <div class="credential-value">{{ $array['identificacion'] }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="credential-card">
                    <table>
                        <tr>
                            <td class="credential-icon-cell">
                                <div class="credential-icon">🔑</div>
                            </td>
                            <td class="credential-info-cell">
                                <div class="credential-label">Contraseña</div>
                                <div class="credential-value">123</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- 🎯 CONTENIDO SOLO PARA CREDENCIALES COMPLETAS --}}
        @if(!isset($array['tipo_credenciales']) || $array['tipo_credenciales'] !== 'simples')
            <!-- Benefits -->
            <div class="info-section">
                <div class="section-title">
                    Beneficios Incluidos
                </div>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <div class="benefit-content">
                            <div class="benefit-icon">🚀</div>
                            <div class="benefit-title">Automatización</div>
                            <div class="benefit-description">Convierte días de trabajo en minutos</div>
                        </div>
                    </div>

                    <a href="https://academy.perseo.ec/" target="_blank" class="benefit-item benefit-link">
                        <div class="benefit-content">
                            <div class="benefit-icon">🎓</div>
                            <div class="benefit-title">Academy Perseo</div>
                            <div class="benefit-description">Capacitaciones y recursos de aprendizaje</div>
                        </div>
                    </a>

                    <a href="https://asesores-perseo.com/soporte/crear-ticket" target="_blank" class="benefit-item benefit-link">
                        <div class="benefit-content">
                            <div class="benefit-icon">💬</div>
                            <div class="benefit-title">Soporte Técnico</div>
                            <div class="benefit-description">Canal de tickets y soporte especializado</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="footer-note">
                <p class="note-text">
                    <span class="icon-attachment"></span>
                    <strong>Archivos adjuntos incluidos:</strong> Términos y condiciones, contrato y procedimiento de ingreso.
                    <br>¡Revisa todos los documentos para sacar el máximo provecho de tu sistema!
                </p>
            </div>
        @else
            {{-- 🎯 MENSAJE SIMPLE PARA CREDENCIALES SIMPLES --}}
            <div class="footer-note">
                <p class="note-text">
                    Si tienes dudas, puedes contactar con nuestro soporte técnico.
                    <br><strong>¡Que tengas un excelente día!</strong>
                </p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="font-size: 14px; margin-bottom: 8px; font-weight: 600;">Síguenos en redes sociales</div>

        <div class="social-links">
            <a href="https://www.facebook.com/sistemacontableperseoec" target="_blank">
                <img src="{{ $message->embed(public_path() . '/assets/media/facebook.png') }}" alt="Facebook">
            </a>
            <a href="https://www.youtube.com/channel/UC5vW4mwvCNbWpCxRr_aqI7g" target="_blank">
                <img src="{{ $message->embed(public_path() . '/assets/media/youtube.png') }}" alt="YouTube">
            </a>
            <a href="https://www.instagram.com/sistemacontableperseoec/?hl=es" target="_blank">
                <img src="{{ $message->embed(public_path() . '/assets/media/instagram.png') }}" alt="Instagram">
            </a>
        </div>

        <div class="footer-text">© {{ date('Y') }} PERSEO. Todos los derechos reservados</div>
        <div class="footer-text">Quito - Ecuador</div>
    </div>
</div>

</body>
</html>
