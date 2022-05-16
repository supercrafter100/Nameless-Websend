{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$WEBSEND}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$WEBSEND}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">

                        <!-- Success and Error Messages -->
                        {include file='includes/alerts.tpl'}


                        <!-- Websend hooks -->
                        <h5 style="display: inline">{$AVAILABLE_HOOKS}</h5>
                        <div class="float-md-right">
                            <a class="btn btn-primary" href="{$BACK_LINK}">{$BACK}</a>
                        </div>

                        <hr />

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{$HOOK}</th>
                                    <th>{$STATUS}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach from=$HOOKS item=hook}
                                    <tr>
                                        <td><a href="{$hook.link}">{$hook.description}</a></td>
                                        <td>
                                            {if $hook.enabled}
                                                <span class="badge badge-success">{$ENABLED}</span>
                                            {else}
                                                <span class="badge badge-danger">{$DISABLED}</span>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>


<!-- Scripts -->
{include file='scripts.tpl'}
</body>