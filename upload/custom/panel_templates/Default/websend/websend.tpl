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
                    {if isset($NEW_UPDATE)}
                    {if $NEW_UPDATE_URGENT eq true}
                    <div class="alert alert-danger">
                        {else}
                        <div class="alert alert-primary alert-dismissible" id="updateAlert">
                            <button type="button" class="close" id="closeUpdate" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {/if}
                            {$NEW_UPDATE}
                            <br />
                            <a href="{$UPDATE_LINK}" class="btn btn-primary" style="text-decoration:none">{$UPDATE}</a>
                            <hr />
                            {$CURRENT_VERSION}
                            <br />
                            {$NEW_VERSION}
                    </div>
                        {/if}

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
                                    <div class="card-body terminal-element terminal-logs" id="console">Logs should appear here, it seems something is wrong.</div>
                                    <div class="card-body terminal-element terminal-input d-flex justify-content-between">
                                        <span><span id="terminal-container-name">container</span>:~/$ </span>
                                        <div class="terminal-element terminal-input-field">
                                            <form id="command_post" action="" method="post">
                                                <input type="text" name="command" class="terminal-input-form" placeholder="" autofocus>
                                                <input type="hidden" name="token" value="{$TOKEN}">
                                            </form>
                                        </div>
                                        <div class="terminal-element terminal-input-send" id="command-send-button" onClick="javascript:this.parentNode.submit()"><i class="fa fa-paper-plane"></i></div>
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
<!-- ./wrapper -->

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
        let formData = new FormData(form);
        fetch(document.location.href, {
            method: 'POST',
            body: formData
        })
        form.reset();
    });
    {/literal}
</script>

</body>