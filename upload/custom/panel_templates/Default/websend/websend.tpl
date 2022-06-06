{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">
    
    <!-- Wrapper -->
    {include file='sidebar.tpl'}

    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{$WEBSEND}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                                <li class="breadcrumb-item active">{$WEBSEND}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    {include 'includes/alerts.tpl'}

                        <!-- Spacing -->
                        <div style="height:1rem;"></div>

                        <div class="alert alert-info alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            This feature still lacks features and only supports one server. You should configure your minecraft server in staffcp --> integrations --> minecraft --> minecraft servers. The server with ID 1 gets used here. You should also set up server id 1 in the namelessmc plugin
                        </div>

                        <div class="card">
                            <div class="card-body">

                                <!-- Server console -->
                                <div class="card mb-3 terminal">
                                    <div class="card-header terminal-element">
                                        <h5 class="d-flex justify-content-between">Terminal<span class="terminal-status"><div class="circle status-online" id="active-status-indicator"></div></span></h5>
                                    </div>
                                    <div class="card-body terminal-element terminal-logs" id="console">Logs should appear here, it seems something is wrong or no logs have been sent.</div>
                                    <div class="card-body terminal-element terminal-input d-flex justify-content-between">
                                        <span><span id="terminal-container-name">container</span>:~/$ </span>
                                        <div class="terminal-element terminal-input-field">
                                            <form class="terminal-input-form" id="command_post" action="" method="post">
                                                <input type="text" name="command" class="terminal-input-form" placeholder="" autofocus>
                                                <input type="hidden" name="token" value="{$TOKEN}">
                                            </form>
                                        </div>
                                        <div class="terminal-element terminal-input-send" id="command-send-button"><i class="fa fa-paper-plane"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Spacing -->
                        <div style="height:1rem;"></div>
                    </div>
            </section>
        </div>
    </div>
</div>
{include file='footer.tpl'}

<!-- Styles -->
<style>
    .terminal p { padding-bottom: 0; margin-bottom: 0; }
    .terminal-logs, .terminal-input { font-size: 0.8em; font-family: monospace; }
    .terminal-logs { white-space: pre-wrap; overflow-y: scroll; overflow-x: hidden; height: 100%; }
    .terminal-input-field { display: flex; width: 100%; padding: 0px 20px 0px 3px; }
    .terminal-input-form { width: 100%; background-color: inherit; font-size: 1em; color: inherit; border-width: 0px; outline: none; }
    .terminal-input-send { width: 13.17px; height: 13.17px; transform: scale(2.8); display: inline-block; transition: transform 150ms ease; }
    .terminal-input-send:hover { transform: scale(3.4); }
    .terminal-input-send:active { transform: scale(3); }
    .terminal-input-send:focus { outline: none; }
    .terminal-input-send i, .terminal-input-send i:hover { transform: scale(0.5); width: 100%; height: 100%; display: inline-block; }
    .status-online { background-color: #4CAF50; }
    .status-offline { background-color: #F44336; }
    .card-header h5 { margin: 0px; background-color: inherit; }
    .circle { height: 8px; width: 8px; transform: translateY(-3px); border-radius: 50%; display: inline-block; }
    span.terminal-status { text-align: right; }
    .terminal { height: calc(100vh - 200px); }

    .terminal-element.card-header { background-color: #eeeeee }
    .terminal { background-color: #eeeeee; color: black; }
    html.dark .terminal { background-color: #161c25 !important; color: white }
    html.dark .terminal-element.card-header { background-color: #161c25 !important; }
</style>

{include file='scripts.tpl'}

<script>
    {literal}
    async function makeRequest() {

        const response = await fetch('{/literal}{$CONSOLE_URL}{literal}').then((res) => res.json()).then((res) => res.content);
        const console = document.getElementById('console');

        // Check if user is scrolled up
        const isScrolledToBottom = console.scrollHeight - console.clientHeight + 1;
        console.innerHTML = response.map((line) => `<p class="console">${line}</p>`).join('');

        if (isScrolledToBottom) {
            console.scrollTop = console.scrollHeight;
        }
    }

    makeRequest();

    const interval = {/literal}{$REQUEST_INTERVAL}{literal} * 1000;
    setInterval(makeRequest, interval);

    const form = document.getElementById('command_post');
    form.addEventListener('submit',function (e) {
        e.preventDefault();
        sendCommand();
    });

    const button = document.getElementById('command-send-button');
    button.addEventListener('click', function (e) {
        e.preventDefault();
        sendCommand()
    });

    function sendCommand() {
        const formData = new FormData(form);
        fetch(document.location.href, {
            method: 'POST',
            body: formData
        })
        form.reset();

        $('body').toast({
            showIcon: 'fa-solid fa-check move-right',
            message: 'Command sent!',
            class: 'success',
            progressUp: true,
            displayTime: 6000,
            showProgress: 'bottom',
            pauseOnHover: false,
            position: 'bottom left',
        });
    }
    {/literal}
</script>

</body>

</html>