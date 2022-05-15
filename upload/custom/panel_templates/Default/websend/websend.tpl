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
                                <div id="terminal" class="ui centered card form-control" style="height: calc(100vh - 200px); ">
                                    <div class="card-header">
                                        <h5 class="d-flex justify-content-between">Server Console</h5>
                                    </div>
                                    <div id="console" class="card-body" style="background-color: hsla(0, 0%, 10%, 1.00); white-space: pre-wrap; overflow-y: scroll; overflow-x: hidden; height: 100%; font-size: 0.8em; font-family: monospace;"></div>
                                </div>

                                <!-- Spacing -->
                                <div style="height:1rem;"></div>

                                <div id="commandline" class="ui centered card">
                                    <div class="content">
                                        <div class="ui form">
                                            <form id="command_post" action="" method="post">
                                                <div class="field">
                                                    <div class="form-group">
                                                        <label for="command">Enter command (without /)</label>
                                                        <input id="command" name="command" type="text" class="form-control" placeholder="{$COMMAND_LINE}">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="hidden" name="token" value="{$TOKEN}">
                                                        <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
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
        console.innerHTML = response.join('<br />');

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