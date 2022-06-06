{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
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
                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="maxConsoleRecords">{$TERM_CONSOLE_MAX_LINES}</label>
                                        <input type="number" class="form-control" id="maxConsoleRecords" name="maxConsoleRecords"
                                               placeholder="{$TERM_CONSOLE_MAX_LINES}" value="{$SETTINGS_CONSOLE_MAX_LINES}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="consoleRequestInterval">{$TERM_CONSOLE_REQUEST_INTERVAL}</label>
                                        <input type="number" class="form-control" id="consoleRequestInterval" name="consoleRequestInterval"
                                            placeholder="{$TERM_CONSOLE_REQUEST_INTERVAL}" value="{$SETTINGS_CONSOLE_REQUEST_INTERVAL}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="maxDisplayedRecords">{$TERM_MAX_DISPLAYED_RECORDS}</label>
                                        <input type="number" class="form-control" id="maxDisplayedRecords" name="maxDisplayedRecords"
                                               placeholder="{$TERM_MAX_DISPLAYED_RECORDS}" value="{$SETTINGS_MAX_DISPLAYED_RECORDS}">
                                    </div>
                                </div>
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