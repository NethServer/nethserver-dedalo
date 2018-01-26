=======
Hotspot
=======

An hotspot allow you to provide Wireless/Wired connectivity through accounting of users (it is tipically used for guests).


General
========

General configurations.

Enable
    Enable/Disable Hotspot.

Network Device
    Choose the network interface for the hotspot, chosse between free interfaces.

Network
    The hotspot's guests will obtain an IP address from this network. You must choose a private network different from other ones managed by
    NethServer Enterprise. Use a CIDR format (default: 192.168.182.0/24).

Enable transparent proxy on hotspot
    If checked, all web traffic will be redirect to Squid proxy.

Enable content filter
    If checked, web traffic will be filtered using the content filter (SquiGuard). 
    
Log traffic
    As default configuration, Squid will not log the hotspot web traffic for privacy reasons.
    If this option is checked, all hotspot web traffic will be logged inside ``/var/log/squid/hotspot.log`` file.


Portal
=======

Customize the captive portal page.

Header
    Text shown in the upper side of the captive portal page.

Footer
    Text shown in the lower side of the captive portal page.

Disclaimer
    Disclaimer message with Terms Of Usage.

Allowed Sites
    Websites allowed without authentication.

Allowed Sites To Show
    How many allowed websites to show in the portal.


Server
======

Set the radius server to use.

Hostname / IP
    Set the IP address or the hostname of the radius server. The system is already set to use the Operation Center.

Secret
    Passphrase used by the radius server. The default one is used for the Operation Center.

Authentication port
    The port used by the radius server for authorization/authentication (Default: 1812).

Accounting port
    The port used by the radius server for accounting (Default: 1813).


