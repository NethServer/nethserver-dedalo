Name: nethserver-dedalo
Version: 1.0.5
Release: 1%{?dist}
Summary: Dedalo integration for NethServer
BuildArch: noarch

License: GPLv3
URL: https://github.com/nethesis/icaro
Source: %{name}-%{version}.tar.gz

BuildRequires: nethserver-devtools
Requires: nethserver-firewall-base, dedalo

%description
Dedalo capitve portal based on CoovaChilli

%prep
%setup -q


%build
%{makedocs}
perl createlinks


%install
rm -rf %{buildroot}
(cd root   ; find . -depth -print | cpio -dump %{buildroot})
%{genfilelist} %{buildroot} > e-smith-%{version}-filelist


%files -f e-smith-%{version}-filelist
%defattr(-,root,root)
%dir %{_nseventsdir}/%{name}-update

%changelog
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

