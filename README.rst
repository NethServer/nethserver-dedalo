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
- ``Id``: name of the Hotspot already present inside Icaro, eg: ``MyHotelCompany``
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

