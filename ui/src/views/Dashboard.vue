<template>
  <div>
    <h1>{{$t('dashboard.title')}}</h1>
      <!-- error message -->
      <div v-if="errorMessage" class="alert alert-danger alert-dismissable">
        <button type="button" class="close" @click="closeErrorMessage()" aria-label="Close">
          <span class="pficon pficon-close"></span>
        </button>
        <span class="pficon pficon-error-circle-o"></span>
        {{ errorMessage }}.
      </div>

      <div v-show="!uiLoaded" class="spinner spinner-lg"></div>
      <div v-show="uiLoaded">
        <div id="pie-chart-users"></div>

        <!-- todo delete   todo delete
        <a href="#" data-toggle="popover" data-html="true"
          :title="$t('dashboard.user_info')"
          :data-content="userInfo"
        >
          popover test
        </a> -->

        <!-- todo qui sotto v-if="uiLoaded" -->
        <vue-good-table 
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="tableColumns"
          :rows="dashboardData.hotspotUsers"
          :lineNumbers="false"
          :defaultSortBy="{field: 'ipAddress', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a href="#" data-toggle="popover" data-html="true"
                :title="$t('dashboard.user_info')"
                :id="'popover-' + props.row.ipAddress | sanitize"
                @click="getIpAddressInfo(props.row.ipAddress)"
              >
                {{ props.row.macAddress }}
              </a>
            </td>
            <td class="fancy">
              {{ props.row.ipAddress}}
            </td>
            <td class="fancy">
              <span :class="['fa', props.row.status === 'pass' ? 'fa-check green' : 'fa-times red']"></span>
            </td>
            <td class="fancy">
              {{ props.row.sessionKey }}
            </td>
            <td class="fancy">
              {{ readableDuration(parseInt(props.row.sessionTimeElapsed)) }}
            </td>
            <td class="fancy">
              {{ readableDuration(parseInt(props.row.idleTimeElapsed)) }}
            </td>
            <td class="fancy">
              {{ readableBytes(parseInt(props.row.inputOctetsDownloaded)) }}
            </td>
            <td class="fancy">
              {{ readableBytes(parseInt(props.row.outputOctetsUploaded)) }}
            </td>
            <td class="fancy">
              {{ props.row.downBandwidthPerc }}
            </td>
            <td class="fancy">
              {{ props.row.upBandwidthPerc }}
            </td>
          </template>
        </vue-good-table>
      </div>
  </div>
</template>

<script>
export default {
  name: "Dashboard",
  props: {
  },
  mounted() {
    this.getToken()
  },
  data() {
    return {
      uiLoaded: false,
      errorMessage: null,
      tableLangsTexts: this.tableLangs(),
      // dashboardData: null, // todo uncomment
      dashboardData: { // todo delete
        hotspotUsers: []
      },
      userInfo: 'todo',
      token: '',
      icaroHost: '',
      authenticated: false,
      tableColumns: [
        {
          label: this.$i18n.t("dashboard.mac_address"),
          field: "macAddress",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.ip_address"),
          field: "ipAddress",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.connected"),
          field: "status",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.session_key"),
          field: "sessionKey",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.session_time"),
          field: "sessionTimeElapsed",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.idle_time"),
          field: "idleTimeElapsed",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.downloaded"),
          field: "inputOctetsDownloaded"
        },
        {
          label: this.$i18n.t("dashboard.uploaded"),
          field: "outputOctetsUploaded"
        },
        {
          label: this.$i18n.t("dashboard.download_bandwidth"),
          field: "downBandwidthPerc"
        },
        {
          label: this.$i18n.t("dashboard.upload_bandwidth"),
          field: "upBandwidthPerc"
        }
      ]
    };
  },
  methods: {
    readDashboardData() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/dashboard/read"],
        { "appInfo": "dashboardData" },
        null,
        function(success) {
          var dashboardOutput = JSON.parse(success);
          ctx.readDashboardDataSuccess(dashboardOutput)
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("dashboard.error_retrieving_dashboard_data"), error)
        }
      );
    },
    readDashboardDataSuccess(dashboardOutput) {
      this.dashboardData = dashboardOutput.dashboardData
      this.initUsersChart()
      this.uiLoaded = true
      this.initPopovers()
    },
    showErrorMessage(errorMessage, error) {
      console.error(errorMessage, error) /* eslint-disable-line no-console */
      this.errorMessage = errorMessage
    },
    closeErrorMessage() {
      this.errorMessage = null
    },
    readableBytes(bytes) {
      if (bytes == 0) {
        return '0 B'
      }
      var i = Math.floor(Math.log(bytes) / Math.log(1024)),
      sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
    },
    readableDuration(sec_num) {
      var hours   = Math.floor(sec_num / 3600);
      var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
      var seconds = sec_num - (hours * 3600) - (minutes * 60);

      if (hours   < 10) {hours   = "0"+hours;}
      if (minutes < 10) {minutes = "0"+minutes;}
      if (seconds < 10) {seconds = "0"+seconds;}
      return hours+':'+minutes+':'+seconds;
    },
    initUsersChart() {
      var c3ChartDefaults = $().c3ChartDefaults();

      var pieData = {
        type : 'pie',
        colors: {
          'dnat users': $.pfPaletteColors.blue,
          'pass users': $.pfPaletteColors.green
        },
        columns: [
          ['dnat users', parseInt(this.dashboardData.dnatUsers)],
          ['pass users', parseInt(this.dashboardData.passUsers)]
        ]
      };

      var pieChartConfig = c3ChartDefaults.getDefaultPieConfig();
      pieChartConfig.bindto = '#pie-chart-users';
      pieChartConfig.data = pieData;
      pieChartConfig.legend = {
        show: true,
        position: 'right'
      };
      pieChartConfig.size = {
        width: 301,
        height: 211
      };
      var pieChartLegend = c3.generate(pieChartConfig);
    },
    getIpAddressInfo(ipAddress) {
      if (!this.authenticated) {
        // todo fix
        this.userInfo = this.$i18n.t("dashboard.please_authenticate_to_retrieve_user_info")
      } else {
        var popover = $("#" + this.$options.filters.sanitize("popover-" + ipAddress));
        var popoverData= popover.data("bs.popover");

        // if (popoverData.is(':visible')) { todo fix
        //   popoverData.hide();
        // } else {
          // show spinner on popover
          popoverData.options.content = '<div class="spinner spinner-sm"></div>';
          popoverData.show();

          var ctx = this;
          nethserver.exec(
            ["nethserver-dedalo/dashboard/read"],
            {
              "appInfo": "ipAddressInfo",
              "ipAddress": ipAddress,
              "token": this.token,
              "icaroHost": this.icaroHost
            },
            null,
            function(success) {
              var ipAddressInfoOutput = JSON.parse(success);
              ctx.getIpAddressInfoSuccess(ipAddressInfoOutput, popoverData)
            },
            function(error) {
              this.userInfo = ctx.$i18n.t("dashboard.error_retrieving_user_info")
              console.error(error) /* eslint-disable-line no-console */
            }
          );
        // }
      }
    },
    getIpAddressInfoSuccess(ipAddressInfoOutput, popoverData) {
      var ipAddressInfo = ipAddressInfoOutput.ipAddressInfo
      
      // if (ipAddressInfo.message) { // todo uncomment
      //   // an error occured
      //   this.userInfo = this.$i18n.t("dashboard.error_retrieving_user_info")
      //   console.error(ipAddressInfo.message)
      // } else {
        var userId = 672062 // todo ipAddressInfo.data[0].user_id
        var ctx = this;
        nethserver.exec(
          ["nethserver-dedalo/dashboard/read"],
          {
            "appInfo": "userInfo",
            "userId": userId,
            "token": this.token,
            "icaroHost": this.icaroHost
          },
          null,
          function(success) {
            var userInfoOutput = JSON.parse(success);
            ctx.getUserInfoSuccess(userInfoOutput, popoverData)
          },
          function(error) {
            ctx.userInfo = ctx.$i18n.t("dashboard.error_retrieving_user_info")
            console.error(error) /* eslint-disable-line no-console */
          }
        );
      // } // uncomment
    },
    getUserInfoSuccess(userInfoOutput, popoverData) {
      var userInfo = userInfoOutput.userInfo
      // if (userInfo.message) { // todo uncomment
      //   // an error occured
      //   this.userInfo = this.$i18n.t("dashboard.error_retrieving_user_info")
      //   console.error(userInfo.message)
      // } else {
        userInfo.name = "Tony Stark" // todo delete
        userInfo.email = "tony@stakindustries.com"
        userInfo.account_type = "Facebook"
        
        this.userInfo = '<p>' + this.$i18n.t("dashboard.user_info_name") + ': <b>' + userInfo.name + '</b></p>' + 
                        '<p>' + this.$i18n.t("dashboard.user_info_email") + ': <b>' + userInfo.email + '</b></p>' +
                        '<p>' + this.$i18n.t("dashboard.user_info_account_type") + ': <b>' + userInfo.account_type + '</b></p>'

        popoverData.options.content = this.userInfo;
        popoverData.show();
      // } // uncomment
    },
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
      var tokenData = tokenOutput.tokenData
      this.token = tokenData.token;
      this.icaroHost = tokenData.icaroHost;

      if (this.token) {
        this.authenticated = true
      } else {
        this.authenticated = false
      }
      this.readDashboardData()
    },
    initPopovers() {
      console.log("popover length", $('[data-toggle=popover]').length) // todo del
      console.log('dashboardData.hotspotUsers', this.dashboardData.hotspotUsers) // todo del

      // Initialize Popovers
      setTimeout(function() {
        $('[data-toggle=popover]').popovers()
          .on('hidden.bs.popover', function (e) {
            $(e.target).data('bs.popover').inState.click = false;
        });
      }, 250)
    }
  }
};
</script>

<style>
.red {
    color: #cc0000;
}

.green {
    color: #3f9c35;
}

#pie-chart-users {
  width: 301px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 20px;
}
</style>
