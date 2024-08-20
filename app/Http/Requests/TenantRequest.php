<?php

namespace Crater\Http\Requests;

class TenantRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'tenant_name' => ['required', 'string'],
            'tenant_code' => ['required', 'int', 'between:200,999'],
            'package' => ['required'],
            'ext_length' => ['required', 'integer', 'between:2,16'],
            'country' => ['required', 'integer'],
            'area_code' => ['nullable', 'integer'],
            'national' => ['required'],
            'international' => ['required'],
            //optional
            'glocom_dns_srv_lookup' => ['nullable', 'integer'],
            'glocomproxy' => ['nullable', 'integer'],
            'apusername' => ['nullable', 'string'],
            'appassword' => ['nullable', 'string'],
            'defaultserver' => ['nullable', 'string'],
            'announcetrunks' => ['nullable', 'integer'],
            'absolutetimeout' => ['nullable', 'integer'],
            'cdrvoicemail' => ['nullable', 'integer', 'between:0,2'],
            'faxformat' => ['nullable', 'string', 'in:letter,legal,A4,auto'],
            'faxfiletype' => ['nullable', 'integer', 'between:1,3'],
            'default_location' => ['nullable', 'integer', 'between:1,2'],
            'enabletcalls' => ['nullable', 'string', 'in:yes,no'],
            'disabletcid' => ['nullable', 'string', 'in:yes,no'],
            'tenantcid' => ['nullable', 'string'],
            'tenant_faxcid' => ['nullable', 'string'],
            'usedefaultcid' => ['nullable', 'string', 'in:yes,no'],
            'faxcid' => ['nullable', 'string'],
            'usedidcid' => ['nullable', 'string', 'in:yes,no'],
            'finde164' => ['nullable', 'string'],
            'recordlimit' => ['nullable', 'integer'],
            'showdirosc' => ['nullable', 'string', 'in:yes,no'],
            'recordglobal' => ['nullable', 'string', 'in:yes,no'],
            'recordsilent' => ['nullable', 'string', 'in:yes,no'],
            'mp3_auto_conv' => ['nullable', 'string', 'in:yes,no'],
            'mp3_auto_tag' => ['nullable', 'string', 'in:yes,no'],
            'recordbeep' => ['nullable', 'string', 'in:yes,no'],
            'recordformat' => ['nullable', 'string', 'in:gsm,wav,wav49,g729,ogg'],
            'audiolang' => ['nullable', 'string'],
            'cpark_timeout' => ['nullable', 'integer'],
            'cpark_dial' => ['nullable', 'string'],
            'cpark_goto' => ['nullable', 'string'],
            'limitsound' => ['nullable', 'string', 'in:yes,no'],
            //req?
            'limitemail' => ['nullable', 'string'],
            'notifyemail' => ['nullable', 'string'],
            //
            'leavenational' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],
            'currencypos' => ['nullable', 'string', 'in:left,right'],
            'dialed_num_minimum_length' => ['nullable', 'integer'],
            'pstn_mode' => ['nullable', 'integer', 'between:1,2'],
            'callgroups' => ['nullable', 'string'],
            'localcodecs' => ['nullable', 'string'],
            'remotecodecs' => ['nullable', 'string'],
            'networkcodecs' => ['nullable', 'string'],
            'hdcheck' => ['nullable', 'string', 'in:yes,no'],
            'hdlockext' => ['nullable', 'integer'],
            'hdlockdevice' => ['nullable', 'integer'],
            'hdautologout' => ['nullable', 'string', 'in:yes,no'],
            'hdlogoutinactive' => ['nullable', 'integer'],
            'ringtonelocal' => ['nullable', 'string'],
            'hidecallerid' => ['nullable', 'string', 'in:yes,no'],
            'allowescallerid' => ['nullable', 'string', 'in:yes,no'],
            'enablecallerid' => ['nullable', 'string', 'in:yes,no'],
            'enablecnamlookup' => ['nullable', 'string', 'in:yes,no'],
            'setcidforgrouphunt' => ['nullable', 'string', 'in:yes,no'],
            'cidmatchdid' => ['nullable', 'string', 'in:yes,no'],
            'dropanonymous' => ['nullable', 'string', 'in:yes,no'],
            'didsaveupdatecid' => ['nullable', 'string', 'in:yes,no'],
            'forceunknown' => ['nullable', 'string', 'in:yes,no'],
            'hideextnodir' => ['nullable', 'string', 'in:yes,no'],
            'custompresencetime' => ['nullable', 'integer'],
            'usedynfeatures' => ['nullable', 'string', 'in:yes,no'],
            //Deprecated
            'nobillingfwd' => ['nullable', 'string', 'in:yes,no'],
            //
            'cf_call_rating_disable' => ['nullable', 'string', 'in:yes,no'],
            'jbimpl' => ['nullable', 'string', 'in:inherit,disabled,fixed,adaptive'],
            'jbmaxsize' => ['nullable', 'integer'],
            'jbresyncthreshold' => ['nullable', 'integer'],
            'jbtargetextra' => ['nullable', 'string', 'in:yes,no'],
            'allowextipauth' => ['nullable', 'string', 'in:yes,no'],
            'voiceskipping' => ['nullable', 'string', 'in:yes,no'],
            'email_from' => ['nullable', 'string'],
            'vm_email_from' => ['nullable', 'string'],
            'local_channels' => ['nullable', 'string'],
            'remote_channels' => ['nullable', 'string'],
            'conferences' => ['nullable', 'string'],
            'queues' => ['nullable', 'string'],
            'auto_attendants' => ['nullable', 'string'],
            'dahdi' => ['nullable', 'string'],
            'dids_notify_email' => ['nullable', 'string'],
            'dids_notify_order' => ['nullable', 'string'],
            'es_ambulatory' => ['nullable', 'string'],
            'es_fire' => ['nullable', 'string'],
            'es_police' => ['nullable', 'string'],
            'es_notification_email' => ['nullable', 'string'],
            'ldap_enabled' => ['nullable', 'string', 'in:yes,no'],
            'ldap_local_exts' => ['nullable', 'string'],
            'ldap_hotdesking' => ['nullable', 'string'],
            'ldap_password' => ['nullable', 'string'],
            // others
        ];

    }

    public function authorize(): bool
    {
        return true;
    }
}
