#
# Copyright (C) 2019 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see COPYING.
#

use strict;

package NethServer::Firewall::Hotspot;
use NethServer::Firewall qw(register_callback);
use esmith::ConfigDB;
use esmith::util;

register_callback(\&hotspot_network);

#
# Search inside hotspot network
#
sub hotspot_network
{
    my $value = shift;

    my $db = esmith::ConfigDB->open_ro();
    my $status = $db->get_prop('dedalo', 'status') || 'disabled';
    if ($status eq 'enabled') {
        my $net= $db->get_prop('dedalo', 'Network') || return '';
        if (Net::IPv4Addr::ipv4_in_network($net, $value)) {
            return 'hotsp';
        }

    }

    return '';
}

