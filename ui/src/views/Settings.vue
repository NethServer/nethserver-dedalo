<template>
  <div>
    <h1>{{$t('settings.title')}}</h1>
    
    <!-- error message -->
    <div v-if="errorMessage" class="alert alert-danger alert-dismissable">
      <button type="button" class="close" @click="closeErrorMessage()" aria-label="Close">
        <span class="pficon pficon-close"></span>
      </button>
      <span class="pficon pficon-error-circle-o"></span>
      {{ errorMessage }}.
    </div>

    <!-- warning message -->
    <div v-if="warningMessage" class="alert alert-warning alert-dismissable">
      <button type="button" class="close" @click="closeWarningMessage()" aria-label="Close">
        <span class="pficon pficon-close"></span>
      </button>
      <span class="pficon pficon-warning-triangle-o"></span>
      {{ warningMessage }}.
    </div>

    <div v-if="!uiLoaded" class="spinner spinner-lg"></div>
    <div v-if="uiLoaded">
      <!-- authentication form -->
      <div v-if="!authenticated">
        <form class="form-horizontal" v-on:submit.prevent="btAuthenticateClick">
          <!-- hostname -->
          <div class="form-group" :class="{ 'has-error': showErrorHostname }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.hostname')}}</label>
            <div class="col-sm-5">
              <input type="input" class="form-control" v-model="dedaloConfig.IcaroHost" required>
              <span class="help-block" v-if="showErrorHostname">{{$t('settings.hostname_validation')}}</span>
            </div>
          </div>
          <!-- username -->
          <div class="form-group" :class="{ 'has-error': showErrorUsername }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.username')}}</label>
            <div class="col-sm-5">
              <input type="input" class="form-control" v-model="username" required>
              <span class="help-block" v-if="showErrorUsername">{{$t('settings.username_validation')}}</span>
            </div>
          </div>
          <!-- password -->
          <div class="form-group" :class="{ 'has-error': showErrorPassword }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.password')}}</label>
            <div class="col-sm-5">
              <input
                :type="passwordVisible ? 'text' : 'password'"
                class="form-control"
                v-model="password"
                required
              >
              <span class="help-block" v-if="showErrorPassword">{{$t('settings.password_validation')}}</span>
            </div>
            <!-- toggle password visibility -->
            <div class="col-sm-2 adjust-index">
              <button
                tabindex="-1"
                type="button"
                class="btn btn-primary"
                @click="togglePasswordVisibility()"
              >
                <span :class="[!passwordVisible ? 'fa fa-eye' : 'fa fa-eye-slash']"></span>
              </button>
            </div>
          </div>
          <!-- authenticate button -->
          <div class="form-group">
            <label class="col-sm-2 control-label" for="textInput-modal-markup"></label>
            <div class="col-sm-5">
              <button class="btn btn-primary" type="submit">{{$t('settings.authenticate')}}</button>
            </div>
          </div>
        </form>
      </div>

      <!-- registration form -->
      <div v-if="authenticated && !registered">
        <form class="form-horizontal" v-on:submit.prevent="btRegisterClick">
          <!-- hotspot -->
          <div class="form-group" :class="{ 'has-error': showErrorHotspot }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.parent_hotspot')}}</label>
            <div class="col-sm-5">
              <select required type="text" class="combobox form-control" v-model="selectedHotspotIndex">
                <option
                  v-for="(hotspot, index) in hotspotList"
                  v-bind:key="index"
                  :value="index"
                >{{ hotspot.name }} ({{ hotspot.description }})</option>
              </select>
              <span class="help-block" v-if="showErrorHotspot">{{$t('settings.hotspot_validation')}}</span>
            </div>
          </div>
          <!-- unit name -->
          <div class="form-group">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.unit_name')}}</label>
            <div class="col-sm-5">
              <input type="input" class="form-control" v-model="dedaloConfig.UnitName" required disabled>
            </div>
          </div>
          <!-- unit description -->
          <div class="form-group">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.unit_description')}}</label>
            <div class="col-sm-5">
              <input type="input" class="form-control" v-model="dedaloConfig.Description">
            </div>
          </div>
          <!-- network device -->
          <div class="form-group" :class="{ 'has-error': showErrorNetworkDevice }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.network_device')}}</label>
            <div class="col-sm-5">
              <select required type="text" class="combobox form-control" v-model="networkDevice" @change="changedNetworkDevice()">
                <option
                  v-for="(device, index) in networkDeviceList"
                  v-bind:key="index"
                  :value="device.name"
                >{{ device.name + (device.hotspot_assigned ? (" - " + $t('settings.hotspot_assigned')) : "") }}</option>
              </select>
              <span class="help-block" v-if="showErrorNetworkDevice">{{$t('settings.network_device_validation')}}</span>
            </div>
          </div>
          <!-- network address -->
          <div class="form-group" :class="{ 'has-error': showErrorNetworkAddress }">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.network_address')}}
              <doc-info
                :placement="'top'"
                :title="$t('settings.network_address')"
                :chapter="'network_address'"
                :inline="true"
              ></doc-info>
            </label>
            <div class="col-sm-3">
              <input type="input" required class="form-control" v-model="dedaloConfig.Network">
              <span class="help-block" v-if="showErrorNetworkAddress">{{$t('settings.network_address_validation')}}</span>
            </div>
            <!-- default dhcp range -->
            <div class="col-sm-2 adjust-index">
              <button
                tabindex="-1"
                type="button"
                class="btn btn-primary"
                @click="defaultDhcpRange()"
              >
              {{$t('settings.default_dhcp_range')}}
              </button>
            </div>
          </div>
          <!-- dhcp range start -->
          <div class="form-group" :class="{ 'has-error': showErrorDhcpRangeStart }">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.dhcp_range_start')}}
              <doc-info
                :placement="'top'"
                :title="$t('settings.dhcp_range_start')"
                :chapter="'dhcp_range_start'"
                :inline="true"
              ></doc-info>
            </label>
            <div class="col-sm-3">
              <input type="input" required class="form-control" v-model="dhcpRangeStart">
              <span class="help-block" v-if="showErrorDhcpRangeStart">
                {{ errorDhcpRangeStart === 'invalid_range' ? $t('settings.dhcp_range_start_validation_invalid_range') : $t('settings.dhcp_range_start_validation') }}
              </span>
            </div>
          </div>
          <!-- dhcp range end -->
          <div class="form-group" :class="{ 'has-error': showErrorDhcpRangeEnd }">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.dhcp_range_end')}}
              <doc-info
                :placement="'top'"
                :title="$t('settings.dhcp_range_end')"
                :chapter="'dhcp_range_end'"
                :inline="true"
              ></doc-info>
            </label>
            <div class="col-sm-3">
              <input type="input" required class="form-control" v-model="dhcpRangeEnd">
              <span class="help-block" v-if="showErrorDhcpRangeEnd">
                {{ errorDhcpRangeEnd === 'invalid_range' ? $t('settings.dhcp_range_end_validation_invalid_range') : $t('settings.dhcp_range_end_validation') }}
              </span>
            </div>
          </div>
          <!-- register button -->
          <div class="form-group">
            <label class="col-sm-2 control-label" for="textInput-modal-markup"></label>
            <div class="col-sm-5">
              <button class="btn btn-primary" type="submit">{{$t('settings.register')}}</button>
            </div>
          </div>
        </form>
      </div>

      <!-- registration completed form -->
      <div v-if="authenticated && registered">
        <form class="form-horizontal" v-on:submit.prevent="btSaveClick">
          <h3>{{$t('settings.hotspot')}}</h3>
          <!-- hotspot -->
          <div class="form-group">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.parent_hotspot')}}</label>
            <div class="col-sm-5">
              <input type="input" class="form-control" v-model="hotspotList[selectedHotspotIndex].name" required disabled>
            </div>
          </div>
          <!-- network device -->
          <div class="form-group" :class="{ 'has-error': showErrorNetworkDevice }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.network_device')}}</label>
            <div class="col-sm-5">
              <select required type="text" class="combobox form-control" v-model="networkDevice" @change="changedNetworkDevice()">
                <option
                  v-for="(device, index) in networkDeviceList"
                  v-bind:key="index"
                  :value="device.name"
                >{{ device.name + (device.hotspot_assigned ? (" - " + $t('settings.hotspot_assigned')) : "") }}</option>
              </select>
              <span class="help-block" v-if="showErrorNetworkDevice">{{$t('settings.network_device_validation')}}</span>
            </div>
          </div>
          <!-- network address -->
          <div class="form-group" :class="{ 'has-error': showErrorNetworkAddress }">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.network_address')}}
              <doc-info
                :placement="'top'"
                :title="$t('settings.network_address')"
                :chapter="'network_address'"
                :inline="true"
              ></doc-info>
            </label>
            <div class="col-sm-3">
              <input type="input" required class="form-control" v-model="dedaloConfig.Network">
              <span class="help-block" v-if="showErrorNetworkAddress">{{$t('settings.network_address_validation')}}</span>
            </div>
            <!-- default dhcp range -->
            <div class="col-sm-2 adjust-index">
              <button
                tabindex="-1"
                type="button"
                class="btn btn-primary"
                @click="defaultDhcpRange()"
              >
              {{$t('settings.default_dhcp_range')}}
              </button>
            </div>
          </div>
          <!-- dhcp range start -->
          <div class="form-group" :class="{ 'has-error': showErrorDhcpRangeStart }">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.dhcp_range_start')}}
              <doc-info
                :placement="'top'"
                :title="$t('settings.dhcp_range_start')"
                :chapter="'dhcp_range_start'"
                :inline="true"
              ></doc-info>
            </label>
            <div class="col-sm-3">
              <input type="input" required class="form-control" v-model="dhcpRangeStart">
              <span class="help-block" v-if="showErrorDhcpRangeStart">
                {{ errorDhcpRangeStart === 'invalid_range' ? $t('settings.dhcp_range_start_validation_invalid_range') : $t('settings.dhcp_range_start_validation') }}
              </span>
            </div>
          </div>
          <!-- dhcp range end -->
          <div class="form-group" :class="{ 'has-error': showErrorDhcpRangeEnd }">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.dhcp_range_end')}}
              <doc-info
                :placement="'top'"
                :title="$t('settings.dhcp_range_end')"
                :chapter="'dhcp_range_end'"
                :inline="true"
              ></doc-info>
            </label>
            <div class="col-sm-3">
              <input type="input" required class="form-control" v-model="dhcpRangeEnd">
              <span class="help-block" v-if="showErrorDhcpRangeEnd">
                {{ errorDhcpRangeEnd === 'invalid_range' ? $t('settings.dhcp_range_end_validation_invalid_range') : $t('settings.dhcp_range_end_validation') }}
              </span>
            </div>
          </div>

          <div class="divider" v-if="proxyStatus === 'enabled'"></div>
          <h3 v-if="proxyStatus === 'enabled'">{{$t('settings.proxy')}}</h3>

          <!-- proxy -->
          <div class="form-group" :class="{ 'has-error': showErrorProxy }" v-if="proxyStatus === 'enabled'">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.enable_transparent_proxy_on_hotspot')}}</label>
            <div class="col-sm-5">
              <toggle-button
                class="min-toggle"
                :width="40"
                :height="20"
                :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                :value="dedaloConfig.Proxy === 'enabled'"
                :sync="true"
                @change="toggleProxy()"
              />
              <span class="help-block" v-if="showErrorProxy">{{$t('settings.proxy_validation')}}</span>
            </div>
          </div>
          <!-- proxy - log traffic -->
          <div class="form-group" v-if="dedaloConfig.Proxy === 'enabled' && proxyStatus === 'enabled'" :class="{ 'has-error': showErrorLogTraffic }">
            <label
              class="col-sm-2 control-label"
              for="textInput-modal-markup"
            >{{$t('settings.log_traffic')}}</label>
            <div class="col-sm-5">
              <input
                @click="toggleLogTraffic()"
                v-model="logTraffic"
                type="checkbox"
                class="form-control"
              >
              <span class="help-block" v-if="showErrorLogTraffic">{{$t('settings.log_traffic_validation')}}</span>
            </div>
          </div>
          <!-- save button -->
          <div class="form-group">
            <label class="col-sm-2 control-label" for="textInput-modal-markup"></label>
            <div class="col-sm-5">
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </div>

          <div class="divider"></div>
          <h3>{{$t('settings.unregister')}}</h3>

          <!-- unregister button -->
          <div class="form-group margin-top-20">
            <label class="col-sm-2 control-label" for="textInput-modal-markup">
              {{$t('settings.unregister_unit')}}
            </label>
            <div class="col-sm-5">
              <button class="btn btn-danger" type="button" @click="btUnregisterClick()">{{$t('settings.unregister')}}</button>
            </div>
          </div>
        </form>
      </div>

      <div class="modal" id="unregisterModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">{{$t('settings.unregister_unit')}}</h4>
            </div>
            <form class="form-horizontal" v-on:submit.prevent="unregister()">
              <div class="modal-body">
                <div class="form-group">
                  <label class="col-sm-3 control-label" for="textInput-modal-markup">{{$t('are_you_sure')}}?</label>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
                <button class="btn btn-danger" type="submit">{{$t('settings.unregister')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Settings",
  mounted() {
    this.getToken()
  },
  data() {
    return {
      uiLoaded: false,
      username: "",
      password: "",
      passwordVisible: false,
      dedaloConfig: null,
      authenticated: false,
      registered: false,
      showErrorHostname: false,
      showErrorUsername: false,
      showErrorPassword: false,
      selectedHotspotIndex: 0,
      hotspotList: [],
      networkDevice: "",
      networkDeviceList: [],
      accountType: "",
      token: "",
      showErrorHotspot: false,
      showErrorNetworkDevice: false,
      showErrorNetworkAddress: false,
      showErrorProxy: false,
      showErrorLogTraffic: false,
      errorMessage: null,
      proxyStatus: "",
      logTraffic: false,
      oldNetworkDevice: "",
      warningMessage: null,
      showErrorDhcpRangeStart: false,
      showErrorDhcpRangeEnd: false,
      dhcpRangeStart: "",
      dhcpRangeEnd: "",
      errorDhcpRangeStart: "",
      errorDhcpRangeEnd: ""
    }
  },
  methods: {
    getToken() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/authentication/read"],
        { "appInfo": "token" },
        null,
        function(success) {
          var tokenOutput = JSON.parse(success);
          ctx.tokenSuccess(tokenOutput)
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_token_from_file"), error)
        }
      );
    },
    tokenSuccess(tokenOutput) {
      this.token = tokenOutput.tokenData.token;

      if (this.token) {
        this.authenticated = true
      } else {
        this.authenticated = false
      }
      this.getConfig()
    },
    getConfig() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/authentication/read"],
        { "appInfo": "configuration" },
        null,
        function(success) {
          success = JSON.parse(success);
          ctx.dedaloConfig = success.configuration.props;
          ctx.logTraffic = ctx.dedaloConfig.LogTraffic === 'enabled'

          if (ctx.token) {
            ctx.authenticationSuccess()
          } else {
            ctx.uiLoaded = true
          }
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_reading_configuration"), error)
        }
      );
    },
    showErrorMessage(errorMessage, error) {
      console.error(errorMessage, error) /* eslint-disable-line no-console */
      this.errorMessage = errorMessage
    },
    togglePasswordVisibility() {
      this.passwordVisible = !this.passwordVisible;
    },
    btAuthenticateClick() {
      this.showErrorHostname = false;
      this.showErrorUsername = false;
      this.showErrorPassword = false;
      this.errorMessage = null;

      var authObj = {
        "action": "authenticate",
        "hostname": this.dedaloConfig.IcaroHost,
        "username": this.username,
        "password": this.password
      }
      var ctx = this;      
      nethserver.exec(
        ["nethserver-dedalo/authentication/validate"],
        authObj,
        null,
        function(success) {
          ctx.authenticationValidationSuccess(authObj)
        },
        function(error, data) {
          ctx.authenticationValidationError(error, data)
        }
      );
    },
    authenticationValidationSuccess(authObj) {
      var ctx = this
      // execute authentication
      nethserver.exec(
        ["nethserver-dedalo/authentication/execute"],
        authObj,
        null,
        function(authOutput) {
          authOutput = JSON.parse(authOutput);
          
          if (!authOutput.token) {
            ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_authentication_token"))
            return
          }

          if (authOutput.account_type != "reseller") {
            ctx.showErrorMessage(ctx.$i18n.t("settings.account_type_expected_reseller") + " " + authOutput.account_type)
            return
          }
          ctx.token = authOutput.token
          ctx.saveTokenToFile()
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.authentication_error"), error)
        }
      );
    },
    saveTokenToFile() {
      var saveTokenObj = {
        "action": "saveToken",
        "hostname": this.dedaloConfig.IcaroHost,
        "token": this.token
      }
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/authentication/execute"],
        saveTokenObj,
        null,
        function(success) {
          ctx.authenticationSuccess()
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_saving_token_to_file"), error)
        }
      );
    },
    authenticationSuccess() {
      this.authenticated = true
      this.uiLoaded = false;
      // retrieve hotspot list
      var jsonObj = {
        "appInfo": "hotspots",
        "hostname": this.dedaloConfig.IcaroHost,
        "token": this.token
      }
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/read"],
        jsonObj,
        null,
        function(success) {
          var hotspotsOutput = JSON.parse(success);
          ctx.readHotspotsSuccess(hotspotsOutput)
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_hotspot_list"), error)
        }
      );
    },
    authenticationValidationError(error, data) {
      var errorData = JSON.parse(data);

      for (var e in errorData.attributes) {
        var attr = errorData.attributes[e]
        var param = attr.parameter;

        if (param === 'hostname') {
          this.showErrorHostname = true;
        } else if (param === 'username') {
          this.showErrorUsername = true;
        } else if (param === 'password') {
          this.showErrorPassword = true;
        }
      }
    },
    readHotspotsSuccess(hotspotsOutput) {
      this.hotspotsLoaded(hotspotsOutput)

      // retrieve network devices
      var jsonObj = {
        "appInfo": "networkDevices"
      }
      var ctx = this
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/read"],
        jsonObj,
        null,
        function(success) {
          var networkDevicesOutput = JSON.parse(success);
          ctx.readNetworkDevicesSuccess(networkDevicesOutput)
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_network_devices"), error)
        }
      );
    },
    readNetworkDevicesSuccess(networkDevicesOutput) {
      var networkDevicesOk = this.networkDevicesLoaded(networkDevicesOutput)
      if (!networkDevicesOk) {
        return
      }
      var dhcpRangeObj;

      if (this.dedaloConfig.DhcpStart) {
        // dhcp range is present in configuration
        dhcpRangeObj = {
          "appInfo": "dhcpRange",
          "dhcpStart": this.dedaloConfig.DhcpStart,
          "dhcpEnd": this.dedaloConfig.DhcpEnd,
          "networkAddress": this.dedaloConfig.Network
        }
      } else {
        // dhcp range not present in configuration
        dhcpRangeObj = {
          "appInfo": "dhcpRange",
          "networkAddress": this.dedaloConfig.Network
        }
      }
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/read"],
        dhcpRangeObj,
        null,
        function(success) {
          var dhcpRangeOutput = JSON.parse(success);
          ctx.dhcpRangeSuccess(dhcpRangeOutput)
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_range_dhcp"), error)
        }
      );
    },
    dhcpRangeSuccess(dhcpRangeOutput) {
      var dhcpRange = dhcpRangeOutput.dhcpRange
      this.dhcpRangeStart = dhcpRange.start
      this.dhcpRangeEnd = dhcpRange.end

      if (!this.dedaloConfig.UnitName) {
        // retrieve hostname and assign it to UnitName
        var jsonObj = {
          "appInfo": "hostname"
        }
        var ctx = this;
        nethserver.exec(
          ["nethserver-dedalo/settings/registration/read"],
          jsonObj,
          null,
          function(success) {
            var hostnameOutput = JSON.parse(success);
            ctx.dedaloConfig.UnitName = hostnameOutput.hostname
            ctx.uiLoaded = true
          },
          function(error, data) {
            ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_hostname"), error)
          }
        );
      }

      if (this.dedaloConfig.Id) {
        this.registrationSuccess()
      }
      this.uiLoaded = true
    },
    btRegisterClick() {
      this.showErrorHotspot = false;
      this.showErrorUnitDescription = false;
      this.showErrorNetworkDevice = false;
      this.showErrorNetworkAddress = false;
      this.showErrorDhcpRangeStart = false;
      this.showErrorDhcpRangeEnd = false;

      var registerObjValidate = {
        "hotspotId": this.hotspotList[this.selectedHotspotIndex].id,
        "unitDescription": this.dedaloConfig.Description,
        "networkDevice": this.networkDevice,
        "networkAddress": this.dedaloConfig.Network,
        "dhcpRangeStart": this.dhcpRangeStart,
        "dhcpRangeEnd": this.dhcpRangeEnd
      }
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/validate"],
        registerObjValidate,
        null,
        function(success) {
          ctx.registrationValidationSuccess(registerObjValidate)
        },
        function(error, data) {
          ctx.registrationValidationError(error, data)
        }
      );
    },
    registrationValidationError(error, data) {
      var errorData = JSON.parse(data);

      for (var e in errorData.attributes) {
        var attr = errorData.attributes[e]
        var param = attr.parameter;

        if (param === 'hotspot') {
          this.showErrorHotspot = true;

        } else if (param === 'unitDescription') {
          this.showErrorUnitDescription = true;
        } else if (param === 'networkDevice') {
          this.showErrorNetworkDevice = true;
        } else if (param === 'networkAddress') {
          this.showErrorNetworkAddress = true;
        } else if (param === 'dhcpRangeStart') {
          this.showErrorDhcpRangeStart = true;
          this.errorDhcpRangeStart = attr.error;
        } else if (param === 'dhcpRangeEnd') {
          this.showErrorDhcpRangeEnd = true;
          this.errorDhcpRangeEnd = attr.error;
        }
      }
    },
    registrationValidationSuccess(registerObjValidate) {
      this.uiLoaded = false
      nethserver.notifications.success = this.$i18n.t("settings.registration_successful");
      nethserver.notifications.error = this.$i18n.t("settings.registration_failed");

      var registerObjExecute = {
        "action": "register",
        "hotspotId": registerObjValidate.hotspotId,
        "hotspotName": this.hotspotList[this.selectedHotspotIndex].name,
        "unitDescription": registerObjValidate.unitDescription,
        "networkDevice": registerObjValidate.networkDevice,
        "networkAddress": registerObjValidate.networkAddress,
        "hostname": this.dedaloConfig.IcaroHost,
        "unitName": this.dedaloConfig.UnitName,
        "dhcpRangeStart": this.dhcpRangeStart,
        "dhcpRangeEnd": this.dhcpRangeEnd
      }
      var ctx = this

      // execute registration
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/execute"],
        registerObjExecute,
        function(stream) {
          console.info("dedalo-register", stream); /* eslint-disable-line no-console */
        },
        function(success) {
          ctx.registrationSuccess()
        },
        function(error) {
          console.error(error)  /* eslint-disable-line no-console */
        }
      );
    },
    registrationSuccess() {
      this.registered = true
      this.oldNetworkDevice = this.networkDevice
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/settings/configuration/read"],
        {},
        null,
        function(success) {
          var proxyStatusOutput = JSON.parse(success);
          ctx.proxyStatus = proxyStatusOutput.proxyStatus
          ctx.uiLoaded = true
        },
        function(error, data) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_proxy_status"), error)
        }
      );
    },
    btSaveClick() {
      // validation
      this.showErrorNetworkDevice = false;
      this.showErrorNetworkAddress = false;
      this.showErrorProxy = false;
      this.showErrorLogTraffic = false;
      this.showErrorDhcpRangeStart = false;
      this.showErrorDhcpRangeEnd = false;
      this.warningMessage = null

      var networkDeviceObj = this.networkDeviceList.find(dev => dev.name === this.networkDevice);

      var configObjValidate = {
        "network": this.dedaloConfig.Network,
        "proxy": this.dedaloConfig.Proxy,
        "logTraffic": this.logTraffic ? 'enabled' : 'disabled',
        "device": this.networkDevice,
        "ipAddress": networkDeviceObj.ip_address,
        "dhcpRangeStart": this.dhcpRangeStart,
        "dhcpRangeEnd": this.dhcpRangeEnd
      }
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/settings/configuration/validate"],
        configObjValidate,
        null,
        function(success) {
          ctx.configurationValidationSuccess(configObjValidate)
        },
        function(error, data) {
          ctx.configurationValidationError(error, data)
        }
      );
    },
    configurationValidationSuccess(configObjValidate) {
      nethserver.notifications.success = this.$i18n.t("settings.configuration_update_successful");
      nethserver.notifications.error = this.$i18n.t("settings.configuration_update_failed");

      var ctx = this
      nethserver.exec(
        ["nethserver-dedalo/settings/configuration/update"],
        configObjValidate,
        function(stream) {
          console.info("dedalo-configuration-update", stream); /* eslint-disable-line no-console */
        },
        function(success) {
          ctx.configurationUpdateSuccess()
        },
        function(error) {
          console.error(error)  /* eslint-disable-line no-console */
        }
      );
    },
    configurationValidationError(error, data) {
      var errorData = JSON.parse(data);

      for (var e in errorData.attributes) {
        var attr = errorData.attributes[e]
        var param = attr.parameter;

        if (param === 'network') {
          this.showErrorNetworkAddress = true;
        } else if (param === 'proxy') {
          this.showErrorProxy = true;
        } else if (param === 'logTraffic') {
          this.showErrorLogTraffic = true;
        } else if (param === 'device') {
          this.showErrorNetworkDevice = true;
        } else if (param === 'dhcpRangeStart') {
          this.showErrorDhcpRangeStart = true;
          this.errorDhcpRangeStart = attr.error;
        } else if (param === 'dhcpRangeEnd') {
          this.showErrorDhcpRangeEnd = true;
          this.errorDhcpRangeEnd = attr.error;
        }
      }
    },
    configurationUpdateSuccess() {
      if (this.networkDevice != this.oldNetworkDevice) {
        this.unregisterAndRegister()
      } else {
        this.getConfig()
      }
    },
    unregisterAndRegister() {
      // unregister unit with old network address
      nethserver.notifications.success = this.$i18n.t("settings.unregister_successful");
      nethserver.notifications.error = this.$i18n.t("settings.unregister_failed");

      var unregisterObj = {
        "action": "unregister",
        "logout": false
      }
      var ctx = this
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/execute"],
        unregisterObj,
        function(stream) {
          console.info("dedalo-unregister", stream); /* eslint-disable-line no-console */
        },
        function(success) {
          // re-register unit with new network address
          nethserver.notifications.success = ctx.$i18n.t("settings.registration_successful");
          nethserver.notifications.error = ctx.$i18n.t("settings.registration_failed");

          var registerObjExecute = {
            "action": "register",
            "hotspotId": ctx.hotspotList[ctx.selectedHotspotIndex].id,
            "hotspotName": ctx.hotspotList[ctx.selectedHotspotIndex].name,
            "unitDescription": ctx.dedaloConfig.Description,
            "networkDevice": ctx.networkDevice,
            "networkAddress": ctx.dedaloConfig.Network,
            "hostname": ctx.dedaloConfig.IcaroHost,
            "unitName": ctx.dedaloConfig.UnitName,
            "dhcpRangeStart": ctx.dhcpRangeStart,
            "dhcpRangeEnd": ctx.dhcpRangeEnd
          }
          nethserver.exec(
            ["nethserver-dedalo/settings/registration/execute"],
            registerObjExecute,
            function(stream) {
              console.info("dedalo-register", stream); /* eslint-disable-line no-console */
            },
            function(success) {
              ctx.getConfig()
            },
            function(error) {
              console.error(error)  /* eslint-disable-line no-console */
            }
          );
        },
        function(error) {
          console.error(error)  /* eslint-disable-line no-console */
        }
      );
    },
    btUnregisterClick() {
      $("#unregisterModal").modal("show");
    },
    unregister() {
      $("#unregisterModal").modal("hide");
      this.uiLoaded = false
      nethserver.notifications.success = this.$i18n.t("settings.unregister_successful");
      nethserver.notifications.error = this.$i18n.t("settings.unregister_failed");

      var unregisterObj = {
        "action": "unregister",
        "logout": true
      }
      var ctx = this

      nethserver.exec(
        ["nethserver-dedalo/settings/registration/execute"],
        unregisterObj,
        function(stream) {
          console.info("dedalo-unregister", stream); /* eslint-disable-line no-console */
        },
        function(success) {
          ctx.unregisterSuccess()
        },
        function(error) {
          console.error(error)  /* eslint-disable-line no-console */
        }
      );
    },
    unregisterSuccess() {
      this.registered = false
      this.authenticated = false
      this.password = ""
      this.getToken()
    },
    networkDevicesLoaded(networkDevicesOutput) {
      var networkDevices = networkDevicesOutput.networkDevices

      if (networkDevices.length == 0) {
        this.showErrorMessage(this.$i18n.t("settings.no_network_device_found"))
        return false
      }
      this.networkDeviceList = []
      var networkDevice
      var foundHotspotDevice = false

      for (networkDevice of networkDevices) {
        this.networkDeviceList.push(networkDevice)

        if (networkDevice.hotspot_assigned) {
          this.networkDevice = networkDevice.name
          foundHotspotDevice = true
        }
      }
      
      if (!foundHotspotDevice) {
        this.networkDevice = this.networkDeviceList[0].name
      }

      var device = this.networkDeviceList.find(dev => dev.name === this.networkDevice);

      if (device.hotspot_assigned && device.ip_address) {
        this.warningMessage = this.$i18n.t("settings.warning_hotspot_device_ip_address")
      }

      return true
    },
    hotspotsLoaded(hotspotsOutput) {
      var hotspots = hotspotsOutput.hotspots.data
      this.hotspotList = []
      var hotspot

      for (hotspot of hotspots) {
        this.hotspotList.push(hotspot)
      }

      if (!this.selectedHotspotIndex) {
        // select the first hotspot by default
        this.selectedHotspotIndex = 0
      }
    },
    toggleProxy() {
      if (this.dedaloConfig.Proxy === "enabled") {
        this.dedaloConfig.Proxy = "disabled"
      } else {
        this.dedaloConfig.Proxy = "enabled"
      }
    },
    toggleLogTraffic() {
      if (this.dedaloConfig.LogTraffic === "enabled") {
        this.dedaloConfig.LogTraffic = "disabled"
      } else {
        this.dedaloConfig.LogTraffic = "enabled"
      }
    },
    closeErrorMessage() {
      this.errorMessage = null
    },
    closeWarningMessage() {
      this.warningMessage = null
    },
    changedNetworkDevice() {
      this.warningMessage = null
      var device = this.networkDeviceList.find(dev => dev.name === this.networkDevice);

      if (device.hotspot_assigned && device.ip_address) {
        this.warningMessage = this.$i18n.t("settings.warning_hotspot_device_ip_address")
      }
    },
    defaultDhcpRange() {
      var dhcpRangeObj = {
        "appInfo": "dhcpRange",
        "networkAddress": this.dedaloConfig.Network
      }
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/settings/registration/read"],
        dhcpRangeObj,
        null,
        function(success) {
          var dhcpRangeOutput = JSON.parse(success);
          var dhcpRange = dhcpRangeOutput.dhcpRange
          ctx.dhcpRangeStart = dhcpRange.start
          ctx.dhcpRangeEnd = dhcpRange.end
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_range_dhcp"), error)
        }
      );
    }
  }
};
</script>

<style scoped>
.margin-top-20 {
  margin-top: 20px
}
</style>
