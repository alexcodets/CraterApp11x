<?php

return  [
  0 =>
   [
    'GET' =>
     [
      '' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'index',
      ],
      '/' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'index',
      ],
      '/recent-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'reloadRecentTablesList',
      ],
      '/git-revision' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'gitRevision',
      ],
      '/browse-foreigners' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\BrowseForeignersController',
        1 => 'index',
      ],
      '/changelog' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ChangeLogController',
        1 => 'index',
      ],
      '/check-relations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\CheckRelationsController',
        1 => 'index',
      ],
      '/database/central-columns' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\CentralColumnsController',
        1 => 'index',
      ],
      '/database/data-dictionary' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\DataDictionaryController',
        1 => 'index',
      ],
      '/database/designer' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\DesignerController',
        1 => 'index',
      ],
      '/database/events' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\EventsController',
        1 => 'index',
      ],
      '/database/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\ExportController',
        1 => 'index',
      ],
      '/database/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\ImportController',
        1 => 'index',
      ],
      '/database/multi-table-query' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\MultiTableQueryController',
        1 => 'index',
      ],
      '/database/multi-table-query/tables' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\MultiTableQueryController',
        1 => 'table',
      ],
      '/database/operations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\OperationsController',
        1 => 'index',
      ],
      '/database/qbe' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\QueryByExampleController',
        1 => 'index',
      ],
      '/database/routines' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\RoutinesController',
        1 => 'index',
      ],
      '/database/search' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\SearchController',
        1 => 'index',
      ],
      '/database/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\SqlController',
        1 => 'index',
      ],
      '/database/structure' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'index',
      ],
      '/database/structure/favorite-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'addRemoveFavoriteTablesAction',
      ],
      '/database/structure/real-row-count' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'handleRealRowCountRequestAction',
      ],
      '/database/tracking' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\TrackingController',
        1 => 'index',
      ],
      '/database/triggers' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\TriggersController',
        1 => 'index',
      ],
      '/error-report' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ErrorReportController',
        1 => 'index',
      ],
      '/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportController',
        1 => 'index',
      ],
      '/export/check-time-out' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportController',
        1 => 'checkTimeOut',
      ],
      '/gis-data-editor' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\GisDataEditorController',
        1 => 'index',
      ],
      '/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ImportController',
        1 => 'index',
      ],
      '/import-status' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ImportStatusController',
        1 => 'index',
      ],
      '/license' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\LicenseController',
        1 => 'index',
      ],
      '/lint' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\LintController',
        1 => 'index',
      ],
      '/logout' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\LogoutController',
        1 => 'index',
      ],
      '/navigation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\NavigationController',
        1 => 'index',
      ],
      '/normalization' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\NormalizationController',
        1 => 'index',
      ],
      '/phpinfo' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\PhpInfoController',
        1 => 'index',
      ],
      '/preferences/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\ExportController',
        1 => 'index',
      ],
      '/preferences/features' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\FeaturesController',
        1 => 'index',
      ],
      '/preferences/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\ImportController',
        1 => 'index',
      ],
      '/preferences/main-panel' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\MainPanelController',
        1 => 'index',
      ],
      '/preferences/manage' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\ManageController',
        1 => 'index',
      ],
      '/preferences/navigation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\NavigationController',
        1 => 'index',
      ],
      '/preferences/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\SqlController',
        1 => 'index',
      ],
      '/preferences/two-factor' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\TwoFactorController',
        1 => 'index',
      ],
      '/schema-export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SchemaExportController',
        1 => 'index',
      ],
      '/server/binlog' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\BinlogController',
        1 => 'index',
      ],
      '/server/collations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\CollationsController',
        1 => 'index',
      ],
      '/server/databases' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\DatabasesController',
        1 => 'index',
      ],
      '/server/engines' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\EnginesController',
        1 => 'index',
      ],
      '/server/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\ExportController',
        1 => 'index',
      ],
      '/server/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\ImportController',
        1 => 'index',
      ],
      '/server/plugins' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\PluginsController',
        1 => 'index',
      ],
      '/server/privileges' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\PrivilegesController',
        1 => 'index',
      ],
      '/server/replication' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\ReplicationController',
        1 => 'index',
      ],
      '/server/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\SqlController',
        1 => 'index',
      ],
      '/server/status' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\StatusController',
        1 => 'index',
      ],
      '/server/status/advisor' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\AdvisorController',
        1 => 'index',
      ],
      '/server/status/monitor' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\MonitorController',
        1 => 'index',
      ],
      '/server/status/processes' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\ProcessesController',
        1 => 'index',
      ],
      '/server/status/queries' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\QueriesController',
        1 => 'index',
      ],
      '/server/status/variables' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\VariablesController',
        1 => 'index',
      ],
      '/server/user-groups' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\UserGroupsController',
        1 => 'index',
      ],
      '/server/variables' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\VariablesController',
        1 => 'index',
      ],
      '/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'index',
      ],
      '/sql/get-default-fk-check-value' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'getDefaultForeignKeyCheckValue',
      ],
      '/table/add-field' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\AddFieldController',
        1 => 'index',
      ],
      '/table/change' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ChangeController',
        1 => 'index',
      ],
      '/table/chart' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ChartController',
        1 => 'index',
      ],
      '/table/create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\CreateController',
        1 => 'index',
      ],
      '/table/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ExportController',
        1 => 'index',
      ],
      '/table/find-replace' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\FindReplaceController',
        1 => 'index',
      ],
      '/table/get-field' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\GetFieldController',
        1 => 'index',
      ],
      '/table/gis-visualization' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\GisVisualizationController',
        1 => 'index',
      ],
      '/table/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ImportController',
        1 => 'index',
      ],
      '/table/indexes' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\IndexesController',
        1 => 'index',
      ],
      '/table/indexes/rename' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\IndexesController',
        1 => 'indexRename',
      ],
      '/table/operations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\OperationsController',
        1 => 'index',
      ],
      '/table/recent-favorite' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\RecentFavoriteController',
        1 => 'index',
      ],
      '/table/relation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\RelationController',
        1 => 'index',
      ],
      '/table/replace' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ReplaceController',
        1 => 'index',
      ],
      '/table/search' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\SearchController',
        1 => 'index',
      ],
      '/table/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\SqlController',
        1 => 'index',
      ],
      '/table/structure' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'index',
      ],
      '/table/structure/change' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'change',
      ],
      '/table/tracking' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\TrackingController',
        1 => 'index',
      ],
      '/table/triggers' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\TriggersController',
        1 => 'index',
      ],
      '/table/zoom-search' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ZoomSearchController',
        1 => 'index',
      ],
      '/themes' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ThemesController',
        1 => 'index',
      ],
      '/transformation/overview' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\TransformationOverviewController',
        1 => 'index',
      ],
      '/transformation/wrapper' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\TransformationWrapperController',
        1 => 'index',
      ],
      '/user-password' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\UserPasswordController',
        1 => 'index',
      ],
      '/version-check' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\VersionCheckController',
        1 => 'index',
      ],
      '/view/create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ViewCreateController',
        1 => 'index',
      ],
      '/view/operations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ViewOperationsController',
        1 => 'index',
      ],
    ],
    'POST' =>
     [
      '' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'index',
      ],
      '/' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'index',
      ],
      '/set-theme' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'setTheme',
      ],
      '/collation-connection' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'setCollationConnection',
      ],
      '/recent-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'reloadRecentTablesList',
      ],
      '/git-revision' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\HomeController',
        1 => 'gitRevision',
      ],
      '/browse-foreigners' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\BrowseForeignersController',
        1 => 'index',
      ],
      '/check-relations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\CheckRelationsController',
        1 => 'index',
      ],
      '/columns' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ColumnController',
        1 => 'all',
      ],
      '/config/get' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ConfigController',
        1 => 'get',
      ],
      '/config/set' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ConfigController',
        1 => 'set',
      ],
      '/database/central-columns' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\CentralColumnsController',
        1 => 'index',
      ],
      '/database/central-columns/populate' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\CentralColumnsController',
        1 => 'populateColumns',
      ],
      '/database/designer' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\DesignerController',
        1 => 'index',
      ],
      '/database/events' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\EventsController',
        1 => 'index',
      ],
      '/database/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\ExportController',
        1 => 'index',
      ],
      '/database/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\ImportController',
        1 => 'index',
      ],
      '/database/multi-table-query/query' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\MultiTableQueryController',
        1 => 'displayResults',
      ],
      '/database/operations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\OperationsController',
        1 => 'index',
      ],
      '/database/operations/collation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\OperationsController',
        1 => 'collation',
      ],
      '/database/qbe' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\QueryByExampleController',
        1 => 'index',
      ],
      '/database/routines' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\RoutinesController',
        1 => 'index',
      ],
      '/database/search' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\SearchController',
        1 => 'index',
      ],
      '/database/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\SqlController',
        1 => 'index',
      ],
      '/database/sql/autocomplete' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\SqlAutoCompleteController',
        1 => 'index',
      ],
      '/database/sql/format' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\SqlFormatController',
        1 => 'index',
      ],
      '/database/structure' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'index',
      ],
      '/database/structure/add-prefix' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'addPrefix',
      ],
      '/database/structure/add-prefix-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'addPrefixTable',
      ],
      '/database/structure/central-columns-add' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'centralColumnsAdd',
      ],
      '/database/structure/central-columns-make-consistent' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'centralColumnsMakeConsistent',
      ],
      '/database/structure/central-columns-remove' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'centralColumnsRemove',
      ],
      '/database/structure/change-prefix-form' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'changePrefixForm',
      ],
      '/database/structure/copy-form' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'copyForm',
      ],
      '/database/structure/copy-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'copyTable',
      ],
      '/database/structure/copy-table-with-prefix' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'copyTableWithPrefix',
      ],
      '/database/structure/drop-form' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'dropForm',
      ],
      '/database/structure/drop-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'dropTable',
      ],
      '/database/structure/empty-form' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'emptyForm',
      ],
      '/database/structure/empty-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'emptyTable',
      ],
      '/database/structure/favorite-table' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'addRemoveFavoriteTablesAction',
      ],
      '/database/structure/real-row-count' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'handleRealRowCountRequestAction',
      ],
      '/database/structure/replace-prefix' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'replacePrefix',
      ],
      '/database/structure/show-create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\StructureController',
        1 => 'showCreate',
      ],
      '/database/tracking' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\TrackingController',
        1 => 'index',
      ],
      '/database/triggers' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\TriggersController',
        1 => 'index',
      ],
      '/databases' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\DatabaseController',
        1 => 'all',
      ],
      '/error-report' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ErrorReportController',
        1 => 'index',
      ],
      '/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportController',
        1 => 'index',
      ],
      '/export/tables' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Database\\ExportController',
        1 => 'tables',
      ],
      '/export/template/create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportTemplateController',
        1 => 'create',
      ],
      '/export/template/delete' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportTemplateController',
        1 => 'delete',
      ],
      '/export/template/load' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportTemplateController',
        1 => 'load',
      ],
      '/export/template/update' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ExportTemplateController',
        1 => 'update',
      ],
      '/gis-data-editor' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\GisDataEditorController',
        1 => 'index',
      ],
      '/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ImportController',
        1 => 'index',
      ],
      '/import-status' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ImportStatusController',
        1 => 'index',
      ],
      '/lint' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\LintController',
        1 => 'index',
      ],
      '/logout' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\LogoutController',
        1 => 'index',
      ],
      '/navigation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\NavigationController',
        1 => 'index',
      ],
      '/normalization' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\NormalizationController',
        1 => 'index',
      ],
      '/preferences/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\ExportController',
        1 => 'index',
      ],
      '/preferences/features' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\FeaturesController',
        1 => 'index',
      ],
      '/preferences/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\ImportController',
        1 => 'index',
      ],
      '/preferences/main-panel' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\MainPanelController',
        1 => 'index',
      ],
      '/preferences/manage' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\ManageController',
        1 => 'index',
      ],
      '/preferences/navigation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\NavigationController',
        1 => 'index',
      ],
      '/preferences/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\SqlController',
        1 => 'index',
      ],
      '/preferences/two-factor' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Preferences\\TwoFactorController',
        1 => 'index',
      ],
      '/schema-export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SchemaExportController',
        1 => 'index',
      ],
      '/server/binlog' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\BinlogController',
        1 => 'index',
      ],
      '/server/databases' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\DatabasesController',
        1 => 'index',
      ],
      '/server/databases/create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\DatabasesController',
        1 => 'create',
      ],
      '/server/databases/destroy' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\DatabasesController',
        1 => 'destroy',
      ],
      '/server/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\ExportController',
        1 => 'index',
      ],
      '/server/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\ImportController',
        1 => 'index',
      ],
      '/server/privileges' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\PrivilegesController',
        1 => 'index',
      ],
      '/server/replication' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\ReplicationController',
        1 => 'index',
      ],
      '/server/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\SqlController',
        1 => 'index',
      ],
      '/server/status/monitor/chart' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\MonitorController',
        1 => 'chartingData',
      ],
      '/server/status/monitor/slow-log' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\MonitorController',
        1 => 'logDataTypeSlow',
      ],
      '/server/status/monitor/general-log' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\MonitorController',
        1 => 'logDataTypeGeneral',
      ],
      '/server/status/monitor/log-vars' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\MonitorController',
        1 => 'loggingVars',
      ],
      '/server/status/monitor/query' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\MonitorController',
        1 => 'queryAnalyzer',
      ],
      '/server/status/processes' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\ProcessesController',
        1 => 'index',
      ],
      '/server/status/processes/refresh' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\ProcessesController',
        1 => 'refresh',
      ],
      '/server/status/variables' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\VariablesController',
        1 => 'index',
      ],
      '/server/user-groups' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Server\\UserGroupsController',
        1 => 'index',
      ],
      '/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'index',
      ],
      '/sql/get-relational-values' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'getRelationalValues',
      ],
      '/sql/get-enum-values' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'getEnumValues',
      ],
      '/sql/get-set-values' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'getSetValues',
      ],
      '/sql/set-column-preferences' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\SqlController',
        1 => 'setColumnOrderOrVisibility',
      ],
      '/table/add-field' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\AddFieldController',
        1 => 'index',
      ],
      '/table/change' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ChangeController',
        1 => 'index',
      ],
      '/table/change/rows' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ChangeController',
        1 => 'rows',
      ],
      '/table/chart' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ChartController',
        1 => 'index',
      ],
      '/table/create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\CreateController',
        1 => 'index',
      ],
      '/table/delete/confirm' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\DeleteController',
        1 => 'confirm',
      ],
      '/table/delete/rows' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\DeleteController',
        1 => 'rows',
      ],
      '/table/export' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ExportController',
        1 => 'index',
      ],
      '/table/export/rows' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ExportController',
        1 => 'rows',
      ],
      '/table/find-replace' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\FindReplaceController',
        1 => 'index',
      ],
      '/table/get-field' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\GetFieldController',
        1 => 'index',
      ],
      '/table/gis-visualization' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\GisVisualizationController',
        1 => 'index',
      ],
      '/table/import' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ImportController',
        1 => 'index',
      ],
      '/table/indexes' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\IndexesController',
        1 => 'index',
      ],
      '/table/indexes/rename' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\IndexesController',
        1 => 'indexRename',
      ],
      '/table/maintenance/analyze' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\MaintenanceController',
        1 => 'analyze',
      ],
      '/table/maintenance/check' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\MaintenanceController',
        1 => 'check',
      ],
      '/table/maintenance/checksum' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\MaintenanceController',
        1 => 'checksum',
      ],
      '/table/maintenance/optimize' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\MaintenanceController',
        1 => 'optimize',
      ],
      '/table/maintenance/repair' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\MaintenanceController',
        1 => 'repair',
      ],
      '/table/partition/analyze' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'analyze',
      ],
      '/table/partition/check' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'check',
      ],
      '/table/partition/drop' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'drop',
      ],
      '/table/partition/optimize' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'optimize',
      ],
      '/table/partition/rebuild' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'rebuild',
      ],
      '/table/partition/repair' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'repair',
      ],
      '/table/partition/truncate' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\PartitionController',
        1 => 'truncate',
      ],
      '/table/operations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\OperationsController',
        1 => 'index',
      ],
      '/table/recent-favorite' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\RecentFavoriteController',
        1 => 'index',
      ],
      '/table/relation' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\RelationController',
        1 => 'index',
      ],
      '/table/replace' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ReplaceController',
        1 => 'index',
      ],
      '/table/search' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\SearchController',
        1 => 'index',
      ],
      '/table/sql' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\SqlController',
        1 => 'index',
      ],
      '/table/structure' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'index',
      ],
      '/table/structure/add-key' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'addKey',
      ],
      '/table/structure/browse' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'browse',
      ],
      '/table/structure/central-columns-add' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'addToCentralColumns',
      ],
      '/table/structure/central-columns-remove' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'removeFromCentralColumns',
      ],
      '/table/structure/change' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'change',
      ],
      '/table/structure/drop' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'drop',
      ],
      '/table/structure/drop-confirm' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'dropConfirm',
      ],
      '/table/structure/fulltext' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'fulltext',
      ],
      '/table/structure/index' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'addIndex',
      ],
      '/table/structure/move-columns' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'moveColumns',
      ],
      '/table/structure/partitioning' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'partitioning',
      ],
      '/table/structure/primary' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'primary',
      ],
      '/table/structure/reserved-word-check' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'reservedWordCheck',
      ],
      '/table/structure/save' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'save',
      ],
      '/table/structure/spatial' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'spatial',
      ],
      '/table/structure/unique' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\StructureController',
        1 => 'unique',
      ],
      '/table/tracking' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\TrackingController',
        1 => 'index',
      ],
      '/table/triggers' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\TriggersController',
        1 => 'index',
      ],
      '/table/zoom-search' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\Table\\ZoomSearchController',
        1 => 'index',
      ],
      '/tables' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\TableController',
        1 => 'all',
      ],
      '/transformation/overview' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\TransformationOverviewController',
        1 => 'index',
      ],
      '/transformation/wrapper' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\TransformationWrapperController',
        1 => 'index',
      ],
      '/user-password' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\UserPasswordController',
        1 => 'index',
      ],
      '/version-check' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\VersionCheckController',
        1 => 'index',
      ],
      '/view/create' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ViewCreateController',
        1 => 'index',
      ],
      '/view/operations' =>
       [
        0 => 'PhpMyAdmin\\Controllers\\ViewOperationsController',
        1 => 'index',
      ],
    ],
  ],
  1 =>
   [
    'GET' =>
     [
      0 =>
       [
        'regex' => '~^(?|/server/engines/([^/]+)|/server/engines/([^/]+)/([^/]+)|/server/variables/get/([^/]+)()())$~',
        'routeMap' =>
         [
          2 =>
           [
            0 =>
             [
              0 => 'PhpMyAdmin\\Controllers\\Server\\EnginesController',
              1 => 'show',
            ],
            1 =>
             [
              'engine' => 'engine',
            ],
          ],
          3 =>
           [
            0 =>
             [
              0 => 'PhpMyAdmin\\Controllers\\Server\\EnginesController',
              1 => 'show',
            ],
            1 =>
             [
              'engine' => 'engine',
              'page' => 'page',
            ],
          ],
          4 =>
           [
            0 =>
             [
              0 => 'PhpMyAdmin\\Controllers\\Server\\VariablesController',
              1 => 'getValue',
            ],
            1 =>
             [
              'name' => 'name',
            ],
          ],
        ],
      ],
    ],
    'POST' =>
     [
      0 =>
       [
        'regex' => '~^(?|/server/status/processes/kill/(\\d+)|/server/variables/set/([^/]+)())$~',
        'routeMap' =>
         [
          2 =>
           [
            0 =>
             [
              0 => 'PhpMyAdmin\\Controllers\\Server\\Status\\ProcessesController',
              1 => 'kill',
            ],
            1 =>
             [
              'id' => 'id',
            ],
          ],
          3 =>
           [
            0 =>
             [
              0 => 'PhpMyAdmin\\Controllers\\Server\\VariablesController',
              1 => 'setValue',
            ],
            1 =>
             [
              'name' => 'name',
            ],
          ],
        ],
      ],
    ],
  ],
];
