<?php

declare(strict_types=1);

use Neucore\Controller\App\ApplicationController;
use Neucore\Controller\App\CharController;
use Neucore\Controller\App\TrackingController as AppCorporationController;
use Neucore\Controller\App\EsiController as AppEsiController;
use Neucore\Controller\App\GroupController as AppGroupController;
use Neucore\Controller\PluginController;
use Neucore\Controller\User\AllianceController;
use Neucore\Controller\User\AppController;
use Neucore\Controller\User\AuthController;
use Neucore\Controller\User\CharacterController;
use Neucore\Controller\User\CorporationController;
use Neucore\Controller\User\EsiController;
use Neucore\Controller\User\GroupController;
use Neucore\Controller\User\PlayerController;
use Neucore\Controller\User\RoleController;
use Neucore\Controller\User\ServiceAdminController;
use Neucore\Controller\User\ServiceController;
use Neucore\Controller\User\SettingsController;
use Neucore\Controller\User\SettingsEveLoginController;
use Neucore\Controller\User\StatisticsController;
use Neucore\Controller\User\WatchlistController;

return [
    '/login/{name}'      => ['GET', [AuthController::class, 'login']],
    '/login-callback'    => ['GET', [AuthController::class, 'callback']],

    '/plugin/{id}/{name}'   => ['GET', [PluginController::class, 'request']],

    '/api/user/auth/callback'   => ['GET',  [AuthController::class, 'callback']], // only for backwards compatibility
    '/api/user/auth/result'     => ['GET',  [AuthController::class, 'result']],
    '/api/user/auth/logout'     => ['POST', [AuthController::class, 'logout']],
    '/api/user/auth/csrf-token' => ['GET',  [AuthController::class, 'csrfToken']],

    '/api/app/v1/show' => ['GET', [ApplicationController::class, 'showV1']],

    '/api/app/v1/main/{cid}'                           => ['GET',  [CharController::class, 'mainV1']],
    '/api/app/v2/main/{cid}'                           => ['GET',  [CharController::class, 'mainV2']],
    '/api/app/v1/player/{characterId}'                 => ['GET',  [CharController::class, 'playerV1']],
    '/api/app/v1/characters/{characterId}'             => ['GET',  [CharController::class, 'charactersV1']],
    '/api/app/v1/characters'                           => ['POST', [CharController::class, 'charactersBulk']],
    '/api/app/v1/character-list'                       => ['POST', [CharController::class, 'characterListV1']],
    '/api/app/v1/player-chars/{playerId}'              => ['GET',  [CharController::class, 'playerCharactersV1']],
    '/api/app/v1/player-with-characters/{characterId}' => ['GET',  [CharController::class, 'playerWithCharactersV1']],
    '/api/app/v1/removed-characters/{characterId}'     => ['GET',  [CharController::class, 'removedCharactersV1']],
    '/api/app/v1/incoming-characters/{characterId}'    => ['GET',  [CharController::class, 'incomingCharactersV1']],
    '/api/app/v1/corp-players/{corporationId}'         => ['GET',  [CharController::class, 'corporationPlayersV1']],
    '/api/app/v1/corp-characters/{corporationId}'      => ['GET',  [CharController::class, 'corporationCharactersV1']],

    '/api/app/v1/corporation/{id}/member-tracking' => ['GET', [AppCorporationController::class, 'memberTrackingV1']],

    '/api/app/v1/esi/eve-login/{name}/characters'   => ['GET',    [AppEsiController::class, 'eveLoginCharacters']],
    '/api/app/v1/esi[{path:.*}]'                    => ['GET'  => [AppEsiController::class, 'esiV1'],
                                                        'POST' => [AppEsiController::class, 'esiPostV1']],
    '/api/app/v2/esi[{path:.*}]'                    => ['GET'  => [AppEsiController::class, 'esiV2'],
                                                        'POST' => [AppEsiController::class, 'esiPostV2']],

    '/api/app/v1/groups/{cid}'              => ['GET',  [AppGroupController::class, 'groupsV1']],
    '/api/app/v2/groups/{cid}'              => ['GET',  [AppGroupController::class, 'groupsV2']],
    '/api/app/v1/groups'                    => ['POST', [AppGroupController::class, 'groupsBulkV1']],
    '/api/app/v1/corp-groups/{cid}'         => ['GET',  [AppGroupController::class, 'corpGroupsV1']],
    '/api/app/v2/corp-groups/{cid}'         => ['GET',  [AppGroupController::class, 'corpGroupsV2']],
    '/api/app/v1/corp-groups'               => ['POST', [AppGroupController::class, 'corpGroupsBulkV1']],
    '/api/app/v1/alliance-groups/{aid}'     => ['GET',  [AppGroupController::class, 'allianceGroupsV1']],
    '/api/app/v2/alliance-groups/{aid}'     => ['GET',  [AppGroupController::class, 'allianceGroupsV2']],
    '/api/app/v1/alliance-groups'           => ['POST', [AppGroupController::class, 'allianceGroupsBulkV1']],
    '/api/app/v1/groups-with-fallback'      => ['GET',  [AppGroupController::class, 'groupsWithFallbackV1']],
    '/api/app/v1/group-members/{groupId}'   => ['GET',  [AppGroupController::class, 'members']],

    '/api/user/app/all'                                 => ['GET',    [AppController::class, 'all']],
    '/api/user/app/create'                              => ['POST',   [AppController::class, 'create']],
    '/api/user/app/{id}/show'                           => ['GET',    [AppController::class, 'show']],
    '/api/user/app/{id}/rename'                         => ['PUT',    [AppController::class, 'rename']],
    '/api/user/app/{id}/delete'                         => ['DELETE', [AppController::class, 'delete']],
    '/api/user/app/{id}/add-group/{gid}'                => ['PUT',    [AppController::class, 'addGroup']],
    '/api/user/app/{id}/remove-group/{gid}'             => ['PUT',    [AppController::class, 'removeGroup']],
    '/api/user/app/{id}/managers'                       => ['GET',    [AppController::class, 'managers']],
    '/api/user/app/{id}/add-manager/{pid}'              => ['PUT',    [AppController::class, 'addManager']],
    '/api/user/app/{id}/remove-manager/{pid}'           => ['PUT',    [AppController::class, 'removeManager']],
    '/api/user/app/{id}/add-role/{name}'                => ['PUT',    [AppController::class, 'addRole']],
    '/api/user/app/{id}/remove-role/{name}'             => ['PUT',    [AppController::class, 'removeRole']],
    '/api/user/app/{id}/add-eve-login/{eveLoginId}'     => ['PUT',    [AppController::class, 'addEveLogin']],
    '/api/user/app/{id}/remove-eve-login/{eveLoginId}'  => ['PUT',    [AppController::class, 'removeEveLogin']],
    '/api/user/app/{id}/change-secret'                  => ['PUT',    [AppController::class, 'changeSecret']],

    '/api/user/alliance/all'                     => ['GET',  [AllianceController::class, 'all']],
    '/api/user/alliance/with-groups'             => ['GET',  [AllianceController::class, 'withGroups']],
    '/api/user/alliance/add/{id}'                => ['POST', [AllianceController::class, 'add']],
    '/api/user/alliance/{id}/add-group/{gid}'    => ['PUT',  [AllianceController::class, 'addGroup']],
    '/api/user/alliance/{id}/remove-group/{gid}' => ['PUT',  [AllianceController::class, 'removeGroup']],

    '/api/user/character/find-character/{name}' => ['GET', [CharacterController::class, 'findCharacter']],
    '/api/user/character/find-player/{name}'    => ['GET', [CharacterController::class, 'findPlayer']],
    '/api/user/character/show'                  => ['GET', [CharacterController::class, 'show']],
    '/api/user/character/{id}/update'           => ['PUT', [CharacterController::class, 'update']],

    '/api/user/corporation/all'                                  => ['GET',  [CorporationController::class, 'all']],
    '/api/user/corporation/with-groups'                          => ['GET',  [CorporationController::class, 'withGroups']],
    '/api/user/corporation/add/{id}'                             => ['POST', [CorporationController::class, 'add']],
    '/api/user/corporation/{id}/add-group/{gid}'                 => ['PUT',  [CorporationController::class, 'addGroup']],
    '/api/user/corporation/{id}/remove-group/{gid}'              => ['PUT',  [CorporationController::class, 'removeGroup']],
    '/api/user/corporation/{id}/tracking-director'               => ['GET',  [CorporationController::class, 'trackingDirector']],
    '/api/user/corporation/{id}/get-groups-tracking'             => ['GET',  [CorporationController::class, 'getGroupsTracking']],
    '/api/user/corporation/{id}/add-group-tracking/{groupId}'    => ['PUT',  [CorporationController::class, 'addGroupTracking']],
    '/api/user/corporation/{id}/remove-group-tracking/{groupId}' => ['PUT',  [CorporationController::class, 'removeGroupTracking']],
    '/api/user/corporation/tracked-corporations'                 => ['GET',  [CorporationController::class, 'trackedCorporations']],
    '/api/user/corporation/all-tracked-corporations'             => ['GET',  [CorporationController::class, 'allTrackedCorporations']],
    '/api/user/corporation/{id}/members'                         => ['GET',  [CorporationController::class, 'members']],

    '/api/user/esi/request' =>  ['GET'  => [EsiController::class, 'request'],
                                 'POST' => [EsiController::class, 'requestPost']],

    '/api/user/group/all'                             => ['GET',    [GroupController::class, 'all']],
    '/api/user/group/public'                          => ['GET',    [GroupController::class, 'public']],
    '/api/user/group/create'                          => ['POST',   [GroupController::class, 'create']],
    '/api/user/group/{id}/rename'                     => ['PUT',    [GroupController::class, 'rename']],
    '/api/user/group/{id}/update-description'         => ['PUT',    [GroupController::class, 'updateDescription']],
    '/api/user/group/{id}/set-visibility/{choice}'    => ['PUT',    [GroupController::class, 'setVisibility']],
    '/api/user/group/{id}/set-auto-accept/{choice}'   => ['PUT',    [GroupController::class, 'setAutoAccept']],
    '/api/user/group/{id}/set-is-default/{choice}'    => ['PUT',    [GroupController::class, 'setIsDefault']],
    '/api/user/group/{id}/delete'                     => ['DELETE', [GroupController::class, 'delete']],
    '/api/user/group/{id}/managers'                   => ['GET',    [GroupController::class, 'managers']],
    '/api/user/group/{id}/corporations'               => ['GET',    [GroupController::class, 'corporations']],
    '/api/user/group/{id}/alliances'                  => ['GET',    [GroupController::class, 'alliances']],
    '/api/user/group/{id}/required-groups'            => ['GET',    [GroupController::class, 'requiredGroups']],
    '/api/user/group/{id}/add-required/{groupId}'     => ['PUT',    [GroupController::class, 'addRequiredGroup']],
    '/api/user/group/{id}/remove-required/{groupId}'  => ['PUT',    [GroupController::class, 'removeRequiredGroup']],
    '/api/user/group/{id}/forbidden-groups'           => ['GET',    [GroupController::class, 'forbiddenGroups']],
    '/api/user/group/{id}/add-forbidden/{groupId}'    => ['PUT',    [GroupController::class, 'addForbiddenGroup']],
    '/api/user/group/{id}/remove-forbidden/{groupId}' => ['PUT',    [GroupController::class, 'removeForbiddenGroup']],
    '/api/user/group/{id}/add-manager/{pid}'          => ['PUT',    [GroupController::class, 'addManager']],
    '/api/user/group/{id}/remove-manager/{pid}'       => ['PUT',    [GroupController::class, 'removeManager']],
    '/api/user/group/{id}/applications'               => ['GET',    [GroupController::class, 'applications']],
    '/api/user/group/accept-application/{id}'         => ['PUT',    [GroupController::class, 'acceptApplication']],
    '/api/user/group/deny-application/{id}'           => ['PUT',    [GroupController::class, 'denyApplication']],
    '/api/user/group/{id}/add-member/{pid}'           => ['PUT',    [GroupController::class, 'addMember']],
    '/api/user/group/{id}/remove-member/{pid}'        => ['PUT',    [GroupController::class, 'removeMember']],
    '/api/user/group/{id}/members'                    => ['GET',    [GroupController::class, 'members']],

    '/api/user/player/with-characters'             => ['GET',    [PlayerController::class, 'withCharacters']],
    '/api/user/player/without-characters'          => ['GET',    [PlayerController::class, 'withoutCharacters']],
    '/api/user/player/show'                        => ['GET',    [PlayerController::class, 'show']],
    '/api/user/player/{id}/groups-disabled'        => ['GET',    [PlayerController::class, 'groupsDisabledById']],
    '/api/user/player/groups-disabled'             => ['GET',    [PlayerController::class, 'groupsDisabled']],
    '/api/user/player/add-application/{gid}'       => ['PUT',    [PlayerController::class, 'addApplication']],
    '/api/user/player/remove-application/{gid}'    => ['PUT',    [PlayerController::class, 'removeApplication']],
    '/api/user/player/show-applications'           => ['GET',    [PlayerController::class, 'showApplications']],
    '/api/user/player/leave-group/{gid}'           => ['PUT',    [PlayerController::class, 'leaveGroup']],
    '/api/user/player/set-main/{cid}'              => ['PUT',    [PlayerController::class, 'setMain']],
    '/api/user/player/delete-character/{id}'       => ['DELETE', [PlayerController::class, 'deleteCharacter']],
    '/api/user/player/app-managers'                => ['GET',    [PlayerController::class, 'appManagers']],
    '/api/user/player/group-managers'              => ['GET',    [PlayerController::class, 'groupManagers']],
    '/api/user/player/{id}/set-status/{status}'    => ['PUT',    [PlayerController::class, 'setStatus']],
    '/api/user/player/{id}/add-role/{name}'        => ['PUT',    [PlayerController::class, 'addRole']],
    '/api/user/player/{id}/remove-role/{name}'     => ['PUT',    [PlayerController::class, 'removeRole']],
    '/api/user/player/{id}/show'                   => ['GET',    [PlayerController::class, 'showById']],
    '/api/user/player/{id}/characters'             => ['GET',    [PlayerController::class, 'characters']],
    '/api/user/player/group-characters-by-account' => ['POST',   [PlayerController::class, 'groupCharactersByAccount']],
    '/api/user/player/with-role/{name}'            => ['GET',    [PlayerController::class, 'withRole']],
    '/api/user/player/with-status/{name}'          => ['GET',    [PlayerController::class, 'withStatus']],

    '/api/user/role/{roleName}/required-groups'                  => ['GET', [RoleController::class, 'getRequiredGroups']],
    '/api/user/role/{roleName}/add-required-group/{groupId}'     => ['PUT', [RoleController::class, 'addRequiredGroups']],
    '/api/user/role/{roleName}/remove-required-group/{groupId}'  => ['PUT', [RoleController::class, 'removeRequiredGroups']],

    '/api/user/settings/system/list'                        => ['GET',  [SettingsController::class, 'systemList']],
    '/api/user/settings/system/change/{name}'               => ['PUT',  [SettingsController::class, 'systemChange']],
    '/api/user/settings/system/send-invalid-token-mail'     => ['POST', [SettingsController::class, 'sendInvalidTokenMail']],
    '/api/user/settings/system/send-missing-character-mail' => ['POST', [SettingsController::class, 'sendMissingCharacterMail']],
    '/api/user/settings/eve-login/list'         => ['GET'    => [SettingsEveLoginController::class, 'list']],
    '/api/user/settings/eve-login'              => ['PUT'    => [SettingsEveLoginController::class, 'update']],
    '/api/user/settings/eve-login/{name}'       => ['POST'   => [SettingsEveLoginController::class, 'create']],
    '/api/user/settings/eve-login/{id}'         => ['DELETE' => [SettingsEveLoginController::class, 'delete']],
    '/api/user/settings/eve-login/{id}/tokens'  => ['GET'    => [SettingsEveLoginController::class, 'tokens']],
    '/api/user/settings/eve-login/roles'        => ['GET'    => [SettingsEveLoginController::class, 'roles']],

    '/api/user/watchlist/create'                                => ['POST',   [WatchlistController::class, 'create']],
    '/api/user/watchlist/{id}/rename'                           => ['PUT',    [WatchlistController::class, 'rename']],
    '/api/user/watchlist/{id}/delete'                           => ['DELETE', [WatchlistController::class, 'delete']],
    '/api/user/watchlist/{id}/lock-watchlist-settings/{lock}'   => ['PUT',    [WatchlistController::class, 'lockWatchlistSettings']],
    '/api/user/watchlist/listAll'                               => ['GET',    [WatchlistController::class, 'listAll']],
    '/api/user/watchlist/list-available'                        => ['GET',    [WatchlistController::class, 'listAvailable']],
    '/api/user/watchlist/list-available-manage'                 => ['GET',    [WatchlistController::class, 'listAvailableManage']],
    '/api/user/watchlist/{id}/players'                          => ['GET',    [WatchlistController::class, 'players']],
    '/api/user/watchlist/{id}/players-kicklist'                 => ['GET',    [WatchlistController::class, 'playersKicklist']],
    '/api/user/watchlist/{id}/exemption/list'                   => ['GET',    [WatchlistController::class, 'exemptionList']],
    '/api/user/watchlist/{id}/exemption/add/{player}'           => ['PUT',    [WatchlistController::class, 'exemptionAdd']],
    '/api/user/watchlist/{id}/exemption/remove/{player}'        => ['PUT',    [WatchlistController::class, 'exemptionRemove']],
    '/api/user/watchlist/{id}/corporation/list'                 => ['GET',    [WatchlistController::class, 'corporationList']],
    '/api/user/watchlist/{id}/corporation/add/{corporation}'    => ['PUT',    [WatchlistController::class, 'corporationAdd']],
    '/api/user/watchlist/{id}/corporation/remove/{corporation}' => ['PUT',    [WatchlistController::class, 'corporationRemove']],
    '/api/user/watchlist/{id}/alliance/list'                    => ['GET',    [WatchlistController::class, 'allianceList']],
    '/api/user/watchlist/{id}/alliance/add/{alliance}'          => ['PUT',    [WatchlistController::class, 'allianceAdd']],
    '/api/user/watchlist/{id}/alliance/remove/{alliance}'       => ['PUT',    [WatchlistController::class, 'allianceRemove']],
    '/api/user/watchlist/{id}/group/list'                       => ['GET',    [WatchlistController::class, 'groupList']],
    '/api/user/watchlist/{id}/group/add/{group}'                => ['PUT',    [WatchlistController::class, 'groupAdd']],
    '/api/user/watchlist/{id}/group/remove/{group}'             => ['PUT',    [WatchlistController::class, 'groupRemove']],
    '/api/user/watchlist/{id}/manager-group/list'               => ['GET',    [WatchlistController::class, 'managerGroupList']],
    '/api/user/watchlist/{id}/manager-group/add/{group}'        => ['PUT',    [WatchlistController::class, 'managerGroupAdd']],
    '/api/user/watchlist/{id}/manager-group/remove/{group}'     => ['PUT',    [WatchlistController::class, 'managerGroupRemove']],
    '/api/user/watchlist/{id}/kicklist-corporation/list'                  => ['GET', [WatchlistController::class, 'kicklistCorporationList']],
    '/api/user/watchlist/{id}/kicklist-corporation/add/{corporation}'     => ['PUT', [WatchlistController::class, 'kicklistCorporationAdd']],
    '/api/user/watchlist/{id}/kicklist-corporation/remove/{corporation}'  => ['PUT', [WatchlistController::class, 'kicklistCorporationRemove']],
    '/api/user/watchlist/{id}/kicklist-alliance/list'                     => ['GET', [WatchlistController::class, 'kicklistAllianceList']],
    '/api/user/watchlist/{id}/kicklist-alliance/add/{alliance}'           => ['PUT', [WatchlistController::class, 'kicklistAllianceAdd']],
    '/api/user/watchlist/{id}/kicklist-alliance/remove/{alliance}'        => ['PUT', [WatchlistController::class, 'kicklistAllianceRemove']],
    '/api/user/watchlist/{id}/allowlist-corporation/list'                 => ['GET', [WatchlistController::class, 'allowlistCorporationList']],
    '/api/user/watchlist/{id}/allowlist-corporation/add/{corporation}'    => ['PUT', [WatchlistController::class, 'allowlistCorporationAdd']],
    '/api/user/watchlist/{id}/allowlist-corporation/remove/{corporation}' => ['PUT', [WatchlistController::class, 'allowlistCorporationRemove']],
    '/api/user/watchlist/{id}/allowlist-alliance/list'                    => ['GET', [WatchlistController::class, 'allowlistAllianceList']],
    '/api/user/watchlist/{id}/allowlist-alliance/add/{alliance}'          => ['PUT', [WatchlistController::class, 'allowlistAllianceAdd']],
    '/api/user/watchlist/{id}/allowlist-alliance/remove/{alliance}'       => ['PUT', [WatchlistController::class, 'allowlistAllianceRemove']],

    '/api/user/service/{id}/get'                            => ['GET',  [ServiceController::class, 'get']],
    '/api/user/service/{id}/accounts'                       => ['GET',  [ServiceController::class, 'accounts']],
    '/api/user/service/{id}/register'                       => ['POST', [ServiceController::class, 'register']],
    '/api/user/service/{id}/update-account/{characterId}'   => ['PUT',  [ServiceController::class, 'updateAccount']],
    '/api/user/service/{id}/reset-password/{characterId}'   => ['PUT',  [ServiceController::class, 'resetPassword']],
    '/api/user/service/update-all-accounts/{playerId}'      => ['PUT',  [ServiceController::class, 'updateAllAccounts']],

    '/api/user/service-admin/list'                      => ['GET',    [ServiceAdminController::class, 'list']],
    '/api/user/service-admin/create'                    => ['POST',   [ServiceAdminController::class, 'create']],
    '/api/user/service-admin/{id}/rename'               => ['PUT',    [ServiceAdminController::class, 'rename']],
    '/api/user/service-admin/{id}/delete'               => ['DELETE', [ServiceAdminController::class, 'delete']],
    '/api/user/service-admin/{id}/save-configuration'   => ['PUT',    [ServiceAdminController::class, 'saveConfiguration']],

    '/api/user/statistics/player-logins'                => ['GET', [StatisticsController::class, 'playerLogins']],
    '/api/user/statistics/total-monthly-app-requests'   => ['GET', [StatisticsController::class, 'totalMonthlyAppRequests']],
    '/api/user/statistics/monthly-app-requests'         => ['GET', [StatisticsController::class, 'monthlyAppRequests']],
    '/api/user/statistics/total-daily-app-requests'     => ['GET', [StatisticsController::class, 'totalDailyAppRequests']],
    '/api/user/statistics/hourly-app-requests'          => ['GET', [StatisticsController::class, 'hourlyAppRequests']],
];
