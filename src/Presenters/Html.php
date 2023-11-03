<!DOCTYPE html>
<html>
    <head>
        <title>NoAds Messenger</title>
        <!-- Pripojenie Bootstrap 5 CSS -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="white">
        <meta name="theme-color" content="#ffffff">
        <meta name="google-signin-client_id" content="457514265065-lebcoj1o48gv3macbic8nge3tpp59k3f.apps.googleusercontent.com">


        <link rel="apple-touch-icon" sizes="180x180" href="/media/images/180.png">
        <link rel="icon" sizes="192x192" href="/media/images/192.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/media/images/152.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/media/images/120.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/media/images/76.png">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href=https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css rel=stylesheet>
        <link href=https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-solid.css rel=stylesheet>
        <link href=https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-regular.css rel=stylesheet>
        <link href=https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-light.css rel=stylesheet>
        <link href="/media/styles/app.css" rel="stylesheet">
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <script>
            let user;

            function oauthSignIn() {
                // Google's OAuth 2.0 endpoint for requesting an access token
                var oauth2Endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';

                // Create <form> element to submit parameters to OAuth 2.0 endpoint.
                var form = document.createElement('form');
                form.setAttribute('method', 'GET'); // Send as a GET request.
                form.setAttribute('action', oauth2Endpoint);

                // Parameters to pass to OAuth 2.0 endpoint.
                var params = {'client_id': '457514265065-lebcoj1o48gv3macbic8nge3tpp59k3f.apps.googleusercontent.com',
                    'redirect_uri': 'https://markizagroup.sk',
                    'response_type': 'token',
                    'scope': 'https://www.googleapis.com/auth/drive.metadata.readonly',
                    'include_granted_scopes': 'true',
                    'state': 'pass-through value'};

                // Add form parameters as hidden input values.
                for (var p in params) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', p);
                    input.setAttribute('value', params[p]);
                    form.appendChild(input);
                }

                // Add form to page and submit it to open the OAuth 2.0 endpoint.
                document.body.appendChild(form);
                form.submit();
            }

            function initializeGsi() {

                if (!window.google) {
                    console.log('no goole');
                    return;
                }
                window.google.accounts.id.initialize({
                    client_id: '457514265065-lebcoj1o48gv3macbic8nge3tpp59k3f.apps.googleusercontent.com',
                    callback: handleGoogleSignIn
                });
            }

            function handleGoogleSignIn(res) {
                if (!res.clientId || !res.credential) {
                    console.log('no res');
                    return;
                }

                console.log('logged');
                console.log(res);
                user = res.data?.login.user;
                console.log(user);
            }
            window.onload = function () {
                initializeGsi();
            };
            if ('Notification' in window) {
                if (Notification.permission !== 'granted') {
                    Notification.requestPermission();
                }
            }
        </script>
    </head>
    <body>

        <!-- HEADER FRAGMENT -->
        <div class="headerFragment" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
                <div class="imgage">
                    <i class="fa-duotone fa-circle-user userImage partnerImage"></i>
                </div>
                <div id="userListSection" style="display: none;">
                    <div id="userList">
                        <select class="form-select"></select>
                    </div>
                </div>
                <i class="fa-solid fa-refresh icon-button reloadApp"></i>
                <i class="fa-solid fa-bell icon-button shareButton"></i>
                <i class="fa-solid fa-sign-out icon-button signOut"></i>
            </div>
        </div>



        <div class="container">

            <!-- SIGN FRAGMENT -->
            <div class="signFragment" style="display:none">
                <div><h1 class="mb-0">NoAds Messenger</h1></div>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link" id="nav-google-tab" data-bs-toggle="tab" data-bs-target="#nav-google" type="button" role="tab" aria-controls="nav-google" aria-selected="false">Google</button>
                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Login</button>
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Registration</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade" id="nav-google" role="tabpanel" aria-labelledby="nav-google-tab" tabindex="0">
                        <div id="google">
                            <div class="mb-3">
                                <div id="g_id_onload"
                                     data-client_id="457514265065-lebcoj1o48gv3macbic8nge3tpp59k3f.apps.googleusercontent.com"
                                     data-context="signin"
                                     data-ux_mode="button"
                                     data-state_cookie_domain="https://markizagroup.sk"
                                     data-login_uri="https://markizagroup.sk"
                                     data-itp_support="true">
                                </div>
                                <div class="g_id_signin"
                                     data-type="standard"
                                     data-shape="rectangular"
                                     data-theme="outline"
                                     data-text="signin_with"
                                     data-size="large"
                                     data-logo_alignment="left">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                        <div id="sectionLogin">
                            <div class="mb-3">
                                <input type="text" class="form-control email" placeholder="email">
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control password" placeholder="password">
                            </div>
                            <button id="login" class="btn btn-primary">Log in</button>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                        <div id="sectionRegistration">
                            <div class="mb-3">
                                <input type="text" class="form-control name" placeholder="name">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control email" placeholder="email">
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control password" placeholder="password">
                            </div>
                            <button id="register" class="btn btn-primary">Register</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHAT_MESSAGES FRAGMENT -->
            <div class="card-body">
                <div id="chat-messages" style="display: none;"></div>
            </div>
        </div>

        <!-- SEND MESSAGE FRAGMENT -->
        <div id="messageFragment" style="display: none">
            <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3 scrollDown">
                <div class="imgage">
                    <i class="fa-duotone fa-circle-a userImage meImage"></i>
                </div>
                <input type="text" class="form-control form-control-md" id="message" placeholder="Type message">
                <a class="ms-3" id="send"><i class="fas fa-paper-plane"></i></a>
            </div>
        </div>

        <!-- Pripojenie Bootstrap 5 JS (pred tým musíte pripojiť jQuery a Popper.js) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
        <script src="/media/scripts/app.js"></script>
    </body>
</html>

