Name: nethserver-dedalo
Version: 1.3.1
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

