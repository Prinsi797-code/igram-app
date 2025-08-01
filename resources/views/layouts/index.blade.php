<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Downloader</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #e91e63 0%, #9c27b0 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .search-container {
            max-width: 600px;
            margin: 1px auto 20px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .search-box {
            display: flex;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .search-input {
            flex: 1;
            padding: 16px 20px;
            border: none;
            font-size: 16px;
            outline: none;
            background: transparent;
        }

        .clear-btn {
            background: none;
            border: none;
            padding: 0 15px;
            cursor: pointer;
            color: #666;
            font-size: 18px;
        }

        .download-btn {
            background: #2196F3;
            color: white;
            border: none;
            padding: 16px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .download-btn:hover {
            background: #1976D2;
        }

        .download-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .main-content {
            background: #f5f5f5;
            min-height: calc(100vh - 200px);
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #e91e63;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .search-result {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .profile-header {
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .verification-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #1976D2;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .profile-username {
            color: #666;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .profile-bio {
            color: #444;
            line-height: 1.5;
            margin-bottom: 20px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: lowercase;
        }

        .tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .tab.active {
            color: #e91e63;
            border-bottom: 2px solid #e91e63;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 30px;
        }

        .post-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .post-image-container {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
        }

        .post-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
            background: #f0f0f0;
        }

        .post-image.error {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            color: #666;
            font-size: 2rem;
        }

        .image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            color: #666;
            font-size: 2rem;
            flex-direction: column;
            gap: 10px;
        }

        .image-placeholder span {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .post-card:hover .post-image {
            transform: scale(1.05);
        }

        .post-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .post-actions {
            padding: 20px;
        }

        .post-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            color: #666;
            font-size: 0.9rem;
        }

        .post-stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .post-download {
            width: 100%;
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .post-download:hover {
            background: #1976D2;
        }

        .post-caption {
            margin-top: 10px;
            color: #444;
            font-size: 0.9rem;
            line-height: 1.4;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .error {
            text-align: center;
            padding: 40px;
            color: #e74c3c;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .error h3 {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .profile-stats {
                gap: 20px;
            }

            .posts-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .search-box {
                flex-direction: column;
            }

            .download-btn {
                border-radius: 0 0 12px 12px;
            }
        }

        /* footer */
        .footer-content {
            padding: 15px 0 !important;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 8px !important;
        }


        .social-link-facebook {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: #2EA9B9;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .social-link-facebook:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            color: white;
        }

        .social-link-youtube {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: #2EA9B9;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .social-link-youtube:hover {
            background-color: #FF0000;
            transform: translateY(-2px);
            color: white;
        }

        .social-link-instagram {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: #2EA9B9;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .social-link-instagram:hover {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            transform: translateY(-2px);
            color: white;
        }

        .social-link-linkedin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: #2EA9B9;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .social-link-linkedin:hover {
            background: #0077B5;
            transform: translateY(-2px);
            color: white;
        }

        .social-link-twitter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: #2EA9B9;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .social-link-twitter:hover {
            background: #1DA1F2;
            transform: translateY(-2px);
            color: white;
        }


        .social-link i {
            line-height: 1;
        }

        .footer-text p {
            margin-bottom: 0;
            line-height: 1.3;
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .social-links {
                gap: 10px;
            }

            .social-link {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }

        #footer {
            display: flex;
            justify-content: space-between;
        }

        @media (max-width: 576px) {
            #footer {
                display: block;
            }
        }

        /* form filed ui changes*/
        .form-floating-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-floating-custom .form-control {
            padding: 1rem 0.75rem 0.5rem 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.15);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-floating-custom .form-select {
            padding: 1rem 0.75rem 0.5rem 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background: transparent;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgb(185, 178, 178);
            opacity: 1;
            /* Ensures color shows up */
        }

        /* For WebKit browsers (Chrome, Safari) */
        .form-control::-webkit-input-placeholder {
            color: #b9b2b2;
        }

        /* For Firefox */
        .form-control::-moz-placeholder {
            color: #b9b2b2;
        }

        /* For Internet Explorer */
        .form-control:-ms-input-placeholder {
            color: #b9b2b2;
        }

        /* For Microsoft Edge */
        .form-control::-ms-input-placeholder {
            color: #b9b2b2;
        }

        .form-floating-custom label {
            position: absolute;
            top: 0;
            left: 12px;
            transform: translateY(-50%);
            background: white;
            padding: 0 8px;
            color: #6c757d;
            font-size: 14px;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 1;
            font-weight: 500;
        }

        .form-floating-custom select.form-control {
            cursor: pointer;
        }

        .container-custom {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .form-section {

            padding: 2rem;
            border-radius: 8px;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .footer-content {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .footer-section {
            margin: 10px;
        }

        .footer-section h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 8px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .social-link {
            color: white;
            font-size: 18px;
            transition: color 0.3s;
        }

        .social-link:hover {
            color: #ffffff;
        }

        .footer-text {

            padding-top: 10px;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>

<body>
    <main class="flex-fill">
        @yield('content')
    </main>
    @include('layouts.footer')
</body>

</html>
