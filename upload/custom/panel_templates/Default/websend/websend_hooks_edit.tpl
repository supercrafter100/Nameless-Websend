{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

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

                        <h5 style="display: inline"><strong>{$HOOK_DESCRIPTION}</strong></h5>

                        <div class="float-md-right">
                            <a class="btn btn-primary" href="{$BACK_LINK}">{$BACK}</a>
                        </div>

                        <hr />

                        <!-- Form -->
                        <form action="" method="post">
                            <div class="form-group custom-control custom-switch">
                                <input type="hidden" name="enable_hook" value="0">
                                <input value="1" type="checkbox" name="enable_hook" id="inputEnable" class="custom-control-input" {if $HOOK_ENABLED}checked {/if} />
                                <label class="custom-control-label" for="inputEnable">{$ENABLE_HOOK}</label>
                            </div>
                            <div class="form-group" id="command-group">
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
                                <!-- Commands get automatically put here-->


                            </div>
                            <div class="form-group btn-group">
                                <button class="btn btn-success" type="button" onclick="addCommand()">Add command</button>
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>
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

<script>
    $(document).ready(() => {

        // Load existing commands
        const cmds = `{$COMMANDS_VALUE}`;
        if (cmds.length > 0) {
            const cmds_array = cmds.trim().split("{$PHP_EOL}");

            for (let i = 0; i < cmds_array.length; i++) {
                addCommand(cmds_array[i]);
            }
        } else {
            addCommand()
        }
    })

    function addCommand(command = "") {
        const $cmdGroup = $('#command-group');
        const cmdAmount = $cmdGroup.find('input').length;

        {literal}
        const label = `<label for="cmd${cmdAmount + 1}" id="cmd${cmdAmount + 1}-label">Command ${cmdAmount + 1}</label>`;
        const input = ` <div class="input-group">
                            <input type="text" id="cmd${cmdAmount + 1}" name="cmd${cmdAmount + 1}" class="form-control">
                            <button class="btn btn-outline-danger" type="button" onclick="removeCommand(${cmdAmount + 1})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>`;

        const element = `<div class="form-group commandElement">${label}${input}</div>`;
        $cmdGroup.append(element);

        $(`#cmd${cmdAmount + 1}`).val(command);
        {/literal}
    }

    function removeCommand(id) {
        {literal}
        $(`#cmd${id}`).parent().parent().remove();
        {/literal}
        const cmdGroup = $('#command-group');
        const cmds = cmdGroup.find('input');

        const cmds_array = [];
        for (let i = 0; i < cmds.length; i++) {
            cmds_array.push($(cmds[i]).val());
        }
        $('.commandElement').remove();
        for (const cmd of cmds_array) {
            addCommand(cmd);
        }
    }


</script>
</body>