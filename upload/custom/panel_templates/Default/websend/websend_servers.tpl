{include file='header.tpl'}

<body id="page-top">

    {include file='sidebar.tpl'}

    <div id="content-wrapper" class="d-flex flex-column">

        {include file="navbar.tpl"}
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

                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {$INFO}
                </div>

                <div class="card shadow mb-4">
                    <div class="card-body">
                        {if isset($NO_SERVERS)}
                        <hr />
                        <p>{$NO_SERVERS}</p>
                        {else}
                        <div class="table table-responsive">
                            <table class="table table-striped">
                                <tbody id="sortable">
                                    {foreach from=$SERVERS item=server}
                                        <tr data-id="{$server.id}">
                                            <td><strong>{$server.name}</strong> ({$server.server_id})</td>
                                            <td>
                                                <div class="float-md-right">
                                                    <a href="{$server.edit_link}" class="btn btn-info">{$VIEW}</a>
                                                </div>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </section>
    </div>
<!-- ./wrapper -->

{include file='footer.tpl'}
{include file='scripts.tpl'}
</body>
</html>