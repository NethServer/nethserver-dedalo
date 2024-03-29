Name: nethserver-dedalo
Version: 1.5.1
Release: 1%{?dist}
Summary: Dedalo integration for NethServer
BuildArch: noarch

License: GPLv3
URL: https://github.com/nethesis/icaro
Source: %{name}-%{version}.tar.gz
Source1: %{name}-cockpit.tar.gz

BuildRequires: nethserver-devtools
Requires: nethserver-firewall-base, dedalo

%description
Dedalo captive portal based on CoovaChilli

%prep
%setup -q


%build
%{makedocs}
perl createlinks
sed -i 's/_RELEASE_/%{version}/' %{name}.json
mkdir -p root%{perl_vendorlib}
mv -v NethServer root%{perl_vendorlib}


%install
rm -rf %{buildroot}
(cd root   ; find . -depth -print | cpio -dump %{buildroot})

mkdir -p %{buildroot}/usr/share/cockpit/%{name}/
mkdir -p %{buildroot}/usr/share/cockpit/nethserver/applications/
mkdir -p %{buildroot}/usr/libexec/nethserver/api/%{name}/
tar xvf %{SOURCE1} -C %{buildroot}/usr/share/cockpit/%{name}/
cp -a %{name}.json %{buildroot}/usr/share/cockpit/nethserver/applications/
cp -a api/* %{buildroot}/usr/libexec/nethserver/api/%{name}/

%{genfilelist} %{buildroot} --file /etc/sudoers.d/50_nsapi_nethserver_dedalo 'attr(0440,root,root)' > e-smith-%{version}-filelist


%files -f e-smith-%{version}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update

%changelog
* Thu Nov 10 2022 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.5.1-1
- Dedalo: registration fails if interface-update is triggered - Bug NethServer/dev#6717

* Thu Feb 03 2022 Edoardo Spadoni <edoardo.spadoni@nethesis.it> - 1.5.0-1
- Customize Chilli DHCP Leases - NethServer/dev#6634

* Tue Dec 14 2021 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.4.0-1
- nethserver-dedalo: hotspot users aren't properly authenticated - Bug NethServer/dev#6610

* Fri Nov 27 2020 Matteo Valentini <matteo.valentini@nethesis.it> - 1.3.6-1
- nethserver-dedalo: Ethernet offload is still active if a vlan is used as hotspot interface - Bug NethServer/dev#6343

* Fri Sep 18 2020 Matteo Valentini <matteo.valentini@nethesis.it> - 1.3.5-1
- nethserver-dedalo: disable ethernet offload on hostpot interface - Bug NethServer/dev#6264

* Tue Apr 07 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.4-1
- Restore configuration without network override - NethServer/dev#6099

* Wed Jan 08 2020 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.3-1
- Cockpit: change package Dashboard page title - NethServer/dev#6004

* Mon Oct 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.2-1
- Logs page in Cockpit - Bug NethServer/dev#5866

* Thu Oct 10 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.1-1
- Dedalo: remove bandwidth columns in Dashboard page - NethServer/dev#5860

* Tue Oct 01 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.3.0-1
- Sudoers based authorizations for Cockpit UI - NethServer/dev#5805

* Tue Sep 03 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.1-1
- Cockpit. List correct application version - Nethserver/dev#5819
- Dedalo: username containing '@' - Bug NethServer/dev#5821

* Thu Jul 25 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.0-1
- Dedalo Cockpit UI - NethServer/dev#5790

* Mon Jul 22 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.1-1
- Dedalo: corrupted squid access log - Bug NethServer/dev#5792

* Tue May 28 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.0-1
- Dedalo: support Web Proxy bypasses - NethServer/dev#5765

* Thu Mar 07 2019 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.7-1
- Firewall library: resolve hotspot zone (#15)

* Mon Aug 06 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.6-1
- Web Proxy  not properly working with nethserver-dedalo - Bug NethServer/dev#5548

* Wed Aug 01 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.5-1
- UI: adapt hotspot list for Icaro v30

* Wed May 16 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.4-1
- Write hotspot proxy logs only on /var/log/squid/dedalo.log - nethserver/dev#5473

* Mon May 07 2018 Matteo Valentini <matteo.valentini@nethesis.it> - 1.0.3-1
- Dedalo: DHCP network doesn't change when modified from web UI - Bug NethServer/dev#5475

* Thu Apr 19 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1
- Use Hotspot Id instead of Hotspot name in unit registration - Bug Nethesis/icaro#67

* Tue Apr 03 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1
- UI improvements

* Fri Mar 30 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- Hotspot: add Dedalo client for Icaro - NethServer/dev#5422

