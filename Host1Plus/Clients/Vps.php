<?php

namespace Host1Plus\Clients;

// utilities
use \Host1Plus\Utilities\ArrayParse;

// interfaces/enums
use \Host1Plus\Interfaces\iClient;
use \Host1Plus\Interfaces\iTransport;
use \Host1Plus\Enums\{RequestMethods as RM, Errors, VpsScheduleIntervals as VSI, VpsStatisticRetentions as VSR};

// exceptions
use \Host1Plus\Exception\InvalidRequestBody;

class Vps extends aClient implements iClient
{
    private $ts;

    public function __construct(iTransport $transport)
    {
        $this->ts = $transport;
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getOne(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "vps/{$serverId}");
    }

    /**
     *
     * @param array $params
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function create(array $params)
    {
        $b = [];
        ArrayParse::IntParamSet('productId', $params, $b);
        ArrayParse::StringParamSet('hostname', $params, $b);
        ArrayParse::StringParamSet('billingCycle', $params, $b);
        ArrayParse::IntParamSet('hdd', $params, $b);
        ArrayParse::IntParamSet('cpu', $params, $b);
        ArrayParse::IntParamSet('ram', $params, $b);
        ArrayParse::IntParamSet('ip', $params, $b);
        ArrayParse::IntParamSet('backups', $params, $b);
        ArrayParse::IntParamSet('bandwidth', $params, $b);
        ArrayParse::IntParamSet('networkRate', $params, $b);
        ArrayParse::StringParamSet('osTemplate', $params, $b);

        return $this->ts->Request(RM::POST, 'vps', [], $this->jsonEncodeBody($b));
    }

    /**
     *
     * @param int $serverId
     * @param array $params
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function update(int $serverId, array $params)
    {
        $this->isValidServerId($serverId);

        $b = [];
        ArrayParse::IntParam('hdd', $params, $b);
        ArrayParse::IntParam('cpu', $params, $b);
        ArrayParse::IntParam('ram', $params, $b);
        ArrayParse::IntParam('ip', $params, $b);
        ArrayParse::IntParam('backups', $params, $b);
        ArrayParse::IntParam('bandwidth', $params, $b);
        ArrayParse::IntParam('networkRate', $params, $b);
        ArrayParse::BoolParam('tun', $params, $b);
        ArrayParse::StringParam('paymentGateway', $params, $b);

        return $this->ts->Request(RM::PATCH, "vps/{$serverId}", [], $this->jsonEncodeBody($b));
    }

    /**
     *
     * @param int $serverId
     * @param string $reason
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function suspendUsage(int $serverId, string $reason = '')
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request('POST', "vps/{$serverId}/suspendUsage", [], $this->jsonEncodeBody(['reason' => $reason]));
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function unsuspendUsage(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request('POST', "vps/{$serverId}/unsuspendUsage");
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function terminate(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request('DELETE', "vps/{$serverId}");
    }

    /**
     *
     * @return array
     */
    public function getOsTemplates()
    {
        return $this->ts->Request(RM::GET, 'vps/templates');
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function start(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "vps/{$serverId}/start");
    }

    /**
     *
     * @param int $serverId
     * @param bool $kill
     * @param bool $noForce
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function stop(int $serverId, bool $kill = false, bool $noForce = false)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "vps/{$serverId}/stop", [], $this->jsonEncodeBody(['kill' => $kill, 'noForce' => $noForce]));
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function restart(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "vps/{$serverId}/restart");
    }

    /**
     *
     * @param int $serverId
     * @param string $osTemplate
     * @param string $rootPass
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function reinstall(int $serverId, string $osTemplate, string $rootPass = '')
    {
        $this->isValidServerId($serverId);

        $b = ['osTemplate' => $osTemplate];
        if ($rootPass != '')
            $b['rootpass'] = $rootPass;

        return $this->ts->Request(RM::POST, "vps/{$serverId}/reinstall", [], $this->jsonEncodeBody($b));
    }

    /**
     *
     * @param int $serverId
     * @param string $hostname
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function changeHostname(int $serverId, string $hostname)
    {
        $this->isValidServerId($serverId);

        if ($hostname == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'hostname', 'non empty string', $hostname));

        return $this->ts->Request(RM::PATCH, "vps/{$serverId}/hostname", [], $this->jsonEncodeBody(['hostname' => $hostname]));
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function resetPassword(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "vps/{$serverId}/resetPassword");
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function startVnc(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "vps/{$serverId}/vnc/start");
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function stopVnc(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::POST, "vps/{$serverId}/vnc/stop");
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getVnc(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "vps/{$serverId}/vnc");
    }

    /**
     *
     * @param int $serverId
     * @param string $type
     * @param string $state
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getBackups(int $serverId, string $type = '', string $state = '')
    {
        $this->isValidServerId($serverId);

        $queryParams = [];
        if ($type != '')
            $queryParams['type'] = $type;

        if ($state != '')
            $queryParams['state']= $state;

        return $this->ts->Request(RM::GET, "vps/{$serverId}/backups", $queryParams);
    }

    /**
     *
     * @param int $serverId
     * @param int $backupId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getBackup(int $serverId, string $backupId)
    {
        $this->isValidServerId($serverId);

        if ($backupId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'backupId', 'uuid string', $backupId));

        return $this->ts->Request(RM::GET, "vps/{$serverId}/backups/{$backupId}");
    }

    /**
     *
     * @param int $serverId
     * @param string $name
     * @param string $type
     * @param string $description
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function createBackup(int $serverId, string $name, string $type = '', string $description = '')
    {
        $this->isValidServerId($serverId);

        if ($name == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'name', 'non-empty string', $name));

        $b = ['name' => $name];
        if ($type != '')
            $b['type'] = $type;

        if ($description != '')
            $b['description'] = $description;

        return $this->ts->Request(RM::POST, "vps/{$serverId}/backups", [], $this->jsonEncodeBody($b));
    }

    /**
     *
     * @param int $serverId
     * @param string $backupId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function restoreBackup(int $serverId, string $backupId)
    {
        $this->isValidServerId($serverId);

        if ($backupId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'backupId', 'uuid string', $backupId));

        return $this->ts->Request(RM::POST, "vps/{$serverId}/backups/{$backupId}/restore");
   }

   /**
    *
    * @param int $serverId
    * @param string $backupId
    * @return array
    * @throws \InvalidArgumentException
    */
    public function deleteBackup(int $serverId, string $backupId)
    {
        $this->isValidServerId($serverId);

        if ($backupId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'backupId', 'uuid string', $backupId));

        return $this->ts->Request(RM::DELETE, "vps/{$serverId}/backups/{$backupId}");
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getIPv4(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "vps/{$serverId}/subnets/v4");
    }

    /**
     *
     * @param int $serverId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getIPv6(int $serverId)
    {
        $this->isValidServerId($serverId);

        return $this->ts->Request(RM::GET, "vps/{$serverId}/subnets/v6");
    }

    /**
     *
     * @param int $serverId
     * @param int $amount
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function addIPv6(int $serverId, int $amount = 1)
    {
        $this->isValidServerId($serverId);

        if ($amount <= 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'amount', 'integer above 0', $amount));

        return $this->ts->Request(RM::POST, "vps/{$serverId}/subnets/v6", [], $this->jsonEncodeBody(['amount' => $amount]));
    }

    /**
     *
     * @param int $serverId
     * @param array $notations
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function deleteIPv6(int $serverId, array $notations)
    {
        $this->isValidServerId($serverId);

        if (($c = \count($notations)) == 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'notations', 'array with at least one element', $c));

        return $this->ts->Request(RM::DELETE, "vps/{$serverId}/subnets/v6", [], $this->jsonEncodeBody($notations));
    }

    /**
     *
     * @param int $serverId
     * @param string $ip
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function changePrimaryIp(int $serverId, string $ip)
    {
        $this->isValidServerId($serverId);

        if ($ip == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'ip', 'valid IPv4 address', $ip));

        return $this->ts->Request(RM::POST, "vps/{$serverId}/changePrimaryIp", [], $this->jsonEncodeBody(['ip' => $ip]));
    }

    /**
     *
     * @param int $serverId
     * @param int $from
     * @param int $to
     * @param string $retention
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getStatistics(int $serverId, int $from = -1, int $to = -1, string $retention = '')
    {
        $this->isValidServerId($serverId);

        $queryParams = [];
        if ($retention != '')
        {
            if (!\in_array($retention, VSR::Valid))
                throw new \InvalidArgumentException( \sprintf(Errors::InvalidParameter, 'retention', \join(',', VSR::Valid), $retention) );

            $queryParams['retention'] = $retention;
        }

        if ($from > -1)
            $queryParams['from'] = $from;

        if ($to > -1)
            $queryParams['to'] = $to;

        return $this->ts->Request(RM::GET, "vps/{$serverId}/statistics", $queryParams);
    }

    /**
     *
     * @param int $serverId
     * @param array $params
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getSchedules(int $serverId, array $params = [])
    {
        $this->isValidServerId($serverId);

        $queryParams = [];
        ArrayParse::StringParam('name', $params, $queryParams);
        ArrayParse::StringParam('interval', $params, $queryParams);
        ArrayParse::StringParam('state', $params, $queryParams);
        ArrayParse::IntParam('copyamount', $params, $queryParams);
        ArrayParse::BoolParam('disabled', $params, $queryParams);

        return $this->ts->Request(RM::GET, "vps/{$serverId}/schedules", $queryParams);
    }

    /**
     *
     * @param int $serverId
     * @param int $scheduleId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getSchedule(int $serverId, string $scheduleId)
    {
        $this->isValidServerId($serverId);

        if ($scheduleId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'scheduleId', 'non-empty string', $scheduleId));

        return $this->ts->Request(RM::GET, "vps/{$serverId}/schedules/{$scheduleId}");
    }

    /**
     *
     * @param int $serverId
     * @param string $name
     * @param string $interval
     * @param int $executeAfter
     * @param int $copyAmount
     * @param bool $disabled
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function createSchedule(int $serverId, string $name, string $interval, int $executeAfter, int $copyAmount = 1, bool $disabled = false)
    {
        $this->isValidServerId($serverId);

        if ($name == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'name', 'non-empty string', $name));

        if ($interval == '' || ($interval != VSI::Hourly && $interval != VSI::Daily && $interval != VSI::Weekly && $interval != VSI::Monthly))
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'interval', VSI::Hourly . ',' . VSI::Daily . ',' . VSI::Weekly . ',' . VSI::Monthly, $interval));

        if ($executeAfter <= 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'executeAfter', 'integer above 0', $executeAfter));

        if ($copyAmount <= 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'copyAmount', 'integer above 0', $copyAmount));

        $b = [
            'name'         => $name,
            'interval'     => $interval,
            'executeAfter' => $executeAfter,
            'copyAmount'   => $copyAmount,
            'disabled'     => $disabled
        ];

        return $this->ts->Request(RM::POST, "vps/{$serverId}/schedules", [], $this->jsonEncodeBody($b));
    }

    /**
     *
     * @param int $serverId
     * @param int $scheduleId
     * @param array $params
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidRequestBody
     */
    public function updateSchedule(int $serverId, string $scheduleId, array $params = [])
    {
        if (\count($params) == 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'params', 'array with at least one valid element', 0));

        $this->isValidServerId($serverId);

        if ($scheduleId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'scheduleId', 'non-empty string', $scheduleId));

        $b = [];
        ArrayParse::StringParam('name', $params, $b);
        ArrayParse::StringParam('interval', $params, $b);
        ArrayParse::IntParam('executeAfter', $params, $b);
        ArrayParse::IntParam('copyAmount', $params, $b);
        ArrayParse::BoolParam('disabled', $params, $b);

        if (\count($b) == 0)
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'params', 'array with at least one valid element', 0));

        return $this->ts->Request(RM::PATCH, "vps/{$serverId}/schedules/{$scheduleId}", [], $this->jsonEncodeBody($b));
    }

    /**
     *
     * @param int $serverId
     * @param int $scheduleId
     * @return array
     * @throws \InvalidArgumentException
     */
    public function deleteSchedule(int $serverId, string $scheduleId)
    {
        $this->isValidServerId($serverId);

        if ($scheduleId == '')
            throw new \InvalidArgumentException(\sprintf(Errors::InvalidParameter, 'scheduleId', 'non-empty string', $scheduleId));

        return $this->ts->Request(RM::DELETE, "vps/{$serverId}/schedules/{$scheduleId}");
    }
}