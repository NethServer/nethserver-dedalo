Name: nethserver-dedalo
Version: 1.0.2
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
* Thu Apr 19 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.2-1
- Use Hotspot Id instead of Hotspot name in unit registration - Bug Nethesis/icaro#67

* Tue Apr 03 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.1-1
- UI improvements

* Fri Mar 30 2018 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.0.0-1
- Hotspot: add Dedalo client for Icaro - NethServer/dev#5422

