{include file='header.tpl'}

<body id="page-top">

<div id="wrapper">

    {include file='sidebar.tpl'}

    <div id="content-wrapper" class="d-flex flex-column">

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
                        {$CURRENT_VERSION}<br />
                        {$NEW_VERSION}
                    </div>
                    {/if}

                    <div class="card">
                        <div class="card-body">
                            {if isset($SUCCESS)}
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h5><i class="icon fa fa-check"></i> {$SUCCESS_TITLE}</h5>
                                    {$SUCCESS}
                                </div>
                            {/if}

                            {if isset($ERRORS) && count($ERRORS)}
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> {$ERRORS_TITLE}</h5>
                                    <ul>
                                        {foreach from=$ERRORS item=error}
                                            <li>{$error}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            <strong>{$HOOK_DESCRIPTION}</strong>

                            <div class="float-md-right">
                                <a class="btn btn-primary" href="{$BACK_LINK}">{$BACK}</a>
                            </div>

                            <hr />

                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="inputEnable">{$ENABLE_HOOK}</label>
                                    <input type="checkbox" name="enable_hook" id="inputEnable" class="js-switch" {if $HOOK_ENABLED}checked {/if}/>
                                </div>
                                <div class="form-group">
                                    <div class="callout callout-info">
                                        <h5><i class="icon fa fa-info-circle"></i> {$INFO}</h5>
                                        {$COMMANDS_INFO}
                                        {if count($HOOKS)}
                                            <ul>
                                                {foreach from=$HOOKS key=param item=desc}
                                                    <li><strong>{literal}{{/literal}{$param}{literal}}{/literal}</strong> - {$desc}</li>
                                                {/foreach}
                                            </ul>
                                        {/if}
                                    </div>
                                    <label for="inputCommands">{$COMMANDS}</label>
                                    <textarea id="inputCommands" name="commands" class="form-control">{$COMMANDS_VALUE}</textarea>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="token" value="{$TOKEN}">
                                    <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                                </div>
                            </form>

                        </div>
                    </div>

                    <!-- Spacing -->
                    <div style="height:1rem;"></div>

                </div>
        </section>
    </div>


</div>
<!-- ./wrapper -->

{include file='footer.tpl'}
{include file='scripts.tpl'}

</body>
</html>