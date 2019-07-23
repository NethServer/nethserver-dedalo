=================
nethserver-dedalo
=================

NethServer configuration for Dedalo: https://github.com/nethesis/icaro

Prerequisites:

- 3 network interfaces: 1 for green, 1 for red and 1 for the hotspot

Configuration
=============

The whole process is available from the Server Manager, but these are the required manual steps.

1. Set the ``hotspot`` role to the network interface which will be used by Dedalo.
   The interfarce could also be a VLAN. ::

       db networks setprop enps0 role hotspot

2. Configure at least ``IcaroHost`` and the hotspot ``Id``.
   Please note that the hotspot id should be already present inside Icaro installation.

3. Register the unit to Icaro installation. Use valid reseller credentials ::

       dedalo register -u <user> -p <password>

4. Enable and start dedalo: ::

       config setprop dedalo status enabled
       signal-event nethsever-dedalo-save

At the end Dedalo will create a ``tundedalo`` device which will intercept all traffic from the selected interface (``enps0`` in the above example).

Advanced configuration
----------------------

The ``IcaroHost`` parameter configures all Icaro URLs and can be used anytime Icaro has been installed
using the provided provisiong scripts.

In case Icaro installation is split across multiple servers or it uses customized configuration, you
should manually set ``AaaUrl``, ``SplashPageUrl`` and ``ApiUrl`` parmaters which take precedence over ``IcaroHost``.

Database
========

Properties

- ``IcaroHost``: if specified, this parameter is used to automatic calculate: ``AaaUrl``, ``SplashPageUrl``, ``ApiUrl``
- ``AaaUrl``: (optional) Wax URL, automatically calculated from ``SplashPageUrl``
- ``AllowOrigins``: (optional) hosts allowed to execute CORS requests to Dedalo, automatically calculated from ``SplashPageUrl``
- ``ApiUrl``: (optional) Sun URL, automatically calculated from ``SplashPageUrl``
- ``Description``: a descriptive name of local installation, eg: ``MyHotelAtTheSea``
- ``Id``: id of the Hotspot already present inside Icaro, eg: ``MyHotelCompany``
- ``Name``: name of the Hotspot, it's retrieved from the UI using an API call
- ``SplashPageUrl``:  Wings (capitve portal) URL
- ``UnitName``: hostname of local installation, default to FQDN
- ``Uuid``: auto-generated unique identifier
- ``Proxy``:  can be ``enabled`` or ``disabled``, if ``enabled`` all hotspot traffic will be proxied
- ``Network``: network for clients connected to Dedalo, default to ``192.168.182.0/24``
- ``LogTraffic``: can be ``enabled`` or ``disabled``, if enabled along with Proxy, the proxy will log all hotspot traffic inside ``/var/log/squid/dedalo.log``
- ``status``: can be ``enabled`` or ``disabled``, default to ``disabled``


Example: ::

  dedalo=service
    AaaUrl=
    AllowOrigins=
    ApiUrl=
    Description=MyLabel
    IcaroHost=hotstpot.nethserver.org
    Id=MyHotel
    LogTraffic=disabled
    Network=192.168.182.0/24
    Proxy=disabled
    SplashPageUrl=
    UnitName=myserver.local.nethserver.org
    Uuid=e7529250-1e44-486e-8b54-ab2ac60d5dcc
    status=enabled

Cockpit API
===========

dashboard/read
---------------

This api returns the data to show in the dashboard page and the data about a hotspot user.

Input
^^^^^

- ``appInfo``: ``dashboardData``, ``ipAddressInfo`` or ``userInfo``

Input example (dashboardData)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "dashboardData"
  }

Output example (dashboardData)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "dashboardData": {
      "dnatUsers": 0,
      "passUsers": 1,
      "hotspotUsers": [
        {
          "status": "pass",
          "macAddress": "A0-DB-55-E9-26-2D",
          "inputOctetsDownloaded": "698470",
          "sessionTimeElapsed": "40",
          "sessionTimeLimit": "2591998",
          "inputOctetsLimit": "0",
          "idleTimeLimit": "0",
          "upBandwidthLimit": "0",
          "sessionKey": "156358627200000001",
          "idleTimeElapsed": "2",
          "downBandwidthPerc": "0%",
          "downBandwidthLimit": "0",
          "outputOctetsUploaded": "121630",
          "ipAddress": "192.168.182.2",
          "outputOctetsLimit": "0",
          "upBandwidthPerc": "0%"
        }
      ]
    }
  }

Input example (ipAddressInfo)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "ipAddressInfo",
    "ipAddress": "192.168.182.2",
    "token": "e31e2ca3948cd269293a13c9bd9366361f9af7f66e5fd3bce57f19b3489839ed",
    "icaroHost": "my.nethspot.com"
  }

Output example (ipAddressInfo)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "ipAddressInfo": {
      "total": 1,
      "data": [
        {
          "username": "1659844732073054",
          "hotspot_id": 613,
          "update_time": "2019-07-19T08:51:38Z",
          "hotspot_desc": "hotspot-1",
          "user_id": 670465,
          "device_mac": "A0-DB-55-E9-26-2D",
          "start_time": "2019-07-19T08:51:38Z",
          "unit_id": 1281,
          "bytes_down": 0,
          "bytes_up": 0,
          "stop_time": "0001-01-01T00:00:00Z",
          "auth_time": "0001-01-01T00:00:00Z",
          "duration": 0,
          "unit_mac": "00-0C-B9-41-4C-FA",
          "session_key": "156358627200000001",
          "ip_address": "192.168.182.2",
          "id": 5272132,
          "unit": {
            "hotspot_id": 613,
            "uuid": "9c91558c-8fa4-4ffd-bab3-7ea36f148e67",
            "created": "2019-07-19T08:50:58Z",
            "name": "hs.test.localdomain",
            "secret": "fQvrvBqZ_JfA6nUG",
            "mac_address": "00-0D-B3-41-8C-AA",
            "id": 1281,
            "description": "unit description"
          },
          "device_id": 219238
        }
      ]
    }
  }

Input example (userInfo)
^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "userInfo",
    "userId": 670465,
    "token": "e31e2ca3948cd269293a13c9bd9366361f9af7f66e5fd3bce57f19b3489839ed",
    "icaroHost": "my.nethspot.com"
  }

Output example (userInfo)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "userInfo": {
      "username": "1659844732073054",
      "hotspot_id": 613,
      "marketing_auth": false,
      "account_type": "facebook",
      "name": "Tony Stark",
      "kbps_up": 0,
      "valid_until": "2019-08-18T08:51:37Z",
      "email_verified": false,
      "created": "2019-07-18T10:33:40Z",
      "survey_auth": false,
      "auto_login": false,
      "id": 670465,
      "reason": "",
      "valid_from": "2019-07-18T10:33:40Z",
      "max_navigation_time": 0,
      "country": "",
      "email": "tony@starkindustries.com",
      "kbps_down": 0,
      "max_navigation_traffic": 0
    }
  }

authentication/read
---------------------

This api returns user authentication data and hotspot configuration.

Input
^^^^^^

- ``appInfo``: ``token`` or ``configuration``

Input example (token)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "token"
  }

Output example (token)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "tokenData": {
      "token": "e31e2ca3948cd269293a13c9bd9366361f9af7f66e5fd3bce57f19b3489839ed",
      "icaroHost": "my.nethspot.com"
    }
  }

Input example (configuration)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "configuration"
  }

Output example (configuration)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "configuration": {
      "type": "service",
      "name": "dedalo",
      "props": {
        "status": "enabled",
        "SplashPageUrl": "",
        "IcaroHost": "my.nethspot.com",
        "AllowOrigins": "",
        "Uuid": "9c91558c-8fa4-4ffd-bab3-7ea36f148e67",
        "UnitName": "hs.test.localdomain",
        "ApiUrl": "",
        "Proxy": "disabled",
        "Description": "unit description",
        "Name": "hotspot-1",
        "LogTraffic": "disabled",
        "AaaUrl": "",
        "Id": "613",
        "Network": "192.168.182.0/24"
      }
    }
  }

authentication/validate
-------------------------

This api validates the input for user authentication.

Input
^^^^^^

- ``hostname``: Icaro host to connect to
- ``username``: username for Icaro webapp
- ``password``: password for Icaro webapp

Input example
^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "hostname": "my.nethspot.com",
    "username": "my-user",
    "password": "my-s3cr3t"
  }

Output example
^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "state": "success"
  }

authentication/execute
^^^^^^^^^^^^^^^^^^^^^^^^^^^

This api performs user authentication and token management

Input
^^^^^^

- ``action``: ``authenticate`` or ``saveToken``
- ``hostname``: Icaro host
- ``username``: username for Icaro webapp (only if ``action``: ``authenticate``)
- ``password``: password for Icaro webapp (only if ``action``: ``authenticate``)
- ``token``: authentication token (only if ``action``: ``saveToken``)

Input example (authenticate)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "action": "authenticate",
    "hostname": "my.nethspot.com",
    "username": "my-user",
    "password": "my-s3cr3t"
  }

Output example (authenticate)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "account_type": "reseller",
    "expires": "2019-07-20 10:01:33.588426569 +0000 UTC",
    "id": 1304,
    "status": "success",
    "subscription": {
      "id": 684,
      "valid_from": "2019-07-02T14:22:05Z",
      "valid_until": "2029-06-29T14:22:05Z",
      "created": "2019-07-02T14:22:05Z",
      "account_id": 1304,
      "subscription_plan": {
        "id": 4,
        "code": "premium",
        "name": "Premium",
        "description": "Premium plan",
        "price": 0,
        "period": 3650,
        "included_sms": 20,
        "max_units": 100,
        "advanced_report": true,
        "wings_customization": true,
        "social_analytics": true
      },
      "expired": false
    },
    "token": "e31e2ca3948cd269293a13c9bd9366361f9af7f66e5fd3bce57f19b3489839ed"
  }

Input example (saveToken)
^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "action": "saveToken",
    "hostname": "my.nethspot.com",
    "token": "e31e2ca3948cd269293a13c9bd9366361f9af7f66e5fd3bce57f19b3489839ed"
  }

Output example (saveToken)
^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "state": "success"
  }


settings/registration/read
----------------------------

This api retrieves hotspot list, hostname and network interfaces with role "hotspot" or empty.

Input
^^^^^

- ``appInfo``: ``hotspots``, ``networkDevices`` or ``hostname``
- ``hostname``: Icaro host (only if ``appInfo``: ``hotspots``)
- ``token``: authentication token (only if ``appInfo``: ``hotspots``)

Input example (hotspots)
^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "hotspots",
    "hostname": "my.nethspot.com",
    "token": "e31e2ca3948cd269293a13c9bd9366361f9af7f66e5fd3bce57f19b3489839ed"
  }

Output example (hotspots)
^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "hotspots": {
      "total": 2,
      "data": [
        {
          "Account": {
            "username": "",
            "name": "",
            "created": "0001-01-01T00:00:00Z",
            "email": "",
            "creator_id": 0,
            "type": "",
            "id": 0,
            "uuid": ""
          },
          "name": "hotspot-1",
          "created": "2019-07-04T09:52:15Z",
          "uuid": "5a5f3cb1-311e-4019-b589-d3ce43c43e7f",
          "account_id": 1304,
          "business_address": "test",
          "business_email": "test@test.com",
          "business_vat": "test",
          "business_name": "test",
          "id": 603,
          "description": "description"
        },
        {
          "Account": {
            "username": "",
            "name": "",
            "created": "0001-01-01T00:00:00Z",
            "email": "",
            "creator_id": 0,
            "type": "",
            "id": 0,
            "uuid": ""
          },
          "name": "hotspot-2",
          "created": "2019-07-04T15:56:04Z",
          "uuid": "9d0b3333-1cd6-47dc-972f-54ada5160d7b",
          "account_id": 1304,
          "business_address": "test",
          "business_email": "test@test.com",
          "business_vat": "test",
          "business_name": "test",
          "id": 605,
          "description": "other description"
        }
      ]
    }
  }

Input example (networkDevices)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "networkDevices"
  }

Output example (networkDevices)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "networkDevices": [
      {
        "hotspot_assigned": true,
        "name": "enp0s8"
      },
      {
        "hotspot_assigned": false,
        "name": "enp0s9"
      }
    ]
  }

Input example (hostname)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "appInfo": "hostname"
  }

Output example (hostname)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "hostname": "hotspot.test.nethesis.it"
  }


settings/registration/validate
--------------------------------

This api validates the input for user registration.

Input
^^^^^

- ``hotspotId``: hotspot ID to register
- ``networkDevice``: a network interface with role "hotspot" or empty
- ``networkAddress``: the IP range in CIDR notation used for hotpspot network
- ``dhcpRangeStart``: first IP address to assign to hotspot users
- ``dhcpRangeEnd``: last IP address to assign to hotspot users

Input example
^^^^^^^^^^^^^^^
::

  {
    "hotspotId": 615,
    "networkDevice": "enp3s0",
    "networkAddress": "192.168.182.0/24",
    "dhcpRangeStart": "192.168.182.10",
    "dhcpRangeEnd": "192.168.182.254"
  }

Output example
^^^^^^^^^^^^^^^
::

  {
    "state": "success"
  }


settings/registration/execute
--------------------------------

This api performs unit registration and unregistration.

Input
^^^^^

- ``action``: ``register`` or ``unregister``
- ``hotspotId``: hotspot ID to register (only if ``appInfo``: ``register``)
- ``hotspotName``: hotspot name, as displayed on Icaro host webapp (only if ``appInfo``: ``register``)
- ``unitDescription``: unit description (only if ``appInfo``: ``register``)
- ``networkDevice``: a network interface with role "hotspot" or empty (only if ``appInfo``: ``register``)
- ``networkAddress``: the IP range in CIRD notation used for hotpspot network (only if ``appInfo``: ``register``)
- ``dhcpRangeStart``: first IP address to assign to hotspot users (only if ``appInfo``: ``register``)
- ``dhcpRangeEnd``: last IP address to assign to hotspot users (only if ``appInfo``: ``register``)
- ``hostname``: Icaro host to connect to (only if ``appInfo``: ``register``)
- ``unitName``: hostname of local installation, default to FQDN (only if ``appInfo``: ``register``)
- ``logout``: ``true`` or ``false`` (only if ``appInfo``: ``unregister``)

Input example (register)
^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "action": "register",
    "hotspotId": 615,
    "hotspotName": "hotspot-2",
    "unitDescription": "1054",
    "networkDevice": "enp3s0",
    "networkAddress": "192.168.182.0/24",
    "hostname": "my.nethspot.com",
    "unitName": "hs.test.localdomain",
    "dhcpRangeStart": "192.168.182.10",
    "dhcpRangeEnd": "192.168.182.254"
  }

Input example (unregister)
^^^^^^^^^^^^^^^^^^^^^^^^^^
::

  {
    "action": "unregister",
    "logout": true
  }

settings/configuration/read
-----------------------------

This api returns the status of Squid proxy

Input
^^^^^

- no input

Output example
^^^^^^^^^^^^^^^
::

  {
    "proxyStatus": "enabled"
  }

settings/configuration/validate
--------------------------------

This api validates the input for dedalo configuration update.

Input
^^^^^

- ``network``: the IP range in CIDR notation used for hotpspot network
- ``proxy``: proxy status, can be ``enabled`` or ``disabled``
- ``logTraffic``: ``enabled`` if hotspot traffic should be logged while using proxy, else ``disabled``
- ``device``: a network interface with role "hotspot" or empty
- ``ipAddress``: IP address currently assigned to ``device`` interface (empty string if no address is assigned)
- ``dhcpRangeStart``: first IP address to assign to hotspot users
- ``dhcpRangeEnd``: last IP address to assign to hotspot users

Input example
^^^^^^^^^^^^^^^
::

  {
    "network": "192.168.182.0/24",
    "proxy": "disabled",
    "logTraffic": "enabled",
    "device": "enp3s0",
    "ipAddress": "",
    "dhcpRangeStart": "192.168.182.10",
    "dhcpRangeEnd": "192.168.182.90"
  }

Output example
^^^^^^^^^^^^^^^
::

  {
    "state": "success"
  }

settings/configuration/update
------------------------------

This api updates dedalo configuration.

Input
^^^^^

- same as ``settings/configuration/validate``
