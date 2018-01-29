<?php

namespace Host1Plus\Clients;

// utilities
use \Host1Plus\Utilities\ArrayParse;

// interfaces/enums
use \Host1Plus\Interfaces\iClient;
use \Host1Plus\Interfaces\iTransport;
use \Host1Plus\Enums\{RequestMethods as RM, Errors, CloudStatisticTypes as CST};

class CloudServers extends aClient implements iClient
{
    private $ts;

    public function __construct(iTransport $transport)
    {
        $this->ts = $transport;
    }

    public function getOne(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}");
    }

    public function start(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/start");
    }

    public function stop(int $serverId, bool $forced = false)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/stop", [], $this->jsonEncodeBody(['forced' => $forced]));
    }

    public function reboot(int $serverId, bool $forced = false)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/reboot", [], $this->jsonEncodeBody(['forced' => $forced]));
    }

    public function reinstall(int $serverId, string $templateId)
    {
        $this->isValidServerId($serverId);

        if ($templateId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'templateId', 'non-empty string', $templateId));

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/reinstall", [], $this->jsonEncodeBody(['templateId' => $templateId]));
    }

    public function resetRootPass(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/resetRootPass");
    }

    public function changeNames(int $serverId, array $params)
    {
        $this->isValidServerId($serverId);

        $b = [];
        ArrayParse::StringParam('name', $params, $b);
        ArrayParse::StringParam('displayName', $params, $b);

        if (\count($b) == 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, '[name, displayName]', 'at least one non empty parameter', ''));

        return $this->ts->Request(RM::PATCH, "cloudservers/{$serverId}/names", [], $this->jsonEncodeBody($b));
    }

    public function restore(int $serverId, string $backupId, string $volumeId)
    {
        $this->isValidServerId($serverId);

        if ($backupId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'backupId', 'non-empty string', $backupId));

        if ($volumeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'volumeId', 'non-empty string', $volumeId));

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/restore", [], $this->jsonEncodeBody(['backupId' => $backupId, 'volumeId' => $volumeId]));
    }

    public function getTemplates(int $productId)
    {
        return $this->ts->Request(RM::GET, "cloudservers/templates", ['productId' => $productId]);
    }

    public function create(array $params)
    {
        $b = [];
        ArrayParse::IntParamSet('productId', $params, $b);
        ArrayParse::StringParamSet('hostname', $params, $b);
        ArrayParse::StringParamSet('billingCycle', $params, $b);
        ArrayParse::IntParamSet('cpu', $params, $b);
        ArrayParse::IntParamSet('ram', $params, $b);
        ArrayParse::IntParamSet('hdd', $params, $b);
        ArrayParse::IntParamSet('bandwidth', $params, $b);
        ArrayParse::IntParamSet('ip', $params, $b);
        ArrayParse::IntParamSet('backups', $params, $b);
        ArrayParse::ArrayParamSet('additionalDisks', $params, $b);
        ArrayParse::StringParamSet('osTemplate', $params, $b);

        return $this->ts->Request(RM::POST, "cloudservers", [], $this->jsonEncodeBody($b));
    }

    public function update(int $serverId, array $params)
    {
        $this->isValidServerId($serverId);

        $b = [];
        ArrayParse::StringParam('paymentGateway', $params, $b);
        ArrayParse::IntParam('cpu', $params, $b);
        ArrayParse::IntParam('ram', $params, $b);
        ArrayParse::IntParam('hdd', $params, $b);
        ArrayParse::IntParam('bandwidth', $params, $b);
        ArrayParse::IntParam('ip', $params, $b);
        ArrayParse::IntParam('backups', $params, $b);
        ArrayParse::ArrayParam('additionalDisks', $params, $b);

        return $this->ts->Request(RM::PATCH, "cloudservers/{$serverId}", [], $this->jsonEncodeBody($b));
    }

    public function rescue(int $serverId, bool $reboot = false, bool $forced = false)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/rescue", [], $this->jsonEncodeBody(['reboot' => $reboot, 'forced' => $forced]));
    }

    public function unrescue(int $serverId, bool $reboot = false, bool $forced = false)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/unrescue", [], $this->jsonEncodeBody(['reboot' => $reboot, 'forced' => $forced]));
    }

    public function createVnc(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/vnc");
    }

    public function addSshKey(int $serverId, int $sshKeyId)
    {
        $this->isValidServerId($serverId);

        if ($sshKeyId <= 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'sshKeyId', 'integer above 0', $sshKeyId));

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/sshKeys", [], $this->jsonEncodeBody(['id' => $sshKeyId]));
    }

    public function getIsos(int $serverId, array $tags = [])
    {
        $this->isValidServerId($serverId);

        $queryParams = [];
        if (\count($tags) != 0)
            $queryParams['tags'] = $tags;

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/isos", $queryParams);
    }

    public function getIso(int $serverId, string $isoId, array $tags = [])
    {
        $this->isValidServerId($serverId);

        if ($isoId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'isoId', 'non-empty string', $isoId));

        $queryParams = [];
        if (\count($tags) != 0)
            $queryParams['tags'] = $tags;

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/isos/{$isoId}", $queryParams);
    }

    public function uploadIso(int $serverId, string $name, string $url, bool $bootable = false, string $osTypeId = '')
    {
        $this->isValidServerId($serverId);

        if ($name == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'name', 'non-empty string', $name));

        if ($url == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'url', 'non-empty string', $url));

        $b = ['name' => $name, 'url' => $url, 'bootable' => $bootable];
        if ($osTypeId != '')
            $b['osTypeId'] = $osTypeId;

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/isos", [], $this->jsonEncodeBody($b));
    }

    public function attachIso(int $serverId, string $isoId, bool $reboot = false, bool $forced = false)
    {
        $this->isValidServerId($serverId);

        if ($isoId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'isoId', 'non-empty string', $isoId));

        $b = ['isoId' => $isoId, 'reboot' => $reboot, 'forced' => $forced];

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/attachIso", [], $this->jsonEncodeBody($b));
    }

    public function detachIso(int $serverId, bool $reboot = false, bool $forced = false)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/detachIso", [], $this->jsonEncodeBody(['reboot' => $reboot, 'forced' => $forced]));
    }

    public function installIso(int $serverId, string $isoId)
    {
        $this->isValidServerId($serverId);

        if ($isoId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'isoId', 'non-empty string', $isoId));

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/installIso", [], $this->jsonEncodeBody(['isoId' => $isoId]));
    }

    public function deleteIso(int $serverId, string $isoId)
    {
        $this->isValidServerId($serverId);

        if ($isoId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'isoId', 'non-empty string', $isoId));

        return $this->ts->Request(RM::DELETE, "cloudservers/{$serverId}/isos/{$isoId}");
    }

    public function getLimits(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/limits");
    }

    public function getOsTypes(int $serverId, array $params)
    {
        $this->isValidServerId($serverId);

        $queryParams = [];
        ArrayParse::StringParam('categoryId', $params, $queryParams);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/osTypes", $queryParams);
    }

    public function getOsType(int $serverId, string $osTypeId)
    {
        $this->isValidServerId($serverId);

        if ($osTypeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'osTypeId', 'non-empty string', $osTypeId));

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/osTypes/{$osTypeId}");
    }

    public function getOsCategories(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/osCategories");
    }

    public function getOsCategory(int $serverId, string $categoryId)
    {
        $this->isValidServerId($serverId);

        if ($categoryId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'categoryId', 'non-empty string', $categoryId));

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/osCategories/{$categoryId}");
    }

    public function getTemplatesAvailable(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/templates");
    }

    public function getTemplate(int $serverId, string $templateId)
    {
        $this->isValidServerId($serverId);

        if ($templateId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'templateId', 'non-empty string', $templateId));

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/templates/{$templateId}");
    }

    public function getIPv4(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/subnets/v4");
    }

    public function getIPv6(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/subnets/v6");
    }

    public function getVolumes(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/volumes");
    }

    public function getVolume(int $serverId, string $volumeId)
    {
        $this->isValidServerId($serverId);

        if ($volumeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'volumeId', 'non-empty string', $volumeId));

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/volumes/{$volumeId}");
    }

    public function getBackups(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/backups");
    }

    public function getBackup(int $serverId, string $backupId)
    {
        $this->isValidServerId($serverId);

        if ($backupId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'backupId', 'non-empty string', $backupId));

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/backups/{$backupId}");
    }

    public function createBackup(int $serverId, string $volumeId, array $params = [])
    {
        $this->isValidServerId($serverId);

        if ($volumeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'volumeId', 'non-empty string', $volumeId));

        $body = null;
        if (\count($params) != 0)
        {
            $b = [];
            ArrayParse::StringParam('name', $params, $b);
            if (\count($b) != 0)
                $body = $this->jsonEncodeBody($b);
        }

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/volumes/{$volumeId}/backup", [], $body);
    }

    public function deleteBackup(int $serverId, string $backupId)
    {
        $this->isValidServerId($serverId);

        if ($backupId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'backupId', 'non-empty string', $backupId));

        return $this->ts->Request(RM::DELETE, "cloudservers/{$serverId}/backups/{$backupId}");
    }

    public function getSchedules(int $serverId, string $volumeId)
    {
        $this->isValidServerId($serverId);

        if ($volumeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'volumeId', 'non-empty string', $volumeId));

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/volumes/{$volumeId}/backupSchedules");
    }

    public function createSchedule(int $serverId, string $volumeId, array $params)
    {
        $this->isValidServerId($serverId);

        if ($volumeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'volumeId', 'non-empty string', $volumeId));

        $b = [];
        ArrayParse::StringParamSet('intervalType', $params, $b);
        ArrayParse::StringParamSet('schedule', $params, $b);
        ArrayParse::StringParamSet('timeZone', $params, $b);
        ArrayParse::IntParam('maxCopies', $params, $b);

        return $this->ts->Request(RM::POST, "cloudservers/{$serverId}/volumes/{$volumeId}/backupSchedules", [], $this->jsonEncodeBody($b));
    }

    public function deleteSchedule(int $serverId, string $volumeId, string $scheduleId)
    {
        $this->isValidServerId($serverId);

        if ($volumeId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'volumeId', 'non-empty string', $volumeId));

        if ($scheduleId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'scheduleId', 'non-empty string', $scheduleId));

        return $this->ts->Request(RM::DELETE, "cloudservers/{$serverId}/volumes/{$volumeId}/backupSchedules/{$scheduleId}");
    }

    public function suspendUsage(int $serverId, string $reason = '')
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request('POST', "cloudservers/{$serverId}/suspendUsage", [], $this->jsonEncodeBody(['reason' => $reason]));
    }

    public function unsuspendUsage(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request('POST', "cloudservers/{$serverId}/unsuspendUsage");
    }

    public function terminate(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request('DELETE', "cloudservers/{$serverId}");
    }

    public function getStatistics(int $serverId, string $type = '', int $from = -1, int $to = -1, bool $continuous = false)
    {
        $this->isValidServerId($serverId);

        $queryParams = ['continuous' => $continuous];
        if ($type != '')
        {
            if (!\in_array($type, CST::Allowed))
                throw new \InvalidArgumentException( \sprintf(Errors::InvalidParameter, 'type', \join(',', CST::Allowed), $type) );

            $queryParams['type'] = $type;
        }

        if ($from > -1)
            $queryParams['from'] = $from;

        if ($to > -1)
            $queryParams['to'] = $to;

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/statistics", $queryParams);
    }

    public function getStatisticsSummary(int $serverId, string $type = 'all', int $from = 0, int $to = 0, bool $continuous = false)
    {
        $this->isValidServerId($serverId);

        if (!\in_array($type, CST::Allowed))
            throw new \InvalidArgumentException( \sprintf(Errors::InvalidParameter, 'type', \join(',', CST::Allowed), $type) );

        return $this->ts->Request(RM::GET, "cloudservers/{$serverId}/statistics/summary", ['type' => $type, 'from' => $from, 'to' => $to, 'continuous' => $continuous]);
    }

    public function getTags(array $params)
    {
        $queryParams = [];
        ArrayParse::StringParam('key', $params, $queryParams);
        ArrayParse::StringParam('value', $params, $queryParams);
        ArrayParse::IntParam('page', $params, $queryParams);
        ArrayParse::IntParam('pagesize', $params, $queryParams);
        ArrayParse::StringParam('resourceId', $params, $queryParams);
        ArrayParse::StringParam('resourceType', $params, $queryParams);

        return $this->ts->Request(RM::GET, 'cloudservers/tags', $queryParams);
    }

    public function createTags(string $resourceId, string $resourceType, array $tags)
    {
        return $this->ts->Request(RM::POST, 'cloudservers/tags', [], $this->jsonEncodeBody(['resourceId' => $resourceId, 'resourceType' => $resourceType, 'tags' => $tags]));
    }

    public function deleteTag(string $resourceId, string $resourceType, string $key)
    {
        return $this->ts->Request(RM::DELETE, 'cloudservers/tags', [], $this->jsonEncodeBody(['resourceId' => $resourceId, 'resourceType' => $resourceType, 'key' => $key]));
    }
}